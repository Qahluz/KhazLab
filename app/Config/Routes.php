<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rute Autentikasi Publik
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login/auth', 'Auth::attemptLogin');
$routes->post('logout', 'Auth::logout');

// Rute Terproteksi Administrator
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // Modul Kategori
    $routes->get('kategori', 'Kategori::index');
    $routes->post('kategori/simpan', 'Kategori::store');
    $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
    $routes->post('kategori/hapus/(:num)', 'Kategori::delete/$1');

    // Modul Lokasi
    $routes->get('lokasi', 'Lokasi::index');
    $routes->post('lokasi/simpan', 'Lokasi::store');
    $routes->post('lokasi/update/(:num)', 'Lokasi::update/$1');
    $routes->post('lokasi/hapus/(:num)', 'Lokasi::delete/$1');

    // Modul Data Barang & Pencarian
    $routes->get('barang', 'Barang::index');
    $routes->get('barang/tambah', 'Barang::create');
    $routes->post('barang/simpan', 'Barang::store');
    $routes->get('barang/edit/(:num)', 'Barang::edit/$1');
    $routes->post('barang/update/(:num)', 'Barang::update/$1');
    $routes->post('barang/hapus/(:num)', 'Barang::delete/$1');

    // Modul Peminjaman & Pengembalian
    $routes->get('peminjaman', 'Peminjaman::index');
    $routes->get('peminjaman/tambah', 'Peminjaman::create');
    $routes->post('peminjaman/simpan', 'Peminjaman::store');
    $routes->post('peminjaman/kembalikan/(:num)', 'Peminjaman::returnItem/$1');

    // Modul Laporan
    $routes->get('laporan', 'Laporan::index');
});
