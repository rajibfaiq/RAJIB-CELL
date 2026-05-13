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
        return view('dashboard', ['title' => 'Dashboard']);
    }
}
