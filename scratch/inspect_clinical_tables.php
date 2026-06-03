<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) die('Koneksi gagal: ' . mysqli_connect_error() . "\n");

$tables = ['pemeriksaan', 'rontgen', 'perawatan', 'pengobatan'];
foreach ($tables as $t) {
    echo "\n=== Columns for $t ===\n";
    $r = mysqli_query($db, "SHOW COLUMNS FROM `$t`");
    while ($row = mysqli_fetch_assoc($r)) {
        echo "  {$row['Field']} ({$row['Type']})\n";
    }
}

mysqli_close($db);
