<?php

namespace App\Controllers;

use App\Models\RontgenModel;

class Rontgen extends BaseController
{
    public function save()
    {
        $model = new RontgenModel();
        $id_rontgen = $model->generateNextId();

        $data = [
            'ID_RONTGEN'         => $id_rontgen,
            'ID_PERIKSA'         => $this->request->getPost('id_periksa'),
            'JENIS_RONTGEN'      => $this->request->getPost('jenis_rontgen') ?: null,
            'KETERANGAN_KLINIS'  => $this->request->getPost('keterangan_klinis') ?: null,
            'CATATAN'            => $this->request->getPost('catatan') ?: null,
            'HASIL_RONTGEN'      => $this->request->getPost('hasil_rontgen'),
            'KETERANGAN'         => $this->request->getPost('keterangan') ?: null,
            'STATUS'             => $this->request->getPost('status') ?: 'diminta',
            'TGL_PERMINTAAN'     => date('Y-m-d H:i:s'),
        ];

        if ($model->save($data)) {
            return redirect()->to('/dashboard?page=rontgen')->with('success', 'Data Rontgen berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    /**
     * Radiologi uploads/fills in the result for a rontgen request
     */
    public function uploadHasil($idRontgen)
    {
        $model = new RontgenModel();
        $rontgen = $model->find($idRontgen);

        if (!$rontgen) {
            return redirect()->to('/dashboard?page=rontgen')->with('error', 'Data rontgen tidak ditemukan');
        }

        $model->update($idRontgen, [
            'HASIL_RONTGEN' => $this->request->getPost('hasil_rontgen'),
            'KETERANGAN'    => $this->request->getPost('keterangan') ?: null,
            'STATUS'        => 'selesai',
        ]);

        return redirect()->to('/dashboard?page=rontgen')->with('success', 'Hasil rontgen berhasil disimpan');
    }

    public function delete($id)
    {
        $model = new RontgenModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=rontgen')->with('success', 'Data Rontgen berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=rontgen')->with('error', 'Tidak bisa menghapus rontgen ini karena masih memiliki data terkait.');
        }
    }
}
