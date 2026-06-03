<?php
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) die('Koneksi gagal: ' . mysqli_connect_error() . "\n");

// Clear existing test entries in farmasi so we start fresh
mysqli_query($db, "DELETE FROM farmasi WHERE ID_FARMASI = 'F999'");
mysqli_query($db, "DELETE FROM pengobatan WHERE ID_PENGOBATAN = 'OBT999'");

// 1. Insert a new medicine in farmasi
$id_farmasi = 'F999';
$nama_obat = 'Paracetamol 500mg (Test)';
$jenis_obat = 'Tablet';
$harga_obat = 12500;

$stmt = mysqli_prepare($db, "INSERT INTO farmasi (ID_FARMASI, NAMA_OBAT, JENIS_OBAT, HARGA_OBAT) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssd", $id_farmasi, $nama_obat, $jenis_obat, $harga_obat);
$r1 = mysqli_stmt_execute($stmt);
echo $r1 ? "Insert farmasi OK\n" : "Insert farmasi FAILED: " . mysqli_error($db) . "\n";

// 2. Select from farmasi to verify
$r2 = mysqli_query($db, "SELECT * FROM farmasi WHERE ID_FARMASI = 'F999'");
$row2 = mysqli_fetch_assoc($r2);
print_r($row2);

// 3. Insert a prescription in pengobatan linking to this farmasi item
$id_pengobatan = 'OBT999';
$id_periksa = 'PRK001'; // existing check record
$dosis_obat = '3x1 tablet';

$stmt2 = mysqli_prepare($db, "INSERT INTO pengobatan (ID_PENGOBATAN, ID_PERIKSA, ID_FARMASI, NAMA_OBAT, DOSIS_OBAT, HARGA_OBAT) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt2, "sssssd", $id_pengobatan, $id_periksa, $id_farmasi, $nama_obat, $dosis_obat, $harga_obat);
$r3 = mysqli_stmt_execute($stmt2);
echo $r3 ? "Insert pengobatan OK\n" : "Insert pengobatan FAILED: " . mysqli_error($db) . "\n";

// 4. Select from pengobatan with join to verify relational retrieval
$r4 = mysqli_query($db, "SELECT p.*, f.JENIS_OBAT FROM pengobatan p LEFT JOIN farmasi f ON p.ID_FARMASI = f.ID_FARMASI WHERE p.ID_PENGOBATAN = 'OBT999'");
$row4 = mysqli_fetch_assoc($r4);
print_r($row4);

// Clean up test data
mysqli_query($db, "DELETE FROM farmasi WHERE ID_FARMASI = 'F999'");
mysqli_query($db, "DELETE FROM pengobatan WHERE ID_PENGOBATAN = 'OBT999'");

mysqli_close($db);
