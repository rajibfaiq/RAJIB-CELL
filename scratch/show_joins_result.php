<?php
$host = 'localhost';
$db = 'rumah sakit';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "Koneksi Berhasil ke MariaDB/MySQL [$db]\n\n";

    // Helper function to render database results as an ASCII table, mimicking mysql CLI
    function renderTable($results, $title = "") {
        if (empty($results)) {
            echo "Empty set (0.00 sec)\n\n";
            return;
        }

        if ($title) {
            echo "--- $title ---\n";
        }

        $headers = array_keys($results[0]);
        $widths = [];
        foreach ($headers as $header) {
            $widths[$header] = strlen($header);
        }

        foreach ($results as $row) {
            foreach ($row as $col => $val) {
                $widths[$col] = max($widths[$col], strlen((string)$val));
            }
        }

        // Draw top border
        $border = "+";
        foreach ($widths as $w) {
            $border .= str_repeat("-", $w + 2) . "+";
        }
        echo "$border\n";

        // Draw headers
        $headerLine = "|";
        foreach ($widths as $col => $w) {
            $headerLine .= " " . str_pad($col, $w) . " |";
        }
        echo "$headerLine\n";
        echo "$border\n";

        // Draw rows
        foreach ($results as $row) {
            $rowLine = "|";
            foreach ($widths as $col => $w) {
                $rowLine .= " " . str_pad((string)$row[$col], $w) . " |";
            }
            echo "$rowLine\n";
        }
        echo "$border\n";
        echo count($results) . " rows in set (0.001 sec)\n\n";
    }

    // 1. Pendaftaran JOIN Pasien
    echo "1. Query: SELECT p.NO_PENDAFTARAN, ps.NAMA_PASIEN, p.TANGGAL_DAFTAR FROM pendaftaran p INNER JOIN pasien ps ON p.ID_PASIEN = ps.ID_PASIEN;\n";
    $stmt = $pdo->query("SELECT p.NO_PENDAFTARAN, ps.NAMA_PASIEN, p.TANGGAL_DAFTAR FROM pendaftaran p INNER JOIN pasien ps ON p.ID_PASIEN = ps.ID_PASIEN");
    renderTable($stmt->fetchAll());

    // 2. Pemeriksaan JOIN Pasien & Dokter
    echo "2. Query: SELECT pem.ID_PERIKSA, ps.NAMA_PASIEN, d.NAMA_DOKTER, pem.DIAGNOSA FROM pemeriksaan pem INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN INNER JOIN dokter d ON pem.ID_DOKTER = d.ID_DOKTER;\n";
    $stmt = $pdo->query("SELECT pem.ID_PERIKSA, ps.NAMA_PASIEN, d.NAMA_DOKTER, pem.DIAGNOSA FROM pemeriksaan pem INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN INNER JOIN dokter d ON pem.ID_DOKTER = d.ID_DOKTER");
    renderTable($stmt->fetchAll());

    // 3. Administrasi (Billing) JOIN Pasien
    echo "3. Query: SELECT a.ID_ADMINISTRASI, ps.NAMA_PASIEN, a.BIAYA, a.JENIS_PEMBAYARAN FROM administrasi a INNER JOIN pendaftaran p ON a.NO_PENDAFTARAN = p.NO_PENDAFTARAN INNER JOIN pasien ps ON p.ID_PASIEN = ps.ID_PASIEN;\n";
    $stmt = $pdo->query("SELECT a.ID_ADMINISTRASI, ps.NAMA_PASIEN, a.BIAYA, a.JENIS_PEMBAYARAN FROM administrasi a INNER JOIN pendaftaran p ON a.NO_PENDAFTARAN = p.NO_PENDAFTARAN INNER JOIN pasien ps ON p.ID_PASIEN = ps.ID_PASIEN");
    renderTable($stmt->fetchAll());

    // 4. Kamar JOIN Pasien
    echo "4. Query: SELECT k.ID_KAMAR, k.NOMOR_KAMAR, k.TIPE_KAMAR, k.STATUS, ps.NAMA_PASIEN FROM kamar k LEFT JOIN pemeriksaan pem ON k.ID_PERIKSA = pem.ID_PERIKSA LEFT JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN;\n";
    $stmt = $pdo->query("SELECT k.ID_KAMAR, k.NOMOR_KAMAR, k.TIPE_KAMAR, k.STATUS, IFNULL(ps.NAMA_PASIEN, '-') as NAMA_PASIEN FROM kamar k LEFT JOIN pemeriksaan pem ON k.ID_PERIKSA = pem.ID_PERIKSA LEFT JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN");
    renderTable($stmt->fetchAll());

    // 5. Laboratorium JOIN Pasien
    echo "5. Query: SELECT l.ID_LABORATORIUM, ps.NAMA_PASIEN, l.JENIS_PEMERIKSAAN, l.HASIL_LAB FROM laboratorium l INNER JOIN pemeriksaan pem ON l.ID_PERIKSA = pem.ID_PERIKSA INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN;\n";
    $stmt = $pdo->query("SELECT l.ID_LABORATORIUM, ps.NAMA_PASIEN, l.JENIS_PEMERIKSAAN, l.HASIL_LAB FROM laboratorium l INNER JOIN pemeriksaan pem ON l.ID_PERIKSA = pem.ID_PERIKSA INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN");
    renderTable($stmt->fetchAll());

    // 6. Perawatan JOIN Pasien & Kamar
    echo "6. Query: SELECT pr.ID_PERAWATAN, ps.NAMA_PASIEN, k.NOMOR_KAMAR, pr.RAWAT_INAP, pr.RAWAT_JALAN FROM perawatan pr INNER JOIN pasien ps ON pr.ID_PASIEN = ps.ID_PASIEN LEFT JOIN kamar k ON pr.ID_KAMAR = k.ID_KAMAR;\n";
    $stmt = $pdo->query("SELECT pr.ID_PERAWATAN, ps.NAMA_PASIEN, IFNULL(k.NOMOR_KAMAR, '-') as NOMOR_KAMAR, pr.RAWAT_INAP, pr.RAWAT_JALAN FROM perawatan pr INNER JOIN pasien ps ON pr.ID_PASIEN = ps.ID_PASIEN LEFT JOIN kamar k ON pr.ID_KAMAR = k.ID_KAMAR");
    renderTable($stmt->fetchAll());

    // 7. Pengobatan JOIN Pasien
    echo "7. Query: SELECT ob.ID_PENGOBATAN, ps.NAMA_PASIEN, ob.NAMA_OBAT, ob.DOSIS_OBAT FROM pengobatan ob INNER JOIN pemeriksaan pem ON ob.ID_PERIKSA = pem.ID_PERIKSA INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN;\n";
    $stmt = $pdo->query("SELECT ob.ID_PENGOBATAN, ps.NAMA_PASIEN, ob.NAMA_OBAT, ob.DOSIS_OBAT FROM pengobatan ob INNER JOIN pemeriksaan pem ON ob.ID_PERIKSA = pem.ID_PERIKSA INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN");
    renderTable($stmt->fetchAll());

    // 8. Rontgen JOIN Pasien
    echo "8. Query: SELECT r.ID_RONTGEN, ps.NAMA_PASIEN, r.HASIL_RONTGEN, r.KETERANGAN FROM rontgen r INNER JOIN pemeriksaan pem ON r.ID_PERIKSA = pem.ID_PERIKSA INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN;\n";
    $stmt = $pdo->query("SELECT r.ID_RONTGEN, ps.NAMA_PASIEN, r.HASIL_RONTGEN, r.KETERANGAN FROM rontgen r INNER JOIN pemeriksaan pem ON r.ID_PERIKSA = pem.ID_PERIKSA INNER JOIN pasien ps ON pem.ID_PASIEN = ps.ID_PASIEN");
    renderTable($stmt->fetchAll());

    // 9. Farmasi JOIN Pengobatan
    echo "9. Query: SELECT f.ID_FARMASI, ob.NAMA_OBAT, f.JENIS_OBAT, f.HARGA_OBAT FROM farmasi f LEFT JOIN pengobatan ob ON f.ID_PENGOBATAN = ob.ID_PENGOBATAN;\n";
    $stmt = $pdo->query("SELECT f.ID_FARMASI, IFNULL(ob.NAMA_OBAT, '-') as NAMA_OBAT, f.JENIS_OBAT, f.HARGA_OBAT FROM farmasi f LEFT JOIN pengobatan ob ON f.ID_PENGOBATAN = ob.ID_PENGOBATAN");
    renderTable($stmt->fetchAll());

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
