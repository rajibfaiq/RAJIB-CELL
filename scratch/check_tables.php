<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) die('Koneksi gagal: ' . mysqli_connect_error() . "\n");

echo "=== FARMASI ROWS ===\n";
$r = mysqli_query($db, "SELECT * FROM farmasi LIMIT 10");
while ($row = mysqli_fetch_assoc($r)) {
    print_r($row);
}

echo "\n=== PENGOBATAN ROWS ===\n";
$r = mysqli_query($db, "SELECT * FROM pengobatan LIMIT 10");
while ($row = mysqli_fetch_assoc($r)) {
    print_r($row);
}

mysqli_close($db);
