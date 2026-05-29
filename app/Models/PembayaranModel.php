<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'ID_PEMBAYARAN';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'ID_PEMBAYARAN', 'NO_PENDAFTARAN', 'JENIS_LAYANAN', 'ID_REFERENSI',
        'KETERANGAN_LAYANAN', 'BIAYA', 'JENIS_PEMBAYARAN', 'STATUS',
        'NO_KUITANSI', 'TGL_BAYAR', 'CREATED_AT'
    ];

    /**
     * Get all pembayaran joined with pendaftaran & pasien
     */
    public function getJoinedData()
    {
        return $this->select('pembayaran.*, pasien.NAMA_PASIEN')
                    ->join('pendaftaran', 'pendaftaran.NO_PENDAFTARAN = pembayaran.NO_PENDAFTARAN', 'left')
                    ->join('pasien', 'pasien.ID_PASIEN = pendaftaran.ID_PASIEN', 'left')
                    ->orderBy('pembayaran.CREATED_AT', 'DESC')
                    ->findAll();
    }

    /**
     * Get pembayaran by NO_PENDAFTARAN (riwayat satu kunjungan)
     */
    public function getByPendaftaran($noPendaftaran)
    {
        return $this->where('NO_PENDAFTARAN', $noPendaftaran)
                    ->orderBy('CREATED_AT', 'ASC')
                    ->findAll();
    }

    /**
     * Generate next Pembayaran ID in format PAY001, PAY002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_PEMBAYARAN', 'DESC')->first();

        if (!$lastRow) {
            return 'PAY001';
        }

        $lastNumber = (int) substr($lastRow['ID_PEMBAYARAN'], 3);
        $nextNumber = $lastNumber + 1;

        return 'PAY' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique kuitansi number: KWT-YYYYMMDD-XXX
     */
    public function generateKuitansi(): string
    {
        $today = date('Ymd');
        $prefix = 'KWT-' . $today . '-';

        $lastRow = $this->like('NO_KUITANSI', $prefix, 'after')
                        ->orderBy('NO_KUITANSI', 'DESC')
                        ->first();

        if (!$lastRow) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastRow['NO_KUITANSI'], -3);
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
