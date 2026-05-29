<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministrasiModel extends Model
{
    protected $table            = 'administrasi';
    protected $primaryKey       = 'ID_ADMINISTRASI';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_ADMINISTRASI', 'NO_PENDAFTARAN', 'BIAYA', 'JENIS_PEMBAYARAN'];

    /**
     * Get joined data with Pasien name through Pendaftaran
     */
    public function getJoinedData()
    {
        return $this->select('administrasi.*, pasien.NAMA_PASIEN')
                    ->join('pendaftaran', 'pendaftaran.NO_PENDAFTARAN = administrasi.NO_PENDAFTARAN')
                    ->join('pasien', 'pasien.ID_PASIEN = pendaftaran.ID_PASIEN')
                    ->findAll();
    }

    /**
     * Generate next Administrasi ID in format ADM001, ADM002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_ADMINISTRASI', 'DESC')->first();

        if (!$lastRow) {
            return 'ADM001';
        }

        $lastNumber = (int) substr($lastRow['ID_ADMINISTRASI'], 3);
        $nextNumber = $lastNumber + 1;

        return 'ADM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
