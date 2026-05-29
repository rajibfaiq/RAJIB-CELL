<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestConn extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        try {
            $db->connect();
            if ($db->connID) {
                echo "Koneksi Database Berhasil!<br>";
                echo "Driver: " . $db->getPlatform() . "<br>";
                echo "Database: " . $db->getDatabase();
            } else {
                echo "Koneksi Gagal.";
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
