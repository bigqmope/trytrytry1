<?php
// controllers/AuthController.php
require_once __DIR__ . '/../models/User.php'; 

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Menangani proses login
     */
    public function login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['error' => 'Username dan password harus diisi']);
            return;
        }

        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            // Set session
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            echo json_encode(['success' => 'Login berhasil', 'redirect' => 'index.php?page=dashboard']); 
            
        } else {
            echo json_encode(['error' => 'Username atau password salah']);
        }
    }

    /**
     * Menangani proses logout
     */
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
    
}