<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) die('Koneksi gagal: ' . mysqli_connect_error() . "\n");

// Alter pengobatan.NAMA_OBAT to VARCHAR(100)
$r = mysqli_query($db, "ALTER TABLE pengobatan MODIFY COLUMN NAMA_OBAT VARCHAR(100) NOT NULL");
echo $r ? "ALTER TABLE pengobatan NAMA_OBAT VARCHAR(100): OK\n" : "ERROR: " . mysqli_error($db) . "\n";

mysqli_close($db);
