<?php

namespace App\Controllers;

use App\Models\FarmasiModel;

class Farmasi extends BaseController
{
    public function save()
    {
        $model = new FarmasiModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_farmasi = $this->request->getPost('id_farmasi');
        } else {
            $id_farmasi = $model->generateNextId();
        }
        
        $data = [
            'ID_FARMASI' => $id_farmasi,
            'NAMA_OBAT'  => $this->request->getPost('nama_obat'),
            'JENIS_OBAT' => $this->request->getPost('jenis_obat'),
            'HARGA_OBAT' => $this->request->getPost('harga_obat'),
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_farmasi, $data);
        } else {
            $success = $model->save($data);
        }

        if ($success) {
            return redirect()->to('/dashboard?page=farmasi')->with('success', 'Data Farmasi berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new FarmasiModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=farmasi')->with('success', 'Data Farmasi berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=farmasi')->with('error', 'Tidak bisa menghapus data farmasi ini karena masih memiliki data terkait.');
        }
    }
}
