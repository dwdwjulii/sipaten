<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Arsip;
use App\Models\Pencatatan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HapusDataLama extends Command
{
    /**
     * Nama dan signature dari console command.
     *
     * @var string
     */
    protected $signature = 'app:hapus-data-lama'; // Nama perintah untuk dipanggil

    /**
     * Deskripsi dari console command.
     *
     * @var string
     */
    protected $description = 'Menghapus data pencatatan dan arsip yang lebih tua dari 5 tahun';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses penghapusan data lama...');
        Log::info('Cron Job: Memulai proses penghapusan data lama.');

        // Tentukan tanggal batas (5 tahun yang lalu dari sekarang)
        $batasWaktu = now()->subYears(5);

        // 1. HAPUS ARSIP LAMA (File & Database Record)
        $arsipsLama = Arsip::where('created_at', '<', $batasWaktu)->get();
        $jumlahArsipDihapus = 0;

        foreach ($arsipsLama as $arsip) {
            // Hapus file fisik dari storage
            if (Storage::exists($arsip->path_file)) {
                Storage::delete($arsip->path_file);
            }
            // Hapus record dari database
            $arsip->delete();
            $jumlahArsipDihapus++;
        }

        // 2. HAPUS DATA PENCATATAN LAMA
        // Ini akan otomatis menghapus 'pencatatan_details' jika Anda sudah
        // mengatur onDelete('cascade') pada migration.
        $jumlahPencatatanDihapus = Pencatatan::where('created_at', '<', $batasWaktu)->delete();

        $pesan = "Proses selesai. Arsip dihapus: {$jumlahArsipDihapus}. Pencatatan dihapus: {$jumlahPencatatanDihapus}.";
        $this->info($pesan);
        Log::info($pesan);

        return 0;
    }
}