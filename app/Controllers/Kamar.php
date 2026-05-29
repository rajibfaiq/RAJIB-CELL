<?php

namespace App\Controllers;

use App\Models\KamarModel;

class Kamar extends BaseController
{
    public function save()
    {
        $model = new KamarModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_kamar = $this->request->getPost('id_kamar');
        } else {
            $id_kamar = $model->generateNextId();
        }

        $data = [
            'ID_KAMAR'    => $id_kamar,
            'ID_PERIKSA'  => $this->request->getPost('id_periksa') ?: null,
            'NOMOR_KAMAR' => $this->request->getPost('nomor_kamar'),
            'TIPE_KAMAR'  => $this->request->getPost('tipe_kamar'),
            'STATUS'      => $this->request->getPost('status'),
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_kamar, $data);
        } else {
            $success = $model->insert($data);
        }

        if ($success) {
            return redirect()->to('/dashboard?page=kamarpage')->with('success', 'Data Kamar berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new KamarModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=kamarpage')->with('success', 'Data Kamar berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=kamarpage')->with('error', 'Tidak bisa menghapus kamar ini karena masih memiliki data terkait.');
        }
    }
}
