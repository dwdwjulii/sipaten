<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Perkembangan Ternak</title>
    <style>
        /* CSS UTAMA */
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10px; margin: 0; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 5px 0; font-weight: bold; }
        .header h2 { font-size: 14px; margin: 5px 0; font-weight: bold; }
        .info-section { margin-bottom: 15px; font-size: 11px; }
        .main-table { width: 100%; border-collapse: collapse; font-size: 9px; margin: 15px 0; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .member-section { background-color: #f9f9f9; font-weight: bold; text-align: center; }
        .livestock-detail table { width: 100%; border-collapse: collapse; }
        .livestock-detail th, .livestock-detail td { border: 1px solid #666; padding: 3px; text-align: center; font-size: 8px; }
        .livestock-detail th { background-color: #e8e8e8; font-weight: bold; }
        .child-row td { background-color: #ffffff; }
        .child-row .child-indicator { padding-left: 15px !important; text-align: left !important; font-style: normal; }
        .findings-section { font-size: 9px; text-align: left; vertical-align: middle; }
        .page-break { page-break-before: always; }
        .footer { margin-top: 30px; font-size: 8px; text-align: right; color: #666; }
        .total-row { background-color: #e6f2ff; font-weight: bold; }

        /* CSS UNTUK TABEL DETAIL PERKEMBANGAN (YANG DI DALAM) */
        .livestock-detail-table {
            width: 100%;
            border-collapse: collapse;
            /* KUNCI UTAMA: Memaksa lebar kolom agar konsisten */
            table-layout: fixed; 
        }
        .livestock-detail-table th, 
        .livestock-detail-table td {
            border: 1px solid #666;
            padding: 3px;
            text-align: center;
            font-size: 8px;
            /* Menambahkan word-wrap untuk teks yang panjang */
            word-wrap: break-word;
        }
        .livestock-detail-table th {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        .child-row td {
            background-color: #ffffff;
        }
        .child-row .child-indicator {
            padding-left: 15px !important;
            text-align: left !important;
            font-style: normal;
        }
        
        /* BAGIAN SUMMARY LIST */
        .summary-section { margin-top: 20px; margin-bottom: 20px; padding: 10px; border: 1px solid #000; }
        .summary-section h3 { margin: 0 0 10px 0; font-size: 12px; }
        .summary-list { font-size: 11px; line-height: 1.8; }
        .summary-list li { margin-bottom: 5px; }
        
        /* BAGIAN TANDA TANGAN */
        .signature-section { margin-top: 40px; page-break-inside: avoid; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-table td { width: 33.33%; text-align: center; vertical-align: top; padding-top: 10px; font-size: 10px; }
        .signature-name { text-decoration: underline; font-weight: bold; }
        .signature-space { height: 60px; }
        
        /* BAGIAN DOKUMENTASI */
        .documentation-section { margin-top: 20px; }
        .documentation-title { text-align: center; margin-bottom: 20px; font-size: 16px; font-weight: bold;}
        .documentation-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .documentation-table th, .documentation-table td { border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; }
        .documentation-table th { background-color: #f0f0f0; font-weight: bold; }
        .documentation-table td:first-child { width: 5%; }
        .documentation-table td:nth-child(2) { width: 20%; font-weight: bold; }
        .documentation-image {
            max-width: 100%;
            max-height: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    
    <div class="header">
        <h1>LAPORAN PERKEMBANGAN TERNAK</h1>
        <h2>BANTUAN ANGGARAN DANA DESA (ADD)</h2>
    </div>

    
    <div class="info-section">
        <strong>Nama Kelompok:</strong> Gopala Dwi Amertha Sari<br>
        <strong>Desa/Kecamatan/Kabupaten:</strong> Jinengdalem/Buleleng/Buleleng<br>
        <strong>Jenis Hewan Ternak:</strong> Sapi & Kambing<br>
        <strong>Tanggal Laporan:</strong> <?php echo e($tanggalLaporan ?? now()->translatedFormat('d F Y')); ?>

    </div>

    
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">Nama Anggota</th>
                <th style="width: 8%;">Tahap</th>
                <th style="width: 8%;">Jenis Ternak</th>
                <th style="width: 10%;">Harga Awal</th>
                <th style="width: 10%;">Lokasi Kandang</th>
                <th style="width: 36%;">Perkembangan Ternak</th>
                <th style="width: 20%;">Temuan Lapangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalJantan = 0;
                $totalBetina = 0;
                $totalHarga = 0;
            ?>
            
            <?php $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // PERBAIKAN: Menjumlahkan harga HANYA dari ternak yang TIDAK MATI/TERJUAL
                    $hargaPeriodeIni = $anggota->latestPencatatan 
                        ? $anggota->latestPencatatan->details->sum(function($detail) {
                    
                            // Cek kondisi ternak DARI SNAPSHOT (detail)
                            if (in_array($detail->kondisi_ternak, ['Mati', 'Terjual'])) {
                            return 0; // Jika mati/terjual, nilainya 0
                            }

                            // Jika hidup, baru ambil harga aslinya dari master ternak
                            return $detail->ternak->harga ?? 0;
                        }) 
                        : 0;
                    $totalHarga += $hargaPeriodeIni;
                ?>
                <tr>
                    <td class="member-section"><?php echo e($loop->iteration); ?></td>
                    <td class="member-section"><?php echo e($anggota->nama); ?></td>
                    <td class="member-section"><?php echo e(optional($anggota->tahap)->tahap_ke); ?> (<?php echo e(optional($anggota->tahap)->tahun); ?>)</td>
                    <td class="member-section"><?php echo e(ucfirst($anggota->jenis_ternak)); ?></td>
                    
                    <td class="member-section">Rp <?php echo e(number_format($hargaPeriodeIni, 0, ',', '.')); ?></td>
                    <td class="member-section"><?php echo e($anggota->lokasi_kandang); ?></td>
                    
                    
                    
                    
                    <td class="livestock-detail" style="padding: 2px;">
                        <table class="livestock-detail-table">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Tipe</th>
                                    <th style="width: 20%;">No. Ear Tag</th>
                                    <th style="width: 15%;">Umur</th>
                                    <th style="width: 15%;">Kelamin</th>
                                    <th style="width: 20%;">Kondisi</th>
                                    <th style="width: 15%;">Vaksin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $hasContent = false; ?>

                                <?php $__currentLoopData = $anggota->groupedTernaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <?php if($group['type'] === 'parent_with_children'): ?>
                                        <?php
                                            $hasContent = true;
                                            $indukDetail = $group['induk_detail'];
                                            $induk = $indukDetail->ternak; // Data master ternak
                                            if ($induk && $induk->jenis_kelamin == 'Jantan') $totalJantan++;
                                            if ($induk && $induk->jenis_kelamin == 'Betina') $totalBetina++;
                                        ?>
                                        
                                        <tr>
                                            <td><strong><?php echo e($induk->tipe_ternak ?? 'Induk'); ?></strong></td>
                                            <td><?php echo e($induk->no_ear_tag ?? '-'); ?></td>
                                            <td><?php echo e($indukDetail->umur_saat_dicatat); ?></td>
                                            <td><?php echo e($induk->jenis_kelamin ?? '-'); ?></td>
                                            <td><?php echo e($indukDetail->kondisi_ternak); ?></td>
                                            <td><?php echo e($indukDetail->status_vaksin); ?></td>
                                        </tr>
                                        
                                        <?php $__currentLoopData = $group['anak_details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anakDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $anak = $anakDetail->ternak;
                                                if ($anak && $anak->jenis_kelamin == 'Jantan') $totalJantan++;
                                                if ($anak && $anak->jenis_kelamin == 'Betina') $totalBetina++;
                                            ?>
                                            <tr class="child-row">
                                                <td class="child-indicator" style="padding-left: 10px;">Anak</td>
                                                <td><?php echo e($anak->no_ear_tag ?? '-'); ?></td>
                                                <td><?php echo e($anakDetail->umur_saat_dicatat); ?></td>
                                                <td><?php echo e($anak->jenis_kelamin ?? '-'); ?></td>
                                                <td><?php echo e($anakDetail->kondisi_ternak); ?></td>
                                                <td><?php echo e($anakDetail->status_vaksin); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php $__currentLoopData = $anggota->groupedTernaks->where('type', 'orphan_children'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $hasContent = true; ?>
                                    
                                    <?php $__currentLoopData = $group['anak_details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anakDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $anak = $anakDetail->ternak;
                                            if ($anak && $anak->jenis_kelamin == 'Jantan') $totalJantan++;
                                            if ($anak && $anak->jenis_kelamin == 'Betina') $totalBetina++;
                                        ?>
                                        <tr>
                                            <td>Anak</td>
                                            <td><?php echo e($anak->no_ear_tag ?? '-'); ?></td>
                                            <td><?php echo e($anakDetail->umur_saat_dicatat); ?></td>
                                            <td><?php echo e($anak->jenis_kelamin ?? '-'); ?></td>
                                            <td><?php echo e($anakDetail->kondisi_ternak); ?></td>
                                            <td><?php echo e($anakDetail->status_vaksin); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php if(!$hasContent): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada data pencatatan pada periode ini.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </td>
                    
                    
                    

                    <td class="findings-section">
                        <?php echo e(optional($anggota->latestPencatatan)->temuan_lapangan ?? '-'); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            
            <tr class="total-row">
                <td colspan="4" style="text-align: center;"><strong>TOTAL</strong></td>
                <td style="text-align: center;"><strong>Rp <?php echo e(number_format($totalHarga, 0, ',', '.')); ?></strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    
    <div style="margin-top: 15px; font-size: 11px;">
        <strong>RINGKASAN TERNAK</strong><br>
        Total Ternak Jantan: <?php echo e($totalJantan); ?><br>
        Total Ternak Betina: <?php echo e($totalBetina); ?><br>
        Total Seluruh Ternak: <?php echo e($totalJantan + $totalBetina); ?>

    </div>

    
    <div class="signature-section" style="margin-top: 60px;">
        <div style="text-align: right; margin-bottom: 40px; padding-right: 50px; font-size: 10px;">
            <div>Jinengdalem, <?php echo e($tanggalLaporan ?? now()->translatedFormat('d F Y')); ?></div>
            <div style="margin-top: 5px;">Ketua Kelompok Ternak</div>
            <div>Gopala Dwi Amertha Sari</div>
            <div style="margin-top: 5px;">Desa Jinengdalem</div>
            <div style="height: 80px;"></div>
            <div style="display: inline-block;">Ttd…………………………………</div>
        </div>

        <div style="margin-top: 40px; font-size: 10px;">
            <div style="text-align: center; margin-bottom: 30px;">Mengetahui,</div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div style="margin-bottom: 5px;">Direktur BUMDES Dwi Amertha Sari</div>
                        <div>Desa Jinengdalem</div>
                        <div style="height: 80px;"></div>
                        <div style="display: inline-block;">Ttd…………………………………</div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div style="margin-bottom: 5px;">Kepala Desa Jinengdalem</div>
                        <div style="height: 80px;"></div>
                        <div style="display: inline-block;">Ttd…………………………………</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    
    <div class="page-break"></div>
    <div class="documentation-section">
        <div class="documentation-title">LAMPIRAN DOKUMENTASI TERNAK</div>
        
        <?php
            // Cari jumlah foto maksimal untuk menentukan jumlah kolom
            $maxFotos = 0;
            foreach ($anggotas as $anggota) {
                $latestPencatatan = $anggota->pencatatans->sortByDesc('tanggal_catatan')->first();
                if ($latestPencatatan && $latestPencatatan->foto_dokumentasi) {
                    $fotos = is_array($latestPencatatan->foto_dokumentasi)
                        ? $latestPencatatan->foto_dokumentasi
                        : (json_decode($latestPencatatan->foto_dokumentasi, true) ?? []);
                    $maxFotos = max($maxFotos, count($fotos));
                }
            }
            $maxFotos = max($maxFotos, 1); // Minimal 1 kolom
        ?>
        
        <table class="documentation-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <?php for($i = 1; $i <= $maxFotos; $i++): ?>
                        <th>Foto <?php echo e($i); ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($anggota->nama); ?></td>
                        
                        <?php
                            $latestPencatatan = $anggota->pencatatans->sortByDesc('tanggal_catatan')->first();
                            $fotos = [];
                            if ($latestPencatatan && $latestPencatatan->foto_dokumentasi) {
                                $fotos = is_array($latestPencatatan->foto_dokumentasi)
                                    ? $latestPencatatan->foto_dokumentasi
                                    : (json_decode($latestPencatatan->foto_dokumentasi, true) ?? []);
                            }
                        ?>
                        
                        <?php for($i = 0; $i < $maxFotos; $i++): ?>
                            <td>
                                <?php if(isset($fotos[$i])): ?>
                                    <img src="<?php echo e(storage_path('app/public/' . $fotos[$i])); ?>" class="documentation-image">
                                <?php else: ?>
                                    <div style="color: #ccc;">-</div>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <div class="footer">
        <div>Dicetak oleh: <?php echo e(auth()->user()->name ?? 'System'); ?> pada <?php echo e(now()->translatedFormat('d F Y, H:i')); ?> WITA</div>
    </div>
</body>
</html><?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/pdf/laporan-keseluruhan.blade.php ENDPATH**/ ?>