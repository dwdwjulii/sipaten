<?php


namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pencatatan;
use App\Models\Arsip;
use App\Models\Ternak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $tahaps = \App\Models\Tahap::withCount('anggotas')
            ->orderBy('tahun', 'desc')
            ->orderBy('tahap_ke', 'desc')
            ->get();

        $query = Anggota::with(['ternaks' => function ($q) {
            $q->where('tipe_ternak', 'Induk')
            // Hanya muat ternak yang TIDAK MEMILIKI relasi pencatatanDetails
            // dengan kondisi 'Mati' atau 'Terjual'.
            ->whereDoesntHave('pencatatanDetails', function ($subQuery) {
                $subQuery->whereIn('kondisi_ternak', ['Mati', 'Terjual']);
            })
            ->with('anak');
        }]);

        $tahapDipilih = null;
        if ($request->has('tahap_id')) {
            $tahapDipilih = \App\Models\Tahap::find($request->tahap_id);
            if ($tahapDipilih) {
                $query->where('tahap_id', $tahapDipilih->id);
            }
        }

        $search = $request->input('search');
        $query->when($search, function ($q, $search) {
            return $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('lokasi_kandang', 'like', '%' . $search . '%');
        });
        
        // Ambil nilai 'per_page' dari request, default-nya 10
        $perPage = $request->input('per_page', 10);

        // Handle jika user memilih 'Semua'
        if ($perPage == 'Semua') {
            $total = $query->count();
            $perPage = $total > 0 ? $total : 10;
        } else {
            $perPage = (int) $perPage;
        }

        $anggotas = $query->paginate($perPage);
        
        return view('anggota', compact('anggotas', 'tahaps', 'tahapDipilih'));
    }

    public function store(Request $request)
    {
        // Validasi data utama dan data ternak awal
        $validated = $request->validate([
            'tahap_id' => 'required|exists:tahaps,id',
            'nama' => 'required|string|max:255',
            'jenis_ternak' => 'required|string',
            'jumlah_induk' => 'required|integer|min:1',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date_format:d/m/Y',
            'no_hp' => 'required|string',
            'lokasi_kandang' => 'required|string',
            'status' => 'required|string',
            'ternak_awal' => 'required|array',
            'ternak_awal.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // anggota baru
            $anggota = Anggota::create([
                'tahap_id' => $validated['tahap_id'],
                'nama' => $validated['nama'],
                'jenis_ternak' => $validated['jenis_ternak'],
                'jumlah_induk' => $validated['jumlah_induk'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => \Carbon\Carbon::createFromFormat('d/m/Y', $validated['tanggal_lahir'])->format('Y-m-d'),
                'no_hp' => $validated['no_hp'],
                'lokasi_kandang' => $validated['lokasi_kandang'],
                'status' => $validated['status'],
            ]);

            // Buat record untuk setiap induk awal di tabel ternaks
            foreach ($validated['ternak_awal'] as $dataInduk) {
                $anggota->ternaks()->create([
                    'tipe_ternak' => 'Induk',
                    'harga' => $dataInduk['harga'],
                ]);
            }

            if ($anggota->status === 'aktif') {
                $bulanIni = now()->month;
                $tahunIni = now()->year;

                // Cek apakah periode ini SUDAH diarsipkan
                $sudahDiarsip = Arsip::where('bulan', $bulanIni)
                                    ->where('tahun', $tahunIni)
                                    ->exists();

                // Cek apakah anggota sudah punya placeholder di periode berjalan
                $sudahAdaPlaceholder = Pencatatan::where('anggota_id', $anggota->id)
                                                ->whereMonth('tanggal_catatan', $bulanIni)
                                                ->whereYear('tanggal_catatan', $tahunIni)
                                                ->exists();

                // Hanya buat placeholder jika belum diarsip dan belum ada placeholder
                if (!$sudahDiarsip && !$sudahAdaPlaceholder) {
                    Pencatatan::create([
                        'anggota_id'      => $anggota->id,
                        'tanggal_catatan' => now(),
                        'is_locked'       => false,
                    ]);
                }
            }


            DB::commit();
            return redirect()->back()->with('success', 'Anggota baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    

    public function update(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahap_id' => 'required|exists:tahaps,id',
            'jenis_ternak' => 'required|in:Sapi,Kambing',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'lokasi_kandang' => 'required|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
            'harga_induk' => 'nullable|array',
            'harga_induk.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['tanggal_lahir'])->format('Y-m-d');

            $anggota->update([
                'nama' => $validated['nama'],
                'tahap_id' => $validated['tahap_id'],
                'jenis_ternak' => $validated['jenis_ternak'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $tanggalLahir,
                'no_hp' => $validated['no_hp'],
                'lokasi_kandang' => $validated['lokasi_kandang'],
                'status' => $validated['status'],
            ]);

            if ($anggota->wasChanged('status')) {
                if ($anggota->status === 'non-aktif') {
                    $anggota->pencatatans()->where('is_locked', false)->delete();
                } 
                elseif ($anggota->status === 'aktif') {
                    $bulanIni = now()->month;
                    $tahunIni = now()->year;
                    $sudahDiarsip = \App\Models\Arsip::where('bulan', $bulanIni)->where('tahun', $tahunIni)->exists();

                    if (!$sudahDiarsip) {
                        $placeholderExists = $anggota->pencatatans()
                            ->whereMonth('tanggal_catatan', $bulanIni)
                            ->whereYear('tanggal_catatan', $tahunIni)
                            ->exists();

                        if (!$placeholderExists) {
                            $anggota->pencatatans()->create([
                                'anggota_id' => $anggota->id,
                                'tanggal_catatan' => now(),
                                'is_locked' => false,
                            ]);
                        }
                    }
                }
            }

            $submittedPrices = $request->input('harga_induk', []);
            $cleanPrices = []; // Ini akan menampung harga yang valid (tidak kosong)

            if (is_array($submittedPrices)) {
                foreach ($submittedPrices as $harga) {
                    // Hanya masukkan harga yang benar-benar diisi
                    if (!empty($harga) && $harga !== 'Rp ' && $harga !== 'Rp') {
                        $cleanPrices[] = $harga;
                    }
                }
            }
            
            // Hitung jumlah harga VALID yang dikirim
            $newCount = count($cleanPrices);
            
            // Ambil ternak aktif YANG ADA, DAN hitung riwayatnya
            $existingInduks = Ternak::where('anggota_id', $anggota->id)
                ->where('tipe_ternak', 'Induk')
                ->where('status_aktif', 'aktif')
                ->with(['pencatatanDetails', 'anak']) 
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($existingInduks as $index => $ternak) {
                if (isset($cleanPrices[$index])) {
                    // Ternak masih ada -> Update harga
                    $cleanHarga = (int) str_replace(['Rp ', '.'], '', $cleanPrices[$index]);
                    $ternak->update(['harga' => $cleanHarga]);
                } else {
                    // Ternak dikurangi -> Cek riwayat SECARA MANUAL
                    
                    $punyaRiwayat = false; // Asumsi awal: tidak punya riwayat

                    // Cek apakah punya anak
                    if ($ternak->anak->count() > 0) {
                        $punyaRiwayat = true;
                    }

                    // Cek apakah punya detail pencatatan YANG TERISI
                    if (!$punyaRiwayat) { // Hanya cek jika belum ketemu riwayat
                        foreach ($ternak->pencatatanDetails as $detail) {
                            
                            if (!empty($detail->kondisi_ternak) || 
                                !empty($detail->status_vaksin) || 
                                !empty($detail->umur_saat_dicatat)) 
                            {
                                // Baris ini TERISI, berarti ini riwayat!
                                $punyaRiwayat = true;
                                break; // Stop perulangan
                            }
                        }
                    }

                    if ($punyaRiwayat) {
                        // Punya riwayat -> SOFT DELETE (Nonaktifkan)
                        $ternak->update(['status_aktif' => 'non-aktif']);
                    } else {
                        // Tidak punya riwayat -> HARD DELETE (Hapus permanen)
                        
                        $ternak->pencatatanDetails()->delete();
                        
                        $ternak->delete();
                    }
                }
            }

            // // Tambah ternak baru jika jumlahnya lebih banyak
            if ($newCount > $existingInduks->count()) {
                // Ambil hanya harga-harga BARU (yang diinput setelah ternak yang sudah ada)
                $newPrices = array_slice($cleanPrices, $existingInduks->count());

               // Cari pencatatan aktif (yang belum di-lock) untuk anggota ini
                $activePencatatan = \App\Models\Pencatatan::where('anggota_id', $anggota->id)
                                        ->where('is_locked', false)
                                        ->latest('tanggal_catatan')
                                        ->first();

                foreach ($newPrices as $harga) {
                    $cleanHarga = (int) str_replace(['Rp ', '.'], '', $harga);
                    $newTernak = $anggota->ternaks()->create([ 
                        'id'          => (string) \Illuminate\Support\Str::ulid(),
                        'tipe_ternak' => 'Induk',
                        'harga'       => $cleanHarga,
                        'status_aktif'=> 'aktif',
                    ]);

                    // Jika ada pencatatan aktif
                    if ($activePencatatan) {
                    
                    
                        if ($activePencatatan->details()->exists()) {
                    
                            \App\Models\PencatatanDetail::create([
                                'pencatatan_id'     => $activePencatatan->id,
                                'ternak_id'         => $newTernak->id,
                                'umur_saat_dicatat' => '',
                                'kondisi_ternak'    => '',
                                'status_vaksin'     => '',
                            ]);
                        }
                        
                    }

                }
            
            }

            

            // Hitung ulang dan sinkronisasi jumlah induk (SELALU dijalankan)
            $jumlahIndukAktif = Ternak::where('anggota_id', $anggota->id)
                                    ->where('tipe_ternak', 'Induk')
                                    ->where('status_aktif', 'aktif')
                                    ->count();
        
            // Update kolom 'jumlah_induk' dengan angka yang 100% akurat dari database
            $anggota->update(['jumlah_induk' => $jumlahIndukAktif]);
            
            DB::commit();

            $redirectTahapId = $request->get('current_tahap_id', $validated['tahap_id']);
            
            return redirect()->route('anggota.index', ['tahap_id' => $redirectTahapId])
                            ->with('success', 'Data anggota berhasil diperbarui!');
                            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Update anggota gagal: " . $e->getMessage());
            return back()->with('error', 'Update gagal: ' . $e->getMessage())->withInput();
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $anggota = Anggota::findOrFail($id);
            
            // Simpan tahap_id sebelum dihapus untuk redirect
            $tahapId = $request->get('tahap_id', $anggota->tahap_id);
            
            $anggota->delete();
            
            return redirect()->route('anggota.index', ['tahap_id' => $tahapId])
                           ->with('success', 'Data anggota berhasil dihapus!');
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Hapus gagal: ' . $e->getMessage()]);
        }
    }
}