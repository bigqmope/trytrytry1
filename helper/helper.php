<?php
// helper/helper.php
// Helper global yang dibutuhkan semua file (model & controller)

class Helper {

    // --- Sanitasi input (Encapsulation utility) ---
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    // --- Format Rupiah ---
    public static function rupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    // --- Hitung selisih hari (durasi penitipan) ---
    public static function hitungHari($mulai, $selesai) {
        $start = new DateTime($mulai);
        $end   = new DateTime($selesai);
        return $start->diff($end)->days;
    }

    // --- Validasi tanggal ---
    public static function validTanggal($tanggal) {
        return (bool) strtotime($tanggal);
    }

    // --- Redirect aman ---
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }

    // --- Load file view ---
    public static function view($path, $data = []) {
        extract($data);
        include __DIR__ . '/../views/' . $path . '.php';
    }

    // --- Debug ---
    public static function dd($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }
}

function clean($value) {
    return Helper::sanitize($value);
}

function number_only($value) {
    return floatval(preg_replace('/[^0-9.]/', '', $value));
}
