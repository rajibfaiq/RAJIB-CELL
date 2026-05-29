<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'Home::dashboard');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/register', 'Auth::registerView');
$routes->post('/auth/register', 'Auth::register');

// ====== CRUD ENABLED ======
$routes->post('/pasien/save', 'Pasien::save');
$routes->get('/pasien/delete/(:segment)', 'Pasien::delete/$1');

$routes->post('/dokter/save', 'Dokter::save');
$routes->get('/dokter/delete/(:segment)', 'Dokter::delete/$1');

$routes->post('/pendaftaran/save', 'Pendaftaran::save');
$routes->post('/pendaftaran/updatePendaftaran', 'Pendaftaran::updatePendaftaran');
$routes->get('/pendaftaran/find-pasien', 'Pendaftaran::findPasien');
$routes->get('/pendaftaran/batal/(:segment)', 'Pendaftaran::batal/$1');
$routes->get('/pendaftaran/cetak/(:segment)', 'Pendaftaran::cetakAntrian/$1');
$routes->get('/pendaftaran/delete/(:segment)', 'Pendaftaran::delete/$1');

$routes->post('/farmasi/save', 'Farmasi::save');
$routes->get('/farmasi/delete/(:segment)', 'Farmasi::delete/$1');

$routes->post('/administrasi/save', 'Administrasi::save');
$routes->get('/administrasi/delete/(:segment)', 'Administrasi::delete/$1');

$routes->post('/perawatan/save', 'Perawatan::save');
$routes->get('/perawatan/delete/(:segment)', 'Perawatan::delete/$1');

$routes->post('/pemeriksaan/save', 'Pemeriksaan::save');
$routes->get('/pemeriksaan/delete/(:segment)', 'Pemeriksaan::delete/$1');

$routes->post('/laboratorium/save', 'Laboratorium::save');
$routes->get('/laboratorium/delete/(:segment)', 'Laboratorium::delete/$1');

$routes->post('/kamar/save', 'Kamar::save');
$routes->get('/kamar/delete/(:segment)', 'Kamar::delete/$1');

$routes->post('/rontgen/save', 'Rontgen::save');
$routes->get('/rontgen/delete/(:segment)', 'Rontgen::delete/$1');

$routes->post('/pengobatan/save', 'Pengobatan::save');
$routes->get('/pengobatan/delete/(:segment)', 'Pengobatan::delete/$1');

$routes->post('/perawat/save', 'Perawat::save');
$routes->get('/perawat/delete/(:segment)', 'Perawat::delete/$1');
// ====== END CRUD ENABLED ======

// ====== REVISI BARU SIMRS ======
// Poli API
$routes->get('/poli/dokter/(:segment)', 'Poli::getDokterByPoli/$1');

// Antrian Dokter
$routes->post('/pendaftaran/updateStatus', 'Pendaftaran::updateStatus');
$routes->get('/pendaftaran/antrianDokter', 'Pendaftaran::getAntrianDokter');

// Rujukan Rontgen
$routes->post('/pemeriksaan/rujukRontgen', 'Pemeriksaan::rujukRontgen');
$routes->post('/rontgen/uploadHasil/(:segment)', 'Rontgen::uploadHasil/$1');

// Pembayaran baru
$routes->post('/pembayaran/save', 'Pembayaran::save');
$routes->post('/pembayaran/bayar/(:segment)', 'Pembayaran::bayar/$1');
$routes->get('/pembayaran/kuitansi/(:segment)', 'Pembayaran::cetakKuitansi/$1');
$routes->get('/pembayaran/riwayat/(:segment)', 'Pembayaran::riwayat/$1');
$routes->get('/pembayaran/delete/(:segment)', 'Pembayaran::delete/$1');
// ====== END REVISI BARU SIMRS ======

