<?php

namespace App\Models;

use CodeIgniter\Model;

class LaboratoriumModel extends Model
{
    protected $table            = 'laboratorium';
    protected $primaryKey       = 'ID_LABORATORIUM';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_LABORATORIUM', 'ID_PERIKSA', 'JENIS_PEMERIKSAAN', 'HASIL_LAB'];

    /**
     * Get joined data with Pasien name through Pemeriksaan
     */
    public function getJoinedData()
    {
        return $this->select('laboratorium.*, pasien.NAMA_PASIEN')
                    ->join('pemeriksaan', 'pemeriksaan.ID_PERIKSA = laboratorium.ID_PERIKSA')
                    ->join('pasien', 'pasien.ID_PASIEN = pemeriksaan.ID_PASIEN')
                    ->findAll();
    }

    /**
     * Generate next Laboratorium ID in format LAB001, LAB002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_LABORATORIUM', 'DESC')->first();

        if (!$lastRow) {
            return 'LAB001';
        }

        $lastNumber = (int) substr($lastRow['ID_LABORATORIUM'], 3);
        $nextNumber = $lastNumber + 1;

        return 'LAB' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
