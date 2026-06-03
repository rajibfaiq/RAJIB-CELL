<?php

namespace App\Controllers;

use App\Models\PemeriksaanModel;
use App\Models\RontgenModel;

class Pemeriksaan extends BaseController
{
    public function save()
    {
        $model = new PemeriksaanModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_periksa = $this->request->getPost('id_periksa');
        } else {
            $id_periksa = $model->generateNextId();
        }
        
        $data = [
            'ID_PERIKSA'   => $id_periksa,
            'ID_DOKTER'    => $this->request->getPost('id_dokter'),
            'ID_PASIEN'    => $this->request->getPost('id_pasien'),
            'DIAGNOSA'     => $this->request->getPost('diagnosa'),
        ];

        if ($is_edit != '1') {
            $data['TGL_PERIKSA'] = date('Y-m-d H:i:s');
        }

        if ($is_edit == '1') {
            $success = $model->update($id_periksa, $data);
            if ($success) {
                $pembayaranModel = new \App\Models\PembayaranModel();
                $existingPay = $pembayaranModel->where('JENIS_LAYANAN', 'pemeriksaan')
                                                ->where('ID_REFERENSI', $id_periksa)
                                                ->first();
                if ($existingPay && $existingPay['STATUS'] !== 'lunas') {
                    $pendaftaranModel = new \App\Models\PendaftaranModel();
                    $pendaftaran = $pendaftaranModel->where('ID_PASIEN', $data['ID_PASIEN'])->orderBy('TANGGAL_DAFTAR', 'DESC')->first();
                    $noPendaftaran = $pendaftaran ? $pendaftaran['NO_PENDAFTARAN'] : $existingPay['NO_PENDAFTARAN'];
                    $pembayaranModel->update($existingPay['ID_PEMBAYARAN'], [
                        'NO_PENDAFTARAN' => $noPendaftaran,
                    ]);
                }
            }
        } else {
            $success = $model->save($data);
            if ($success) {
                $pendaftaranModel = new \App\Models\PendaftaranModel();
                $pendaftaran = $pendaftaranModel->where('ID_PASIEN', $data['ID_PASIEN'])->orderBy('TANGGAL_DAFTAR', 'DESC')->first();
                $noPendaftaran = $pendaftaran ? $pendaftaran['NO_PENDAFTARAN'] : 'REG001';

                $pembayaranModel = new \App\Models\PembayaranModel();
                $pembayaranModel->insert([
                    'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                    'NO_PENDAFTARAN'     => $noPendaftaran,
                    'JENIS_LAYANAN'      => 'pemeriksaan',
                    'ID_REFERENSI'       => $id_periksa,
                    'KETERANGAN_LAYANAN' => 'Konsultasi Dokter & Pemeriksaan Fisik (' . $id_periksa . ')',
                    'BIAYA'              => 50000,
                    'STATUS'             => 'belum_bayar',
                    'CREATED_AT'         => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($success) {
            return redirect()->to('/dashboard?page=rekammedis')->with('success', 'Rekam Medis berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan rekam medis');
        }
    }

    /**
     * Dokter creates rontgen referral from pemeriksaan
     */
    public function rujukRontgen()
    {
        $rontgenModel = new RontgenModel();
        $idRontgen = $rontgenModel->generateNextId();

        $data = [
            'ID_RONTGEN'         => $idRontgen,
            'ID_PERIKSA'         => $this->request->getPost('id_periksa'),
            'JENIS_RONTGEN'      => $this->request->getPost('jenis_rontgen'),
            'KETERANGAN_KLINIS'  => $this->request->getPost('keterangan_klinis'),
            'CATATAN'            => $this->request->getPost('catatan') ?: null,
            'STATUS'             => 'diminta',
            'TGL_PERMINTAAN'     => date('Y-m-d H:i:s'),
        ];

        if ($rontgenModel->insert($data)) {
            // Also auto-create a pembayaran record for Rontgen referral (fee Rp 150.000)
            $pemeriksaanModel = new PemeriksaanModel();
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
                'ID_REFERENSI'       => $idRontgen,
                'KETERANGAN_LAYANAN' => 'Pemeriksaan Rontgen: ' . $data['JENIS_RONTGEN'] . ' (' . $idRontgen . ')',
                'BIAYA'              => 150000,
                'STATUS'             => 'belum_bayar',
                'CREATED_AT'         => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/dashboard?page=rekammedis')->with('success', 'Rujukan rontgen berhasil dibuat (ID: ' . $idRontgen . ')');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat rujukan rontgen');
        }
    }

    public function delete($id)
    {
        $model = new PemeriksaanModel();
        try {
            $pembayaranModel = new \App\Models\PembayaranModel();
            $pembayaranModel->where('JENIS_LAYANAN', 'pemeriksaan')
                             ->where('ID_REFERENSI', $id)
                             ->delete();

            $model->delete($id);
            return redirect()->to('/dashboard?page=rekammedis')->with('success', 'Rekam Medis berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=rekammedis')->with('error', 'Tidak bisa menghapus rekam medis ini karena masih memiliki data terkait (lab/rontgen/pengobatan).');
        }
    }
}
