<?php

namespace App\Observers;

use App\Models\Anggota;
use App\Models\Pencatatan; // Perbaiki import model

class AnggotaObserver
{
    /**
     * Handle the Anggota "created" event.
     */
    public function created(Anggota $anggota): void
    {
        // Cek apakah ada siklus pencatatan yang aktif
        $adaSiklusAktif = Pencatatan::where('is_locked', false)->exists();
        
        // Buat placeholder hanya jika anggota aktif dan ada siklus aktif
        if ($adaSiklusAktif && $anggota->status === 'aktif') {
            Pencatatan::create([
                'anggota_id' => $anggota->id,
                'petugas_id' => null,
                'tanggal_catatan' => now(),
                'is_locked' => false,
                'temuan_lapangan' => null,
                'foto_dokumentasi' => null
            ]);
        }
    }

    /**
     * Handle the Anggota "updated" event.
     */
    public function updated(Anggota $anggota): void
    {
        // Jika status anggota diubah menjadi aktif dan belum punya pencatatan aktif
        if ($anggota->status === 'aktif' && $anggota->wasChanged('status')) {
            $adaSiklusAktif = Pencatatan::where('is_locked', false)->exists();
            $sudahPunyaPencatatanAktif = $anggota->pencatatans()
                ->where('is_locked', false)
                ->exists();

            if ($adaSiklusAktif && !$sudahPunyaPencatatanAktif) {
                Pencatatan::create([
                    'anggota_id' => $anggota->id,
                    'petugas_id' => null,
                    'tanggal_catatan' => now(),
                    'is_locked' => false,
                    'temuan_lapangan' => null,
                    'foto_dokumentasi' => null
                ]);
            }
        }
    }
}