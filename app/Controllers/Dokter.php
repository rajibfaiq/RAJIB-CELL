<?php

namespace App\Controllers;

use App\Models\DokterModel;

class Dokter extends BaseController
{
    public function save()
    {
        $model = new DokterModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_dokter = $this->request->getPost('id_dokter');
        } else {
            $id_dokter = $model->generateNextId();
        }
        
        $data = [
            'ID_DOKTER'       => $id_dokter,
            'NAMA_DOKTER'     => $this->request->getPost('nama_dokter'),
            'SPESIALIS'       => '-',
            'NO_IZIN_PRAKTEK' => $this->request->getPost('no_izin_praktek'),
            'JADWAL'          => $this->request->getPost('jadwal') ?: '-',
            'STATUS'          => $this->request->getPost('status') ?: 'Aktif',
            'ID_POLI'         => $this->request->getPost('id_poli') ?: null,
            'KUOTA_HARIAN'    => $this->request->getPost('kuota_harian') ?: 20,
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_dokter, $data);
        } else {
            $success = $model->insert($data);
        }

        if ($success) {
            return redirect()->to('/dashboard?page=dokter')->with('success', 'Data Dokter berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new DokterModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=dokter')->with('success', 'Data Dokter berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=dokter')->with('error', 'Tidak bisa menghapus dokter ini karena masih memiliki data terkait. Hapus data terkait terlebih dahulu.');
        }
    }
}
