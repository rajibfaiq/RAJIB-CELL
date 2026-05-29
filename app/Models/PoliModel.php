<?php

namespace App\Models;

use CodeIgniter\Model;

class PoliModel extends Model
{
    protected $table            = 'poli';
    protected $primaryKey       = 'ID_POLI';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_POLI', 'NAMA_POLI', 'ICON', 'KETERANGAN'];

    /**
     * Generate next Poli ID in format POL001, POL002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_POLI', 'DESC')->first();

        if (!$lastRow) {
            return 'POL001';
        }

        $lastNumber = (int) substr($lastRow['ID_POLI'], 3);
        $nextNumber = $lastNumber + 1;

        return 'POL' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
