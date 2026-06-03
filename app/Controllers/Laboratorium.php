<?php

namespace App\Controllers;

use App\Models\LaboratoriumModel;

class Laboratorium extends BaseController
{
    public function save()
    {
        $model = new LaboratoriumModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_lab = $this->request->getPost('id_lab');
        } else {
            $id_lab = $model->generateNextId();
        }
        
        $data = [
            'ID_LABORATORIUM'   => $id_lab,
            'ID_PERIKSA'        => $this->request->getPost('id_periksa'),
            'JENIS_PEMERIKSAAN' => $this->request->getPost('jenis_periksa'),
            'HASIL_LAB'         => $this->request->getPost('hasil_lab'),
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_lab, $data);
        } else {
            $success = $model->save($data);
        }

        if ($success) {
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
