<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Pencatatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index()
    {
        try {
            // PERBAIKAN: Menggunakan LEFT JOIN untuk menggabungkan data arsip dan status dalam satu query
            $arsipsPerTahun = DB::table('arsips')
                ->select('arsips.tahun', DB::raw('COUNT(arsips.id) as jumlah'), 'status_tahunan.status')
                ->leftJoin('status_tahunan', 'arsips.tahun', '=', 'status_tahunan.tahun')
                ->groupBy('arsips.tahun', 'status_tahunan.status')
                ->orderByDesc('arsips.tahun')
                ->get();

            // Atur status default menjadi 'progress' jika belum ada di tabel status_tahunan
            $arsipsPerTahun->transform(function ($item) {
                $item->status = $item->status ?? 'progress';
                return $item;
            });

            return view('arsip', compact('arsipsPerTahun'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat halaman arsip tahunan: ' . $e->getMessage());
            // Redirect atau tampilkan halaman error yang lebih informatif
            return back()->with('error', 'Tidak dapat memuat data arsip. Silakan coba lagi.');
        }
    }


    public function byYear($tahun)
    {
        // Ambil semua arsip berdasarkan tahun tertentu
        $arsips = Arsip::where('tahun', $tahun)
            ->orderBy('bulan', 'asc')
            ->get();

        return view('arsip-bulan', compact('arsips', 'tahun'));
    }

    public function show(Arsip $arsip)
    {
        // Tampilkan file PDF yang diminta
        return Storage::response($arsip->path_file);
    }

    public function destroy(Arsip $arsip)
    {
        try {
            $tahun = $arsip->tahun;
            $bulan = $arsip->bulan;

            // 🔥 PERBAIKAN: Cek apakah ini arsip TERBARU berdasarkan created_at
            $arsipTerbaru = Arsip::orderBy('created_at', 'desc')->first();
            
            $iniArsipTerbaru = ($arsipTerbaru && $arsipTerbaru->id == $arsip->id);

            // 1. Hapus file fisik dari storage
            Storage::delete($arsip->path_file);

            // 2. Hapus record dari database
            $arsip->delete();

            // 3. 🔥 HANYA buka kembali pencatatan jika ini ARSIP TERBARU
            if ($iniArsipTerbaru) {
                
                // =======================================================
                // 🔥 PENGAMAN BARU (Mulai)
                // =======================================================
                // Cek apakah SUDAH ADA siklus baru yang aktif.
                $adaSiklusBaruAktif = Pencatatan::withoutGlobalScopes()
                                            ->where('is_locked', false)
                                            ->exists();
                
                if ($adaSiklusBaruAktif) {
                    // JANGAN DIBUKA! Siklus baru sedang berjalan.
                    // Cukup log pesan ini untuk developer.
                    \Log::warning('Arsip terbaru dihapus, TAPI pencatatan TIDAK dibuka kembali karena siklus baru sudah aktif.', [
                        'arsip_id_dihapus' => $arsip->id,
                        'tahun' => $tahun,
                        'bulan' => $bulan
                    ]);

                    // Langsung lompat ke langkah 4
                
                } else {
                    // INI AMAN. Tidak ada siklus baru, kita boleh buka lock siklus lama.
                    Pencatatan::whereYear('tanggal_catatan', $tahun)
                              ->whereMonth('tanggal_catatan', $bulan)
                              ->update(['is_locked' => false]);
                    
                    \Log::info('Arsip terbaru dihapus, pencatatan dibuka kembali', [
                        'arsip_id' => $arsip->id,
                        'tahun' => $tahun,
                        'bulan' => $bulan
                    ]);
                }
                // =======================================================
                // 🔥 PENGAMAN BARU (Selesai)
                // =======================================================
                
            } else {
                \Log::info('Arsip lama dihapus, pencatatan tetap locked', [
                    'arsip_id' => $arsip->id,
                    'tahun' => $tahun,
                    'bulan' => $bulan
                ]);
            }

            // 4. Cek apakah masih ada arsip lain di tahun yang sama
            $sisaArsip = Arsip::where('tahun', $tahun)->count();

            // 5. Jika tidak ada arsip yang tersisa, kembalikan statusnya menjadi 'progress'
            if ($sisaArsip === 0) {
                DB::table('status_tahunan')
                    ->where('tahun', $tahun)
                    ->update(['status' => 'progress']);
            }

            // Pesan suksesnya sekarang lebih aman, tidak menyebut "dibuka kembali" kecuali benar-fbenar terjadi
            return redirect()->route('arsip.tahun', $tahun)
                ->with('success', 'Arsip berhasil dihapus.');
                
        } catch (\Exception $e) {
            Log::error('Gagal menghapus arsip: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus arsip.');
        }
    }

    

    public function validasi($tahun)
    {
        try {
            // Menggunakan updateOrInsert: update jika tahun sudah ada, insert jika belum ada.
            \DB::table('status_tahunan')->updateOrInsert(
                ['tahun' => $tahun],
                ['status' => 'selesai', 'updated_at' => now()]
            );

            return redirect()->route('arsip.index')->with('success', "Status arsip untuk tahun $tahun telah berhasil divalidasi.");

        } catch (\Exception $e) {
            \Log::error('Gagal memvalidasi arsip tahunan: ' . $e->getMessage());
            return redirect()->route('arsip.index')->with('error', 'Terjadi kesalahan saat proses validasi.');
        }
    }
}
