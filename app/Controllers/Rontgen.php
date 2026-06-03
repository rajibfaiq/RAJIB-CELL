<?php

namespace App\Controllers;

use App\Models\RontgenModel;

class Rontgen extends BaseController
{
    public function save()
    {
        $model = new RontgenModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_rontgen = $this->request->getPost('id_rontgen');
        } else {
            $id_rontgen = $model->generateNextId();
        }

        $data = [
            'ID_RONTGEN'         => $id_rontgen,
            'ID_PERIKSA'         => $this->request->getPost('id_periksa'),
            'JENIS_RONTGEN'      => $this->request->getPost('jenis_rontgen') ?: null,
            'KETERANGAN_KLINIS'  => $this->request->getPost('keterangan_klinis') ?: null,
            'CATATAN'            => $this->request->getPost('catatan') ?: null,
            'HASIL_RONTGEN'      => $this->request->getPost('hasil_rontgen'),
            'KETERANGAN'         => $this->request->getPost('keterangan') ?: null,
            'STATUS'             => $this->request->getPost('status') ?: 'diminta',
        ];

        if ($is_edit != '1') {
            $data['TGL_PERMINTAAN'] = date('Y-m-d H:i:s');
        }

        if ($is_edit == '1') {
            $success = $model->update($id_rontgen, $data);
            if ($success) {
                $pembayaranModel = new \App\Models\PembayaranModel();
                $existingPay = $pembayaranModel->where('JENIS_LAYANAN', 'rontgen')
                                                ->where('ID_REFERENSI', $id_rontgen)
                                                ->first();
                if ($existingPay && $existingPay['STATUS'] !== 'lunas') {
                    $pemeriksaanModel = new \App\Models\PemeriksaanModel();
                    $pemeriksaan = $pemeriksaanModel->find($data['ID_PERIKSA']);
                    $noPendaftaran = $existingPay['NO_PENDAFTARAN'];
                    if ($pemeriksaan) {
                        $pendaftaranModel = new \App\Models\PendaftaranModel();
                        $pendaftaran = $pendaftaranModel->where('ID_PASIEN', $pemeriksaan['ID_PASIEN'])->orderBy('TANGGAL_DAFTAR', 'DESC')->first();
                        if ($pendaftaran) $noPendaftaran = $pendaftaran['NO_PENDAFTARAN'];
                    }
                    $pembayaranModel->update($existingPay['ID_PEMBAYARAN'], [
                        'NO_PENDAFTARAN' => $noPendaftaran,
                        'KETERANGAN_LAYANAN' => 'Pemeriksaan Rontgen: ' . $data['JENIS_RONTGEN'] . ' (' . $id_rontgen . ')',
                    ]);
                }
            }
        } else {
            $success = $model->save($data);
            if ($success) {
                $pemeriksaanModel = new \App\Models\PemeriksaanModel();
                $pemeriksaan = $pemeriksaanModel->find($data['ID_PERIKSA']);
                $noPendaftaran = 'REG001';
                if ($pemeriksaan) {
                    $pendaftaranModel = new \App\Models\PendaftaranModel();
                    $pendaftaran = $pendaftaranModel->where('ID_PASIEN', $pemeriksaan['ID_PASIEN'])->orderBy('TANGGAL_DAFTAR', 'DESC')->first();
                    if ($pendaftaran) $noPendaftaran = $pendaftaran['NO_PENDAFTARAN'];
                }

                $pembayaranModel = new \App\Models\PembayaranModel();
                $pembayaranModel->insert([
                    'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                    'NO_PENDAFTARAN'     => $noPendaftaran,
                    'JENIS_LAYANAN'      => 'rontgen',
                    'ID_REFERENSI'       => $id_rontgen,
                    'KETERANGAN_LAYANAN' => 'Pemeriksaan Rontgen: ' . $data['JENIS_RONTGEN'] . ' (' . $id_rontgen . ')',
                    'BIAYA'              => 150000,
                    'STATUS'             => 'belum_bayar',
                    'CREATED_AT'         => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($success) {
            return redirect()->to('/dashboard?page=rontgen')->with('success', 'Data Rontgen berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    /**
     * Radiologi uploads/fills in the result for a rontgen request
     */
    public function uploadHasil($idRontgen)
    {
        $model = new RontgenModel();
        $rontgen = $model->find($idRontgen);

        if (!$rontgen) {
            return redirect()->to('/dashboard?page=rontgen')->with('error', 'Data rontgen tidak ditemukan');
        }

        $model->update($idRontgen, [
            'HASIL_RONTGEN' => $this->request->getPost('hasil_rontgen'),
            'KETERANGAN'    => $this->request->getPost('keterangan') ?: null,
            'STATUS'        => 'selesai',
        ]);

        return redirect()->to('/dashboard?page=rontgen')->with('success', 'Hasil rontgen berhasil disimpan');
    }

    public function delete($id)
    {
        $model = new RontgenModel();
        try {
            $pembayaranModel = new \App\Models\PembayaranModel();
            $pembayaranModel->where('JENIS_LAYANAN', 'rontgen')
                             ->where('ID_REFERENSI', $id)
                             ->delete();

            $model->delete($id);
            return redirect()->to('/dashboard?page=rontgen')->with('success', 'Data Rontgen berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=rontgen')->with('error', 'Tidak bisa menghapus rontgen ini karena masih memiliki data terkait.');
        }
    }
}
