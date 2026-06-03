<?php

namespace App\Models;

use CodeIgniter\Model;

class PemeriksaanModel extends Model
{
    protected $table            = 'pemeriksaan';
    protected $primaryKey       = 'ID_PERIKSA';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_PERIKSA', 'ID_DOKTER', 'ID_PERAWATAN', 'ID_PASIEN', 'TGL_PERIKSA', 'DIAGNOSA'];

    /**
     * Get joined data with Pasien, Dokter names, and Registration number
     */
    public function getJoinedData()
    {
        return $this->select('pemeriksaan.*, pasien.NAMA_PASIEN, dokter.NAMA_DOKTER, 
                             pembayaran.STATUS as STATUS_PEMBAYARAN, pembayaran.BIAYA as BIAYA_PEMBAYARAN, pembayaran.ID_PEMBAYARAN,
                             (SELECT pd.NO_PENDAFTARAN FROM pendaftaran pd 
                              WHERE pd.ID_PASIEN = pemeriksaan.ID_PASIEN 
                              ORDER BY 
                                (DATE(pd.TANGGAL_DAFTAR) = DATE(pemeriksaan.TGL_PERIKSA) AND pd.ID_DOKTER = pemeriksaan.ID_DOKTER) DESC, 
                                (DATE(pd.TANGGAL_DAFTAR) = DATE(pemeriksaan.TGL_PERIKSA)) DESC, 
                                (pd.ID_DOKTER = pemeriksaan.ID_DOKTER) DESC, 
                                pd.TANGGAL_DAFTAR DESC 
                              LIMIT 1) AS NO_PENDAFTARAN')
                    ->join('pasien', 'pasien.ID_PASIEN = pemeriksaan.ID_PASIEN')
                    ->join('dokter', 'dokter.ID_DOKTER = pemeriksaan.ID_DOKTER')
                    ->join('pembayaran', "pembayaran.JENIS_LAYANAN = 'pemeriksaan' AND pembayaran.ID_REFERENSI = pemeriksaan.ID_PERIKSA", 'left')
                    ->findAll();
    }

    /**
     * Generate next Pemeriksaan ID in format PRK001, PRK002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_PERIKSA', 'DESC')->first();

        if (!$lastRow) {
            return 'PRK001';
        }

        $lastNumber = (int) substr($lastRow['ID_PERIKSA'], 3);
        $nextNumber = $lastNumber + 1;

        return 'PRK' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
