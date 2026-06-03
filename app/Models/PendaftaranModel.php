<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftaranModel extends Model
{
    protected $table            = 'pendaftaran';
    protected $primaryKey       = 'NO_PENDAFTARAN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'NO_PENDAFTARAN', 'ID_ADMINISTRASI', 'ID_PASIEN',
        'TANGGAL_DAFTAR', 'JAM_PENDAFTARAN',
        'ID_POLI', 'ID_DOKTER', 'NO_ANTRIAN', 'STATUS_ANTRIAN',
        'TANGGAL_KUNJUNGAN', 'SESI_KUNJUNGAN'
    ];

    /**
     * Get joined data with Pasien, Dokter, Poli names
     */
    public function getJoinedData()
    {
        return $this->select('pendaftaran.*, pasien.NAMA_PASIEN, pasien.JENIS_PEMBAYARAN, dokter.NAMA_DOKTER, poli.NAMA_POLI,
                             pembayaran.STATUS as STATUS_PEMBAYARAN, pembayaran.BIAYA as BIAYA_PEMBAYARAN, pembayaran.ID_PEMBAYARAN')
                    ->join('pasien', 'pasien.ID_PASIEN = pendaftaran.ID_PASIEN')
                    ->join('dokter', 'dokter.ID_DOKTER = pendaftaran.ID_DOKTER', 'left')
                    ->join('poli', 'poli.ID_POLI = pendaftaran.ID_POLI', 'left')
                    ->join('pembayaran', "pembayaran.JENIS_LAYANAN = 'pendaftaran' AND pembayaran.ID_REFERENSI = pendaftaran.NO_PENDAFTARAN", 'left')
                    ->orderBy('pendaftaran.TANGGAL_KUNJUNGAN', 'DESC')
                    ->orderBy('pendaftaran.NO_ANTRIAN', 'ASC')
                    ->findAll();
    }

    /**
     * Generate next Pendaftaran ID in format REG001, REG002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('NO_PENDAFTARAN', 'DESC')->first();

        if (!$lastRow) {
            return 'REG001';
        }

        $lastNumber = (int) substr($lastRow['NO_PENDAFTARAN'], 3);
        $nextNumber = $lastNumber + 1;

        return 'REG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get next antrian number for a dokter on a specific date
     */
    public function getNextAntrian($idDokter, $tanggal = null): int
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        $lastAntrian = $this->where('ID_DOKTER', $idDokter)
                            ->where('TANGGAL_KUNJUNGAN', $tanggal)
                            ->orderBy('NO_ANTRIAN', 'DESC')
                            ->first();

        if (!$lastAntrian || !$lastAntrian['NO_ANTRIAN']) {
            return 1;
        }

        return (int) $lastAntrian['NO_ANTRIAN'] + 1;
    }

    /**
     * Get antrian for a specific dokter today
     */
    public function getAntrianDokter($idDokter, $tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        return $this->select('pendaftaran.*, pasien.NAMA_PASIEN, pasien.TGL_LAHIR, pasien.JENIS_KELAMIN')
                    ->join('pasien', 'pasien.ID_PASIEN = pendaftaran.ID_PASIEN')
                    ->where('pendaftaran.ID_DOKTER', $idDokter)
                    ->where('pendaftaran.TANGGAL_KUNJUNGAN', $tanggal)
                    ->orderBy('pendaftaran.NO_ANTRIAN', 'ASC')
                    ->findAll();
    }
}
