<?php

namespace App\Http\Controllers;

use App\Models\Tahap;
use Illuminate\Http\Request;

class TahapController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tahun' => 'required|integer|min:2000',
                'tahap_ke' => 'required|integer|min:1',
            ]);

            // Cek duplikasi tahap
            $exists = Tahap::where('tahap_ke', $validated['tahap_ke'])
                          ->where('tahun', $validated['tahun'])
                          ->exists();
            
            if ($exists) {
                return redirect()->route('anggota.index')
                    ->with('error_tahap', 'Tahap ' . $validated['tahap_ke'] . ' tahun ' . $validated['tahun'] . ' sudah ada!');
            }

            $tahap = Tahap::create($validated);

            
            return redirect()->route('anggota.index', ['tahap_id' => $tahap->id])
                ->with('success_tahap', 'Tahap "' . $tahap->label . '" berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->route('anggota.index')
                ->with('error_tahap', 'Terjadi kesalahan saat menambahkan tahap.');
        }
    }

    public function check($id)
    {
        $tahap = Tahap::withCount('anggotas')->findOrFail($id);

        return response()->json([
            'anggota_count' => $tahap->anggotas_count,
            'tahap_name'    => $tahap->label,
        ]);
    }

    /**
     * Menghapus tahap beserta semua anggota di dalamnya.
     */
    public function destroy(Tahap $tahap)
    {
        try {
            $namaTahap = $tahap->label;
            $jumlahAnggota = $tahap->anggotas()->count();

            // Hapus tahap. Anggota terkait akan otomatis terhapus oleh database
            $tahap->delete();

            if ($jumlahAnggota > 0) {
                return redirect()->route('anggota.index')
                    ->with('deleted_tahap', 'Tahap "' . $namaTahap . '" beserta ' . $jumlahAnggota . ' anggota berhasil dihapus!');
            } else {
                return redirect()->route('anggota.index')
                    ->with('deleted_tahap', 'Tahap "' . $namaTahap . '" berhasil dihapus!');
            }

        } catch (\Exception $e) {

            \Log::error('Gagal menghapus tahap: ' . $e->getMessage());

            return redirect()->route('anggota.index')
                ->with('error_delete_tahap', 'Terjadi kesalahan saat menghapus tahap.');
        }
    }

    public function getOptions()
    {
        $options = Tahap::all()->map(function ($t) {
            return [
                'id' => $t->id,
                'label' => $t->label,
            ];
        });

        return response()->json($options);
    }
}