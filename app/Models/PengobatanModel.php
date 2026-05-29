<?php

namespace App\Models;

use CodeIgniter\Model;

class PengobatanModel extends Model
{
    protected $table            = 'pengobatan';
    protected $primaryKey       = 'ID_PENGOBATAN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_PENGOBATAN', 'ID_PERIKSA', 'NAMA_OBAT', 'DOSIS_OBAT', 'HARGA_OBAT'];

    /**
     * Get joined data with Pasien name through Pemeriksaan
     */
    public function getJoinedData()
    {
        return $this->select('pengobatan.*, pasien.NAMA_PASIEN')
                    ->join('pemeriksaan', 'pemeriksaan.ID_PERIKSA = pengobatan.ID_PERIKSA')
                    ->join('pasien', 'pasien.ID_PASIEN = pemeriksaan.ID_PASIEN')
                    ->orderBy('pengobatan.ID_PENGOBATAN', 'DESC')
                    ->findAll();
    }

    /**
     * Generate next Pengobatan ID in format OBT001, OBT002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_PENGOBATAN', 'DESC')->first();

        if (!$lastRow) {
            return 'OBT001';
        }

        $lastNumber = (int) substr($lastRow['ID_PENGOBATAN'], 3);
        $nextNumber = $lastNumber + 1;

        return 'OBT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
