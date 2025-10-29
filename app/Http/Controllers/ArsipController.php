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
            return back()->with('error', 'Tidak dapat memuat data arsip. Silakan coba lagi.');
        }
    }


    public function byYear($tahun)
    {
       
        $arsips = Arsip::where('tahun', $tahun)
            ->orderBy('bulan', 'asc')
            ->get();

        return view('arsip-bulan', compact('arsips', 'tahun'));
    }

    public function show(Arsip $arsip)
    {
        // Tampilkan file PDF 
        return Storage::response($arsip->path_file);
    }

    public function destroy(Arsip $arsip)
    {
        try {
            $tahun = $arsip->tahun;
            $bulan = $arsip->bulan;

            $arsipTerbaru = Arsip::orderBy('created_at', 'desc')->first();
            
            $iniArsipTerbaru = ($arsipTerbaru && $arsipTerbaru->id == $arsip->id);

            Storage::delete($arsip->path_file);

            // 2. Hapus record dari database
            $arsip->delete();

            if ($iniArsipTerbaru) {
                Pencatatan::whereYear('tanggal_catatan', $tahun)
                        ->whereMonth('tanggal_catatan', $bulan)
                        ->update(['is_locked' => false]);
                
                \Log::info('Arsip terbaru dihapus, pencatatan dibuka kembali', [
                    'arsip_id' => $arsip->id,
                    'tahun' => $tahun,
                    'bulan' => $bulan
                ]);
            } else {
                \Log::info('Arsip lama dihapus, pencatatan tetap locked', [
                    'arsip_id' => $arsip->id,
                    'tahun' => $tahun,
                    'bulan' => $bulan
                ]);
            }

            $sisaArsip = Arsip::where('tahun', $tahun)->count();

            if ($sisaArsip === 0) {
                DB::table('status_tahunan')
                    ->where('tahun', $tahun)
                    ->update(['status' => 'progress']);
            }

            return redirect()->route('arsip.tahun', $tahun)
                ->with('success', 'Arsip berhasil dihapus' . ($iniArsipTerbaru ? ' dan periode pencatatan telah dibuka kembali.' : '.'));
                
        } catch (\Exception $e) {
            Log::error('Gagal menghapus arsip: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus arsip.');
        }
    }

    

    public function validasi($tahun)
    {
        try {
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
