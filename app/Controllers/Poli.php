<?php

namespace App\Controllers;

use App\Models\PoliModel;
use App\Models\DokterModel;
use App\Models\PendaftaranModel;

class Poli extends BaseController
{
    /**
     * API: Get dokter by poli ID (returns JSON)
     */
    public function getDokterByPoli($idPoli)
    {
        $dokterModel = new DokterModel();
        $dokterList = $dokterModel->getByPoli($idPoli);

        // Enrich with sisa kuota dynamically based on selected date & session
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $sesi = $this->request->getGet('sesi') ?: 'Pagi';

        foreach ($dokterList as &$d) {
            $d['SISA_KUOTA'] = $dokterModel->getSisaKuota($d['ID_DOKTER'], $tanggal, $sesi);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $dokterList
        ]);
    }
}
