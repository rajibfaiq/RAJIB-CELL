<?php

namespace App\Models;

use CodeIgniter\Model;

class PerawatanModel extends Model
{
    protected $table            = 'perawatan';
    protected $primaryKey       = 'ID_PERAWATAN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_PERAWATAN', 'ID_KAMAR', 'ID_PASIEN', 'RAWAT_INAP', 'RAWAT_JALAN'];

    /**
     * Get joined data with Pasien and Kamar names
     */
    public function getJoinedData()
    {
        return $this->select('perawatan.*, pasien.NAMA_PASIEN, kamar.NOMOR_KAMAR')
                    ->join('pasien', 'pasien.ID_PASIEN = perawatan.ID_PASIEN')
                    ->join('kamar', 'kamar.ID_KAMAR = perawatan.ID_KAMAR', 'left')
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
