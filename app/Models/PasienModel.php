<?php

namespace App\Models;

use CodeIgniter\Model;

class PasienModel extends Model
{
    protected $table            = 'pasien';
    protected $primaryKey       = 'ID_PASIEN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'ID_PASIEN', 'NAMA_PASIEN', 'ALAMAT_PASIEN', 'TGL_LAHIR', 'JENIS_KELAMIN',
        'NIK', 'NO_TELP', 'PROVINSI', 'KOTA', 'KECAMATAN', 'KELURAHAN',
        'JENIS_PEMBAYARAN', 'NO_BPJS', 'NAMA_ASURANSI', 'NO_POLIS',
        'KONTAK_DARURAT_NAMA', 'KONTAK_DARURAT_TELP'
    ];

    /**
     * Generate next patient ID dynamically supporting prefixes like RM
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_PASIEN', 'DESC')->first();

        if (!$lastRow) {
            return 'RM001';
        }

        $lastId = $lastRow['ID_PASIEN'];
        
        // Find prefix length (non-numeric chars)
        $prefixLength = 0;
        while ($prefixLength < strlen($lastId) && !is_numeric($lastId[$prefixLength])) {
            $prefixLength++;
        }
        
        $prefix = substr($lastId, 0, $prefixLength);
        $lastNumber = (int) substr($lastId, $prefixLength);
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
