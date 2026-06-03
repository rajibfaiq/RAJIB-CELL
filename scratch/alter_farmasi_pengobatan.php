<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) die('Koneksi gagal: ' . mysqli_connect_error() . "\n");

// Add ID_FARMASI to pengobatan table (FK to farmasi)
$r1 = mysqli_query($db, 'ALTER TABLE pengobatan ADD COLUMN IF NOT EXISTS ID_FARMASI VARCHAR(10) DEFAULT NULL AFTER ID_PERIKSA');
echo $r1 ? "ALTER pengobatan ADD ID_FARMASI: OK\n" : "ERROR: " . mysqli_error($db) . "\n";

// Verify farmasi table has NAMA_OBAT (should already exist)
$r2 = mysqli_query($db, "SHOW COLUMNS FROM farmasi LIKE 'NAMA_OBAT'");
$col = mysqli_fetch_assoc($r2);
echo $col ? "farmasi.NAMA_OBAT column: EXISTS\n" : "farmasi.NAMA_OBAT column: MISSING - need to add\n";
if (!$col) {
    $r3 = mysqli_query($db, "ALTER TABLE farmasi ADD COLUMN NAMA_OBAT VARCHAR(150) DEFAULT NULL AFTER ID_FARMASI");
    echo $r3 ? "Added NAMA_OBAT to farmasi: OK\n" : "ERROR: " . mysqli_error($db) . "\n";
}

// Show final schemas
echo "\n--- farmasi columns ---\n";
$r = mysqli_query($db, "SHOW COLUMNS FROM farmasi");
while ($c = mysqli_fetch_assoc($r)) echo "  {$c['Field']} ({$c['Type']})\n";

echo "\n--- pengobatan columns ---\n";
$r = mysqli_query($db, "SHOW COLUMNS FROM pengobatan");
while ($c = mysqli_fetch_assoc($r)) echo "  {$c['Field']} ({$c['Type']})\n";

mysqli_close($db);
echo "\nDone.\n";
