<?php

namespace App\Models;

use CodeIgniter\Model;

class RontgenModel extends Model
{
    protected $table            = 'rontgen';
    protected $primaryKey       = 'ID_RONTGEN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'ID_RONTGEN', 'ID_PERIKSA', 'HASIL_RONTGEN', 'KETERANGAN',
        'JENIS_RONTGEN', 'KETERANGAN_KLINIS', 'CATATAN', 'STATUS', 'TGL_PERMINTAAN'
    ];

    /**
     * Get joined data with Pasien name through Pemeriksaan
     */
    public function getJoinedData()
    {
        return $this->select('rontgen.*, pasien.NAMA_PASIEN, dokter.NAMA_DOKTER')
                    ->join('pemeriksaan', 'pemeriksaan.ID_PERIKSA = rontgen.ID_PERIKSA')
                    ->join('pasien', 'pasien.ID_PASIEN = pemeriksaan.ID_PASIEN')
                    ->join('dokter', 'dokter.ID_DOKTER = pemeriksaan.ID_DOKTER', 'left')
                    ->orderBy('rontgen.TGL_PERMINTAAN', 'DESC')
                    ->findAll();
    }

    /**
     * Get permintaan rontgen baru (status = diminta)
     */
    public function getPermintaanBaru()
    {
        return $this->select('rontgen.*, pasien.NAMA_PASIEN, dokter.NAMA_DOKTER')
                    ->join('pemeriksaan', 'pemeriksaan.ID_PERIKSA = rontgen.ID_PERIKSA')
                    ->join('pasien', 'pasien.ID_PASIEN = pemeriksaan.ID_PASIEN')
                    ->join('dokter', 'dokter.ID_DOKTER = pemeriksaan.ID_DOKTER', 'left')
                    ->where('rontgen.STATUS', 'diminta')
                    ->orderBy('rontgen.TGL_PERMINTAAN', 'ASC')
                    ->findAll();
    }

    /**
     * Get rontgen by ID_PERIKSA (for rekam medis view)
     */
    public function getByPeriksa($idPeriksa)
    {
        return $this->where('ID_PERIKSA', $idPeriksa)->findAll();
    }

    /**
     * Generate next Rontgen ID in format RTG001, RTG002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_RONTGEN', 'DESC')->first();

        if (!$lastRow) {
            return 'RTG001';
        }

        $lastNumber = (int) substr($lastRow['ID_RONTGEN'], 3);
        $nextNumber = $lastNumber + 1;

        return 'RTG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
