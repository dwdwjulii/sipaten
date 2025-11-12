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
    
    public function index(Request $request)
    {
        
        // Role Admin
        if (auth()->user()->role === 'admin') {

            // --- Persiapan data tahap ---
            $tahaps = Tahap::orderBy('tahun', 'desc')->orderBy('tahap_ke', 'desc')->get();
            
            $tahapDipilih = $request->filled('tahap_id') 
                ? Tahap::find($request->tahap_id) 
                : null;

            $timestampSiklusTerbaru = Pencatatan::withoutGlobalScopes()->max('tanggal_catatan');
            
            $semuaSiklusTerbaruDiarsip = true; 

            if ($timestampSiklusTerbaru) {
                
                $semuaSiklusTerbaruDiarsip = !Pencatatan::withoutGlobalScopes()
                                                      ->where('tanggal_catatan', $timestampSiklusTerbaru)
                                                      ->where('is_locked', false)
                                                      ->exists();
            }

            $query = Anggota::with(['tahap', 'ternaks', 'latestPencatatan.details.ternak']) 
                ->withCount(['ternaks' => function ($q) { 
                    $q->where('status_aktif', 'aktif');
                }])
                ->where('status', 'aktif')
                ->orderBy('nama');


            if ($timestampSiklusTerbaru && !$semuaSiklusTerbaruDiarsip) {
  
                $query->whereHas('pencatatans', function ($q) use ($timestampSiklusTerbaru) {
                    $q->where('is_locked', false)
                      ->where('tanggal_catatan', $timestampSiklusTerbaru);
                });
            } else {
                
                $query->whereRaw('1 = 0'); 
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
                    $query->whereHas('latestPencatatan', function ($q) {
                        $q->has('details');
                    });
                } elseif ($status === 'belum') {
                    $query->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('latestPencatatan')
                                ->orWhereHas('latestPencatatan', function ($q) {
                                    $q->doesntHave('details');
                                });
                    });
                }
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            if ($perPage == 'Semua') {
                $total = $query->count();
                $perPage = $total > 0 ? $total : 10;
            } else {
                $perPage = (int) $perPage;
            }

            $anggotas = $query->paginate($perPage);


            $statusKeseluruhan = null;
            $statusArsip = false;

            if ($timestampSiklusTerbaru) {
                if ($semuaSiklusTerbaruDiarsip) {
                    
                    $timestampCarbon = Carbon::parse($timestampSiklusTerbaru);
                    $bulanSiklus = $timestampCarbon->month;
                    $tahunSiklus = $timestampCarbon->year;
                    
                    $arsipTerbaru = Arsip::orderBy('created_at', 'desc')->first();
                    
                    $adaArsipUntukSiklusTerbaru = false;
                    if ($arsipTerbaru) {
                        $adaArsipUntukSiklusTerbaru = ($arsipTerbaru->bulan == $bulanSiklus && 
                                                    $arsipTerbaru->tahun == $tahunSiklus);
                    }
                    
                    \Log::info('🔍 DEBUG Alert Kondisi 3/4', [
                        'timestamp_siklus_terbaru' => $timestampSiklusTerbaru,
                        'bulan_siklus' => $bulanSiklus,
                        'tahun_siklus' => $tahunSiklus,
                        'arsip_terbaru_id' => $arsipTerbaru?->id,
                        'arsip_terbaru_bulan' => $arsipTerbaru?->bulan,
                        'arsip_terbaru_tahun' => $arsipTerbaru?->tahun,
                        'arsip_terbaru_created' => $arsipTerbaru?->created_at,
                        'ada_arsip_untuk_siklus_terbaru' => $adaArsipUntukSiklusTerbaru,
                        'semua_siklus_terbaru_diarsip' => $semuaSiklusTerbaruDiarsip
                    ]);
                    
                    if ($adaArsipUntukSiklusTerbaru) {
                        $adaTernakAktifUntukSiklusBaru = Anggota::where('status', 'aktif')
                            ->whereHas('ternaks', function ($q_ternak) {
                                $q_ternak->where('status_aktif', 'aktif');
                            })
                            ->exists();

                        if ($adaTernakAktifUntukSiklusBaru) {
                            $statusKeseluruhan = 'success';
                            $statusArsip = true; 
                        } else {
                            $statusKeseluruhan = null; 
                            $statusArsip = false; 
                        }
                    } else {
                        $statusKeseluruhan = null;
                        $statusArsip = false;
                    }

                } else {
                   
                    $statusKeseluruhan = 'success'; 

                    foreach ($anggotas as $anggota) {
                        $p = $anggota->latestPencatatan; 
                       
                        $jumlahTernakAktifSekarang = $anggota->ternaks_count; 

                        // Cek jika anggota punya ternak tapi tidak punya record pencatatan
                        if ($jumlahTernakAktifSekarang > 0 && !$p) {
                            $statusKeseluruhan = 'error'; 
                            break;
                        }
                        
                        $jumlahDetailAktifTercatat = $p->details->filter(function($detail) {
                            return !empty($detail->kondisi_ternak) && 
                                $detail->ternak && 
                                $detail->ternak->status_aktif === 'aktif';
                        })->count();
                        $pernahAdaDetail = $p->details->filter(function($detail) {
                            return !empty($detail->kondisi_ternak);
                        })->isNotEmpty();
                       
                        if ($jumlahTernakAktifSekarang > 0 && !$pernahAdaDetail) {
                            $statusKeseluruhan = 'error'; 
                            break; 
                        }
                        
                        if ($pernahAdaDetail && $jumlahDetailAktifTercatat < $jumlahTernakAktifSekarang) {
                            $statusKeseluruhan = 'warning'; 
            
                        }
                        
                        \Log::info('Cek Status Anggota (FIXED)', [
                            'anggota_id' => $anggota->id,
                            'nama' => $anggota->nama,
                            'ternak_aktif_sekarang' => $jumlahTernakAktifSekarang,
                            'detail_aktif_tercatat' => $jumlahDetailAktifTercatat,
                            'pernah_ada_detail' => $pernahAdaDetail,
                            'status_sementara' => $statusKeseluruhan
                    ]);
                }
                
                $statusArsip = false;
                }
            } else {
                
                $adaTernakAktifUntukSiklusBaru = Anggota::where('status', 'aktif')
                    ->whereHas('ternaks', function ($q_ternak) {
                        $q_ternak->where('status_aktif', 'aktif');
                    })
                    ->exists();
                    
                if($adaTernakAktifUntukSiklusBaru) {
                    $statusKeseluruhan = 'success';
                    $statusArsip = true;
                } else {
                    $statusKeseluruhan = null;
                    $statusArsip = false;
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

 
        // Role Petugas 

        else {
 
            $tahaps = Tahap::all();
            $tahapDipilih = $request->filled('tahap_id')
                ? Tahap::find($request->tahap_id)
                : null;
                
            $timestampSiklusTerbaru = Pencatatan::withoutGlobalScopes()->max('tanggal_catatan');
            $semuaSiklusTerbaruDiarsip = true;
            if ($timestampSiklusTerbaru) {
                   
                    $semuaSiklusTerbaruDiarsip = !Pencatatan::withoutGlobalScopes()
                                                    ->where('tanggal_catatan', $timestampSiklusTerbaru)
                                                    ->where('is_locked', false)
                                                    ->exists();
            }

            $periodeTerkunci = !$timestampSiklusTerbaru || $semuaSiklusTerbaruDiarsip;


            if ($periodeTerkunci) {
                return view('petugas.pencatatan', [
                    'anggotas' => collect(),
                    'tahaps' => $tahaps,
                    'tahapDipilih' => $tahapDipilih,
                    'locked' => true
                ]);
            }
            
            $query = Anggota::with(['tahap', 'latestPencatatan.details'])
                ->withCount(['ternaks' => function ($q) {
                    $q->where('status_aktif', 'aktif');
                }])
                ->where('status', 'aktif')
                // Ambil anggota yang punya pencatatan aktif (is_locked = false) 
                ->whereHas('pencatatans', function ($q) use ($timestampSiklusTerbaru) {
                    $q->where('is_locked', false)
                        ->where('tanggal_catatan', $timestampSiklusTerbaru);
                });

            // Filter tahap 
            if ($tahapDipilih) {
                $query->where('tahap_id', $tahapDipilih->id);
            }

            if ($request->filled('status')) {
                if ($request->status === 'sudah_dicatat') {
                    $query->whereHas('latestPencatatan', function ($q) {
                        $q->whereHas('details');
                    });
                } elseif ($request->status === 'belum_dicatat') {
                    $query->whereHas('latestPencatatan', function ($q) {
                        $q->whereDoesntHave('details');
                    });
                }
            }
            
            // Filter search 
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


    public function create(Anggota $anggota)
    {
        // Batasi role
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'AKSES DITOLAK');
        }

    
        $timestampSiklusTerbaru = Pencatatan::withoutGlobalScopes()->max('tanggal_catatan');
        
        $siklusTerbaruAktif = false;
        if ($timestampSiklusTerbaru) {
            $siklusTerbaruAktif = Pencatatan::withoutGlobalScopes()
                                    ->where('tanggal_catatan', $timestampSiklusTerbaru)
                                    ->where('is_locked', false)
                                    ->exists();
        }

        if (!$siklusTerbaruAktif) {
            return redirect()->route('pencatatan.index')
                ->with('error', 'Saat ini tidak ada periode pencatatan yang aktif.');
        }

        $pencatatanSiklusIni = $anggota->pencatatans()
            ->where('tanggal_catatan', $timestampSiklusTerbaru)
            ->where('is_locked', false)
            ->first(); 
 
        if (!$pencatatanSiklusIni) {
            return redirect()
                ->route('pencatatan.index')
                ->with('error', 'Periode untuk anggota ini belum dimulai oleh admin.');
        }

        if ($pencatatanSiklusIni->is_locked) {
            return redirect()
                ->route('pencatatan.index')
                ->with('error', 'Catatan periode ini sudah diarsipkan dan tidak bisa diubah.');
        }

        if ($pencatatanSiklusIni->details()->exists()) {
            return redirect()
                ->route('pencatatan.index')
                ->with('info', 'Catatan sudah ada untuk periode ini.');
        }

       
        // Load anggota dengan tahap
        $anggota->load('tahap');

        $ternaksAktif = Ternak::where('anggota_id', $anggota->id)
            ->where('status_aktif', 'aktif') 
            ->with([
                'anak' => function($query) {
                    $query->where('status_aktif', 'aktif'); 
                }, 
                'induk'
            ])
            ->get();

        $groupedTernaks = [];

        $induksAktif = $ternaksAktif->where('tipe_ternak', 'Induk');
        
        foreach ($induksAktif as $induk) {
            $groupedTernaks[] = [
                'induk' => $induk,
                'anak' => $induk->anak, 
                'type' => 'active_parent',
                'group_index' => count($groupedTernaks) + 1
            ];
        }
        
        // Bagian (induknya sudah mati/terjual atau tidak punya induk)
        $anaksYatim = $ternaksAktif->filter(function($ternak) {
            // Hanya proses anak ternak
            if ($ternak->tipe_ternak !== 'Anak') {
                return false;
            }

            if (!$ternak->induk_id) {
                return true;
            }
 
            $induknya = $ternak->induk; 
            
            if (!$induknya) {
 
                return true;
            }
            
            // Induk sudah mati/terjual atau tidak aktif
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
        
 
        $pencatatanBulanIni = $pencatatanSiklusIni;

        return view('petugas.pencatatan-create', compact('anggota', 'pencatatanBulanIni', 'groupedTernaks'));
    }


    /**
     * Menyimpan data pencatatan baru dari form.
     */
    public function store(Request $request)
    {
        
        if (auth()->user()->role !== 'petugas') {
            abort(403, 'AKSES DITOLAK');
        }

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
                'required_without:ternaks.*.ternak_id',
                'exists:ternaks,id'
            ],
        ]);

        DB::beginTransaction();
        try {
           
            $pencatatan = Pencatatan::findOrFail($validated['pencatatan_id']);

            if ($pencatatan->is_locked) {
                return back()->with('error', 'Gagal, catatan ini sudah diarsipkan dan tidak bisa diubah.');
            }

            $fotoPaths = $pencatatan->foto_dokumentasi ?? [];
            if ($request->hasFile('foto_dokumentasi')) {
                foreach ($request->file('foto_dokumentasi') as $file) {
                    $path = $file->store('dokumentasi_ternak', 'public');
                    $fotoPaths[] = $path;
                }
            }
            
            $pencatatan->update([
                'petugas_id' => Auth::id(),
                'temuan_lapangan' => $validated['temuan_lapangan'],
                'foto_dokumentasi' => $fotoPaths,
            ]);

            $pencatatan->details()->delete();

            foreach ($validated['ternaks'] as $dataTernak) {
                
                $ternakId = null;
                $ternakMaster = null; 

                $statusAktif = in_array($dataTernak['kondisi_ternak'], ['Mati', 'Terjual']) ? 'nonaktif' : 'aktif';

                if (isset($dataTernak['ternak_id']) && !empty($dataTernak['ternak_id'])) {
                    $ternakId = $dataTernak['ternak_id'];
                    $ternakMaster = Ternak::find($ternakId);

                    if ($ternakMaster) {
                        $updateDataMaster = [
                            'no_ear_tag'    => $dataTernak['no_ear_tag'],
                            'jenis_kelamin' => $dataTernak['jenis_kelamin'],
                            'tipe_ternak'   => $dataTernak['tipe_ternak'],
                            'status_aktif'  => $statusAktif, 
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
                    // Pembuatan ternak baru
                    $createDataMaster = [
                        'anggota_id'    => $pencatatan->anggota_id,
                        'induk_id'      => $dataTernak['induk_id'] ?? null,
                        'tipe_ternak'   => $dataTernak['tipe_ternak'], 
                        'no_ear_tag'    => $dataTernak['no_ear_tag'],
                        'jenis_kelamin' => $dataTernak['jenis_kelamin'],
                        'harga'         => 0, 
                        'status_aktif'  => $statusAktif, 
                    ];

                    if ($createDataMaster['tipe_ternak'] === 'Induk') {
                         $createDataMaster['induk_id'] = null;
                    } 

                    $newTernak = Ternak::create($createDataMaster);
                    $ternakId = $newTernak->id;
                    $ternakMaster = $newTernak; 
                }


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
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function exportLaporanKeseluruhan(Request $request)
    {

        $anggotas = Anggota::where('status', 'aktif')
            ->with([
                'tahap',
                'latestPencatatan' => function ($query) {
                    $query->with('details.ternak');
                }
            ])
            ->get();

        foreach ($anggotas as $anggota) {

            if (!$anggota->latestPencatatan) {
                $anggota->groupedTernaks = collect();
                continue; 
            }
            
            $allDetails = $anggota->latestPencatatan->details;
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
           
            $timestampSiklusTerbaru = Pencatatan::withoutGlobalScopes()->max('tanggal_catatan');

            $siklusTerbaruAktif = false;
            if ($timestampSiklusTerbaru) {
                $siklusTerbaruAktif = Pencatatan::withoutGlobalScopes()
                                        ->where('tanggal_catatan', $timestampSiklusTerbaru)
                                        ->where('is_locked', false)
                                        ->exists();
            }
 
            if (!$siklusTerbaruAktif) {
                return back()->with('error', 'Tidak ada pekerjaan aktif yang bisa diarsipkan.');
            }
 
            $adaCatatanWajibYangKosong = Pencatatan::where('tanggal_catatan', $timestampSiklusTerbaru) 
                ->where('is_locked', false)
                ->whereDoesntHave('details') 
                ->whereHas('anggota', function ($q_anggota) { 
                    $q_anggota->where('status', 'aktif') 
                                ->whereHas('ternaks', function ($q_ternak) { 
                                    $q_ternak->where('status_aktif', 'aktif'); 
                                });
                })
                ->exists();
            
            if ($adaCatatanWajibYangKosong) {
                return back()->with('error', 'Gagal, masih ada data aktif yang belum dilengkapi oleh petugas.');
            }

            $pencatatansAktif = Pencatatan::where('tanggal_catatan', $timestampSiklusTerbaru) 
                                        ->where('is_locked', false)
                                        ->get();
            $anggotaIds = $pencatatansAktif->pluck('anggota_id')->unique();

            $anggotas = Anggota::whereIn('id', $anggotaIds)
                ->with([
                    'tahap',
                    'latestPencatatan' => function ($query) {
                        $query->with('details.ternak');
                    }
                ])
                ->get();
                
            // (Logika pengelompokan ternak)
            foreach ($anggotas as $anggota) {
                if (!$anggota->latestPencatatan) {
                    $anggota->groupedTernaks = collect();
                    continue; 
                }
                $allDetails = $anggota->latestPencatatan->details;
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
            }

            $timestamp = now()->format('Y-m-d_H-i'); 
            $namaFile = 'Arsip Gabungan - ' . $timestamp . '.pdf';
            $pathFile = 'arsip/keseluruhan/' . $namaFile;

            // Buat PDF 
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

            $lockedCount = Pencatatan::where('tanggal_catatan', $timestampSiklusTerbaru) 
                ->where('is_locked', false)
                ->update(['is_locked' => true]);

            return redirect()->route('pencatatan.index')
                ->with('success', "Arsip berhasil! {$lockedCount} catatan dari siklus aktif telah dikunci.");
                
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
        $pencatatan->load('anggota.tahap', 'petugas', 'details.ternak');

        return view('admin.pencatatan.show', compact('pencatatan'));
    }



    /**
     * Menampilkan form edit
     */
    public function edit(string $id)
    {
        $pencatatan = Pencatatan::with([
            'anggota.ternaks',    
            'anggota.tahap',      
            'details.ternak'      
        ])->findOrFail($id);

        if ($pencatatan->is_locked) {
            return redirect()->route('pencatatan.index')
                ->with('error', 'Catatan ini sudah diarsipkan dan tidak bisa diubah. Silakan buat catatan baru.');
        }

        $pencatatan->load('details.ternak');
        
        $allDetails = $pencatatan->details;
        $groupedDetails = [];
        
        $indukDetails = $allDetails->filter(function($detail) {
            return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
        });

        // ID semua ternak induk 
        $indukTernakIds = $indukDetails->pluck('ternak_id');

        // Buat grup untuk induk beserta anak-anaknya
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

        // ANAK YATIM (anak yang induknya tidak ada di pencatatan)
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
        
        $anggota = $pencatatan->anggota->load('ternaks'); 

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

            if ($request->has('delete_details')) {
                $detailIdsToDelete = $request->input('delete_details', []);
                
                if (!empty($detailIdsToDelete)) {

                    $detailsToDelete = PencatatanDetail::whereIn('id', $detailIdsToDelete)->get();
                    $ternakIdsToDeactivate = $detailsToDelete->pluck('ternak_id')->unique()->filter(); 

                    PencatatanDetail::whereIn('id', $detailIdsToDelete)->delete();

                    if ($ternakIdsToDeactivate->isNotEmpty()) {
                        \Log::info('Menonaktifkan Ternak Master karena detail dihapus', $ternakIdsToDeactivate->toArray());
                        Ternak::whereIn('id', $ternakIdsToDeactivate)
                              ->update(['status_aktif' => 'nonaktif']);
                    }
                }
            }

            \Log::info('Data ternaks yang diterima:', $request->ternaks);

            // Proses setiap data ternak
            foreach ($request->ternaks as $key => $ternakData) {
                
                \Log::info("Processing ternak key: {$key}", $ternakData);
                
                if (!isset($ternakData['tipe_ternak'])) {
                    \Log::warning("Skipped - no tipe_ternak for key: {$key}");
                    continue;
                }

                if (isset($ternakData['detail_id']) && !empty($ternakData['detail_id'])) {

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

                    if (in_array($ternakData['kondisi_ternak'], ['Mati', 'Terjual'])) {
                    // Jika ternak mati/terjual (nonaktif)
                        $updateData['status_aktif'] = 'nonaktif';
                    } else {
                        // Jika ternak sakit/sehat (aktif)
                        $updateData['status_aktif'] = 'aktif';
                    }
                    
                    $affected = DB::table('ternaks')
                        ->where('id', $ternakMaster->id)
                        ->update(array_merge($updateData, ['updated_at' => now()]));
                    
                    \Log::info('Ternak updated successfully', [
                        'ternak_id' => $ternakMaster->id,
                        'kondisi' => $ternakData['kondisi_ternak'],
                        'status_aktif' => $updateData['status_aktif'],
                        'harga' => $ternakMaster->harga,
                        'affected_rows' => $affected
                    ]);

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

            DB::statement('SELECT 1'); 

            $anggotaId = $pencatatan->anggota_id;
 
            $jumlahIndukAktif = DB::table('ternaks')
                ->where('anggota_id', $anggotaId)
                ->where('tipe_ternak', 'Induk')
                ->where('status_aktif', 'aktif')
                ->lockForUpdate() 
                ->count();

            \Log::info('Sinkronisasi jumlah induk', [
                'anggota_id' => $anggotaId,
                'jumlah_induk_aktif' => $jumlahIndukAktif
            ]);

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
     * Menghapus
     */
    public function destroy(string $id)
    {
        //
    }

    public function reset()
    {
        try {
           
            $adaPekerjaanAktif = Pencatatan::withoutGlobalScopes()
                                        ->where('is_locked', false)
                                        ->exists();

            if ($adaPekerjaanAktif) {
                return redirect()->route('pencatatan.index')
                    ->with('error', 'Gagal! Harap arsipkan semua pekerjaan dari siklus sebelumnya terlebih dahulu.');
            }
            
            $timestampSiklusBaru = now();

            DB::transaction(function () use ($timestampSiklusBaru) { 
 
                $anggotasUntukPeriodeBaru = Anggota::where('status', 'aktif')
                    ->whereHas('ternaks', function ($q_ternak) { 
                        // Filter ternak yang masih 'aktif'
                        $q_ternak->where('status_aktif', 'aktif');
                    })
                    ->get();

                foreach ($anggotasUntukPeriodeBaru as $anggota) {
                    Pencatatan::create([
                        'anggota_id'      => $anggota->id,
                        'tanggal_catatan' => $timestampSiklusBaru, 
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