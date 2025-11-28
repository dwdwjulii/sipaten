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
use Carbon\Carbon;  

class PencatatanController extends Controller
{
    /**
     * Menampilkan halaman utama "To-Do List" untuk Petugas.
     */
    public function index(Request $request)
    {
        // ==========================================================
        // BAGIAN 1: Role Admin
        // ==========================================================
        if (auth()->user()->role === 'admin') {

            // --- BAGIAN 1: Persiapan data tahap ---
            $tahaps = Tahap::orderBy('tahun', 'desc')->orderBy('tahap_ke', 'desc')->get();
            
            $tahapDipilih = $request->filled('tahap_id') 
                ? Tahap::find($request->tahap_id) 
                : null;

                
            // --- PERSIAPAN LOGIKA SIKLUS (UNTUK BAGIAN 2 & 3) ---
            // Cek apakah ada siklus aktif (data yang belum di-lock)
            $adaSiklusAktif = Pencatatan::withoutGlobalScopes()
                                        ->where('is_locked', false)
                                        ->exists();

            // Kita tetap ambil timestamp terbaru HANYA untuk keperluan Arsip (Bagian 3)
            $timestampSiklusTerbaru = Pencatatan::withoutGlobalScopes()->max('tanggal_catatan');
            // --- AKHIR PERSIAPAN SIKLUS ---


            // ==========================================================
            // --- BAGIAN 2: Query data anggota (REVISI FINAL) ---
            // ==========================================================
            // 🔥 PERBAIKAN 1 (ADMIN QUERY): Gunakan 'pencatatans' bukan 'latestPencatatan'
            $query = Anggota::with([
                'tahap', 
                'ternaks', 
                'pencatatans' => function($q) { 
                    $q->where('is_locked', false) // Ambil yang aktif saja
                      ->with('details.ternak');   // Load detailnya sekalian
                }
            ]) 
            ->withCount(['ternaks' => function ($q) { 
                $q->where('status_aktif', 'aktif');
            }])
            ->where('status', 'aktif')
            ->orderBy('nama');


            // LOGIKA INTI: Tampilkan anggota HANYA JIKA ada siklus aktif
            if ($adaSiklusAktif) {
                // JIKA ADA SIKLUS AKTIF (Data apapun yang belum diarsip)...
                // ...tampilkan anggota yang memiliki pencatatan UNLOCKED tersebut.
                $query->whereHas('pencatatans', function ($q) {
                    $q->where('is_locked', false);
                });
            } else {
                // JIKA TIDAK ADA SIKLUS AKTIF (Semua terkunci/kosong)
                $query->whereRaw('1 = 0'); // Kosongkan tabel
            }

            // Filter berdasarkan tahap yang dipilih
            if ($tahapDipilih) {
                $query->where('tahap_id', $tahapDipilih->id);
            }

            // Filter berdasarkan pencarian nama
            if ($request->filled('search')) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            }

            // Filter status
            if ($request->filled('status_laporan')) {
                $status = $request->status_laporan;

                if ($status === 'sudah') {
                    // 🔥 UPDATE FILTER: Cek di koleksi pencatatans
                    $query->whereHas('pencatatans', function ($q) {
                        $q->where('is_locked', false)->has('details');
                    });
                } elseif ($status === 'belum') {
                    $query->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('pencatatans', function($q){
                            $q->where('is_locked', false);
                        })
                        ->orWhereHas('pencatatans', function ($q) {
                            $q->where('is_locked', false)->doesntHave('details');
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

            $anggotas = $query->paginate($perPage);

            
            
            // =================================================================
            // --- BAGIAN 3: Hitung status keseluruhan pencatatan (REVISI FINAL) ---
            // =================================================================

            $statusKeseluruhan = null;
            $statusArsip = false;

            // 🔥 LOGIKA STATUS JUGA DISESUAIKAN
            if ($adaSiklusAktif) {
                 // 4. CASE B: SIKLUS SEDANG AKTIF (Belum diarsip)

                 $statusKeseluruhan = 'success'; // Asumsi awal
                
                 foreach ($anggotas as $anggota) {
                    
                    // 🔥 PERBAIKAN 2 (ADMIN STATUS): Pilih record terbaik dari koleksi
                    // Jangan pakai $anggota->latestPencatatan, karena itu query baru ke DB dan kena Ghost Record
                    $p = $anggota->pencatatans->sortByDesc(function($item) {
                        return $item->details->count();
                    })->first();

                    $jumlahTernakAktifSekarang = $anggota->ternaks_count; 

                    // Cek jika anggota punya ternak tapi tidak punya record pencatatan
                    if ($jumlahTernakAktifSekarang > 0 && !$p) {
                        $statusKeseluruhan = 'error'; 
                        break;
                    }
                    
                    if (!$p) continue;

                    $jumlahDetailAktifTercatat = $p->details->filter(function($detail) {
                        return !empty($detail->kondisi_ternak) && 
                            $detail->ternak && 
                            $detail->ternak->status_aktif === 'aktif';
                    })->count();

                    $pernahAdaDetail = $p->details->filter(function($detail) {
                        return !empty($detail->kondisi_ternak);
                    })->isNotEmpty();

                    // 1. Jika ada ternak aktif sekarang TAPI TIDAK PERNAH ada detail
                    if ($jumlahTernakAktifSekarang > 0 && !$pernahAdaDetail) {
                        $statusKeseluruhan = 'error'; 
                        break; 
                    }
                    
                    // 2. Jika PERNAH ada detail TAPI jumlah detail aktif KURANG dari ternak aktif sekarang
                    if ($pernahAdaDetail && $jumlahDetailAktifTercatat < $jumlahTernakAktifSekarang) {
                        $statusKeseluruhan = 'warning'; 
                    }
                }
                
                $statusArsip = false;

            } else {
                // CASE A: TIDAK ADA SIKLUS AKTIF (Entah belum mulai atau sudah diarsip semua)
                
                // Cek apakah ini karena baru saja diarsip? (Untuk menampilkan Alert "Siap Mulai Baru")
                if ($timestampSiklusTerbaru) {
                    $timestampCarbon = Carbon::parse($timestampSiklusTerbaru);
                    $bulanSiklus = $timestampCarbon->month;
                    $tahunSiklus = $timestampCarbon->year;
                    
                    $arsipTerbaru = Arsip::orderBy('created_at', 'desc')->first();
                    
                    $adaArsipUntukSiklusTerbaru = false;
                    if ($arsipTerbaru) {
                        $adaArsipUntukSiklusTerbaru = ($arsipTerbaru->bulan == $bulanSiklus && 
                                                       $arsipTerbaru->tahun == $tahunSiklus);
                    }

                    if ($adaArsipUntukSiklusTerbaru) {
                         $adaTernakAktif = Anggota::where('status', 'aktif')
                            ->whereHas('ternaks', fn($q) => $q->where('status_aktif', 'aktif'))
                            ->exists();

                        if ($adaTernakAktif) {
                            $statusKeseluruhan = 'success';
                            $statusArsip = true; // <-- Alert 4 (Sudah Arsip, Siap Mulai Baru)
                        }
                    }
                }
            }
            
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
            $tahaps = Tahap::all();
            $tahapDipilih = $request->filled('tahap_id') ? Tahap::find($request->tahap_id) : null;
            
            $adaSiklusAktif = Pencatatan::withoutGlobalScopes()->where('is_locked', false)->exists();

            // Petugas terkunci jika TIDAK ADA siklus aktif
            $periodeTerkunci = !$adaSiklusAktif;

            if ($periodeTerkunci) {
                return view('petugas.pencatatan', [
                    'anggotas' => collect(),
                    'tahaps' => $tahaps,
                    'tahapDipilih' => $tahapDipilih,
                    'locked' => true
                ]);
            }
            
            // 🔥 PERBAIKAN 3 (PETUGAS QUERY): Ganti 'latestPencatatan' dengan 'pencatatans'
            $query = Anggota::with([
                'tahap', 
                'pencatatans' => function($q) { // <--- GANTI INI JUGA
                    $q->where('is_locked', false)
                      ->with('details.ternak');
                }
            ])
            ->withCount(['ternaks' => function ($q) {
                $q->where('status_aktif', 'aktif');
            }])
            ->where('status', 'aktif')
            ->whereHas('pencatatans', function ($q) {
                $q->where('is_locked', false);
            });

            if ($tahapDipilih) {
                $query->where('tahap_id', $tahapDipilih->id);
            }

            if ($request->filled('status')) {
                if ($request->status === 'sudah_dicatat') {
                    // 🔥 UPDATE FILTER: Cek di koleksi pencatatans
                    $query->whereHas('pencatatans', function ($q) {
                        $q->where('is_locked', false)->has('details');
                    });
                } elseif ($request->status === 'belum_dicatat') {
                    $query->whereHas('pencatatans', function ($q) {
                        $q->where('is_locked', false)->doesntHave('details');
                    });
                }
            }
            
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
        // 🚩 AWAL PERBAIKAN: Logika Siklus Aktif (Berdasarkan Status Lock, BUKAN Tanggal)
        // 🚩 =====================================================================
        
        // 1. Cek apakah ada siklus aktif secara global (ada data yang belum di-lock)
        // Kita tidak peduli tanggal berapa, yang penting ada yang 'unlocked'
        $adaSiklusAktif = Pencatatan::withoutGlobalScopes()
                                    ->where('is_locked', false)
                                    ->exists();

        // 2. Jika sistem terkunci semua (tidak ada siklus jalan), tolak.
        if (!$adaSiklusAktif) {
            return redirect()->route('pencatatan.index')
                ->with('error', 'Saat ini tidak ada periode pencatatan yang aktif.');
        }

        // 3. Ambil placeholder pencatatan milik anggota ini yang MASIH TERBUKA
        // 🔥 PERBAIKAN UTAMA: Hapus ->where('tanggal_catatan', ...)
        // Cukup cari yang milik user ini DAN belum di-lock.
        $pencatatanSiklusIni = $anggota->pencatatans()
            ->where('is_locked', false)
            ->first(); // HANYA boleh ada satu per anggota per siklus yang aktif

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
            ->withCount('anak')
            ->with([
                'anak' => function($query) {
                    // Eager load anak yang masih aktif
                    $query->where('status_aktif', 'aktif') // <-- INI JUGA CUKUP
                    ->withCount('anak');
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
        // 1. Ambil semua anggota dengan pencatatan AKTIF
        // 🔥 PERBAIKAN: Ganti 'latestPencatatan' dengan 'pencatatans' yang difilter is_locked=false
        // Ini agar kita bisa mengambil semua kandidat data (termasuk yang tgl 27 dan 28)
        $anggotas = Anggota::where('status', 'aktif')
            ->with([
                'tahap',
                // Load juga ternak untuk hitung harga total (fix harga 0)
                'ternaks' => function($q) {
                    $q->where('status_aktif', 'aktif')
                      ->where('tipe_ternak', 'Induk');
                },
                'pencatatans' => function ($query) {
                    $query->where('is_locked', false)
                          ->with('details.ternak');
                }
            ])
            ->get();

        // 2. Loop setiap anggota untuk memilih data yang TEPAT
        foreach ($anggotas as $anggota) {
            
            // 🔥 PERBAIKAN HARGA: Hitung on-the-fly agar sinkron
            $realTotalHarga = $anggota->ternaks->sum('harga');
            $anggota->setAttribute('total_harga_induk', $realTotalHarga);

            // 🔥 PERBAIKAN UTAMA: SMART SELECT
            // Cari pencatatan yang punya detail terbanyak.
            // Ini akan membuang Ghost Record (Tgl 28 kosong) dan mengambil Record Asli (Tgl 27 isi).
            $pencatatanPDF = $anggota->pencatatans->sortByDesc(function($p) {
                return $p->details->count();
            })->first();

            // Jika tidak ketemu data yang valid, kosongkan grup
            if (!$pencatatanPDF) {
                $anggota->groupedTernaks = collect();
                continue; 
            }
            
            $allDetails = $pencatatanPDF->details; // Gunakan data yang terpilih
            
            // --- LOGIKA GROUPING (SAMA SEPERTI SEBELUMNYA) ---
            $groupedTernaks = [];
            
            $indukDetails = $allDetails->filter(function($detail) {
                return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
            });

            $indukTernakIds = $indukDetails->pluck('ternak_id');

            foreach ($indukDetails as $indukDetail) {
                $groupedTernaks[] = [
                    'induk_detail' => $indukDetail,
                    'anak_details' => $allDetails->filter(function($detail) use ($indukDetail) {
                        return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id;
                    }),
                    'type' => 'parent_with_children',
                ];
            }

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
            
            // Tempelkan pencatatan terpilih ke objek anggota agar bisa diakses di View PDF
            // untuk mengambil foto dokumentasi & temuan lapangan
            $anggota->setRelation('latestPencatatan', $pencatatanPDF);
        }

        $bulanTahun = now()->format('F Y');
        $fileName = 'Laporan Keseluruhan - ' . $bulanTahun . '.pdf';

        $pdf = Pdf::loadView('pdf.laporan-keseluruhan', compact('anggotas', 'bulanTahun'));
        return $pdf->download($fileName);
    }


    /**
     * Mengarsipkan data pencatatan.
     */
    public function archiveLaporanKeseluruhan(Request $request)
    {
        try {
            // 1. Cek Siklus Aktif (Global)
            $adaSiklusAktif = Pencatatan::withoutGlobalScopes()
                                        ->where('is_locked', false)
                                        ->exists();
            
            if (!$adaSiklusAktif) {
                return back()->with('error', 'Tidak ada pekerjaan aktif yang bisa diarsipkan.');
            }
            
            // 2. Validasi Kelengkapan Data (Sama)
            $anggotaBelumLengkap = Anggota::where('status', 'aktif')
                ->whereHas('ternaks', function ($q) {
                    $q->where('status_aktif', 'aktif');
                })
                ->whereDoesntHave('pencatatans', function ($q) {
                    $q->where('is_locked', false)
                      ->has('details');
                })
                ->exists();
            
            if ($anggotaBelumLengkap) {
                return back()->with('error', 'Gagal, masih ada data anggota aktif yang belum dilengkapi oleh petugas.');
            }

            // 3. Ambil Data untuk PDF
            $pencatatansAktif = Pencatatan::where('is_locked', false)->get();
            $anggotaIds = $pencatatansAktif->pluck('anggota_id')->unique();
            
            $anggotas = Anggota::whereIn('id', $anggotaIds)
                ->with([
                    'tahap',
                    // 🔥 PERBAIKAN UTAMA: Load Relasi 'ternaks' agar Accessor harga bisa menghitung benar
                    'ternaks' => function($q) {
                        $q->where('status_aktif', 'aktif')
                          ->where('tipe_ternak', 'Induk');
                    },
                    'pencatatans' => function ($query) {
                        $query->where('is_locked', false)->with('details.ternak');
                    }
                ])
                ->get();
            
            // Loop Data untuk PDF (Logic Smart Select)
            foreach ($anggotas as $anggota) {
                
                 // Opsi Tambahan: Paksa set atribut jika Accessor tidak ada atau bermasalah
                 // Tapi dengan meload 'ternaks' di atas, biasanya ini sudah otomatis benar.
                 $realTotalHarga = $anggota->ternaks->sum('harga');
                 $anggota->setAttribute('total_harga_induk', $realTotalHarga);

                 // -----------------------------------------------------------

                 $pencatatanPDF = $anggota->pencatatans->sortByDesc(function($p) {
                    return $p->details->count();
                 })->first();

                 if (!$pencatatanPDF) { 
                    $anggota->groupedTernaks = collect();
                    continue; 
                 }

                 $allDetails = $pencatatanPDF->details;
                 
                 $groupedTernaks = [];
                 $indukDetails = $allDetails->filter(function($detail) { 
                     return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk'; 
                 });
                 $indukTernakIds = $indukDetails->pluck('ternak_id');
                 
                 foreach ($indukDetails as $indukDetail) {
                    $groupedTernaks[] = [
                        'induk_detail' => $indukDetail,
                        'anak_details' => $allDetails->filter(function($detail) use ($indukDetail) { 
                            return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id; 
                        }),
                        'type' => 'parent_with_children',
                    ];
                 }
                 
                 $anakYatimDetails = $allDetails->filter(function($detail) use ($indukTernakIds) { 
                     return $detail->ternak && $detail->ternak->tipe_ternak === 'Anak' && !$indukTernakIds->contains($detail->ternak->induk_id); 
                 });
                 
                 if ($anakYatimDetails->isNotEmpty()) {
                    $groupedTernaks[] = [
                        'induk_detail' => null, 
                        'anak_details' => $anakYatimDetails, 
                        'type' => 'orphan_children'
                    ];
                 }
                 $anggota->groupedTernaks = collect($groupedTernaks);
            }
            
            // 4. Generate PDF (Sama)
            $timestamp = now()->format('Y-m-d_H-i'); 
            $namaFile = 'Arsip Laporan - ' . $timestamp . '.pdf';
            $pathFile = 'arsip/keseluruhan/' . $namaFile;

            $pdf = \Pdf::loadView('pdf.laporan-keseluruhan', [
                'anggotas'   => $anggotas,
                'bulanTahun' => "Laporan Gabungan (Diarsip " . now()->format('d M Y') . ")",
            ]);

            \Storage::put('public/' . $pathFile, $pdf->output());

            // 5. Simpan Arsip (Sama)
            $firstRecord = $pencatatansAktif->first();
            $siklusCarbon = $firstRecord ? Carbon::parse($firstRecord->tanggal_catatan) : now();
            
            Arsip::create([
                'diarsipkan_oleh' => auth()->id(),
                'nama_file'       => $namaFile,
                'path_file'       => 'public/' . $pathFile,
                'bulan'           => $siklusCarbon->month,
                'tahun'           => $siklusCarbon->year,
            ]);

            // 6. Kunci Data (Sama)
            Pencatatan::where('is_locked', false)
                ->update(['is_locked' => true]);

            $jumlahLaporan = $anggotaIds->count();

            return redirect()->route('pencatatan.index')
                ->with('success', "Arsip berhasil! Laporan untuk {$jumlahLaporan} anggota telah dikunci.");
                
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
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 1. Load Pencatatan
        $pencatatan = Pencatatan::with([
            'anggota.ternaks',    
            'anggota.tahap',      
            // Load details.ternak dengan count anak untuk pengaman dropdown
            'details.ternak' => function($q) {
                $q->withCount('anak'); 
            }
        ])->findOrFail($id);

        if ($pencatatan->is_locked) {
            return redirect()->route('pencatatan.index')
                ->with('error', 'Catatan ini sudah diarsipkan.');
        }

        // ------------------------------------------------------------------
        // 🔥 PERBAIKAN UTAMA: MERGE STRATEGY + HYDRATION
        // ------------------------------------------------------------------

        // A. Ambil semua Data Master Ternak yang AKTIF milik anggota ini
        //    Ini untuk menangkap Induk/Anak baru yang ditambahkan SETELAH pencatatan dibuat
        $masterTernaks = Ternak::where('anggota_id', $pencatatan->anggota_id)
            ->where('status_aktif', 'aktif')
            ->withCount('anak') // Penting untuk pengaman dropdown
            ->get();

        // B. Ambil ID ternak yang SUDAH ada di tabel details
        $recordedTernakIds = $pencatatan->details->pluck('ternak_id')->toArray();

        // C. Siapkan Koleksi Utama (Mulai dengan data yang sudah tercatat)
        $allData = $pencatatan->details;

        // D. Cari Ternak "Hantu" (Ada di Master, tapi belum di Details)
        foreach ($masterTernaks as $ternak) {
            if (!in_array($ternak->id, $recordedTernakIds)) {
                // Buat Detail Dummy (Objek sementara di memori)
                $dummyDetail = new PencatatanDetail();
                $dummyDetail->pencatatan_id = $pencatatan->id;
                $dummyDetail->ternak_id = $ternak->id;
                
                // 🔥 CRITICAL FIX: ISI NILAI DEFAULT (HYDRATE)
                // Agar form tidak kosong dan lolos validasi 'required'
                $dummyDetail->umur_saat_dicatat = $ternak->umur_ternak ?? ''; // Ambil umur dari master jika ada
                $dummyDetail->kondisi_ternak    = 'Sehat'; // Default: Anggap Sehat
                $dummyDetail->status_vaksin     = 'Belum'; // Default: Anggap Belum
                
                // Manual set relasi agar blade bisa akses $detail->ternak->...
                $dummyDetail->setRelation('ternak', $ternak); 

                // Masukkan ke koleksi utama untuk ditampilkan
                $allData->push($dummyDetail);
            }
        }

        // ------------------------------------------------------------------
        // SEKARANG GROUPING MENGGUNAKAN $allData (BUKAN $pencatatan->details)
        // ------------------------------------------------------------------
        
        $groupedDetails = [];
        
        // 1. Filter Induk dari $allData
        $indukDetails = $allData->filter(function($detail) {
            return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
        });

        // Ambil ID Induk untuk pengecekan orphan nanti
        $indukTernakIds = $indukDetails->pluck('ternak_id'); 

        // 2. Pasangkan Anak ke Induk
        foreach ($indukDetails as $indukDetail) {
            $groupedDetails[] = [
                'induk' => $indukDetail,
                // Cari anak di $allData yang induk_id nya sesuai
                'anak' => $allData->filter(function($detail) use ($indukDetail) {
                    return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id;
                }),
                'type' => 'active_parent',
                'group_index' => count($groupedDetails) + 1
            ];
        }

        // 3. Cari Anak Yatim (Orphan) di $allData
        $anakYatimDetails = $allData->filter(function($detail) use ($indukTernakIds) {
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
        
        $anggota = $pencatatan->anggota; 

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
            
            // Validasi field dalam array ternaks
            'ternaks.*.tipe_ternak' => 'required|string',
            'ternaks.*.no_ear_tag' => 'required|string',
            'ternaks.*.jenis_kelamin' => 'required|string',
            'ternaks.*.umur_ternak' => 'required|string',
            'ternaks.*.kondisi_ternak' => 'required|string',
            'ternaks.*.status_vaksin' => 'required|string',
            
            // Validasi ID
            'ternaks.*.induk_id' => ['nullable'], 
            'ternaks.*.ternak_id' => ['nullable'], // Tambahkan ini agar lolos validasi
        ]);

        DB::beginTransaction();
        
        try {
            // 1. Handle foto
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

            // 2. Update pencatatan utama
            $pencatatan->update([
                'petugas_id' => auth()->id(), 
                'temuan_lapangan' => $validated['temuan_lapangan'] ?? null,
                'foto_dokumentasi' => $fotoPaths,
            ]);

            // 3. Hapus detail yang ditandai
            if ($request->has('delete_details')) {
                $detailIdsToDelete = $request->input('delete_details', []);
                if (!empty($detailIdsToDelete)) {
                    $detailsToDelete = PencatatanDetail::whereIn('id', $detailIdsToDelete)->get();
                    $ternakIdsToDeactivate = $detailsToDelete->pluck('ternak_id')->unique()->filter(); 

                    PencatatanDetail::whereIn('id', $detailIdsToDelete)->delete();

                    if ($ternakIdsToDeactivate->isNotEmpty()) {
                        Ternak::whereIn('id', $ternakIdsToDeactivate)
                             ->update(['status_aktif' => 'nonaktif']);
                    }
                }
            }

            // 4. LOOP DATA TERNAK (DIPERBAIKI: MENANGANI 3 KASUS)
            foreach ($request->ternaks as $key => $ternakData) {
                
                if (!isset($ternakData['tipe_ternak'])) continue;

                // ------------------------------------------------------------
                // KASUS 1: UPDATE DATA LAMA (Punya detail_id)
                // ------------------------------------------------------------
                if (isset($ternakData['detail_id']) && !empty($ternakData['detail_id'])) {
                    $detail = PencatatanDetail::find($ternakData['detail_id']);
                    if (!$detail) continue;

                    $ternakMaster = Ternak::find($detail->ternak_id);
                    if (!$ternakMaster) continue;

                    // Helper untuk update master (Status, Tipe, dll)
                    $this->updateMasterTernak($ternakMaster, $ternakData);
                    
                    // Update Detail Snapshot
                    $detail->update([
                        'umur_saat_dicatat'  => $ternakData['umur_ternak'],
                        'kondisi_ternak'     => $ternakData['kondisi_ternak'],
                        'status_vaksin'      => $ternakData['status_vaksin'],
                    ]);
                } 
                // ------------------------------------------------------------
                // 🔥 KASUS 2: DATA BARU DARI MASTER (Punya ternak_id, tapi TIDAK punya detail_id)
                // ------------------------------------------------------------
                // Bagian ini yang HILANG di kode Anda sebelumnya. 
                // Ini menangani ternak yang muncul dari "Merge Strategy" di edit.
                elseif (isset($ternakData['ternak_id']) && !empty($ternakData['ternak_id'])) {
                    
                    $ternakMaster = Ternak::find($ternakData['ternak_id']);
                    
                    if ($ternakMaster) {
                        // 1. Update Master dulu (siapa tau statusnya berubah mati/jual saat ini)
                        $this->updateMasterTernak($ternakMaster, $ternakData);

                        // 2. Buat Detail Baru (Tiket Masuk ke laporan ini)
                        PencatatanDetail::create([
                            'pencatatan_id'      => $pencatatan->id,
                            'ternak_id'          => $ternakMaster->id, 
                            'umur_saat_dicatat'  => $ternakData['umur_ternak'],
                            'kondisi_ternak'     => $ternakData['kondisi_ternak'],
                            'status_vaksin'      => $ternakData['status_vaksin'],
                        ]);
                    }
                }
                // ------------------------------------------------------------
                // KASUS 3: TAMBAH ANAK BARU (Punya induk_id)
                // ------------------------------------------------------------
                elseif (isset($ternakData['induk_id'])) {
                    $anakBaru = Ternak::create([
                        'anggota_id'    => $pencatatan->anggota_id,
                        'induk_id'      => $ternakData['induk_id'],
                        'tipe_ternak'   => 'Anak',
                        'no_ear_tag'    => $ternakData['no_ear_tag'],
                        'jenis_kelamin' => $ternakData['jenis_kelamin'],
                        'harga' => 0,
                        'status_aktif' => 'aktif'
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

            // 5. Sinkronisasi Akhir (Update Jumlah Induk di Tabel Anggota)
            DB::statement('SELECT 1'); 
            $anggotaId = $pencatatan->anggota_id;
            
            $jumlahIndukAktif = DB::table('ternaks')
                ->where('anggota_id', $anggotaId)
                ->where('tipe_ternak', 'Induk')
                ->where('status_aktif', 'aktif')
                ->lockForUpdate()
                ->count();

            DB::table('anggotas')->where('id', $anggotaId)->update([
                'jumlah_induk' => $jumlahIndukAktif,
                'updated_at' => now()
            ]);
            
            DB::commit();

            return redirect()->route('pencatatan.index', [], 303)
                ->with('success', 'Catatan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Helper function untuk update master (taruh di dalam class controller yang sama)
    private function updateMasterTernak($ternakMaster, $ternakData)
    {
        $updateData = [
            'tipe_ternak'   => $ternakData['tipe_ternak'],
            'no_ear_tag'    => $ternakData['no_ear_tag'],
            'jenis_kelamin' => $ternakData['jenis_kelamin'],
        ];

        if ($ternakData['tipe_ternak'] === 'Induk') {
            $updateData['induk_id'] = null;
        }

        if (in_array($ternakData['kondisi_ternak'], ['Mati', 'Terjual'])) {
            $updateData['status_aktif'] = 'nonaktif';
        } else {
            $updateData['status_aktif'] = 'aktif';
        }
        
        DB::table('ternaks')
            ->where('id', $ternakMaster->id)
            ->update(array_merge($updateData, ['updated_at' => now()]));
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
            // Gunakan withoutGlobalScopes untuk konsistensi
            $adaPekerjaanAktif = Pencatatan::withoutGlobalScopes()
                                        ->where('is_locked', false)
                                        ->exists();

            if ($adaPekerjaanAktif) {
                return redirect()->route('pencatatan.index')
                    ->with('error', 'Gagal! Harap arsipkan semua pekerjaan dari siklus sebelumnya terlebih dahulu.');
            }
            
            // 🔥 PERBAIKAN UTAMA: Tentukan timestamp SIKLUS di SINI, di LUAR loop.
            $timestampSiklusBaru = now();

            // Jika lolos, lanjutkan proses dalam sebuah transaksi
            DB::transaction(function () use ($timestampSiklusBaru) { // <-- Kirim timestamp ke transaksi
                
                // 2. Ambil HANYA anggota aktif yang MASIH PUNYA TERNAK HIDUP.
                $anggotasUntukPeriodeBaru = Anggota::where('status', 'aktif')
                    ->whereHas('ternaks', function ($q_ternak) { 
                        // Filter ternak yang masih 'aktif'
                        $q_ternak->where('status_aktif', 'aktif');
                    })
                    ->get();

                // 3. Buat placeholder baru HANYA untuk anggota yang memenuhi kriteria di atas.
                foreach ($anggotasUntukPeriodeBaru as $anggota) {
                    Pencatatan::create([
                        'anggota_id'      => $anggota->id,
                        'tanggal_catatan' => $timestampSiklusBaru, // <-- Gunakan timestamp yang SAMA
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