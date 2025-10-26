<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Tahap;
use App\Models\Pencatatan; 
use App\Models\Ternak;
use App\Models\Arsip;
use App\Models\PencatatanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;  

class PencatatanController extends Controller
{
    /**
     * Menampilkan halaman utama "To-Do List" untuk Petugas.
     */
    public function index(Request $request)
    {
        
        $statusArsip = false;
        $statusKeseluruhan = null;


        // ==========================================================
        // PREP: Tentukan tahap yang dipilih (jika ada)
        // ==========================================================
        $tahapDipilih = $request->filled('tahap_id')
            ? Tahap::find($request->tahap_id)
            : null;

        // Ambil semua tahap (dropdown filter)
        $tahaps = Tahap::all();

        $siklusAktif = Pencatatan::where('is_locked', false)
            ->latest('tanggal_catatan')
            ->first();

        // Jika ada siklus yang belum dikunci, gunakan tanggal dari siklus itu.
        // Jika tidak ada (semua sudah diarsip), baru gunakan tanggal sekarang.
        if ($siklusAktif) {
            $bulanAktif = $siklusAktif->tanggal_catatan->month;
            $tahunAktif = $siklusAktif->tanggal_catatan->year;
        } else {
            $bulanAktif = now()->month;
            $tahunAktif = now()->year;
        }
        // ==========================================================
        // 🔧 PERBAIKAN BULAN AKTIF - SELESAI
        // ==========================================================


        // File: app/Http/Controllers/PencatatanController.php -> method index()

        // ==========================================================
        // BAGIAN 1: Role Admin
        // ==========================================================
        if (auth()->user()->role === 'admin') {

            // --- BAGIAN 1: Persiapan data tahap ---
            $tahaps = Tahap::orderBy('tahun', 'desc')->orderBy('tahap_ke', 'desc')->get();
            
            // 👇 PERBAIKAN DI SINI: Gunakan 'tahap_id' bukan 'tahap'
            $tahapDipilih = $request->filled('tahap_id') 
                ? Tahap::find($request->tahap_id) 
                : null;

            
            // --- BAGIAN 2: Query data anggota ---
            $query = Anggota::with(['tahap', 'ternaks', 'latestPencatatan.details'])
                ->withCount(['ternaks' => function ($q) { // <-- TAMBAHKAN INI
                    $q->where('status_aktif', 'aktif');
                }])
                ->where('status', 'aktif')
                ->whereHas('pencatatans', function ($q) {
                    $q->where('is_locked', false);
                })
                ->orderBy('nama');

            // Filter berdasarkan tahap yang dipilih (ini sekarang akan berfungsi)
            if ($tahapDipilih) {
                $query->where('tahap_id', $tahapDipilih->id);
            }

            // Filter berdasarkan pencarian nama
            if ($request->filled('search')) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('status_laporan')) {
                $status = $request->status_laporan;

                if ($status === 'sudah') {
                    // Cari anggota yang punya pencatatan TERAKHIR DAN pencatatan itu punya DETAIL (sudah diisi)
                    $query->whereHas('latestPencatatan', function ($q) {
                        $q->has('details');
                    });
                } elseif ($status === 'belum') {
                    // Cari anggota yang:
                    // 1. Tidak punya pencatatan sama sekali (latestPencatatan is null)
                    // ATAU
                    // 2. Punya pencatatan TERAKHIR tapi TIDAK punya detail (belum diisi)
                    $query->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('latestPencatatan')
                                ->orWhereHas('latestPencatatan', function ($q) {
                                    $q->doesntHave('details');
                                });
                    });
                }
            }

            // Jalankan query dengan pagination
            $perPage = $request->input('per_page', 10);
            if ($perPage == 'Semua') {
                $total = $query->count();
                $perPage = $total > 0 ? $total : 10;
            } else {
                $perPage = (int) $perPage;
            }

            // Gunakan $perPage di sini
            $anggotas = $query->paginate($perPage);

            // --- BAGIAN 3: Hitung status keseluruhan pencatatan (Logika Baru) ---
            $adaPekerjaanAktif = Pencatatan::where('is_locked', false)->exists();
            $statusKeseluruhan = null; // Default

            if ($adaPekerjaanAktif) {
                // Ambil semua pencatatan aktif
                // Ambil semua pencatatan aktif DENGAN relasi yang benar
                $pencatatanAktif = Pencatatan::where('is_locked', false)
                ->with([
                    'details.ternak', // WAJIB ADA untuk memvalidasi ternak di filter
                    'anggota' => function ($q) {
                        $q->withCount(['ternaks' => fn($sub) => $sub->where('status_aktif', 'aktif')]);
                    }
                ])
                ->get();

                $hasError = false;
                $hasWarning = false;

                foreach ($pencatatanAktif as $p) {
                    if (!$p->anggota || $p->anggota->ternaks_count == 0) continue; 

                        $jumlahTernakAktif = $p->anggota->ternaks_count;
                        
                        // Logika KETAT (digunakan untuk membandingkan dengan Ternak Aktif):
                        $jumlahDetailLengkap = $p->details->filter(fn($d) => 
                            !empty($d->kondisi_ternak) && 
                            $d->ternak && 
                            $d->ternak->status_aktif === 'aktif'
                        )->count();
                        
                        // Logika LONGGAR (Cek apakah ada detail yang sudah diisi, TIDAK peduli status ternak masternya)
                        $detailsExistAndFilled = $p->details->filter(fn($d) => !empty($d->kondisi_ternak))->isNotEmpty();
                        
                        // 1. ERROR (Belum Dicatat Sama Sekali)
                        // Gunakan logika LONGGAR: ERROR hanya jika ada ternak aktif DAN belum ada isian sama sekali.
                        if ($jumlahTernakAktif > 0 && !$detailsExistAndFilled) { 
                            $hasError = true;
                        }

                        // 2. WARNING (Perlu Update / Belum Lengkap)
                        // WARNING jika ada isian (Longgar) DAN jumlah isian LENGKAP (Ketat) kurang dari total ternak aktif.
                        // Jika Komang Arnold ada isian (Perlu Update), maka $detailsExistAndFilled=true.
                        if ($detailsExistAndFilled && $jumlahDetailLengkap < $jumlahTernakAktif) {
                            $hasWarning = true; 
                        }
                    }
                
                // --- PENENTUAN STATUS GLOBAL AKHIR (PRIORITAS) ---
                if ($hasError) {
                    $statusKeseluruhan = 'error'; 
                } elseif ($hasWarning) {
                    $statusKeseluruhan = 'warning'; 
                } else {
                    $statusKeseluruhan = 'success'; // Semua sudah lengkap atau tidak ada ternak aktif
                }

                $statusArsip = false;

            } else {
                // Jika TIDAK ada pekerjaan aktif, berarti semua sudah diarsip.
                // Cek apakah perlu menampilkan tombol "Mulai Pencatatan Baru".
                $adaAnggotaUntukPeriodeBaru = Anggota::where('status', 'aktif')
                    ->whereHas('ternaks', function ($q_ternak) {
                        $q_ternak->whereDoesntHave('pencatatanDetails', function ($q_detail) {
                            $q_detail->whereIn('kondisi_ternak', ['Mati', 'Terjual']);
                        });
                    })
                    ->exists();

                if ($adaAnggotaUntukPeriodeBaru) {
                    $statusKeseluruhan = 'success';
                    $statusArsip = true;
                } else {
                    $statusKeseluruhan = null;
                    $statusArsip = false;   
                }
            }
            
            // --- BAGIAN 4: Kirim ke view ---
            return view('pencatatan', compact(
                'anggotas',
                'tahaps',
                'tahapDipilih',
                'statusKeseluruhan',
                'statusArsip'
            ));
        }

    
        // ==========================================================
        // BAGIAN 2: Role Petugas
        // ==========================================================
        else {
            // 🚩 PERBAIKAN 3: Logika kunci disederhanakan.
            // Periode terkunci jika sudah tidak ada lagi pekerjaan aktif sama sekali.
            $periodeTerkunci = !Pencatatan::where('is_locked', false)->exists();

            if ($periodeTerkunci) {
                return view('petugas.pencatatan', [
                    'anggotas' => collect(),
                    'tahaps' => $tahaps,
                    'tahapDipilih' => $tahapDipilih,
                    'locked' => true
                ]);
            }
            
            // 🚩 PERBAIKAN 4: Query diubah untuk menampilkan semua tugas aktif petugas.
            $query = Anggota::with(['tahap', 'latestPencatatan.details.ternak'])
                ->withCount(['ternaks' => function ($q) {
                    $q->where('status_aktif', 'aktif');
                }])
                ->where('status', 'aktif')
                // Ambil anggota yang punya pencatatan aktif (is_locked = false), tak peduli bulannya.
                ->whereHas('pencatatans', function ($q) {
                    $q->where('is_locked', false);
                });

            // Filter tahap tidak berubah
            if ($tahapDipilih) {
                $query->where('tahap_id', $tahapDipilih->id);
            }

            if ($request->filled('status')) {
                if ($request->status === 'sudah_dicatat') {
                    // Cari anggota yang pencatatan aktif terbarunya sudah punya detail
                    $query->whereHas('latestPencatatan', function ($q) {
                        $q->whereHas('details');
                    });
                } elseif ($request->status === 'belum_dicatat') {
                    // Cari anggota yang pencatatan aktif terbarunya belum punya detail
                    $query->whereHas('latestPencatatan', function ($q) {
                        $q->whereDoesntHave('details');
                    });
                }
            }
            
            // Filter search tidak berubah
            if ($request->filled('search')) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            }

            $anggotas = $query->paginate(12);

            return view('petugas.pencatatan', [
                'anggotas' => $anggotas,
                'tahaps' => $tahaps,
                'tahapDipilih' => $tahapDipilih,
                'locked' => false
            ]);
        }
    }


    /**
     * Menampilkan form untuk membuat/melengkapi pencatatan.
     */
    // app/Http/Controllers/PencatatanController.php

    // app/Http/Controllers/PencatatanController.php

    public function create(Anggota $anggota)
    {
        // 🔒 Batasi role
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'AKSES DITOLAK');
        }

        // 🚩 =====================================================================
        // 🚩 AWAL PERBAIKAN: Terapkan logika siklus aktif di sini juga.
        // 🚩 =====================================================================
        $siklusAktif = Pencatatan::where('is_locked', false)
            ->latest('tanggal_catatan')
            ->first();
        
        // Jika tidak ada siklus aktif sama sekali, tolak.
        if (!$siklusAktif) {
            return redirect()->route('pencatatan.index')
                ->with('error', 'Saat ini tidak ada periode pencatatan yang aktif.');
        }

        // Ambil bulan dan tahun dari siklus yang benar-benar aktif
        $bulanAktif = $siklusAktif->tanggal_catatan->month;
        $tahunAktif = $siklusAktif->tanggal_catatan->year;

        // ✅ Cek placeholder pencatatan berdasarkan siklus aktif
        $pencatatanSiklusIni = $anggota->pencatatans()
            ->whereMonth('tanggal_catatan', $bulanAktif)
            ->whereYear('tanggal_catatan', $tahunAktif)
            ->where('is_locked', false)
            ->latest()
            ->first();
        // 🚩 =====================================================================
        // 🚩 AKHIR PERBAIKAN
        // 🚩 =====================================================================

        // 1. Belum ada placeholder (admin belum reset) → tolak
        if (!$pencatatanSiklusIni) {
            return redirect()
                ->route('pencatatan.index')
                ->with('error', 'Periode untuk anggota ini belum dimulai oleh admin.');
        }

        // 2. Sudah ada tapi locked
        if ($pencatatanSiklusIni->is_locked) {
            return redirect()
                ->route('pencatatan.index')
                ->with('error', 'Catatan periode ini sudah diarsipkan dan tidak bisa diubah.');
        }

        // 3. Kalau sudah ada detail → kembalikan ke halaman utama
        if ($pencatatanSiklusIni->details()->exists()) {
            return redirect()
                ->route('pencatatan.index')
                ->with('info', 'Catatan sudah ada untuk periode ini.');
        }

        // 🔥 =====================================================================
        // 🔥 SOLUSI 2: KELOMPOKKAN TERNAK DENGAN LOGIKA ANAK YATIM
        // 🔥 =====================================================================
        
        
        
        // Load anggota dengan tahap
        $anggota->load('tahap');

        // Ambil semua ternak AKTIF (induk dan anak) HANYA berdasarkan status_aktif
        $ternaksAktif = Ternak::where('anggota_id', $anggota->id)
            ->where('status_aktif', 'aktif') // <-- CUKUP DENGAN FILTER INI
            ->with([
                'anak' => function($query) {
                    // Eager load anak yang masih aktif
                    $query->where('status_aktif', 'aktif'); // <-- INI JUGA CUKUP
                }, 
                'induk'
            ])
            ->get();

        // Kelompokkan ternak berdasarkan kategori (Sisa kode setelah ini sudah benar)
        $groupedTernaks = [];
        
        // 1️⃣ GRUP INDUK AKTIF beserta anak-anaknya
        $induksAktif = $ternaksAktif->where('tipe_ternak', 'Induk');
        
        foreach ($induksAktif as $induk) {
            $groupedTernaks[] = [
                'induk' => $induk,
                'anak' => $induk->anak, // Relasi hasMany yang sudah di-filter
                'type' => 'active_parent',
                'group_index' => count($groupedTernaks) + 1
            ];
        }
        
        // 2️⃣ GRUP ANAK YATIM (induknya sudah mati/terjual atau tidak punya induk)
        $anaksYatim = $ternaksAktif->filter(function($ternak) {
            // Hanya proses anak ternak
            if ($ternak->tipe_ternak !== 'Anak') {
                return false;
            }
            
            // ✅ Case 1: Tidak punya induk sama sekali (data lama / manual entry)
            if (!$ternak->induk_id) {
                return true;
            }
            
            // ✅ Case 2: Cek kondisi induk menggunakan relasi 'induk'
            $induknya = $ternak->induk; // 👈 Gunakan 'induk' sesuai model
            
            if (!$induknya) {
                // Induk tidak ditemukan (foreign key exist tapi data tidak ada)
                return true;
            }
            
            // ✅ Case 3: Induk sudah mati/terjual atau tidak aktif
            return in_array($induknya->kondisi_ternak, ['Mati', 'Terjual']) 
                || $induknya->status_aktif !== 'aktif';
        });
        
        // Jika ada anak yatim, tambahkan sebagai grup terpisah
        if ($anaksYatim->isNotEmpty()) {
            $groupedTernaks[] = [
                'induk' => null,
                'anak' => $anaksYatim,
                'type' => 'orphan',
                'group_index' => count($groupedTernaks) + 1
            ];
        }
        
        // 🔥 =====================================================================
        // 🔥 AKHIR SOLUSI 2
        // 🔥 =====================================================================

        // Ganti nama variabel agar konsisten
        $pencatatanBulanIni = $pencatatanSiklusIni;

        return view('petugas.pencatatan-create', compact('anggota', 'pencatatanBulanIni', 'groupedTernaks'));
    }


    /**
     * Menyimpan data pencatatan baru dari form.
     */
    public function store(Request $request)
    {
        // Pastikan hanya petugas yang bisa menyimpan
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'AKSES DITOLAK');
        }

        // 1. Validasi input, pastikan 'pencatatan_id' (ID placeholder) ada
        $validated = $request->validate([
            'pencatatan_id' => 'required|exists:pencatatans,id',
            'anggota_id' => 'required|exists:anggotas,id',
            'temuan_lapangan' => 'nullable|string',
            'foto_dokumentasi.*' => 'nullable|image|max:2048',
            'ternaks' => 'required|array',
            'ternaks.*.tipe_ternak' => 'required|in:Induk,Anak',
            'ternaks.*.no_ear_tag' => 'required|in:Ada,Tidak Ada',
            'ternaks.*.jenis_kelamin' => 'required|in:Jantan,Betina',
            'ternaks.*.umur_ternak' => 'required|string',
            'ternaks.*.kondisi_ternak' => 'required|string',
            'ternaks.*.status_vaksin' => 'required|string',
            'ternaks.*.ternak_id' => 'nullable|exists:ternaks,id',
            'ternaks.*.induk_id' => [
                'nullable',
                // Wajib diisi jika ini adalah anak baru (tidak punya ternak_id)
                'required_without:ternaks.*.ternak_id',
                'exists:ternaks,id'
            ],
        ]);

        DB::beginTransaction();
        try {
            // 2. KRUSIAL: Cari placeholder berdasarkan ID yang dikirim dari form
            $pencatatan = Pencatatan::findOrFail($validated['pencatatan_id']);

            // Keamanan tambahan: Cek lagi apakah placeholder sudah terkunci
            if ($pencatatan->is_locked) {
                return back()->with('error', 'Gagal, catatan ini sudah diarsipkan dan tidak bisa diubah.');
            }

            // 3. Siapkan data foto dokumentasi
            $fotoPaths = $pencatatan->foto_dokumentasi ?? [];
            if ($request->hasFile('foto_dokumentasi')) {
                foreach ($request->file('foto_dokumentasi') as $file) {
                    // Simpan ke folder dokumentasi_ternak tanpa prefix 'public/'
                    $path = $file->store('dokumentasi_ternak', 'public');
                    $fotoPaths[] = $path;
                }
            }
            
            // 4. UPDATE data utama pada record placeholder yang sudah ditemukan
            $pencatatan->update([
                'petugas_id' => Auth::id(),
                // 'tanggal_catatan' => now(), // Perbarui tanggal ke waktu pengisian
                'temuan_lapangan' => $validated['temuan_lapangan'],
                'foto_dokumentasi' => $fotoPaths,
            ]);

            // 5. Hapus detail lama jika ada (untuk memastikan data bersih)
            $pencatatan->details()->delete();

            // 6. Loop semua data ternak dari form untuk membuat detail baru
            foreach ($validated['ternaks'] as $dataTernak) {
                
                $ternakId = null;
                $ternakMaster = null; 

                // --- Tentukan status_aktif berdasarkan kondisi ---
                $statusAktif = in_array($dataTernak['kondisi_ternak'], ['Mati', 'Terjual']) ? 'nonaktif' : 'aktif';

                // --- Logika Update/Create Ternak Master (dengan status_aktif yang benar) ---
                if (isset($dataTernak['ternak_id']) && !empty($dataTernak['ternak_id'])) {
                    // A. Ini adalah update untuk ternak yang SUDAH ADA
                    $ternakId = $dataTernak['ternak_id'];
                    $ternakMaster = Ternak::find($ternakId);

                    if ($ternakMaster) {
                        $updateDataMaster = [
                            'no_ear_tag'    => $dataTernak['no_ear_tag'],
                            'jenis_kelamin' => $dataTernak['jenis_kelamin'],
                            'tipe_ternak'   => $dataTernak['tipe_ternak'],
                            'status_aktif'  => $statusAktif, // <-- Langsung set status
                        ];
                        // Handle jika dipromosikan jadi Induk
                        if ($dataTernak['tipe_ternak'] === 'Induk') {
                             $updateDataMaster['induk_id'] = null; 
                        }
                        $ternakMaster->update($updateDataMaster);
                    } else {
                         \Log::warning("Store: Ternak master not found for existing ternak_id: {$ternakId}");
                         continue; 
                    }

                } else {
                    // B. Ini adalah pembuatan ternak BARU
                    $createDataMaster = [
                        'anggota_id'    => $pencatatan->anggota_id,
                        'induk_id'      => $dataTernak['induk_id'] ?? null,
                        'tipe_ternak'   => $dataTernak['tipe_ternak'], 
                        'no_ear_tag'    => $dataTernak['no_ear_tag'],
                        'jenis_kelamin' => $dataTernak['jenis_kelamin'],
                        'harga'         => 0, 
                        'status_aktif'  => $statusAktif, // <-- Langsung set status
                    ];

                    // Handle jika ternak BARU ini langsung di-set sebagai Induk
                    if ($createDataMaster['tipe_ternak'] === 'Induk') {
                         $createDataMaster['induk_id'] = null;
                    } 

                    $newTernak = Ternak::create($createDataMaster);
                    $ternakId = $newTernak->id;
                    $ternakMaster = $newTernak; 
                }

                // --- Buat Pencatatan Detail ---
                // (Tidak perlu lagi update status_aktif di sini)
                if ($ternakMaster) { 
                    PencatatanDetail::create([
                        'pencatatan_id'     => $pencatatan->id,
                        'ternak_id'         => $ternakId,
                        'umur_saat_dicatat' => $dataTernak['umur_ternak'],
                        'kondisi_ternak'    => $dataTernak['kondisi_ternak'],
                        'status_vaksin'     => $dataTernak['status_vaksin'],
                    ]);
                } else {
                     \Log::error("Store: Failed to find or create ternak master for data: ", $dataTernak);
                }

            }

            $anggota = $pencatatan->anggota;
            $jumlahIndukAktif = $anggota->ternaks()
                                      ->where('tipe_ternak', 'Induk')
                                      ->where('status_aktif', 'aktif')
                                      ->count();
            $anggota->update(['jumlah_induk' => $jumlahIndukAktif]);
            
            DB::commit();

            
            return response()->redirectToRoute('pencatatan.index', [], 303)
                ->with('success', 'Catatan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Memberikan pesan error yang lebih spesifik saat development
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function exportLaporanKeseluruhan(Request $request)
    {
        // 1. Ambil semua anggota dengan pencatatan TERAKHIR mereka (latestPencatatan)
        $anggotas = Anggota::where('status', 'aktif')
            ->with([
                'tahap',
                // PENTING: Eager load pencatatan terakhir DENGAN detail dan ternaknya
                'latestPencatatan' => function ($query) {
                    $query->with('details.ternak');
                }
            ])
            ->get();

        // 2. Loop setiap anggota untuk membuat struktur 'groupedTernaks' dari data PENCATATAN
        foreach ($anggotas as $anggota) {
            // Jika anggota tidak punya pencatatan, buat koleksi kosong
            if (!$anggota->latestPencatatan) {
                $anggota->groupedTernaks = collect();
                continue; // Lanjut ke anggota berikutnya
            }
            
            $allDetails = $anggota->latestPencatatan->details;
            $groupedTernaks = [];
            
            // Ambil semua detail yang merupakan INDUK di pencatatan ini
            $indukDetails = $allDetails->filter(function($detail) {
                return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
            });

            // Ambil ID ternak dari induk-induk di atas
            $indukTernakIds = $indukDetails->pluck('ternak_id');

            // Buat grup untuk setiap INDUK beserta ANAK-ANAKNYA
            foreach ($indukDetails as $indukDetail) {
                $groupedTernaks[] = [
                    'induk_detail' => $indukDetail,
                    'anak_details' => $allDetails->filter(function($detail) use ($indukDetail) {
                        return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id;
                    }),
                    'type' => 'parent_with_children',
                ];
            }

            // Cari ANAK YATIM (anak yang induknya TIDAK ADA di pencatatan ini)
            $anakYatimDetails = $allDetails->filter(function($detail) use ($indukTernakIds) {
                return $detail->ternak && 
                    $detail->ternak->tipe_ternak === 'Anak' && 
                    !$indukTernakIds->contains($detail->ternak->induk_id);
            });

            if ($anakYatimDetails->isNotEmpty()) {
                $groupedTernaks[] = [
                    'induk_detail' => null,
                    'anak_details' => $anakYatimDetails,
                    'type' => 'orphan_children',
                ];
            }
            
            $anggota->groupedTernaks = collect($groupedTernaks);
        }

        $bulanTahun = now()->format('F Y');
        $fileName = 'Laporan Keseluruhan - ' . $bulanTahun . '.pdf';

        $pdf = Pdf::loadView('pdf.laporan-keseluruhan', compact('anggotas', 'bulanTahun'));
        return $pdf->download($fileName);
    }


    /**
     * Mengarsipkan data pencatatan.
     */
    // app/Http/Controllers/PencatatanController.php

    public function archiveLaporanKeseluruhan(Request $request)
    {
        try {
            // 1. Validasi: Pastikan ada setidaknya satu catatan aktif untuk diarsip
            $adaPekerjaanAktif = Pencatatan::where('is_locked', false)->exists();
            if (!$adaPekerjaanAktif) {
                return back()->with('error', 'Tidak ada pekerjaan aktif yang bisa diarsipkan.');
            }

            // 2. Validasi: Pastikan SEMUA catatan aktif yang SEHARUSNYA diisi, sudah terisi.
            $adaCatatanWajibYangKosong = Pencatatan::where('is_locked', false)
                ->whereDoesntHave('details') // Cari catatan kosong...
                ->whereHas('anggota', function ($q_anggota) { // ...milik anggota...
                    $q_anggota->where('status', 'aktif') // ...yang aktif DAN...
                              ->whereHas('ternaks', function ($q_ternak) { // ...masih punya ternak...
                                  // ...yang BELUM tercatat mati atau terjual.
                                  $q_ternak->whereDoesntHave('pencatatanDetails', function ($q_detail) {
                                      $q_detail->whereIn('kondisi_ternak', ['Mati', 'Terjual']);
                                  });
                              });
                })
                ->exists();
            
            // --- 👇 PERBAIKAN LOGIKA IF DI SINI (tanda '!' dihapus) 👇 ---
            if ($adaCatatanWajibYangKosong) {
                return back()->with('error', 'Gagal, masih ada data aktif yang belum dilengkapi oleh petugas.');
            }

            // 3. Ambil ID semua anggota yang memiliki pencatatan aktif.
            $pencatatansAktif = Pencatatan::where('is_locked', false)->get();
            $anggotaIds = $pencatatansAktif->pluck('anggota_id')->unique();
            
            // 🔥 PERBAIKAN UTAMA: Ambil anggota berdasarkan PENCATATAN TERAKHIR MEREKA
            $anggotas = Anggota::whereIn('id', $anggotaIds)
                ->with([
                    'tahap',
                    // PENTING: Eager load pencatatan terakhir DENGAN detail dan ternaknya
                    'latestPencatatan' => function ($query) {
                        $query->with('details.ternak');
                    }
                ])
                ->get();
                
            // 🔥 LOGIKA PENGELOMPOKAN BERDASARKAN DETAIL PENCATATAN
            foreach ($anggotas as $anggota) {
                // Jika anggota tidak punya pencatatan, buat koleksi kosong (pengaman)
                if (!$anggota->latestPencatatan) {
                    $anggota->groupedTernaks = collect();
                    continue; // Lanjut ke anggota berikutnya
                }
                
                $allDetails = $anggota->latestPencatatan->details;
                $groupedTernaks = [];
                
                // Ambil semua detail yang merupakan INDUK di pencatatan ini
                $indukDetails = $allDetails->filter(function($detail) {
                    return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
                });

                // Ambil ID ternak dari induk-induk di atas
                $indukTernakIds = $indukDetails->pluck('ternak_id');

                // Buat grup untuk setiap INDUK beserta ANAK-ANAKNYA
                foreach ($indukDetails as $indukDetail) {
                    $groupedTernaks[] = [
                        'induk_detail' => $indukDetail,
                        'anak_details' => $allDetails->filter(function($detail) use ($indukDetail) {
                            return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id;
                        }),
                        'type' => 'parent_with_children',
                    ];
                }

                // Cari ANAK YATIM (anak yang induknya TIDAK ADA di pencatatan ini)
                $anakYatimDetails = $allDetails->filter(function($detail) use ($indukTernakIds) {
                    return $detail->ternak && 
                        $detail->ternak->tipe_ternak === 'Anak' && 
                        !$indukTernakIds->contains($detail->ternak->induk_id);
                });

                if ($anakYatimDetails->isNotEmpty()) {
                    $groupedTernaks[] = [
                        'induk_detail' => null,
                        'anak_details' => $anakYatimDetails,
                        'type' => 'orphan_children',
                    ];
                }
                
                $anggota->groupedTernaks = collect($groupedTernaks);
            }
            
            

            // 4. Buat nama file dan path (tidak berubah)
           
            $timestamp = now()->format('Y-m-d_H-i'); 
            $namaFile = 'Arsip Gabungan - ' . $timestamp . '.pdf';
            $pathFile = 'arsip/keseluruhan/' . $namaFile;

            // Buat PDF (tidak berubah)
            $pdf = \Pdf::loadView('pdf.laporan-keseluruhan', [
                'anggotas'   => $anggotas,
                'bulanTahun' => "Laporan Gabungan (Diarsip " . now()->format('d M Y') . ")",
            ]);

            \Storage::put('public/' . $pathFile, $pdf->output());

            Arsip::create([
                'diarsipkan_oleh' => auth()->id(),
                'nama_file'       => $namaFile,
                'path_file'       => 'public/' . $pathFile,
                'bulan'           => now()->month,
                'tahun'           => now()->year,
            ]);

            // 5. Kunci SEMUA catatan yang statusnya is_locked = false.
            $lockedCount = Pencatatan::where('is_locked', false)
                ->update(['is_locked' => true]);

            return redirect()->route('pencatatan.index')
                ->with('success', "Arsip berhasil! {$lockedCount} catatan dari semua periode aktif telah dikunci.");
                
        } catch (\Exception $e) {
            \Log::error('Arsip Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengarsipkan laporan: ' . $e->getMessage());
        }
    }



    /**
     * Menampilkan halaman detail satu pencatatan.
     */
    public function show(Pencatatan $pencatatan)
    {
        // Gunakan Eager Loading untuk efisiensi
        $pencatatan->load('anggota.tahap', 'petugas', 'details.ternak');

        // Nama view-nya sesuaikan dengan file view Anda
        return view('admin.pencatatan.show', compact('pencatatan'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Ambil pencatatan dengan semua relasi yang dibutuhkan
        $pencatatan = Pencatatan::with([
            'anggota.ternaks',    // Untuk jumlah induk
            'anggota.tahap',      // Untuk tahap
            'details.ternak'      // Untuk data ternak yang sudah dicatat
        ])->findOrFail($id);

        if ($pencatatan->is_locked) {
            return redirect()->route('pencatatan.index')
                ->with('error', 'Catatan ini sudah diarsipkan dan tidak bisa diubah. Silakan buat catatan baru.');
        }

        // Eager load relasi yang dibutuhkan untuk efisiensi
        $pencatatan->load('details.ternak');
        
        $allDetails = $pencatatan->details;
        $groupedDetails = [];
        
        // 1. Ambil semua detail yang merupakan INDUK
        $indukDetails = $allDetails->filter(function($detail) {
            return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
        });

        // Ambil ID semua ternak induk yang ada di pencatatan ini
        $indukTernakIds = $indukDetails->pluck('ternak_id');

        // 2. Buat grup untuk setiap INDUK beserta ANAK-ANAKNYA
        foreach ($indukDetails as $indukDetail) {
            $groupedDetails[] = [
                'induk' => $indukDetail,
                'anak' => $allDetails->filter(function($detail) use ($indukDetail) {
                    return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id;
                }),
                'type' => 'active_parent',
                'group_index' => count($groupedDetails) + 1
            ];
        }

        // 3. Cari ANAK YATIM (anak yang induknya tidak ada di pencatatan ini)
        $anakYatimDetails = $allDetails->filter(function($detail) use ($indukTernakIds) {
            return $detail->ternak 
                && $detail->ternak->tipe_ternak === 'Anak' 
                && !$indukTernakIds->contains($detail->ternak->induk_id);
        });

        if ($anakYatimDetails->isNotEmpty()) {
            $groupedDetails[] = [
                'induk' => null,
                'anak' => $anakYatimDetails,
                'type' => 'orphan',
                'group_index' => count($groupedDetails) + 1
            ];
        }
        
        $anggota = $pencatatan->anggota->load('ternaks'); // Muat semua ternak untuk dropdown

        // Kirim $indukDetails sebagai data utama untuk di-loop
        return view('petugas.pencatatan-edit', compact('pencatatan', 'anggota', 'groupedDetails'));  
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pencatatan = Pencatatan::with('anggota.ternaks')->findOrFail($id);

        if ($pencatatan->is_locked) {
            return redirect()
                ->route('pencatatan.show', $pencatatan->id)
                ->with('error', 'Catatan ini sudah diarsipkan dan tidak bisa diubah.');
        }

        $validated = $request->validate([
            'temuan_lapangan' => 'nullable|string',
            'foto_dokumentasi' => 'nullable|array',
            'foto_dokumentasi.*' => 'file|mimes:jpg,jpeg,png|max:2048',
            'existing_photos' => 'nullable|array',
            'existing_photos.*' => 'string',
            'ternaks' => 'required|array',
            'ternaks.*' => 'required|array',
            'delete_details' => 'nullable|array',
            'ternaks.*.tipe_ternak' => 'required|string',
            'ternaks.*.no_ear_tag' => 'required|string',
            'ternaks.*.jenis_kelamin' => 'required|string',
            'ternaks.*.umur_ternak' => 'required|string',
            'ternaks.*.kondisi_ternak' => 'required|string',
            'ternaks.*.status_vaksin' => 'required|string',
            'ternaks.*.induk_id' => ['nullable', 'required_without:ternaks.*.detail_id', 'exists:ternaks,id'],
        ]);

        DB::beginTransaction();
        
        try {
            // Handle foto
            $fotoPaths = $request->input('existing_photos', []);
            if ($request->hasFile('foto_dokumentasi')) {
                foreach ($request->file('foto_dokumentasi') as $foto) {
                    $fotoPaths[] = $foto->store('dokumentasi', 'public');
                }
            }
            $oldPhotos = $pencatatan->foto_dokumentasi ?? [];
            $photosToDelete = array_diff($oldPhotos, $fotoPaths);
            foreach ($photosToDelete as $photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            // Update pencatatan utama
            $pencatatan->update([
                'petugas_id' => auth()->id(), 
                'temuan_lapangan' => $validated['temuan_lapangan'] ?? null,
                'foto_dokumentasi' => $fotoPaths,
            ]);

            // Hapus detail yang ditandai DAN nonaktifkan ternak masternya
            if ($request->has('delete_details')) {
                $detailIdsToDelete = $request->input('delete_details', []);
                
                if (!empty($detailIdsToDelete)) {
                    // 1. Ambil detail yang akan dihapus untuk mendapatkan ternak_id-nya
                    $detailsToDelete = PencatatanDetail::whereIn('id', $detailIdsToDelete)->get();
                    $ternakIdsToDeactivate = $detailsToDelete->pluck('ternak_id')->unique()->filter(); // Ambil ID ternak unik & filter null

                    // 2. Hapus detailnya
                    PencatatanDetail::whereIn('id', $detailIdsToDelete)->delete();

                    // 3. Nonaktifkan ternak master yang bersangkutan
                    if ($ternakIdsToDeactivate->isNotEmpty()) {
                        \Log::info('Menonaktifkan Ternak Master karena detail dihapus', $ternakIdsToDeactivate->toArray());
                        Ternak::whereIn('id', $ternakIdsToDeactivate)
                              ->update(['status_aktif' => 'nonaktif']);
                    }
                }
            }

            // 🔥 PERBAIKAN TOTAL: Tambahkan logging untuk debug
            \Log::info('Data ternaks yang diterima:', $request->ternaks);

            // Proses setiap data ternak
            foreach ($request->ternaks as $key => $ternakData) {
                
                \Log::info("Processing ternak key: {$key}", $ternakData);
                
                if (!isset($ternakData['tipe_ternak'])) {
                    \Log::warning("Skipped - no tipe_ternak for key: {$key}");
                    continue;
                }

                // 🔥 PERBAIKAN: Cek apakah ini update (ada detail_id) atau create baru
                if (isset($ternakData['detail_id']) && !empty($ternakData['detail_id'])) {
                    // INI ADALAH UPDATE TERNAK EXISTING
                    $detail = PencatatanDetail::find($ternakData['detail_id']);

                    if (!$detail) {
                        \Log::error("Detail not found for detail_id: {$ternakData['detail_id']}");
                        continue;
                    }

                    $ternakMaster = Ternak::find($detail->ternak_id);
                    
                    if (!$ternakMaster) {
                        \Log::error("Ternak master not found for ternak_id: {$detail->ternak_id}");
                        continue;
                    }

                    // Siapkan data update
                    $updateData = [
                        'tipe_ternak'   => $ternakData['tipe_ternak'],
                        'no_ear_tag'    => $ternakData['no_ear_tag'],
                        'jenis_kelamin' => $ternakData['jenis_kelamin'],
                    ];

                    // Promosi anak jadi induk
                    if ($ternakData['tipe_ternak'] === 'Induk') {
                        $updateData['induk_id'] = null;
                    }

                    // 🔥 LOGIKA KUNCI: Set status berdasarkan kondisi
                    if (in_array($ternakData['kondisi_ternak'], ['Mati', 'Terjual'])) {
                    // Jika ternak mati/terjual, HANYA set status nonaktif
                        $updateData['status_aktif'] = 'nonaktif';
                    } else {
                        // Jika ternak hidup/sehat, HANYA set status aktif
                        $updateData['status_aktif'] = 'aktif';
                    }
                    // HARGA TIDAK DIUBAH SAMA SEKALI OLEH PETUGAS
                    
                    // 🔥 PERBAIKAN: Update menggunakan Query Builder untuk memastikan tersimpan
                    $affected = DB::table('ternaks')
                        ->where('id', $ternakMaster->id)
                        ->update(array_merge($updateData, ['updated_at' => now()]));
                    
                    // 🔍 DEBUG LOG
                    \Log::info('Ternak updated successfully', [
                        'ternak_id' => $ternakMaster->id,
                        'kondisi' => $ternakData['kondisi_ternak'],
                        'status_aktif' => $updateData['status_aktif'],
                        'harga' => $ternakMaster->harga,
                        'affected_rows' => $affected
                    ]);

                    // Update detail pencatatan (snapshot)
                    $detail->update([
                        'umur_saat_dicatat'  => $ternakData['umur_ternak'],
                        'kondisi_ternak'     => $ternakData['kondisi_ternak'],
                        'status_vaksin'      => $ternakData['status_vaksin'],
                    ]);
                } 
                // Tambah anak baru
                elseif (isset($ternakData['induk_id'])) {
                    \Log::info("Creating new anak with induk_id: {$ternakData['induk_id']}");
                    $anakBaru = Ternak::create([
                        'anggota_id'    => $pencatatan->anggota_id,
                        'induk_id'      => $ternakData['induk_id'],
                        'tipe_ternak'   => 'Anak',
                        'no_ear_tag'    => $ternakData['no_ear_tag'],
                        'jenis_kelamin' => $ternakData['jenis_kelamin'],
                        'harga' => 0,
                    ]);

                    PencatatanDetail::create([
                        'pencatatan_id'      => $pencatatan->id,
                        'ternak_id'          => $anakBaru->id,
                        'umur_saat_dicatat'  => $ternakData['umur_ternak'],
                        'kondisi_ternak'     => $ternakData['kondisi_ternak'],
                        'status_vaksin'      => $ternakData['status_vaksin'],
                    ]);
                }
            }

            // 🔥 PERBAIKAN: Flush semua model yang di-update ke database
            DB::statement('SELECT 1'); // Dummy query untuk flush perubahan
            
            // 🔥 PERBAIKAN: Sinkronisasi DALAM transaksi, SEBELUM commit
            $anggotaId = $pencatatan->anggota_id;
            
            // Query fresh dari database (masih dalam transaksi) dengan lock untuk memastikan data terbaru
            $jumlahIndukAktif = DB::table('ternaks')
                ->where('anggota_id', $anggotaId)
                ->where('tipe_ternak', 'Induk')
                ->where('status_aktif', 'aktif')
                ->lockForUpdate() // Paksa baca data terbaru dalam transaksi
                ->count();

            \Log::info('Sinkronisasi jumlah induk', [
                'anggota_id' => $anggotaId,
                'jumlah_induk_aktif' => $jumlahIndukAktif
            ]);
            
            // 🔍 DEBUG: Cek semua ternak untuk anggota ini
            $allTernaks = DB::table('ternaks')
                ->where('anggota_id', $anggotaId)
                ->where('tipe_ternak', 'Induk')
                ->select('id', 'status_aktif')
                ->get();
            \Log::info('All induk for anggota:', $allTernaks->toArray());

            // Update jumlah induk
            DB::table('anggotas')
                ->where('id', $anggotaId)
                ->update([
                    'jumlah_induk' => $jumlahIndukAktif,
                    'updated_at' => now()
                ]);

            // Commit semua perubahan sekaligus
            DB::commit();

            \Log::info('Update pencatatan completed successfully', [
                'pencatatan_id' => $pencatatan->id,
                'anggota_id' => $anggotaId,
                'final_jumlah_induk' => $jumlahIndukAktif
            ]);

            return redirect()
                ->route('pencatatan.index', [], 303)
                ->with('success', 'Catatan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating pencatatan: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // app/Http/Controllers/PencatatanController.php

    public function reset()
    {
        try {
            // 1. "Penjaga Gerbang": Cek apakah masih ada pekerjaan aktif yang belum diarsipkan.
            $adaPekerjaanAktif = Pencatatan::where('is_locked', false)->exists();

            if ($adaPekerjaanAktif) {
                return redirect()->route('pencatatan.index')
                    ->with('error', 'Gagal! Harap arsipkan semua pekerjaan dari siklus sebelumnya terlebih dahulu.');
            }
            
            // Jika lolos, lanjutkan proses dalam sebuah transaksi
            DB::transaction(function () {
                // --- 👇 PERBAIKAN UTAMA DI SINI 👇 ---
                // 2. Ambil HANYA anggota aktif yang MASIH PUNYA TERNAK HIDUP.
                $anggotasUntukPeriodeBaru = Anggota::where('status', 'aktif')
                    ->whereHas('ternaks', function ($q_ternak) { // Cek apakah anggota punya ternak...
                        // ...yang BELUM tercatat mati atau terjual.
                        $q_ternak->whereDoesntHave('pencatatanDetails', function ($q_detail) {
                            $q_detail->whereIn('kondisi_ternak', ['Mati', 'Terjual']);
                        });
                    })
                    ->get();

                // 3. Buat placeholder baru HANYA untuk anggota yang memenuhi kriteria di atas.
                foreach ($anggotasUntukPeriodeBaru as $anggota) {
                    Pencatatan::create([
                        'anggota_id'      => $anggota->id,
                        'tanggal_catatan' => now(), // Tanggal placeholder dibuat
                        'is_locked'       => false,
                        'petugas_id'      => null,
                    ]);
                }
            });
            
            return redirect()->route('pencatatan.index')
                ->with('success', "Periode pencatatan baru berhasil dimulai!");

        } catch (\Exception $e) {
            \Log::error('Reset Error: ' . $e->getMessage());
            return redirect()->route('pencatatan.index')
                ->with('error', 'Terjadi kesalahan saat memulai periode baru: ' . $e->getMessage());
        }
    }



}