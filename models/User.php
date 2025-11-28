<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Cek login user. Mengembalikan user array tanpa password jika sukses, atau false jika gagal.
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function login(string $username, string $password) {
        $sql = "SELECT id_user as id, username, password, nama_lengkap, role FROM user WHERE username = :username LIMIT 1";
        $stmt = $this->db->query($sql, ['username' => $username]);
        $user = $stmt->fetch();
        if (!$user) {
            return false;
        }

        // password stored as hashed. Gunakan password_verify
        if (password_verify($password, $user['password'])) {
            // jangan kembalikan field password
            unset($user['password']);
            return $user;
        }

        return false;
    }

    public function getById($id) {
        $sql = "SELECT id_user as id, username, nama_lengkap, role, created_at FROM user WHERE id_user = :id LIMIT 1";
        $stmt = $this->db->query($sql, ['id' => $id]);
        return $stmt->fetch();
}