<?php
require_once __DIR__ . '/../core/Database.php';

/**
 * Model User
 * Untuk autentikasi user saat login (Kriteria Fungsionalitas)
 */
class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * LOGIN - Autentikasi user (READ)
     * * @param string $username
     * @param string $password (plain text)
     * @return array|false User data atau false jika gagal
     */
    public function login($username, $password) {
        $sql = "SELECT * FROM user WHERE username = :username LIMIT 1";
        
        // Menggunakan query() wrapper dengan parameter
        $stmt = $this->db->query($sql, ['username' => $username]);
        $user = $stmt->fetch();
        
        // Cek password (diasumsikan password di database di-hash)
        if ($user && password_verify($password, $user['password'])) {
            $user['id'] = $user['id_user']; // FIX: Tambahkan key 'id' untuk konsistensi
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * GET BY ID (READ - Digunakan untuk memuat data user ke session)
     * * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT id_user as id, username, nama_lengkap, role, created_at FROM user WHERE id_user = :id";
        
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->fetch();
    }
}