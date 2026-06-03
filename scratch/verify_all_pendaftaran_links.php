<?php
// Use raw mysqli to verify the queries.
$db = mysqli_connect('localhost', 'root', '', 'rumah sakit');
if (!$db) die('Koneksi gagal: ' . mysqli_connect_error() . "\n");

echo "=== VERIFYING PEMERIKSAAN QUERY ===\n";
$sql1 = "SELECT pemeriksaan.*, pasien.NAMA_PASIEN, dokter.NAMA_DOKTER, pendaftaran.NO_PENDAFTARAN
         FROM pemeriksaan
         JOIN pasien ON pasien.ID_PASIEN = pemeriksaan.ID_PASIEN
         JOIN dokter ON dokter.ID_DOKTER = pemeriksaan.ID_DOKTER
         LEFT JOIN pendaftaran ON pendaftaran.ID_PASIEN = pemeriksaan.ID_PASIEN 
              AND pendaftaran.ID_DOKTER = pemeriksaan.ID_DOKTER 
              AND DATE(pendaftaran.TANGGAL_DAFTAR) = DATE(pemeriksaan.TGL_PERIKSA)
         LIMIT 3";
$r1 = mysqli_query($db, $sql1);
if ($r1) {
    echo "Pemeriksaan query OK. Rows returned: " . mysqli_num_rows($r1) . "\n";
    while ($row = mysqli_fetch_assoc($r1)) {
        echo " - ID: {$row['ID_PERIKSA']}, Pasien: {$row['NAMA_PASIEN']}, No Daftar: " . ($row['NO_PENDAFTARAN'] ?: 'NULL') . "\n";
    }
} else {
    echo "Pemeriksaan query FAILED: " . mysqli_error($db) . "\n";
}

echo "\n=== VERIFYING RONTGEN QUERY ===\n";
$sql2 = "SELECT rontgen.*, pasien.NAMA_PASIEN, dokter.NAMA_DOKTER, pendaftaran.NO_PENDAFTARAN
         FROM rontgen
         JOIN pemeriksaan ON pemeriksaan.ID_PERIKSA = rontgen.ID_PERIKSA
         JOIN pasien ON pasien.ID_PASIEN = pemeriksaan.ID_PASIEN
         LEFT JOIN dokter ON dokter.ID_DOKTER = pemeriksaan.ID_DOKTER
         LEFT JOIN pendaftaran ON pendaftaran.ID_PASIEN = pemeriksaan.ID_PASIEN 
              AND pendaftaran.ID_DOKTER = pemeriksaan.ID_DOKTER 
              AND DATE(pendaftaran.TANGGAL_DAFTAR) = DATE(pemeriksaan.TGL_PERIKSA)
         LIMIT 3";
$r2 = mysqli_query($db, $sql2);
if ($r2) {
    echo "Rontgen query OK. Rows returned: " . mysqli_num_rows($r2) . "\n";
    while ($row = mysqli_fetch_assoc($r2)) {
        echo " - ID: {$row['ID_RONTGEN']}, Pasien: {$row['NAMA_PASIEN']}, No Daftar: " . ($row['NO_PENDAFTARAN'] ?: 'NULL') . "\n";
    }
} else {
    echo "Rontgen query FAILED: " . mysqli_error($db) . "\n";
}

echo "\n=== VERIFYING PERAWATAN QUERY ===\n";
$sql3 = "SELECT perawatan.ID_PERAWATAN, perawatan.ID_PASIEN, perawatan.TGL_PERAWATAN, pasien.NAMA_PASIEN, pendaftaran.NO_PENDAFTARAN
         FROM perawatan
         JOIN pasien ON pasien.ID_PASIEN = perawatan.ID_PASIEN
         LEFT JOIN pendaftaran ON pendaftaran.ID_PASIEN = perawatan.ID_PASIEN 
              AND DATE(pendaftaran.TANGGAL_DAFTAR) = DATE(perawatan.TGL_PERAWATAN)
         LIMIT 3";
$r3 = mysqli_query($db, $sql3);
if ($r3) {
    echo "Perawatan query OK. Rows returned: " . mysqli_num_rows($r3) . "\n";
    while ($row = mysqli_fetch_assoc($r3)) {
        echo " - ID: {$row['ID_PERAWATAN']}, Pasien: {$row['NAMA_PASIEN']}, No Daftar: " . ($row['NO_PENDAFTARAN'] ?: 'NULL') . "\n";
    }
} else {
    echo "Perawatan query FAILED: " . mysqli_error($db) . "\n";
}

echo "\n=== VERIFYING PENGOBATAN QUERY ===\n";
$sql4 = "SELECT pengobatan.*, pasien.NAMA_PASIEN, pendaftaran.NO_PENDAFTARAN
         FROM pengobatan
         JOIN pemeriksaan ON pemeriksaan.ID_PERIKSA = pengobatan.ID_PERIKSA
         JOIN pasien ON pasien.ID_PASIEN = pemeriksaan.ID_PASIEN
         LEFT JOIN pendaftaran ON pendaftaran.ID_PASIEN = pemeriksaan.ID_PASIEN 
              AND pendaftaran.ID_DOKTER = pemeriksaan.ID_DOKTER 
              AND DATE(pendaftaran.TANGGAL_DAFTAR) = DATE(pemeriksaan.TGL_PERIKSA)
         LIMIT 3";
$r4 = mysqli_query($db, $sql4);
if ($r4) {
    echo "Pengobatan query OK. Rows returned: " . mysqli_num_rows($r4) . "\n";
    while ($row = mysqli_fetch_assoc($r4)) {
        echo " - ID: {$row['ID_PENGOBATAN']}, Pasien: {$row['NAMA_PASIEN']}, No Daftar: " . ($row['NO_PENDAFTARAN'] ?: 'NULL') . "\n";
    }
} else {
    echo "Pengobatan query FAILED: " . mysqli_error($db) . "\n";
}

mysqli_close($db);
