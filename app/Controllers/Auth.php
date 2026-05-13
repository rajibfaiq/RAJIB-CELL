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

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/');
    }
}
