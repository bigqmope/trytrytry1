<?php
$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';
include __DIR__ . '/template/header.php';

// Load data
require_once __DIR__ . '/../models/Hewan.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Kandang.php';

$hewanModel = new Hewan();
$transaksiModel = new Transaksi();
$kandangModel = new Kandang();

// Hitung data
$totalHewanDititipkan = $transaksiModel->getTotalHewanAktif();
$totalKucingDititipkan = $transaksiModel->getTotalHewanAktifByJenis('Kucing');
$totalAnjingDititipkan = $transaksiModel->getTotalHewanAktifByJenis('Anjing');
$kandangTersedia = $kandangModel->countByType();
?>

<h2 class="mb-4">Dashboard</h2>

<!-- STATISTICS CARDS -->
<div class="row g-4 mb-5">
    <!-- Total Hewan Dititipkan -->
    <div class="col-lg-4 col-md-6">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><?= $totalHewanDititipkan ?></h4>
                        <p class="mb-0 small">Total Hewan Dititipkan</p>
                    </div>
                    <i class="bi bi-grid-3x3-gap fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Kucing Dititipkan -->
    <div class="col-lg-4 col-md-6">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><?= $totalKucingDititipkan ?></h4>
                        <p class="mb-0 small">Kucing Dititipkan</p>
                    </div>
                    <i class="bi bi-bootstrap fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Anjing Dititipkan -->
    <div class="col-lg-4 col-md-6">
        <div class="card bg-warning text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><?= $totalAnjingDititipkan ?></h4>
                        <p class="mb-0 small">Anjing Dititipkan</p>
                    </div>
                    <i class="bi bi-calendar-week fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KANDANG TERSEKSA -->
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-house-door text-primary fs-1"></i>
                <h3 class="mt-2"><?= $kandangTersedia['Kecil'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Kandang Kecil Tersedia</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-house text-success fs-1"></i>
                <h3 class="mt-2"><?= $kandangTersedia['Sedang'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Kandang Sedang Tersedia</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-house-fill text-warning fs-1"></i>
                <h3 class="mt-2"><?= $kandangTersedia['Besar'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Kandang Besar Tersedia</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/template/footer.php'; ?>