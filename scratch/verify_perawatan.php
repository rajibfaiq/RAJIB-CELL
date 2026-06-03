<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) {
    die('Koneksi gagal: ' . mysqli_connect_error() . "\n");
}

// Simulate exactly what PerawatanModel::getJoinedData() does
$sql = "SELECT perawatan.ID_PERAWATAN, perawatan.ID_PASIEN, perawatan.ID_KAMAR, 
               perawatan.TGL_PERAWATAN, perawatan.RAWAT_JALAN, perawatan.RAWAT_INAP,
               pasien.NAMA_PASIEN, kamar.NOMOR_KAMAR
        FROM perawatan
        INNER JOIN pasien ON pasien.ID_PASIEN = perawatan.ID_PASIEN
        LEFT JOIN kamar ON kamar.ID_KAMAR = perawatan.ID_KAMAR
        ORDER BY perawatan.TGL_PERAWATAN DESC";

$result = mysqli_query($db, $sql);
if (!$result) {
    die('Query error: ' . mysqli_error($db) . "\n");
}

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

echo "=== Simulasi getJoinedData() ===\n";
echo "Total rows: " . count($rows) . "\n\n";

$hasJalan = false;
$hasInap = false;
echo "--- Rawat Jalan ---\n";
foreach ($rows as $r) {
    if ($r['RAWAT_JALAN'] == 1) {
        $hasJalan = true;
        $tgl = !empty($r['TGL_PERAWATAN']) ? date('d M Y', strtotime($r['TGL_PERAWATAN'])) : '-';
        echo "  {$r['ID_PERAWATAN']} | {$r['NAMA_PASIEN']} | Tgl: {$tgl} | Kamar: " . ($r['NOMOR_KAMAR'] ?: '-') . "\n";
    }
}
if (!$hasJalan) echo "  (Tidak ada data Rawat Jalan)\n";

echo "\n--- Rawat Inap ---\n";
foreach ($rows as $r) {
    if ($r['RAWAT_INAP'] == 1) {
        $hasInap = true;
        $tgl = !empty($r['TGL_PERAWATAN']) ? date('d M Y', strtotime($r['TGL_PERAWATAN'])) : '-';
        echo "  {$r['ID_PERAWATAN']} | {$r['NAMA_PASIEN']} | Tgl: {$tgl} | Kamar: " . ($r['NOMOR_KAMAR'] ?: '-') . "\n";
    }
}
if (!$hasInap) echo "  (Tidak ada data Rawat Inap)\n";

mysqli_close($db);
echo "\n=== SELESAI ===\n";
