<?php
// Kata sandi yang ingin di-hash
$password_plain = 'password123';

// Jalankan fungsi hashing
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

// Tampilkan hasilnya
echo $hashed_password;

// Contoh hasil yang akan ditampilkan: $2y$10$................................
?>