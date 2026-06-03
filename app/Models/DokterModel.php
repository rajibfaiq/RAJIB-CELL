<?php

namespace App\Models;

use CodeIgniter\Model;

class DokterModel extends Model
{
    protected $table            = 'dokter';
    protected $primaryKey       = 'ID_DOKTER';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ID_DOKTER', 'NAMA_DOKTER', 'SPESIALIS', 'NO_IZIN_PRAKTEK', 'JADWAL', 'STATUS', 'ID_POLI', 'KUOTA_HARIAN'];

    /**
     * Generate next Dokter ID in format D001, D002, ...
     */
    public function generateNextId(): string
    {
        $lastRow = $this->orderBy('ID_DOKTER', 'DESC')->first();

        if (!$lastRow) {
            return 'D001';
        }

        $lastNumber = (int) substr($lastRow['ID_DOKTER'], 1);
        $nextNumber = $lastNumber + 1;

        return 'D' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get dokter by poli with joined poli name
     */
    public function getByPoli($idPoli)
    {
        return $this->where('ID_POLI', $idPoli)
                    ->where('STATUS', 'Aktif')
                    ->findAll();
    }

    /**
     * Get sisa kuota untuk dokter pada tanggal dan sesi tertentu
     */
    public function getSisaKuota($idDokter, $tanggal = null, $sesi = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        $dokter = $this->find($idDokter);
        if (!$dokter) return 0;

        $kuotaHarian = $dokter['KUOTA_HARIAN'] ?? 20;
        // Quota per session is 1/3 of daily quota, or full daily quota if no session specified
        $quotaLimit = $sesi ? ceil($kuotaHarian / 3) : $kuotaHarian;

        $pendaftaranModel = new PendaftaranModel();
        $query = $pendaftaranModel->where('ID_DOKTER', $idDokter)
                                   ->where('STATUS_ANTRIAN !=', 'batal')
                                   ->where('TANGGAL_KUNJUNGAN', $tanggal);

        if ($sesi) {
            $query->where('SESI_KUNJUNGAN', $sesi);
        }

        $terdaftar = $query->countAllResults();

        return max(0, $quotaLimit - $terdaftar);
    }

    /**
     * Get all dokter with poli name
     */
    public function getWithPoli()
    {
        $today = date('Y-m-d');
        return $this->select("dokter.*, poli.NAMA_POLI, 
                             (SELECT COUNT(*) FROM pendaftaran pd 
                              WHERE pd.ID_DOKTER = dokter.ID_DOKTER 
                                AND pd.TANGGAL_KUNJUNGAN = '{$today}'
                                AND pd.STATUS_ANTRIAN != 'batal') AS TERDAFTAR_HARI_INI")
                    ->join('poli', 'poli.ID_POLI = dokter.ID_POLI', 'left')
                    ->findAll();
    }
}
