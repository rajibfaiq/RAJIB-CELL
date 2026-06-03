<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) {
    die('Koneksi gagal: ' . mysqli_connect_error() . "\n");
}

// Add TGL_PERAWATAN column
$r1 = mysqli_query($db, 'ALTER TABLE perawatan ADD COLUMN IF NOT EXISTS TGL_PERAWATAN DATE DEFAULT NULL AFTER ID_PASIEN');
echo $r1 ? "ALTER TABLE OK\n" : ("ALTER ERROR: " . mysqli_error($db) . "\n");

// Fill existing rows with today's date
$r2 = mysqli_query($db, "UPDATE perawatan SET TGL_PERAWATAN = CURDATE() WHERE TGL_PERAWATAN IS NULL");
echo $r2 ? ("UPDATE OK: " . mysqli_affected_rows($db) . " rows updated\n") : ("UPDATE ERROR: " . mysqli_error($db) . "\n");

// Verify
$r3 = mysqli_query($db, "SELECT ID_PERAWATAN, TGL_PERAWATAN, RAWAT_JALAN, RAWAT_INAP FROM perawatan ORDER BY TGL_PERAWATAN DESC");
echo "\nVerify perawatan table:\n";
while ($row = mysqli_fetch_assoc($r3)) {
    echo "  {$row['ID_PERAWATAN']} | TGL: {$row['TGL_PERAWATAN']} | JALAN: {$row['RAWAT_JALAN']} | INAP: {$row['RAWAT_INAP']}\n";
}

mysqli_close($db);
echo "\nDone.\n";
