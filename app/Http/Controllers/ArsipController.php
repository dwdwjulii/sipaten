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
            // data arsip dan status dalam satu query
            $arsipsPerTahun = DB::table('arsips')
                ->select('arsips.tahun', DB::raw('COUNT(arsips.id) as jumlah'), 'status_tahunan.status')
                ->leftJoin('status_tahunan', 'arsips.tahun', '=', 'status_tahunan.tahun')
                ->groupBy('arsips.tahun', 'status_tahunan.status')
                ->orderByDesc('arsips.tahun')
                ->get();

            $arsipsPerTahun->transform(function ($item) {
                $item->status = $item->status ?? 'progress';
                return $item;
            });

            return view('arsip', compact('arsipsPerTahun'));
        } catch (\Exception $e) {
            Log::error('Gagal memuat halaman arsip tahunan: ' . $e->getMessage());
            // Redirect atau tampilkan halaman error
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
            $tahun = $arsip->tahun; // Simpan tahun sebelum arsip dihapus
            $bulan = $arsip->bulan; // Simpan bulan untuk membuka kembali 'is_locked'

            // Hapus file fisik dari storage
            Storage::delete($arsip->path_file);

            // Hapus record dari database
            $arsip->delete();

            // Buka kembali (unlock) record pencatatan yang terkait
            Pencatatan::whereYear('tanggal_catatan', $tahun)
                      ->whereMonth('tanggal_catatan', $bulan)
                      ->update(['is_locked' => false]);

            // Cek apakah masih ada arsip lain di tahun yang sama
            $sisaArsip = Arsip::where('tahun', $tahun)->count();

            // Jika tidak ada arsip yang tersisa, kembalikan statusnya menjadi 'progress'
            if ($sisaArsip === 0) {
                DB::table('status_tahunan')
                    ->where('tahun', $tahun)
                    ->update(['status' => 'progress']);
            }

            return redirect()->route('arsip.tahun', $tahun)->with('success', 'Arsip berhasil dihapus dan periode pencatatan telah dibuka kembali.');
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
