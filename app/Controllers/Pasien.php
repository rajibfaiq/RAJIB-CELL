<?php

namespace App\Controllers;

use App\Models\PasienModel;

class Pasien extends BaseController
{
    public function save()
    {
        $model = new PasienModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_pasien = $this->request->getPost('id_pasien');
        } else {
            $id_pasien = $model->generateNextId();
        }
        
        $data = [
            'ID_PASIEN'     => $id_pasien,
            'NAMA_PASIEN'   => $this->request->getPost('nama_pasien'),
            'ALAMAT_PASIEN' => $this->request->getPost('alamat_pasien'),
            'TGL_LAHIR'     => $this->request->getPost('tgl_lahir'),
            'JENIS_KELAMIN' => $this->request->getPost('jenis_kelamin'),
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_pasien, $data);
        } else {
            $success = $model->insert($data);
        }

        if ($success) {
            return redirect()->to('/dashboard?page=pasien')->with('success', 'Data Pasien berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new PasienModel();
        
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=pasien')->with('success', 'Data Pasien berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=pasien')->with('error', 'Tidak bisa menghapus pasien ini karena masih memiliki data terkait (pendaftaran/pemeriksaan/perawatan). Hapus data terkait terlebih dahulu.');
        }
    }
}
