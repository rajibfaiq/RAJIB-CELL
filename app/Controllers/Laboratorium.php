<?php

namespace App\Controllers;

use App\Models\LaboratoriumModel;

class Laboratorium extends BaseController
{
    public function save()
    {
        $model = new LaboratoriumModel();
        $id_lab = $model->generateNextId();
        
        $data = [
            'ID_LABORATORIUM'   => $id_lab,
            'ID_PERIKSA'        => $this->request->getPost('id_periksa'),
            'JENIS_PEMERIKSAAN' => $this->request->getPost('jenis_periksa'),
            'HASIL_LAB'         => $this->request->getPost('hasil_lab'),
        ];

        if ($model->save($data)) {
            return redirect()->to('/dashboard?page=laboratorium')->with('success', 'Data Lab berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data lab');
        }
    }

    public function delete($id)
    {
        $model = new LaboratoriumModel();
        $model->delete($id);
        return redirect()->to('/dashboard?page=laboratorium')->with('success', 'Data Lab berhasil dihapus');
    }
}
