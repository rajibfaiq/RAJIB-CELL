<?php

namespace App\Controllers;

use App\Models\FarmasiModel;

class Farmasi extends BaseController
{
    public function save()
    {
        $model = new FarmasiModel();
        $id_farmasi = $model->generateNextId();
        
        $data = [
            'ID_FARMASI' => $id_farmasi,
            'JENIS_OBAT' => $this->request->getPost('jenis_obat'),
            'HARGA_OBAT' => $this->request->getPost('harga_obat'),
        ];

        if ($model->save($data)) {
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
