<?php
$pageTitle  = 'Halaman Tidak Ditemukan';
$activeMenu = $activeMenu ?? '';
include __DIR__ . '/template/header.php';
?>

<div class="text-center py-5">
    <h1 class="display-5 fw-bold mb-3">404</h1>
    <p class="lead mb-4">Halaman yang kamu minta tidak ditemukan.</p>
    <a href="index.php?page=dashboard" class="btn btn-primary">
        Kembali ke Dashboard
    </a>
</div>

<?php include __DIR__ . '/template/footer.php'; ?>
