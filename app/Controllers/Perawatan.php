<?php

namespace App\Controllers;

use App\Models\PerawatanModel;

class Perawatan extends BaseController
{
    public function save()
    {
        $model = new PerawatanModel();
        
        $is_edit = $this->request->getPost('is_edit');
        if ($is_edit == '1') {
            $id_perawatan = $this->request->getPost('id_perawatan');
            $current = $model->find($id_perawatan);
        } else {
            $id_perawatan = $model->generateNextId();
            $current = null;
        }
        
        $id_kamar = empty($this->request->getPost('id_kamar')) ? null : $this->request->getPost('id_kamar');
        $tgl = $this->request->getPost('tgl_perawatan') ?: date('Y-m-d');
        $data = [
            'ID_PERAWATAN'  => $id_perawatan,
            'ID_KAMAR'      => $id_kamar,
            'ID_PASIEN'     => $this->request->getPost('id_pasien'),
            'TGL_PERAWATAN' => $tgl,
            'RAWAT_INAP'    => $this->request->getPost('jenis_rawat') == 'inap' ? 1 : 0,
            'RAWAT_JALAN'   => $this->request->getPost('jenis_rawat') == 'jalan' ? 1 : 0,
        ];

        if ($is_edit == '1') {
            $success = $model->update($id_perawatan, $data);
            if ($success) {
                $pendaftaranModel = new \App\Models\PendaftaranModel();
                $pendaftaran = $pendaftaranModel->where('ID_PASIEN', $data['ID_PASIEN'])->orderBy('TANGGAL_DAFTAR', 'DESC')->first();
                $noPendaftaran = $pendaftaran ? $pendaftaran['NO_PENDAFTARAN'] : 'REG001';

                $pembayaranModel = new \App\Models\PembayaranModel();
                
                if ($data['RAWAT_INAP'] == 1) {
                    $existingKamar = $pembayaranModel->where('JENIS_LAYANAN', 'kamar')
                                                      ->where('ID_REFERENSI', $id_perawatan)
                                                      ->first();
                    if ($existingKamar) {
                        if ($existingKamar['STATUS'] !== 'lunas') {
                            $pembayaranModel->update($existingKamar['ID_PEMBAYARAN'], [
                                'NO_PENDAFTARAN' => $noPendaftaran,
                            ]);
                        }
                    } else {
                        $pembayaranModel->insert([
                            'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'kamar',
                            'ID_REFERENSI'       => $id_perawatan,
                            'KETERANGAN_LAYANAN' => 'Biaya Rawat Inap (' . $id_perawatan . ')',
                            'BIAYA'              => 250000,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                    $pembayaranModel->where('JENIS_LAYANAN', 'perawatan')
                                     ->where('ID_REFERENSI', $id_perawatan)
                                     ->delete();
                } else {
                    $existingJalan = $pembayaranModel->where('JENIS_LAYANAN', 'perawatan')
                                                      ->where('ID_REFERENSI', $id_perawatan)
                                                      ->first();
                    if ($existingJalan) {
                        if ($existingJalan['STATUS'] !== 'lunas') {
                            $pembayaranModel->update($existingJalan['ID_PEMBAYARAN'], [
                                'NO_PENDAFTARAN' => $noPendaftaran,
                            ]);
                        }
                    } else {
                        $pembayaranModel->insert([
                            'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'perawatan',
                            'ID_REFERENSI'       => $id_perawatan,
                            'KETERANGAN_LAYANAN' => 'Tindakan Rawat Jalan (' . $id_perawatan . ')',
                            'BIAYA'              => 30000,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                    $pembayaranModel->where('JENIS_LAYANAN', 'kamar')
                                     ->where('ID_REFERENSI', $id_perawatan)
                                     ->delete();
                }
            }
        } else {
            $success = $model->insert($data);
            if ($success) {
                $pendaftaranModel = new \App\Models\PendaftaranModel();
                $pendaftaran = $pendaftaranModel->where('ID_PASIEN', $data['ID_PASIEN'])->orderBy('TANGGAL_DAFTAR', 'DESC')->first();
                $noPendaftaran = $pendaftaran ? $pendaftaran['NO_PENDAFTARAN'] : 'REG001';

                $pembayaranModel = new \App\Models\PembayaranModel();
                if ($data['RAWAT_INAP'] == 1) {
                    $pembayaranModel->insert([
                        'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                        'NO_PENDAFTARAN'     => $noPendaftaran,
                        'JENIS_LAYANAN'      => 'kamar',
                        'ID_REFERENSI'       => $id_perawatan,
                        'KETERANGAN_LAYANAN' => 'Biaya Rawat Inap (' . $id_perawatan . ')',
                        'BIAYA'              => 250000,
                        'STATUS'             => 'belum_bayar',
                        'CREATED_AT'         => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $pembayaranModel->insert([
                        'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                        'NO_PENDAFTARAN'     => $noPendaftaran,
                        'JENIS_LAYANAN'      => 'perawatan',
                        'ID_REFERENSI'       => $id_perawatan,
                        'KETERANGAN_LAYANAN' => 'Tindakan Rawat Jalan (' . $id_perawatan . ')',
                        'BIAYA'              => 30000,
                        'STATUS'             => 'belum_bayar',
                        'CREATED_AT'         => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        if ($success) {
            $kamarModel = new \App\Models\KamarModel();
            if ($is_edit == '1' && $current) {
                $old_kamar = $current['ID_KAMAR'];
                if ($old_kamar !== $id_kamar) {
                    if (!empty($old_kamar)) {
                        $kamarModel->update($old_kamar, ['STATUS' => 'Tersedia']);
                    }
                    if (!empty($id_kamar)) {
                        $kamarModel->update($id_kamar, ['STATUS' => 'Terisi']);
                    }
                }
            } else {
                if (!empty($id_kamar)) {
                    $kamarModel->update($id_kamar, ['STATUS' => 'Terisi']);
                }
            }
            $redirectTab = $data['RAWAT_INAP'] == 1 ? 'inap' : 'jalan';
            return redirect()->to('/dashboard?page=rawatjalan&tab=' . $redirectTab)->with('success', 'Data Perawatan berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function delete($id)
    {
        $model = new PerawatanModel();
        try {
            $perawatan = $model->find($id);
            if ($perawatan && !empty($perawatan['ID_KAMAR'])) {
                $kamarModel = new \App\Models\KamarModel();
                $kamarModel->update($perawatan['ID_KAMAR'], ['STATUS' => 'Tersedia']);
            }
            
            $pembayaranModel = new \App\Models\PembayaranModel();
            $pembayaranModel->where('ID_REFERENSI', $id)
                             ->whereIn('JENIS_LAYANAN', ['kamar', 'perawatan'])
                             ->delete();

            $model->delete($id);
            $redirectTab = (isset($perawatan['RAWAT_INAP']) && $perawatan['RAWAT_INAP'] == 1) ? 'inap' : 'jalan';
            return redirect()->to('/dashboard?page=rawatjalan&tab=' . $redirectTab)->with('success', 'Data Perawatan berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=rawatjalan')->with('error', 'Tidak bisa menghapus data perawatan ini karena masih memiliki data terkait.');
        }
    }
}
