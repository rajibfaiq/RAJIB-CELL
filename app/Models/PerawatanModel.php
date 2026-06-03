<?php

namespace App\Models;

use CodeIgniter\Model;

class PerawatanModel extends Model
{
    protected $table            = 'perawatan';
    protected $primaryKey       = 'ID_PERAWATAN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_PERAWATAN', 'ID_KAMAR', 'ID_PASIEN', 'TGL_PERAWATAN', 'RAWAT_INAP', 'RAWAT_JALAN'];

    /**
     * Get joined data with Pasien and Kamar names
     */
    public function getJoinedData()
    {
        return $this->select('perawatan.ID_PERAWATAN, perawatan.ID_PASIEN, perawatan.ID_KAMAR, perawatan.TGL_PERAWATAN, perawatan.RAWAT_JALAN, perawatan.RAWAT_INAP, pasien.NAMA_PASIEN, kamar.NOMOR_KAMAR, 
                             (SELECT pd.NO_PENDAFTARAN FROM pendaftaran pd 
                              WHERE pd.ID_PASIEN = perawatan.ID_PASIEN 
                              ORDER BY 
                                (DATE(pd.TANGGAL_DAFTAR) = DATE(perawatan.TGL_PERAWATAN)) DESC, 
                                pd.TANGGAL_DAFTAR DESC 
                              LIMIT 1) AS NO_PENDAFTARAN,
                             p_kamar.STATUS as STATUS_KAMAR, p_kamar.BIAYA as BIAYA_KAMAR,
                             p_jalan.STATUS as STATUS_JALAN, p_jalan.BIAYA as BIAYA_JALAN')
                    ->join('pasien', 'pasien.ID_PASIEN = perawatan.ID_PASIEN')
                    ->join('kamar', 'kamar.ID_KAMAR = perawatan.ID_KAMAR', 'left')
                    ->join('pembayaran p_kamar', "p_kamar.JENIS_LAYANAN = 'kamar' AND p_kamar.ID_REFERENSI = perawatan.ID_PERAWATAN", 'left')
                    ->join('pembayaran p_jalan', "p_jalan.JENIS_LAYANAN = 'perawatan' AND p_jalan.ID_REFERENSI = perawatan.ID_PERAWATAN", 'left')
                    ->findAll();
    }

    /**
     * Generate next Perawatan ID in format RAW001, RAW002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_PERAWATAN', 'DESC')->first();

        if (!$lastRow) {
            return 'RAW001';
        }

        $lastNumber = (int) substr($lastRow['ID_PERAWATAN'], 3);
        $nextNumber = $lastNumber + 1;

        return 'RAW' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
