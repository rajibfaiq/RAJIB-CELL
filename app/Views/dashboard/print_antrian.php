<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Antrian - <?= $p['NO_PENDAFTARAN'] ?></title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 14px; line-height: 1.5; background: #fff; }
        .ticket-box { max-width: 380px; margin: 0 auto; border: 2px dashed #ccc; padding: 20px; border-radius: 10px; background: #fff; text-align: center; }
        .header { border-bottom: 2px double #333; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; margin: 0; color: #2c3e50; font-weight: 800; text-transform: uppercase; }
        .header p { margin: 2px 0; color: #7f8c8d; font-size: 11px; }
        .queue-number-label { font-size: 12px; text-transform: uppercase; color: #7f8c8d; font-weight: 600; letter-spacing: 1px; margin-top: 15px; }
        .queue-number { font-size: 64px; font-weight: 900; color: #4a7dc7; margin: 5px 0; line-height: 1; }
        .details { background: #f8f9fa; border: 1px solid #eee; border-radius: 6px; padding: 12px; margin: 15px 0; text-align: left; }
        .detail-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; border-bottom: 1px dashed #eee; padding-bottom: 4px; }
        .detail-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .detail-row span:first-child { color: #7f8c8d; }
        .detail-row span:last-child { font-weight: 700; color: #2c3e50; }
        .footer { font-size: 10px; color: #7f8c8d; margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px; }
        .print-btn-no-print { margin-top: 15px; padding: 8px 16px; background: #4a7dc7; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        @media print {
            .print-btn-no-print { display: none; }
            body { padding: 0; }
            .ticket-box { border: none; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket-box">
        <div class="header">
            <h1>RS SEJAHTERA</h1>
            <p>Jl. Kesehatan No. 123, Jakarta Selatan</p>
            <p>Telp: (021) 123-4567 | www.rssejahtera.com</p>
        </div>

        <div>
            <span style="font-size:11px; font-family:monospace; background:#e8f0fe; color:#4a7dc7; padding:2px 8px; border-radius:10px; font-weight:bold;"><?= $p['NO_PENDAFTARAN'] ?></span>
        </div>

        <div class="queue-number-label">Nomor Antrian Anda</div>
        <div class="queue-number"><?= $p['NO_ANTRIAN'] ?></div>

        <div class="details">
            <div class="detail-row">
                <span>Pasien:</span>
                <span><?= $p['NAMA_PASIEN'] ?> (<?= $p['ID_PASIEN'] ?>)</span>
            </div>
            <div class="detail-row">
                <span>Poli:</span>
                <span><?= $p['NAMA_POLI'] ?></span>
            </div>
            <div class="detail-row">
                <span>Dokter:</span>
                <span><?= $p['NAMA_DOKTER'] ?></span>
            </div>
            <div class="detail-row">
                <span>Tgl Kunjungan:</span>
                <span><?= date('d M Y', strtotime($p['TANGGAL_KUNJUNGAN'])) ?></span>
            </div>
            <div class="detail-row">
                <span>Sesi:</span>
                <span>Sesi <?= $p['SESI_KUNJUNGAN'] ?></span>
            </div>
            <div class="detail-row" style="background:#e8f0fe; padding: 6px; border-radius: 4px; border:none; margin-top:6px;">
                <span style="color:#4a7dc7; font-weight:700;">Estimasi Jam:</span>
                <span style="color:#4a7dc7; font-weight:800; font-size:13px;"><?= $p['ESTIMASI_JAM'] ?></span>
            </div>
        </div>

        <div class="footer">
            <p><strong>PENTING:</strong> Harap datang 15 menit sebelum estimasi jam panggil. Tunjukkan tiket ini ke perawat atau petugas loket.</p>
            <p style="margin-top: 5px;">Terima kasih atas kepercayaan Anda.</p>
        </div>
        
        <button class="print-btn-no-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak Ulang</button>
    </div>
</body>
</html>
