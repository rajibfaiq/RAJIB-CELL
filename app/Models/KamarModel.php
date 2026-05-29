<?php

namespace App\Models;

use CodeIgniter\Model;

class KamarModel extends Model
{
    protected $table            = 'kamar';
    protected $primaryKey       = 'ID_KAMAR';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_KAMAR', 'ID_PERIKSA', 'NOMOR_KAMAR', 'TIPE_KAMAR', 'STATUS'];

    /**
     * Get joined data with patient name through optional Pemeriksaan
     */
    public function getJoinedData()
    {
        return $this->select('kamar.*, pasien.NAMA_PASIEN')
                    ->join('pemeriksaan', 'pemeriksaan.ID_PERIKSA = kamar.ID_PERIKSA', 'left')
                    ->join('pasien', 'pasien.ID_PASIEN = pemeriksaan.ID_PASIEN', 'left')
                    ->findAll();
    }

    /**
     * Generate next Kamar ID in format K001, K002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_KAMAR', 'DESC')->first();

        if (!$lastRow) {
            return 'K001';
        }

        $lastNumber = (int) substr($lastRow['ID_KAMAR'], 1);
        $nextNumber = $lastNumber + 1;

        return 'K' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
