<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use App\Models\Ternak;
use App\Models\Arsip;
use App\Models\Pencatatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan data utama untuk halaman dashboard.
     */
    public function index()
    {

        // Card Statistik Utama 
        $totalPengguna = User::count();
        // Hanya menghitung anggota yang statusnya aktif
        $totalAnggota = Anggota::where('status', 'aktif')->count();
        $totalArsip = Arsip::count(); // Arsip adalah data historis, jadi count() sudah benar.

        // Query dasar untuk ternak yang masih hidup 
        $ternakAktifQuery = Ternak::whereHas('anggota', function ($q) {
                // Pastikan anggotanya juga aktif
                $q->where('status', 'aktif');
            })
            // cek status_aktif di tabel ternaks
            ->where('status_aktif', 'aktif');

        // Statistik Detail di kolom kanan (berdasarkan query ternak hidup)
        
        $totalHargaTernak = (clone $ternakAktifQuery)->sum('harga');
        $totalJumlahTernak = (clone $ternakAktifQuery)->count(); 
        
        // Data untuk Pie Chart Persebaran Kandang (hanya dari anggota aktif)
        $persebaranKandang = Anggota::where('status', 'aktif')
                                    ->select('lokasi_kandang', DB::raw('count(*) as total'))
                                    ->groupBy('lokasi_kandang')
                                    ->pluck('total', 'lokasi_kandang');
        
        // Data untuk dropdown Anggota di bar chart (hanya anggota aktif)
        $semuaAnggota = Anggota::where('status', 'aktif')->orderBy('nama')->get();
        
        $initialChartData = [
            'labels' => [],
            'datasets' => [
                ['label' => 'Sapi', 'data' => []],
                ['label' => 'Kambing', 'data' => []],
            ]
        ];
        $initialAnggotaId = null; 

        return view('dashboard', compact(
            'totalPengguna',
            'totalAnggota',
            'totalArsip',
            'totalHargaTernak',
            'totalJumlahTernak',
            'persebaranKandang',
            'semuaAnggota',
            'initialChartData',
            'initialAnggotaId'
        ));
    }

    /**
     * Mengambil data historis dari pencatatan untuk chart.
     */
    public function getChartData(Request $request)
    {
        // Query dasar yang efisien menggunakan JOIN dan agregasi SQL
        $query = Pencatatan::select(
            DB::raw('YEAR(pencatatans.tanggal_catatan) as tahun'),
            DB::raw('MONTH(pencatatans.tanggal_catatan) as bulan'),
            DB::raw('COUNT(DISTINCT CASE WHEN anggotas.jenis_ternak = "sapi" AND pencatatan_details.kondisi_ternak NOT IN ("Mati", "Terjual") THEN ternaks.id END) as total_sapi'),
            DB::raw('COUNT(DISTINCT CASE WHEN anggotas.jenis_ternak = "kambing" AND pencatatan_details.kondisi_ternak NOT IN ("Mati", "Terjual") THEN ternaks.id END) as total_kambing')
        )
        ->join('pencatatan_details', 'pencatatans.id', '=', 'pencatatan_details.pencatatan_id')
        ->join('anggotas', 'pencatatans.anggota_id', '=', 'anggotas.id')
        ->join('ternaks', 'pencatatan_details.ternak_id', '=', 'ternaks.id')
        ->groupBy('tahun', 'bulan')
        ->orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc');

        // Filter per anggota jika ID dipilih dari dropdown
        if ($request->filled('anggota_id')) {
            $query->where('pencatatans.anggota_id', $request->anggota_id);
        }

        $results = $query->get();
        
        $labels = $results->map(fn($row) => date("M Y", mktime(0, 0, 0, $row->bulan, 1, $row->tahun)));

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Sapi', 'data' => $results->pluck('total_sapi')],
                ['label' => 'Kambing', 'data' => $results->pluck('total_kambing')]
            ]
        ]);
    }
}

