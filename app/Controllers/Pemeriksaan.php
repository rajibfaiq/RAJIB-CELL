<?php

namespace App\Controllers;

use App\Models\PemeriksaanModel;
use App\Models\RontgenModel;

class Pemeriksaan extends BaseController
{
    public function save()
    {
        $model = new PemeriksaanModel();
        $id_periksa = $model->generateNextId();
        
        $data = [
            'ID_PERIKSA'   => $id_periksa,
            'ID_DOKTER'    => $this->request->getPost('id_dokter'),
            'ID_PASIEN'    => $this->request->getPost('id_pasien'),
            'TGL_PERIKSA'  => date('Y-m-d H:i:s'),
            'DIAGNOSA'     => $this->request->getPost('diagnosa'),
        ];

        if ($model->save($data)) {
            return redirect()->to('/dashboard?page=rekammedis')->with('success', 'Rekam Medis berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan rekam medis');
        }
    }

    /**
     * Dokter creates rontgen referral from pemeriksaan
     */
    public function rujukRontgen()
    {
        $rontgenModel = new RontgenModel();
        $idRontgen = $rontgenModel->generateNextId();

        $data = [
            'ID_RONTGEN'         => $idRontgen,
            'ID_PERIKSA'         => $this->request->getPost('id_periksa'),
            'JENIS_RONTGEN'      => $this->request->getPost('jenis_rontgen'),
            'KETERANGAN_KLINIS'  => $this->request->getPost('keterangan_klinis'),
            'CATATAN'            => $this->request->getPost('catatan') ?: null,
            'STATUS'             => 'diminta',
            'TGL_PERMINTAAN'     => date('Y-m-d H:i:s'),
        ];

        if ($rontgenModel->insert($data)) {
            return redirect()->to('/dashboard?page=rekammedis')->with('success', 'Rujukan rontgen berhasil dibuat (ID: ' . $idRontgen . ')');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat rujukan rontgen');
        }
    }

    public function delete($id)
    {
        $model = new PemeriksaanModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=rekammedis')->with('success', 'Rekam Medis berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=rekammedis')->with('error', 'Tidak bisa menghapus rekam medis ini karena masih memiliki data terkait (lab/rontgen/pengobatan).');
        }
    }
}
