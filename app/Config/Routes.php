<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login/auth', 'Auth::attemptLogin');
$routes->post('logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/analitik', 'Dashboard::analitik');
    $routes->get('dashboard/export-csv', 'Dashboard::exportAnalitikCsv');

    $routes->get('kategori', 'Kategori::index');
    $routes->get('lokasi', 'Lokasi::index');
    $routes->get('barang', 'Barang::index');

    $routes->get('peminjaman', 'Peminjaman::index');
    $routes->get('peminjaman/tambah', 'Peminjaman::create');
    $routes->post('peminjaman/simpan', 'Peminjaman::store');
    $routes->post('peminjaman/kembalikan/(:num)', 'Peminjaman::returnItem/$1');

    $routes->get('laporan', 'Laporan::index');
    $routes->get('laporan/cetak-peminjaman', 'Laporan::cetakPeminjaman');
    $routes->get('laporan/export-csv', 'Laporan::exportCsv');
});

$routes->group('', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->post('kategori/simpan', 'Kategori::store');
    $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
    $routes->post('kategori/hapus/(:num)', 'Kategori::delete/$1');

    $routes->post('lokasi/simpan', 'Lokasi::store');
    $routes->post('lokasi/update/(:num)', 'Lokasi::update/$1');
    $routes->post('lokasi/hapus/(:num)', 'Lokasi::delete/$1');

    $routes->get('barang/tambah', 'Barang::create');
    $routes->post('barang/simpan', 'Barang::store');
    $routes->get('barang/edit/(:num)', 'Barang::edit/$1');
    $routes->post('barang/update/(:num)', 'Barang::update/$1');
    $routes->post('barang/hapus/(:num)', 'Barang::delete/$1');

    $routes->post('demo/reset', 'Demo::reset');
});
