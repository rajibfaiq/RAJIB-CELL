<?php

namespace App\Controllers;

use App\Models\PembayaranModel;

class Pembayaran extends BaseController
{
    /**
     * Save new pembayaran record (kasir creates per-service transaction)
     */
    public function save()
    {
        $model = new PembayaranModel();
        $id_pembayaran = $model->generateNextId();

        $data = [
            'ID_PEMBAYARAN'      => $id_pembayaran,
            'NO_PENDAFTARAN'     => $this->request->getPost('no_pendaftaran'),
            'JENIS_LAYANAN'      => $this->request->getPost('jenis_layanan'),
            'ID_REFERENSI'       => $this->request->getPost('id_referensi') ?: null,
            'KETERANGAN_LAYANAN' => $this->request->getPost('keterangan_layanan') ?: null,
            'BIAYA'              => $this->request->getPost('biaya'),
            'JENIS_PEMBAYARAN'   => $this->request->getPost('jenis_pembayaran') ?: 'Tunai',
            'STATUS'             => 'belum_bayar',
            'CREATED_AT'         => date('Y-m-d H:i:s'),
        ];

        if ($model->insert($data)) {
            return redirect()->to('/dashboard?page=pembayaran')->with('success', 'Tagihan berhasil dibuat');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat tagihan');
        }
    }

    /**
     * Mark pembayaran as paid (lunas)
     */
    public function bayar($idPembayaran)
    {
        $model = new PembayaranModel();
        $pembayaran = $model->find($idPembayaran);

        if (!$pembayaran) {
            return redirect()->to('/dashboard?page=pembayaran')->with('error', 'Data pembayaran tidak ditemukan');
        }

        $noKuitansi = $model->generateKuitansi();
        $jenisBayar = $this->request->getPost('jenis_pembayaran') ?: $pembayaran['JENIS_PEMBAYARAN'];

        $model->update($idPembayaran, [
            'STATUS'           => 'lunas',
            'NO_KUITANSI'      => $noKuitansi,
            'TGL_BAYAR'        => date('Y-m-d H:i:s'),
            'JENIS_PEMBAYARAN' => $jenisBayar,
        ]);

        return redirect()->to('/dashboard?page=pembayaran')->with('success', 'Pembayaran berhasil! No. Kuitansi: ' . $noKuitansi);
    }

    /**
     * Print receipt for a payment transaction
     */
    public function cetakKuitansi($idPembayaran)
    {
        $model = new PembayaranModel();
        
        // Custom join to get patient details
        $pembayaran = $model->select('pembayaran.*, pasien.NAMA_PASIEN, pasien.ID_PASIEN, pendaftaran.TANGGAL_DAFTAR')
                            ->join('pendaftaran', 'pendaftaran.NO_PENDAFTARAN = pembayaran.NO_PENDAFTARAN', 'left')
                            ->join('pasien', 'pasien.ID_PASIEN = pendaftaran.ID_PASIEN', 'left')
                            ->find($idPembayaran);

        if (!$pembayaran) {
            echo "<h3>Data pembayaran tidak ditemukan.</h3>";
            return;
        }

        $html = '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Kuitansi Pembayaran ' . $pembayaran['NO_KUITANSI'] . '</title>
            <style>
                body { font-family: "Segoe UI", Tahoma, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 14px; line-height: 1.5; }
                .kuitansi-box { max-width: 650px; margin: 0 auto; border: 1px solid #ddd; padding: 25px; border-radius: 8px; background: #fff; }
                .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
                .header h1 { font-size: 20px; margin: 0; color: #2c3e50; font-weight: 800; }
                .header p { margin: 2px 0; color: #7f8c8d; font-size: 11px; }
                .title-block { text-align: center; margin-bottom: 25px; }
                .title-block h2 { font-size: 16px; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: #2c3e50; border-bottom: 1px solid #eee; display: inline-block; padding-bottom: 5px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #eee; }
                .info-item { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12px; }
                .info-item span:first-child { color: #7f8c8d; }
                .info-item span:last-child { font-weight: 700; color: #333; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                .table th { padding: 10px 12px; background: #eaeeef; font-weight: 700; text-align: left; border-bottom: 2px solid #ddd; font-size: 12px; }
                .table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }
                .table tr:last-child td { border-bottom: none; }
                .total-box { display: flex; justify-content: flex-end; align-items: center; gap: 10px; font-size: 16px; font-weight: 700; margin-bottom: 30px; padding: 12px; background: #e8f0fe; border-radius: 6px; }
                .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; text-align: center; margin-top: 40px; }
                .signature-box { display: flex; flex-direction: column; justify-content: space-between; height: 100px; }
                .signature-title { font-size: 12px; color: #7f8c8d; }
                .signature-name { font-weight: 700; font-size: 13px; text-decoration: underline; }
                @media print {
                    body { background: #fff; padding: 0; }
                    .kuitansi-box { border: none; padding: 0; box-shadow: none; }
                }
            </style>
        </head>
        <body onload="window.print()">
            <div class="kuitansi-box">
                <div class="header">
                    <div>
                        <h1>RS SEJAHTERA</h1>
                        <p>Jl. Kesehatan No. 123, Jakarta Selatan</p>
                        <p>Telp: (021) 123-4567 | info@rssejahtera.com</p>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="margin: 0; color: #4a7dc7; font-weight: 800;">KUITANSI ASLI</h3>
                        <p style="font-weight: 700; color:#333; font-family: monospace; font-size: 12px; margin-top: 5px;">' . $pembayaran['NO_KUITANSI'] . '</p>
                    </div>
                </div>

                <div class="title-block">
                    <h2>Bukti Pembayaran Layanan</h2>
                </div>

                <div class="info-grid">
                    <div>
                        <div class="info-item"><span>No. RM Pasien:</span><span>' . $pembayaran['ID_PASIEN'] . '</span></div>
                        <div class="info-item"><span>Nama Pasien:</span><span>' . $pembayaran['NAMA_PASIEN'] . '</span></div>
                        <div class="info-item"><span>No. Pendaftaran:</span><span>' . $pembayaran['NO_PENDAFTARAN'] . '</span></div>
                    </div>
                    <div>
                        <div class="info-item"><span>Tanggal Bayar:</span><span>' . date("d M Y, H:i", strtotime($pembayaran['TGL_BAYAR'])) . ' WIB</span></div>
                        <div class="info-item"><span>Metode Bayar:</span><span>' . $pembayaran['JENIS_PEMBAYARAN'] . '</span></div>
                        <div class="info-item"><span>Status Tagihan:</span><span style="color: #27ae60; text-transform: uppercase;">LUNAS</span></div>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID Item</th>
                            <th>Layanan / Deskripsi Tindakan</th>
                            <th>Keterangan Tambahan</th>
                            <th style="text-align: right; width: 120px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-family: monospace;">' . $pembayaran['ID_PEMBAYARAN'] . '</td>
                            <td><strong style="text-transform: capitalize;">' . str_replace("_", " ", $pembayaran['JENIS_LAYANAN']) . '</strong></td>
                            <td>' . ($pembayaran['KETERANGAN_LAYANAN'] ?: "-") . '</td>
                            <td style="text-align: right; font-weight: 700;">Rp ' . number_format($pembayaran['BIAYA'], 0, ",", ".") . '</td>
                        </tr>
                    </tbody>
                </table>

                <div class="total-box">
                    <span style="color:#555;">TOTAL DIBAYAR:</span>
                    <span style="color:#2c3e50; font-size: 20px;">Rp ' . number_format($pembayaran['BIAYA'], 0, ",", ".") . '</span>
                </div>

                <div style="font-size: 11px; color:#888; font-style: italic; border-top: 1px solid #eee; padding-top: 10px; margin-top: 20px;">
                    * Pembayaran telah divalidasi oleh sistem administrasi RS Sejahtera dan dianggap sah sebagai bukti pelunasan biaya layanan kesehatan.
                </div>

                <div class="signatures">
                    <div class="signature-box">
                        <div class="signature-title">Pasien / Keluarga</div>
                        <div class="signature-name">' . $pembayaran['NAMA_PASIEN'] . '</div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-title">Kasir RS Sejahtera</div>
                        <div class="signature-name">' . session()->get('fullname') . '</div>
                    </div>
                </div>
            </div>
        </body>
        </html>';

        return $this->response->setBody($html);
    }


    /**
     * Delete pembayaran record
     */
    public function delete($id)
    {
        $model = new PembayaranModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=pembayaran')->with('success', 'Data pembayaran berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=pembayaran')->with('error', 'Tidak bisa menghapus data pembayaran ini.');
        }
    }

    /**
     * API: Get riwayat pembayaran for a specific pendaftaran (JSON)
     * Auto-syncs all clinical transactions into billing items before returning data.
     */
    public function riwayat($noPendaftaran)
    {
        $model = new PembayaranModel();

        // === STEP 1: AUTO-SYNC BILLING FROM ALL CLINICAL TRANSACTIONS ===
        $pendaftaranModel = new \App\Models\PendaftaranModel();
        $pendaftaran = $pendaftaranModel->find($noPendaftaran);
        if ($pendaftaran) {
            $idPasien       = $pendaftaran['ID_PASIEN'];
            $idDokter       = $pendaftaran['ID_DOKTER'];
            $db = \Config\Database::connect();

            // 0. Pendaftaran / Registration Fee
            $pasienModel = new \App\Models\PasienModel();
            $pasienInfo = $pasienModel->find($idPasien);
            $jenisPembayaran = $pasienInfo ? ($pasienInfo['JENIS_PEMBAYARAN'] ?? 'Umum') : 'Umum';
            $pendaftaranFee = ($jenisPembayaran === 'BPJS' || $jenisPembayaran === 'Asuransi') ? 0 : 25000;
            
            $existsReg = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                               ->where('JENIS_LAYANAN', 'pendaftaran')
                               ->countAllResults();
            if (!$existsReg) {
                $model->insert([
                    'ID_PEMBAYARAN'      => $model->generateNextId(),
                    'NO_PENDAFTARAN'     => $noPendaftaran,
                    'JENIS_LAYANAN'      => 'pendaftaran',
                    'ID_REFERENSI'       => $noPendaftaran,
                    'KETERANGAN_LAYANAN' => 'Biaya pendaftaran pasien (' . $jenisPembayaran . ')',
                    'BIAYA'              => $pendaftaranFee,
                    'STATUS'             => $pendaftaranFee > 0 ? 'belum_bayar' : 'lunas',
                    'CREATED_AT'         => date('Y-m-d H:i:s'),
                ]);
            }

            // A. Pemeriksaan / Konsultasi Dokter
            // Match using the subquery fallback to resolve which examinations belong to this registration
            $periksaList = $db->query(
                "SELECT pm.* FROM pemeriksaan pm
                 WHERE pm.ID_PASIEN = ?
                   AND (
                     SELECT pd.NO_PENDAFTARAN FROM pendaftaran pd
                     WHERE pd.ID_PASIEN = pm.ID_PASIEN
                     ORDER BY 
                       (DATE(pd.TANGGAL_DAFTAR) = DATE(pm.TGL_PERIKSA) AND pd.ID_DOKTER = pm.ID_DOKTER) DESC, 
                       (DATE(pd.TANGGAL_DAFTAR) = DATE(pm.TGL_PERIKSA)) DESC, 
                       (pd.ID_DOKTER = pm.ID_DOKTER) DESC, 
                       pd.TANGGAL_DAFTAR DESC 
                     LIMIT 1
                   ) = ?",
                [$idPasien, $noPendaftaran]
            )->getResultArray();

            foreach ($periksaList as $pm) {
                // Cek sudah ada belum
                $exists = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                ->where('JENIS_LAYANAN', 'pemeriksaan')
                                ->where('ID_REFERENSI', $pm['ID_PERIKSA'])
                                ->countAllResults();
                if (!$exists) {
                    $model->insert([
                        'ID_PEMBAYARAN'      => $model->generateNextId(),
                        'NO_PENDAFTARAN'     => $noPendaftaran,
                        'JENIS_LAYANAN'      => 'pemeriksaan',
                        'ID_REFERENSI'       => $pm['ID_PERIKSA'],
                        'KETERANGAN_LAYANAN' => 'Konsultasi Dokter & Pemeriksaan Fisik (' . $pm['ID_PERIKSA'] . ')',
                        'BIAYA'              => 50000,
                        'STATUS'             => 'belum_bayar',
                        'CREATED_AT'         => date('Y-m-d H:i:s'),
                    ]);
                }

                // B. Rontgen dari pemeriksaan ini
                $rontgenModel = new \App\Models\RontgenModel();
                $rontgens = $rontgenModel->where('ID_PERIKSA', $pm['ID_PERIKSA'])->findAll();
                foreach ($rontgens as $rtg) {
                    $existsRtg = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                       ->where('JENIS_LAYANAN', 'rontgen')
                                       ->where('ID_REFERENSI', $rtg['ID_RONTGEN'])
                                       ->countAllResults();
                    if (!$existsRtg) {
                        $model->insert([
                            'ID_PEMBAYARAN'      => $model->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'rontgen',
                            'ID_REFERENSI'       => $rtg['ID_RONTGEN'],
                            'KETERANGAN_LAYANAN' => 'Pemeriksaan Rontgen: ' . ($rtg['JENIS_RONTGEN'] ?? '-'),
                            'BIAYA'              => 150000,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                // C. Laboratorium dari pemeriksaan ini
                $labModel = new \App\Models\LaboratoriumModel();
                $labs = $labModel->where('ID_PERIKSA', $pm['ID_PERIKSA'])->findAll();
                foreach ($labs as $lb) {
                    $existsLab = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                       ->where('JENIS_LAYANAN', 'laboratorium')
                                       ->where('ID_REFERENSI', $lb['ID_LABORATORIUM'])
                                       ->countAllResults();
                    if (!$existsLab) {
                        $model->insert([
                            'ID_PEMBAYARAN'      => $model->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'laboratorium',
                            'ID_REFERENSI'       => $lb['ID_LABORATORIUM'],
                            'KETERANGAN_LAYANAN' => 'Pemeriksaan Lab: ' . ($lb['JENIS_PEMERIKSAAN'] ?? '-'),
                            'BIAYA'              => 75000,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                // D. Resep Obat / Pengobatan dari pemeriksaan ini
                $pengobatanModel = new \App\Models\PengobatanModel();
                $farmasiModel    = new \App\Models\FarmasiModel();
                $prescriptions   = $pengobatanModel->where('ID_PERIKSA', $pm['ID_PERIKSA'])->findAll();
                foreach ($prescriptions as $rx) {
                    $existsRx = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                      ->where('JENIS_LAYANAN', 'farmasi')
                                      ->where('ID_REFERENSI', $rx['ID_PENGOBATAN'])
                                      ->countAllResults();
                    if (!$existsRx) {
                        // Cari harga obat dari resep obat langsung, fall back ke master farmasi
                        $hargaObat = 20000; // default
                        if (isset($rx['HARGA_OBAT']) && (float) $rx['HARGA_OBAT'] > 0) {
                            $hargaObat = (float) $rx['HARGA_OBAT'];
                        } elseif (!empty($rx['NAMA_OBAT'])) {
                            $drug = $farmasiModel->like('NAMA_OBAT', $rx['NAMA_OBAT'])->first();
                            if ($drug && !empty($drug['HARGA_OBAT'])) {
                                $hargaObat = (float) $drug['HARGA_OBAT'];
                            }
                        }
                        $model->insert([
                            'ID_PEMBAYARAN'      => $model->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'farmasi',
                            'ID_REFERENSI'       => $rx['ID_PENGOBATAN'],
                            'KETERANGAN_LAYANAN' => 'Obat Resep: ' . ($rx['NAMA_OBAT'] ?? '-') . ' (' . ($rx['DOSIS_OBAT'] ?? '-') . ')',
                            'BIAYA'              => $hargaObat,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            // E. Perawatan (Rawat Jalan & Rawat Inap) berdasarkan pasien & resolved registration
            $kamarModel     = new \App\Models\KamarModel();
            // Match perawatan using subquery fallback to resolve which treatments belong to this registration
            $perawatans = $db->query(
                "SELECT pw.* FROM perawatan pw
                 WHERE pw.ID_PASIEN = ?
                   AND (
                     SELECT pd.NO_PENDAFTARAN FROM pendaftaran pd
                     WHERE pd.ID_PASIEN = pw.ID_PASIEN
                     ORDER BY 
                       (DATE(pd.TANGGAL_DAFTAR) = DATE(pw.TGL_PERAWATAN)) DESC, 
                       pd.TANGGAL_DAFTAR DESC 
                     LIMIT 1
                   ) = ?",
                [$idPasien, $noPendaftaran]
            )->getResultArray();

            foreach ($perawatans as $pw) {
                // Rawat Inap – billed as 'kamar'
                if (!empty($pw['RAWAT_INAP']) && !empty($pw['ID_KAMAR'])) {
                    $existsKamar = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                         ->where('JENIS_LAYANAN', 'kamar')
                                         ->where('ID_REFERENSI', $pw['ID_PERAWATAN'])
                                         ->countAllResults();
                    if (!$existsKamar) {
                        $kamar      = $kamarModel->find($pw['ID_KAMAR']);
                        $tarifKamar = 250000; // default
                        $nomorKamar = $kamar['NOMOR_KAMAR'] ?? $pw['ID_KAMAR'];
                        $tipeKamar  = $kamar['TIPE_KAMAR'] ?? 'Standard';

                        $model->insert([
                            'ID_PEMBAYARAN'      => $model->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'kamar',
                            'ID_REFERENSI'       => $pw['ID_PERAWATAN'],
                            'KETERANGAN_LAYANAN' => 'Biaya Rawat Inap: Kamar No.' . $nomorKamar . ' (' . $tipeKamar . ')',
                            'BIAYA'              => $tarifKamar,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                // Rawat Jalan – billed as 'perawatan'
                if (!empty($pw['RAWAT_JALAN'])) {
                    $existsRawatJalan = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                              ->where('JENIS_LAYANAN', 'perawatan')
                                              ->where('ID_REFERENSI', $pw['ID_PERAWATAN'])
                                              ->countAllResults();
                    if (!$existsRawatJalan) {
                        $model->insert([
                            'ID_PEMBAYARAN'      => $model->generateNextId(),
                            'NO_PENDAFTARAN'     => $noPendaftaran,
                            'JENIS_LAYANAN'      => 'perawatan',
                            'ID_REFERENSI'       => $pw['ID_PERAWATAN'],
                            'KETERANGAN_LAYANAN' => 'Tindakan Rawat Jalan (' . $pw['ID_PERAWATAN'] . ')',
                            'BIAYA'              => 30000,
                            'STATUS'             => 'belum_bayar',
                            'CREATED_AT'         => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            // F. Administrasi
            $administrasiModel = new \App\Models\AdministrasiModel();
            $adminList = $administrasiModel->where('NO_PENDAFTARAN', $noPendaftaran)->findAll();
            foreach ($adminList as $adm) {
                $existsAdm = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                   ->where('JENIS_LAYANAN', 'administrasi')
                                   ->where('ID_REFERENSI', $adm['ID_ADMINISTRASI'])
                                   ->countAllResults();
                if (!$existsAdm) {
                    $model->insert([
                        'ID_PEMBAYARAN'      => $model->generateNextId(),
                        'NO_PENDAFTARAN'     => $noPendaftaran,
                        'JENIS_LAYANAN'      => 'administrasi',
                        'ID_REFERENSI'       => $adm['ID_ADMINISTRASI'],
                        'KETERANGAN_LAYANAN' => 'Biaya Administrasi (' . $adm['ID_ADMINISTRASI'] . ')',
                        'BIAYA'              => $adm['BIAYA'],
                        'STATUS'             => 'belum_bayar',
                        'CREATED_AT'         => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    // Update the fee if it changed in administrasi
                    $existing = $model->where('NO_PENDAFTARAN', $noPendaftaran)
                                      ->where('JENIS_LAYANAN', 'administrasi')
                                      ->where('ID_REFERENSI', $adm['ID_ADMINISTRASI'])
                                      ->first();
                    if ($existing && $existing['STATUS'] !== 'lunas' && (float)$existing['BIAYA'] !== (float)$adm['BIAYA']) {
                        $model->update($existing['ID_PEMBAYARAN'], [
                            'BIAYA' => $adm['BIAYA']
                        ]);
                    }
                }
            }
        }

        // === STEP 2: FETCH ALL SYNCED BILLING DATA ===
        $data = $model->getByPendaftaran($noPendaftaran);

        $total = 0;
        $lunas = 0;
        foreach ($data as $d) {
            $total += (float) $d['BIAYA'];
            if ($d['STATUS'] === 'lunas') $lunas += (float) $d['BIAYA'];
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'data'    => $data,
            'summary' => [
                'total' => $total,
                'lunas' => $lunas,
                'belum' => $total - $lunas,
            ],
        ]);
    }
}
