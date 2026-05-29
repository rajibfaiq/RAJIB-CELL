<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        $session = session();
        $model = new \App\Models\UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        log_message('debug', 'Attempting login for username: ' . $username);
        
        try {
            $data = $model->where('username', $username)->first();
        } catch (\Exception $e) {
            log_message('error', 'Database error during login: ' . $e->getMessage());
            $session->setFlashdata('error', 'Terjadi kesalahan pada database.');
            return redirect()->to('/');
        }
        
        if ($data) {
            log_message('debug', 'User found: ' . $username);
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            if ($authenticatePassword) {
                log_message('debug', 'Password verified for: ' . $username);
                $ses_data = [
                    'id'       => $data['id'],
                    'username' => $data['username'],
                    'fullname' => $data['fullname'],
                    'role'     => $data['role'],
                    'ID_DOKTER' => $data['ID_DOKTER'] ?? null,
                    'ID_PASIEN' => $data['ID_PASIEN'] ?? null,
                    'logged_in'     => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('/dashboard');
            } else {
                log_message('debug', 'Password mismatch for: ' . $username);
                $session->setFlashdata('error', 'Password salah.');
                return redirect()->to('/');
            }
        } else {
            log_message('debug', 'User not found: ' . $username);
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->to('/');
        }
    }

    public function registerView()
    {
        return view('register');
    }

    public function register()
    {
        $session = session();
        $userModel = new \App\Models\UserModel();
        $pasienModel = new \App\Models\PasienModel();

        // 1. Validate Form Input
        $nik = $this->request->getPost('nik');
        $username = $this->request->getPost('username');

        // NIK validation (16 digits)
        if (strlen($nik) !== 16 || !is_numeric($nik)) {
            $session->setFlashdata('error', 'NIK harus berupa 16 digit angka.');
            return redirect()->back()->withInput();
        }

        // NIK uniqueness check
        $existingPasien = $pasienModel->where('NIK', $nik)->first();
        if ($existingPasien) {
            $session->setFlashdata('error', 'NIK sudah terdaftar di sistem.');
            return redirect()->back()->withInput();
        }

        // Username uniqueness check
        $existingUser = $userModel->where('username', $username)->first();
        if ($existingUser) {
            $session->setFlashdata('error', 'Username sudah digunakan oleh akun lain.');
            return redirect()->back()->withInput();
        }

        // Phone number validation (numeric)
        $noTelp = $this->request->getPost('no_telp');
        if (!is_numeric($noTelp)) {
            $session->setFlashdata('error', 'Nomor telepon harus berupa angka.');
            return redirect()->back()->withInput();
        }

        // 2. Insert Pasien Details
        $idPasien = $pasienModel->generateNextId();
        $pasienData = [
            'ID_PASIEN'            => $idPasien,
            'NAMA_PASIEN'          => $this->request->getPost('fullname'),
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
            $session->setFlashdata('error', 'Gagal mendaftarkan data pasien.');
            return redirect()->back()->withInput();
        }

        // 3. Insert User Account details
        $userData = [
            'username'  => $username,
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'fullname'  => $this->request->getPost('fullname'),
            'role'      => 'pasien',
            'ID_PASIEN' => $idPasien,
        ];

        if ($userModel->insert($userData)) {
            $session->setFlashdata('success', 'Pendaftaran akun berhasil! Silakan masuk menggunakan akun Anda. Nomor Rekam Medis Anda: ' . $idPasien);
            return redirect()->to('/');
        } else {
            // Rollback patient if user fails
            $pasienModel->delete($idPasien);
            $session->setFlashdata('error', 'Gagal membuat akun login pasien.');
            return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }
}
