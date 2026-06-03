<?php

namespace App\Models;

use CodeIgniter\Model;

class FarmasiModel extends Model
{
    protected $table            = 'farmasi';
    protected $primaryKey       = 'ID_FARMASI';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_FARMASI', 'NAMA_OBAT', 'ID_PENGOBATAN', 'JENIS_OBAT', 'HARGA_OBAT'];

    /**
     * Get joined data with drug name
     */
    public function getJoinedData()
    {
        return $this->orderBy('ID_FARMASI', 'ASC')->findAll();
    }

    /**
     * Generate next Farmasi ID in format F001, F002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_FARMASI', 'DESC')->first();

        if (!$lastRow) {
            return 'F001';
        }

        $lastNumber = (int) substr($lastRow['ID_FARMASI'], 1);
        $nextNumber = $lastNumber + 1;

        return 'F' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
