<?php

namespace App\Controllers;

use App\Models\PengobatanModel;

class Pengobatan extends BaseController
{
    public function save()
    {
        $model = new PengobatanModel();
        $id_pengobatan = $model->generateNextId();

        $data = [
            'ID_PENGOBATAN'  => $id_pengobatan,
            'ID_PERIKSA'     => $this->request->getPost('id_periksa'),
            'NAMA_OBAT'      => $this->request->getPost('nama_obat'),
            'DOSIS_OBAT'     => $this->request->getPost('dosis_obat'),
            'HARGA_OBAT'     => $this->request->getPost('harga_obat') ?: 0,
        ];

        if ($model->save($data)) {
            return redirect()->to('/dashboard?page=pengobatan')->with('success', 'Data Pengobatan berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new PengobatanModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=pengobatan')->with('success', 'Data Pengobatan berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=pengobatan')->with('error', 'Tidak bisa menghapus pengobatan ini karena masih memiliki data terkait.');
        }
    }
}
