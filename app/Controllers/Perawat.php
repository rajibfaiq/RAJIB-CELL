<?php

namespace App\Controllers;

use App\Models\PerawatModel;

class Perawat extends BaseController
{
    public function save()
    {
        $model = new PerawatModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_perawat = $this->request->getPost('id_perawat');
        } else {
            $id_perawat = $model->generateNextId();
        }
        
        $data = [
            'ID_PERAWAT'        => $id_perawat,
            'NAMA_PERAWAT'      => $this->request->getPost('nama_perawat'),
            'SPESIALIS_PERAWAT' => $this->request->getPost('spesialis_perawat') ?: 'Umum',
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_perawat, $data);
        } else {
            $success = $model->insert($data);
        }

        if ($success) {
            return redirect()->to('/dashboard?page=perawat')->with('success', 'Data Perawat berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new PerawatModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=perawat')->with('success', 'Data Perawat berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=perawat')->with('error', 'Tidak bisa menghapus perawat ini karena masih memiliki data terkait.');
        }
    }
}
