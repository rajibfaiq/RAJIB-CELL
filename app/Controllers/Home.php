<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('login');
    }

    public function dashboard()
    {
        $pasienModel = new \App\Models\PasienModel();
        $dokterModel = new \App\Models\DokterModel();
        $perawatModel = new \App\Models\PerawatModel();
        $kamarModel = new \App\Models\KamarModel();
        $adminModel = new \App\Models\AdministrasiModel();
        $farmasiModel = new \App\Models\FarmasiModel();
        $labModel = new \App\Models\LaboratoriumModel();
        $pendaftaranModel = new \App\Models\PendaftaranModel();
        $pemeriksaanModel = new \App\Models\PemeriksaanModel();
        $rontgenModel = new \App\Models\RontgenModel();
        $perawatanModel = new \App\Models\PerawatanModel();
        $pengobatanModel = new \App\Models\PengobatanModel();
        $poliModel = new \App\Models\PoliModel();
        $pembayaranModel = new \App\Models\PembayaranModel();

        $data = [
            'title'              => 'Dashboard',
            'pasien'             => $pasienModel->findAll(),
            'nextPasienId'       => $pasienModel->generateNextId(),
            'dokter'             => $dokterModel->getWithPoli(),
            'nextDokterId'       => $dokterModel->generateNextId(),
            'perawat'            => $perawatModel->findAll(),
            'nextPerawatId'      => $perawatModel->generateNextId(),
            'kamar'              => $kamarModel->getJoinedData(),
            'nextKamarId'        => $kamarModel->generateNextId(),
            'administrasi'       => $adminModel->getJoinedData(),
            'nextAdministrasiId' => $adminModel->generateNextId(),
            'farmasi'            => $farmasiModel->getJoinedData(),
            'nextFarmasiId'      => $farmasiModel->generateNextId(),
            'laboratorium'       => $labModel->getJoinedData(),
            'nextLaboratoriumId' => $labModel->generateNextId(),
            'pendaftaran'        => $pendaftaranModel->getJoinedData(),
            'nextPendaftaranId'  => $pendaftaranModel->generateNextId(),
            'pemeriksaan'        => $pemeriksaanModel->getJoinedData(),
            'nextPemeriksaanId'  => $pemeriksaanModel->generateNextId(),
            'rontgen'            => $rontgenModel->getJoinedData(),
            'nextRontgenId'      => $rontgenModel->generateNextId(),
            'perawatan'          => $perawatanModel->getJoinedData(),
            'nextPerawatanId'    => $perawatanModel->generateNextId(),
            'pengobatan'         => $pengobatanModel->getJoinedData(),
            'nextPengobatanId'   => $pengobatanModel->generateNextId(),
            
            // New data for revisions
            'poli'               => $poliModel->findAll(),
            'pembayaran'         => $pembayaranModel->getJoinedData(),
            'nextPembayaranId'   => $pembayaranModel->generateNextId(),

            'totalPasien'        => $pasienModel->countAll(),
            'totalDokter'        => $dokterModel->countAll(),
            'totalRawatJalan'    => $perawatanModel->where('RAWAT_JALAN', 1)->countAllResults(),
            'totalRawatInap'     => $perawatanModel->where('RAWAT_INAP', 1)->countAllResults(),
        ];

        // If logged-in user is a patient, load their profile and registration records
        $idPasien = session()->get('ID_PASIEN');
        if ($idPasien) {
            $data['pasienProfil'] = $pasienModel->find($idPasien);
            
            // Active queues (waiting or being checked)
            $data['activeQueues'] = $pendaftaranModel->select('pendaftaran.*, dokter.NAMA_DOKTER, poli.NAMA_POLI')
                                                     ->join('dokter', 'dokter.ID_DOKTER = pendaftaran.ID_DOKTER', 'left')
                                                     ->join('poli', 'poli.ID_POLI = pendaftaran.ID_POLI', 'left')
                                                     ->where('pendaftaran.ID_PASIEN', $idPasien)
                                                     ->whereIn('pendaftaran.STATUS_ANTRIAN', ['menunggu', 'sedang_diperiksa'])
                                                     ->orderBy('pendaftaran.TANGGAL_KUNJUNGAN', 'ASC')
                                                     ->findAll();

            // All registrations (history)
            $data['historyQueues'] = $pendaftaranModel->select('pendaftaran.*, dokter.NAMA_DOKTER, poli.NAMA_POLI')
                                                      ->join('dokter', 'dokter.ID_DOKTER = pendaftaran.ID_DOKTER', 'left')
                                                      ->join('poli', 'poli.ID_POLI = pendaftaran.ID_POLI', 'left')
                                                      ->where('pendaftaran.ID_PASIEN', $idPasien)
                                                      ->orderBy('pendaftaran.TANGGAL_KUNJUNGAN', 'DESC')
                                                      ->findAll();
        } else {
            $data['pasienProfil'] = null;
            $data['activeQueues'] = [];
            $data['historyQueues'] = [];
        }

        // If logged-in user is a dokter, load antrian data
        $idDokter = session()->get('ID_DOKTER');
        if ($idDokter) {
            $data['antrianDokter'] = $pendaftaranModel->getAntrianDokter($idDokter);
        } else {
            $data['antrianDokter'] = [];
        }

        return view('dashboard', $data);
    }
}
