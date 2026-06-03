<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

$sql = "SELECT perawatan.*, pasien.NAMA_PASIEN, kamar.NOMOR_KAMAR 
        FROM perawatan 
        INNER JOIN pasien ON pasien.ID_PASIEN = perawatan.ID_PASIEN 
        LEFT JOIN kamar ON kamar.ID_KAMAR = perawatan.ID_KAMAR";

$result = mysqli_query($db, $sql);
if (!$result) {
    die('Query error: ' . mysqli_error($db));
}

$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

echo "Total rows: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "ID_PERAWATAN: {$r['ID_PERAWATAN']} | ID_PASIEN: {$r['ID_PASIEN']} | NAMA_PASIEN: {$r['NAMA_PASIEN']} | RAWAT_JALAN: {$r['RAWAT_JALAN']} | RAWAT_INAP: {$r['RAWAT_INAP']}\n";
}

mysqli_close($db);
