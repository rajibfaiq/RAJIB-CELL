<?php

namespace App\Controllers;

use App\Models\PengobatanModel;

class Pengobatan extends BaseController
{
    public function save()
    {
        $model = new PengobatanModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_pengobatan = $this->request->getPost('id_pengobatan');
        } else {
            $id_pengobatan = $model->generateNextId();
        }

        $data = [
            'ID_PENGOBATAN'  => $id_pengobatan,
            'ID_PERIKSA'     => $this->request->getPost('id_periksa'),
            'ID_FARMASI'     => $this->request->getPost('id_farmasi') ?: null,
            'NAMA_OBAT'      => $this->request->getPost('nama_obat'),
            'DOSIS_OBAT'     => $this->request->getPost('dosis_obat'),
            'HARGA_OBAT'     => $this->request->getPost('harga_obat') ?: 0,
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_pengobatan, $data);
            if ($success) {
                $pembayaranModel = new \App\Models\PembayaranModel();
                $existingPay = $pembayaranModel->where('JENIS_LAYANAN', 'farmasi')
                                                ->where('ID_REFERENSI', $id_pengobatan)
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
                        'NO_PENDAFTARAN'     => $noPendaftaran,
                        'KETERANGAN_LAYANAN' => 'Obat Resep: ' . $data['NAMA_OBAT'] . ' (' . $data['DOSIS_OBAT'] . ')',
                        'BIAYA'              => $data['HARGA_OBAT'] ?: 0,
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
                    'JENIS_LAYANAN'      => 'farmasi',
                    'ID_REFERENSI'       => $id_pengobatan,
                    'KETERANGAN_LAYANAN' => 'Obat Resep: ' . $data['NAMA_OBAT'] . ' (' . $data['DOSIS_OBAT'] . ')',
                    'BIAYA'              => $data['HARGA_OBAT'] ?: 0,
                    'STATUS'             => 'belum_bayar',
                    'CREATED_AT'         => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($success) {
            return redirect()->to('/dashboard?page=pengobatan')->with('success', 'Data Pengobatan berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new PengobatanModel();
        try {
            $pembayaranModel = new \App\Models\PembayaranModel();
            $pembayaranModel->where('JENIS_LAYANAN', 'farmasi')
                             ->where('ID_REFERENSI', $id)
                             ->delete();

            $model->delete($id);
            return redirect()->to('/dashboard?page=pengobatan')->with('success', 'Data Pengobatan berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=pengobatan')->with('error', 'Tidak bisa menghapus pengobatan ini karena masih memiliki data terkait.');
        }
    }
}
