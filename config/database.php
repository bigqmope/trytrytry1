<?php
/**
 * Database Connection Configuration (Hanya Menyimpan Data Konfigurasi Lokal)
 * Hapus semua logika getenv() / Railway.
 */

/**
 * Helper function - Mengembalikan array konfigurasi database lokal
 * @return array
 */
function getDatabaseConfig() {
    return [
        'host' => 'localhost',
        'port' => '3306',
        'dbname' => 'db_penitipan_hewan', // Pastikan nama DB ini benar
        'username' => 'root',
        'password' => 'Sh3Belajar!SQL', 
    ];
}
