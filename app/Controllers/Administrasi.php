<?php

namespace App\Controllers;

use App\Models\AdministrasiModel;

class Administrasi extends BaseController
{
    public function save()
    {
        $model = new AdministrasiModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_admin = $this->request->getPost('id_admin');
        } else {
            $id_admin = $model->generateNextId();
        }
        
        $data = [
            'ID_ADMINISTRASI'  => $id_admin,
            'NO_PENDAFTARAN'   => $this->request->getPost('no_daftar'),
            'BIAYA'            => $this->request->getPost('biaya'),
            'JENIS_PEMBAYARAN' => $this->request->getPost('jenis_bayar'),
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_admin, $data);
            if ($success) {
                $pembayaranModel = new \App\Models\PembayaranModel();
                $existingPay = $pembayaranModel->where('JENIS_LAYANAN', 'administrasi')
                                                ->where('ID_REFERENSI', $id_admin)
                                                ->first();
                if ($existingPay && $existingPay['STATUS'] !== 'lunas') {
                    $pembayaranModel->update($existingPay['ID_PEMBAYARAN'], [
                        'NO_PENDAFTARAN'   => $this->request->getPost('no_daftar'),
                        'BIAYA'            => $this->request->getPost('biaya'),
                        'JENIS_PEMBAYARAN' => $this->request->getPost('jenis_bayar') ?: 'Tunai',
                    ]);
                }
            }
        } else {
            $success = $model->save($data);
            if ($success) {
                $pembayaranModel = new \App\Models\PembayaranModel();
                $pembayaranModel->insert([
                    'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                    'NO_PENDAFTARAN'     => $this->request->getPost('no_daftar'),
                    'JENIS_LAYANAN'      => 'administrasi',
                    'ID_REFERENSI'       => $id_admin,
                    'KETERANGAN_LAYANAN' => 'Biaya Administrasi (' . $id_admin . ')',
                    'BIAYA'              => $this->request->getPost('biaya'),
                    'JENIS_PEMBAYARAN'   => $this->request->getPost('jenis_bayar') ?: 'Tunai',
                    'STATUS'             => 'belum_bayar',
                    'CREATED_AT'         => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($success) {
            return redirect()->to('/dashboard?page=billing')->with('success', 'Data Administrasi berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new AdministrasiModel();
        try {
            $pembayaranModel = new \App\Models\PembayaranModel();
            $existingPay = $pembayaranModel->where('JENIS_LAYANAN', 'administrasi')
                                            ->where('ID_REFERENSI', $id)
                                            ->first();
            if ($existingPay && $existingPay['STATUS'] === 'lunas') {
                return redirect()->to('/dashboard?page=billing')->with('error', 'Tidak bisa menghapus data administrasi ini karena sudah dibayar.');
            }

            if ($existingPay) {
                $pembayaranModel->delete($existingPay['ID_PEMBAYARAN']);
            }

            $model->delete($id);
            return redirect()->to('/dashboard?page=billing')->with('success', 'Data Administrasi berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=billing')->with('error', 'Tidak bisa menghapus data administrasi ini karena masih memiliki data terkait.');
        }
    }
}
