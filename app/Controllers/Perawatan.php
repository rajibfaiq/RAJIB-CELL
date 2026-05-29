<?php

namespace App\Controllers;

use App\Models\PerawatanModel;

class Perawatan extends BaseController
{
    public function save()
    {
        $model = new PerawatanModel();
        $id_perawatan = $model->generateNextId();
        
        $data = [
            'ID_PERAWATAN' => $id_perawatan,
            'ID_KAMAR'     => empty($this->request->getPost('id_kamar')) ? null : $this->request->getPost('id_kamar'),
            'ID_PASIEN'    => $this->request->getPost('id_pasien'),
            'RAWAT_INAP'   => $this->request->getPost('jenis_rawat') == 'inap' ? 1 : 0,
            'RAWAT_JALAN'  => $this->request->getPost('jenis_rawat') == 'jalan' ? 1 : 0,
        ];

        if ($model->save($data)) {
            return redirect()->to('/dashboard?page=rawatjalan')->with('success', 'Data Perawatan berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new PerawatanModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=rawatjalan')->with('success', 'Data Perawatan berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=rawatjalan')->with('error', 'Tidak bisa menghapus data perawatan ini karena masih memiliki data terkait.');
        }
    }
}
