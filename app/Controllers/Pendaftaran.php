<?php

namespace App\Controllers;

use App\Models\PendaftaranModel;
use App\Models\PembayaranModel;
use App\Models\PasienModel;
use App\Models\DokterModel;
use App\Models\PoliModel;

class Pendaftaran extends BaseController
{
    /**
     * Save pendaftaran with poli, dokter, and auto antrian
     */
    public function save()
    {
        $model = new PendaftaranModel();
        $pasienModel = new PasienModel();
        $dokterModel = new DokterModel();
        $poliModel = new PoliModel();
        $pembayaranModel = new PembayaranModel();

        $session = session();
        $isNewPatient = $this->request->getPost('is_new_patient');
        $idPasien = $this->request->getPost('id_pasien');
        $idPoli = $this->request->getPost('id_poli');
        $idDokter = $this->request->getPost('id_dokter');
        $tanggalKunjungan = $this->request->getPost('tanggal_kunjungan');
        $sesiKunjungan = $this->request->getPost('sesi_kunjungan');

        // Validation helper for JSON responses
        $isAjax = $this->request->isAJAX();

        // 1. Process Patient Information
        if ($session->get('role') === 'pasien') {
            // Patient logged in: use their linked profile
            $idPasien = $session->get('ID_PASIEN');
            if (!$idPasien) {
                $msg = 'Akun Anda tidak terhubung dengan profil pasien.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg);
            }
        } elseif ($isNewPatient == '1') {
            // Register new patient on-the-fly (Admin/Petugas only)
            $nik = $this->request->getPost('nik');
            
            // Validate NIK
            if (strlen($nik) !== 16 || !is_numeric($nik)) {
                $msg = 'NIK harus berupa 16 digit angka.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->withInput()->with('error', $msg);
            }

            // NIK uniqueness check
            $existingPasien = $pasienModel->where('NIK', $nik)->first();
            if ($existingPasien) {
                $msg = 'NIK sudah terdaftar di sistem. Silakan gunakan opsi Pasien Lama.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->withInput()->with('error', $msg);
            }

            // Phone validation
            $noTelp = $this->request->getPost('no_telp');
            if (!is_numeric($noTelp)) {
                $msg = 'Nomor telepon harus berupa angka.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->withInput()->with('error', $msg);
            }

            // Create patient
            $idPasien = $pasienModel->generateNextId();
            $pasienData = [
                'ID_PASIEN'            => $idPasien,
                'NAMA_PASIEN'          => $this->request->getPost('nama_pasien'),
                'NIK'                  => $nik,
                'ALAMAT_PASIEN'        => $this->request->getPost('alamat_pasien'),
                'NO_TELP'              => $noTelp,
                'TGL_LAHIR'            => $this->request->getPost('tgl_lahir'),
                'JENIS_KELAMIN'        => $this->request->getPost('jenis_kelamin'),
                'PROVINSI'             => $this->request->getPost('provinsi'),
                'KOTA'                 => $this->request->getPost('kota'),
                'KECAMATAN'            => $this->request->getPost('kecamatan'),
                'KELURAHAN'            => $this->request->getPost('kelurahan'),
                'JENIS_PEMBAYARAN'     => $this->request->getPost('jenis_pembayaran'),
                'NO_BPJS'              => $this->request->getPost('no_bpjs') ?: null,
                'NAMA_ASURANSI'        => $this->request->getPost('nama_asuransi') ?: null,
                'NO_POLIS'             => $this->request->getPost('no_polis') ?: null,
                'KONTAK_DARURAT_NAMA'  => $this->request->getPost('kontak_darurat_nama'),
                'KONTAK_DARURAT_TELP'  => $this->request->getPost('kontak_darurat_telp'),
            ];

            if (!$pasienModel->insert($pasienData)) {
                $msg = 'Gagal menyimpan data pasien baru.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->withInput()->with('error', $msg);
            }
        } else {
            // Existing patient check
            if (empty($idPasien)) {
                $msg = 'Silakan pilih pasien terlebih dahulu.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->withInput()->with('error', $msg);
            }
        }

        // 2. Validate Quota limit
        $sisaKuota = $dokterModel->getSisaKuota($idDokter, $tanggalKunjungan, $sesiKunjungan);
        if ($sisaKuota <= 0) {
            $msg = 'Kuota dokter untuk tanggal & sesi yang dipilih sudah penuh.';
            return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->withInput()->with('error', $msg);
        }

        // 3. Register Appointment
        $no_daftar = $model->generateNextId();
        $noAntrian = $model->getNextAntrian($idDokter, $tanggalKunjungan);

        $data = [
            'NO_PENDAFTARAN'    => $no_daftar,
            'ID_PASIEN'         => $idPasien,
            'ID_POLI'           => $idPoli,
            'ID_DOKTER'         => $idDokter,
            'NO_ANTRIAN'        => $noAntrian,
            'STATUS_ANTRIAN'    => 'menunggu',
            'TANGGAL_DAFTAR'    => date('Y-m-d H:i:s'),
            'JAM_PENDAFTARAN'   => date('Y-m-d H:i:s'),
            'TANGGAL_KUNJUNGAN' => $tanggalKunjungan,
            'SESI_KUNJUNGAN'    => $sesiKunjungan,
        ];

        if ($model->insert($data)) {
            // Fetch Patient to check Payment Method
            $pasienInfo = $pasienModel->find($idPasien);
            $jenisPembayaran = $pasienInfo['JENIS_PEMBAYARAN'] ?? 'Umum';

            // Fee is Rp 25.000 for Umum, Rp 0 for BPJS/Asuransi
            $biaya = 25000;
            if ($jenisPembayaran === 'BPJS' || $jenisPembayaran === 'Asuransi') {
                $biaya = 0;
            }

            // Auto-create pembayaran
            $pembayaranModel->insert([
                'ID_PEMBAYARAN'      => $pembayaranModel->generateNextId(),
                'NO_PENDAFTARAN'     => $no_daftar,
                'JENIS_LAYANAN'      => 'pendaftaran',
                'KETERANGAN_LAYANAN' => 'Biaya pendaftaran pasien (' . $jenisPembayaran . ')',
                'BIAYA'              => $biaya,
                'STATUS'             => $biaya > 0 ? 'belum_bayar' : 'lunas',
                'CREATED_AT'         => date('Y-m-d H:i:s'),
            ]);

            // Estimate calling time: Pagi starts at 08:00, Siang at 13:00, Sore at 17:00
            // Assuming 15 minutes per patient
            $startMinutes = 0;
            if ($sesiKunjungan === 'Pagi') $startMinutes = 8 * 60; // 08:00
            elseif ($sesiKunjungan === 'Siang') $startMinutes = 13 * 60; // 13:00
            elseif ($sesiKunjungan === 'Sore') $startMinutes = 17 * 60; // 17:00

            $estimasiMinutes = $startMinutes + (($noAntrian - 1) * 15);
            $estHours = floor($estimasiMinutes / 60);
            $estMins = $estimasiMinutes % 60;
            $estimasiJam = str_pad($estHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($estMins, 2, '0', STR_PAD_LEFT) . ' WIB';

            $dokterInfo = $dokterModel->find($idDokter);
            $poliInfo = $poliModel->find($idPoli);

            if ($isAjax) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pendaftaran berhasil!',
                    'data' => [
                        'NO_PENDAFTARAN'    => $no_daftar,
                        'NO_ANTRIAN'        => $noAntrian,
                        'NAMA_DOKTER'       => $dokterInfo['NAMA_DOKTER'],
                        'NAMA_POLI'         => $poliInfo['NAMA_POLI'],
                        'TANGGAL_KUNJUNGAN' => date('d M Y', strtotime($tanggalKunjungan)),
                        'SESI_KUNJUNGAN'    => $sesiKunjungan,
                        'ESTIMASI_JAM'      => $estimasiJam
                    ]
                ]);
            }

            return redirect()->to('/dashboard?page=pendaftaran')->with('success', 'Pendaftaran berhasil! No. Antrian: ' . $noAntrian);
        } else {
            $msg = 'Gagal menyimpan pendaftaran.';
            return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg);
        }
    }

    /**
     * API Endpoint: Find patient by NIK or No Rekam Medis (ID_PASIEN)
     */
    public function findPasien()
    {
        $keyword = $this->request->getGet('query');
        if (empty($keyword)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Query kosong.']);
        }

        $pasienModel = new PasienModel();
        $pasien = $pasienModel->groupStart()
                              ->where('ID_PASIEN', $keyword)
                              ->orWhere('NIK', $keyword)
                              ->groupEnd()
                              ->first();

        if ($pasien) {
            return $this->response->setJSON(['status' => 'success', 'data' => $pasien]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Data pasien tidak ditemukan.']);
    }

    /**
     * Update pendaftaran details (Petugas only)
     */
    public function updatePendaftaran()
    {
        $model = new PendaftaranModel();
        $role = session()->get('role');

        if ($role !== 'admin' && $role !== 'petugas') {
            return redirect()->to('/dashboard?page=pendaftaran')->with('error', 'Anda tidak memiliki hak akses untuk mengubah pendaftaran.');
        }

        $noPendaftaran = $this->request->getPost('no_pendaftaran');
        $idDokter = $this->request->getPost('id_dokter');
        $tanggalKunjungan = $this->request->getPost('tanggal_kunjungan');
        $sesiKunjungan = $this->request->getPost('sesi_kunjungan');

        // Check if visit date or session changed, if so recalculate antrian
        $current = $model->find($noPendaftaran);
        $noAntrian = $current['NO_ANTRIAN'];

        if ($current['ID_DOKTER'] !== $idDokter || $current['TANGGAL_KUNJUNGAN'] !== $tanggalKunjungan || $current['SESI_KUNJUNGAN'] !== $sesiKunjungan) {
            // Recalculate
            $noAntrian = $model->getNextAntrian($idDokter, $tanggalKunjungan);
        }

        $data = [
            'ID_POLI'           => $this->request->getPost('id_poli'),
            'ID_DOKTER'         => $idDokter,
            'TANGGAL_KUNJUNGAN' => $tanggalKunjungan,
            'SESI_KUNJUNGAN'    => $sesiKunjungan,
            'NO_ANTRIAN'        => $noAntrian,
            'STATUS_ANTRIAN'    => $this->request->getPost('status_antrian')
        ];

        if ($model->update($noPendaftaran, $data)) {
            return redirect()->to('/dashboard?page=pendaftaran')->with('success', 'Data pendaftaran berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data pendaftaran.');
        }
    }

    /**
     * Cancel pendaftaran (Petugas only)
     */
    public function batal($id)
    {
        $model = new PendaftaranModel();
        $role = session()->get('role');

        if ($role !== 'admin' && $role !== 'petugas') {
            return redirect()->to('/dashboard?page=pendaftaran')->with('error', 'Anda tidak memiliki hak akses untuk membatalkan pendaftaran.');
        }

        if ($model->update($id, ['STATUS_ANTRIAN' => 'batal'])) {
            return redirect()->to('/dashboard?page=pendaftaran')->with('success', 'Pendaftaran berhasil dibatalkan.');
        } else {
            return redirect()->to('/dashboard?page=pendaftaran')->with('error', 'Gagal membatalkan pendaftaran.');
        }
    }

    /**
     * Print queue ticket
     */
    public function cetakAntrian($id)
    {
        $model = new PendaftaranModel();
        $pendaftaran = $model->select('pendaftaran.*, pasien.NAMA_PASIEN, pasien.ID_PASIEN, pasien.JENIS_PEMBAYARAN, dokter.NAMA_DOKTER, poli.NAMA_POLI')
                             ->join('pasien', 'pasien.ID_PASIEN = pendaftaran.ID_PASIEN')
                             ->join('dokter', 'dokter.ID_DOKTER = pendaftaran.ID_DOKTER', 'left')
                             ->join('poli', 'poli.ID_POLI = pendaftaran.ID_POLI', 'left')
                             ->find($id);

        if (!$pendaftaran) {
            echo "<h3>Data pendaftaran tidak ditemukan.</h3>";
            return;
        }

        // Estimate calling time: Pagi starts at 08:00, Siang at 13:00, Sore at 17:00
        $startMinutes = 0;
        if ($pendaftaran['SESI_KUNJUNGAN'] === 'Pagi') $startMinutes = 8 * 60;
        elseif ($pendaftaran['SESI_KUNJUNGAN'] === 'Siang') $startMinutes = 13 * 60;
        elseif ($pendaftaran['SESI_KUNJUNGAN'] === 'Sore') $startMinutes = 17 * 60;

        $estimasiMinutes = $startMinutes + (($pendaftaran['NO_ANTRIAN'] - 1) * 15);
        $estHours = floor($estimasiMinutes / 60);
        $estMins = $estimasiMinutes % 60;
        $pendaftaran['ESTIMASI_JAM'] = str_pad($estHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($estMins, 2, '0', STR_PAD_LEFT) . ' WIB';

        return view('dashboard/print_antrian', ['p' => $pendaftaran]);
    }

    /**
     * Update status antrian (for dokter)
     */
    public function updateStatus()
    {
        $model = new PendaftaranModel();
        $noPendaftaran = $this->request->getPost('no_pendaftaran');
        $statusBaru = $this->request->getPost('status');

        $model->update($noPendaftaran, [
            'STATUS_ANTRIAN' => $statusBaru
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status diperbarui']);
        }

        return redirect()->to('/dashboard?page=antrian')->with('success', 'Status antrian diperbarui');
    }

    /**
     * API: Get antrian for logged-in dokter today (JSON)
     */
    public function getAntrianDokter()
    {
        $model = new PendaftaranModel();
        $idDokter = session()->get('ID_DOKTER');

        if (!$idDokter) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Bukan akun dokter']);
        }

        $data = $model->getAntrianDokter($idDokter);

        $total = count($data);
        $selesai = count(array_filter($data, fn($d) => $d['STATUS_ANTRIAN'] === 'selesai'));
        $menunggu = count(array_filter($data, fn($d) => $d['STATUS_ANTRIAN'] === 'menunggu'));

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'summary' => compact('total', 'selesai', 'menunggu')
        ]);
    }

    public function delete($id)
    {
        $model = new PendaftaranModel();
        try {
            $model->delete($id);
            return redirect()->to('/dashboard?page=pendaftaran')->with('success', 'Data Pendaftaran berhasil dihapus');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('/dashboard?page=pendaftaran')->with('error', 'Tidak bisa menghapus pendaftaran ini karena masih memiliki data terkait.');
        }
    }
}

