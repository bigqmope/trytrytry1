<?php
/**
 * controllers/AuthController.php
 *
 * Tugas:
 *  - Menangani proses autentikasi (login & logout).
 *  - Mengembalikan response JSON untuk request AJAX (login form di views/login.php memakai fetch()).
 *
 * Materi yang digunakan:
 *  - PHP Sessions (session_start(), session_regenerate_id(), session_destroy())
 *  - Keamanan password: password_verify() untuk memeriksa hash (materi: hashing password)
 *  - PDO melalui model User (prepared statements) (materi: PDO & prepared statements)
 *  - Arsitektur ringan MVC (controller memanggil model lalu memutuskan view/response)
 *
 * Catatan: perubahan ini **minimal** dan tidak merombak struktur project. Hanya melengkapi fungsi login/logout
 * supaya session dibuat dengan aman dan response konsisten untuk frontend.
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        // Jangan memulai session di konstruktor kecuali belum ada (tapi aman lakukan pemeriksaan)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Handle login:
     * - Menerima POST: username, password
     * - Memanggil model User->login()
     * - Jika sukses: regenerate session id, simpan user (tanpa password) ke $_SESSION['user']
     * - Kembalikan JSON { success: true } atau { success: false, message: ... }
     *
     * Alasan JSON: views/login.php saat ini menggunakan fetch() untuk submit form,
     * sehingga controller mengembalikan JSON agar frontend menampilkan pesan.
     */
    public function login() {
        // Pastikan method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Ambil input (form urlencoded atau JSON)
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        // juga coba baca raw JSON body jika frontend mengirim JSON
        if (!$username && !$password) {
            $raw = file_get_contents('php://input');
            if ($raw) {
                $json = json_decode($raw, true);
                if (is_array($json)) {
                    $username = $json['username'] ?? $username;
                    $password = $json['password'] ?? $password;
                }
            }
        }

        header('Content-Type: application/json; charset=utf-8');

        if (!$username || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username dan password wajib diisi']);
            return;
        }

        // Gunakan model User untuk proses autentikasi
        $user = $this->userModel->login($username, $password);

        if ($user === false) {
            // login gagal
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
            return;
        }

        // login sukses -> set session
        session_regenerate_id(true); // materi: session fixation prevention

        // simpan hanya data non-sensitive
        $_SESSION['user'] = [
            'id' => $user['id'] ?? null,
            'username' => $user['username'] ?? null,
            'nama' => $user['nama_lengkap'] ?? ($user['nama'] ?? null),
            'role' => $user['role'] ?? null,
        ];

        echo json_encode(['success' => true, 'message' => 'Login berhasil']);
    }

    /**
     * Logout:
     * - Hapus session user dan redirect ke halaman login.
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Hapus hanya data user, lalu destroy
        unset($_SESSION['user']);
        // optionally: clear cookie session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        // redirect ke login page (frontend expects redirect)
        header('Location: index.php?page=login');
        exit;
    }
}