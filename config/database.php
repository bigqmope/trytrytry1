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
        'host' => '127.0.0.1',
        'port' => '3306',
        'dbname' => 'db_penitipan_hewan', // Pastikan nama DB ini benar
        'username' => 'root',
        'password' => '', 
    ];
}
