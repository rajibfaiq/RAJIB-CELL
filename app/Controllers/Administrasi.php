<?php

namespace App\Controllers;

use App\Models\AdministrasiModel;

class Administrasi extends BaseController
{
    public function save()
    {
        $model = new AdministrasiModel();
        $id_admin = $model->generateNextId();
        
        $data = [
            'ID_ADMINISTRASI'  => $id_admin,
            'NO_PENDAFTARAN'   => $this->request->getPost('no_daftar'),
            'BIAYA'            => $this->request->getPost('biaya'),
            'JENIS_PEMBAYARAN' => $this->request->getPost('jenis_bayar'),
        ];

        if ($model->save($data)) {
            return redirect()->to('/dashboard?page=billing')->with('success', 'Data Administrasi berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new AdministrasiModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=billing')->with('success', 'Data Administrasi berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=billing')->with('error', 'Tidak bisa menghapus data administrasi ini karena masih memiliki data terkait.');
        }
    }
}
