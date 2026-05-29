<?php

namespace App\Models;

use CodeIgniter\Model;

class PerawatModel extends Model
{
    protected $table            = 'perawat';
    protected $primaryKey       = 'ID_PERAWAT';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_PERAWAT', 'NAMA_PERAWAT', 'SPESIALIS_PERAWAT'];

    /**
     * Generate next Perawat ID in format PRW001, PRW002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_PERAWAT', 'DESC')->first();

        if (!$lastRow) {
            return 'PRW001';
        }

        $lastNumber = (int) substr($lastRow['ID_PERAWAT'], 3);
        $nextNumber = $lastNumber + 1;

        return 'PRW' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
