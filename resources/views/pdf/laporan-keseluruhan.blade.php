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
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN PERKEMBANGAN TERNAK</h1>
        <h2>BANTUAN ANGGARAN DANA DESA (ADD)</h2>
    </div>

    {{-- Info Kelompok & Tanggal Laporan --}}
    <div class="info-section">
        <strong>Nama Kelompok:</strong> Gopala Dwi Amertha Sari<br>
        <strong>Desa/Kecamatan/Kabupaten:</strong> Jinengdalem/Buleleng/Buleleng<br>
        <strong>Jenis Hewan Ternak:</strong> Sapi & Kambing<br>
        <strong>Tanggal Laporan:</strong> {{ $tanggalLaporan ?? now()->translatedFormat('d F Y') }}
    </div>

    {{-- Tabel Utama --}}
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
            @php
                $totalJantan = 0;
                $totalBetina = 0;
                $totalHarga = 0;
            @endphp
            
            @foreach ($anggotas as $anggota)
                @php
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
                @endphp
                <tr>
                    <td class="member-section">{{ $loop->iteration }}</td>
                    <td class="member-section">{{ $anggota->nama }}</td>
                    <td class="member-section">{{ optional($anggota->tahap)->tahap_ke }} ({{ optional($anggota->tahap)->tahun }})</td>
                    <td class="member-section">{{ ucfirst($anggota->jenis_ternak) }}</td>
                    {{-- BENAR: Menampilkan harga HANYA dari periode ini --}}
                    <td class="member-section">Rp {{ number_format($hargaPeriodeIni, 0, ',', '.') }}</td>
                    <td class="member-section">{{ $anggota->lokasi_kandang }}</td>
                    
                    {{-- ======================================================= --}}
                    {{-- ▼▼▼ KESELURUHAN BLOK INI YANG DIPERBAIKI ▼▼▼          --}}
                    {{-- ======================================================= --}}
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
                                @php $hasContent = false; @endphp

                                @foreach($anggota->groupedTernaks as $group)
                                    
                                    @if ($group['type'] === 'parent_with_children')
                                        @php
                                            $hasContent = true;
                                            $indukDetail = $group['induk_detail'];
                                            $induk = $indukDetail->ternak; // Data master ternak
                                            if ($induk && $induk->jenis_kelamin == 'Jantan') $totalJantan++;
                                            if ($induk && $induk->jenis_kelamin == 'Betina') $totalBetina++;
                                        @endphp
                                        {{-- Baris untuk Induk --}}
                                        <tr>
                                            <td><strong>{{ $induk->tipe_ternak ?? 'Induk' }}</strong></td>
                                            <td>{{ $induk->no_ear_tag ?? '-' }}</td>
                                            <td>{{ $indukDetail->umur_saat_dicatat }}</td>
                                            <td>{{ $induk->jenis_kelamin ?? '-' }}</td>
                                            <td>{{ $indukDetail->kondisi_ternak }}</td>
                                            <td>{{ $indukDetail->status_vaksin }}</td>
                                        </tr>
                                        
                                        @foreach($group['anak_details'] as $anakDetail)
                                            @php
                                                $anak = $anakDetail->ternak;
                                                if ($anak && $anak->jenis_kelamin == 'Jantan') $totalJantan++;
                                                if ($anak && $anak->jenis_kelamin == 'Betina') $totalBetina++;
                                            @endphp
                                            <tr class="child-row">
                                                <td class="child-indicator" style="padding-left: 10px;">Anak</td>
                                                <td>{{ $anak->no_ear_tag ?? '-' }}</td>
                                                <td>{{ $anakDetail->umur_saat_dicatat }}</td>
                                                <td>{{ $anak->jenis_kelamin ?? '-' }}</td>
                                                <td>{{ $anakDetail->kondisi_ternak }}</td>
                                                <td>{{ $anakDetail->status_vaksin }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                                
                                @foreach($anggota->groupedTernaks->where('type', 'orphan_children') as $group)
                                    @php $hasContent = true; @endphp
                                    
                                    @foreach($group['anak_details'] as $anakDetail)
                                        @php
                                            $anak = $anakDetail->ternak;
                                            if ($anak && $anak->jenis_kelamin == 'Jantan') $totalJantan++;
                                            if ($anak && $anak->jenis_kelamin == 'Betina') $totalBetina++;
                                        @endphp
                                        <tr>
                                            <td>Anak</td>
                                            <td>{{ $anak->no_ear_tag ?? '-' }}</td>
                                            <td>{{ $anakDetail->umur_saat_dicatat }}</td>
                                            <td>{{ $anak->jenis_kelamin ?? '-' }}</td>
                                            <td>{{ $anakDetail->kondisi_ternak }}</td>
                                            <td>{{ $anakDetail->status_vaksin }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                @if(!$hasContent)
                                    <tr>
                                        <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada data pencatatan pada periode ini.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                    {{-- ======================================================= --}}
                    {{-- ▲▲▲ AKHIR BLOK YANG DIPERBAIKI ▲▲▲                     --}}
                    {{-- ======================================================= --}}

                    <td class="findings-section">
                        {{ optional($anggota->latestPencatatan)->temuan_lapangan ?? '-' }}
                    </td>
                </tr>
            @endforeach
            
            {{-- Baris Total Harga Saja --}}
            <tr class="total-row">
                <td colspan="4" style="text-align: center;"><strong>TOTAL</strong></td>
                <td style="text-align: center;"><strong>Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    {{-- Bagian Summary List Jantan & Betina --}}
    <div style="margin-top: 15px; font-size: 11px;">
        <strong>RINGKASAN TERNAK</strong><br>
        Total Ternak Jantan: {{ $totalJantan }}<br>
        Total Ternak Betina: {{ $totalBetina }}<br>
        Total Seluruh Ternak: {{ $totalJantan + $totalBetina }}
    </div>

    {{-- Bagian Tanda Tangan --}}
    <div class="signature-section" style="margin-top: 60px;">
        <div style="text-align: right; margin-bottom: 40px; padding-right: 50px; font-size: 10px;">
            <div>Jinengdalem, {{ $tanggalLaporan ?? now()->translatedFormat('d F Y') }}</div>
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

    {{-- Lampiran Dokumentasi --}}
    <div class="page-break"></div>
    <div class="documentation-section">
        <div class="documentation-title">LAMPIRAN DOKUMENTASI TERNAK</div>
        
        @php
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
        @endphp
        
        <table class="documentation-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    @for ($i = 1; $i <= $maxFotos; $i++)
                        <th>Foto {{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($anggotas as $anggota)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $anggota->nama }}</td>
                        
                        @php
                            $latestPencatatan = $anggota->pencatatans->sortByDesc('tanggal_catatan')->first();
                            $fotos = [];
                            if ($latestPencatatan && $latestPencatatan->foto_dokumentasi) {
                                $fotos = is_array($latestPencatatan->foto_dokumentasi)
                                    ? $latestPencatatan->foto_dokumentasi
                                    : (json_decode($latestPencatatan->foto_dokumentasi, true) ?? []);
                            }
                        @endphp
                        
                        @for ($i = 0; $i < $maxFotos; $i++)
                            <td>
                                @if (isset($fotos[$i]))
                                    <img src="{{ storage_path('app/public/' . $fotos[$i]) }}" class="documentation-image">
                                @else
                                    <div style="color: #ccc;">-</div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div>Dicetak oleh: {{ auth()->user()->name ?? 'System' }} pada {{ now()->translatedFormat('d F Y, H:i') }} WITA</div>
    </div>
</body>
</html>