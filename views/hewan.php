<?php
$pageTitle = 'Data Hewan';
$activeMenu = 'hewan';
include __DIR__ . '/template/header.php';

// Load data
require_once __DIR__ . '/../models/Hewan.php';
require_once __DIR__ . '/../models/Kandang.php';

$hewanModel = new Hewan();
$kandangModel = new Kandang();

$hewanList = $hewanModel->getAll();
$kandangCounts = $kandangModel->countByType();
$totalkandangKecil = $kandangCounts['Kecil'] ?? 0;
$totalkandangSedang = $kandangCounts['Sedang'] ?? 0;
$totalkandangBesar = $kandangCounts['Besar'] ?? 0;
?>

<h2 class="mb-3">Data Hewan</h2>

<!-- STATISTICS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <i class="bi bi-house-door text-primary fs-4"></i>
                <h5 class="mt-2 mb-1"><?= $totalkandangKecil ?></h5>
                <small class="text-muted">Kandang Kecil Tersedia</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <i class="bi bi-house text-success fs-4"></i>
                <h5 class="mt-2 mb-1"><?= $totalkandangSedang ?></h5>
                <small class="text-muted">Kandang Sedang Tersedia</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body text-center py-3">
                <i class="bi bi-house-fill text-warning fs-4"></i>
                <h5 class="mt-2 mb-1"><?= $totalkandangBesar ?></h5>
                <small class="text-muted">Kandang Besar Tersedia</small>
            </div>
        </div>
    </div>
</div>

<!-- TABEL DATA HEWAN -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Hewan</th>
                        <th style="width: 100px;">Jenis</th>
                        <th>Ras</th>
                        <th style="width: 100px;">Ukuran</th>
                        <th>Warna</th>
                        <th>Catatan</th>
                        <th style="width: 120px;">Status</th>
                    </tr>
                </thead>
                <tbody id="hewanTableBody">
                    <?php if (empty($hewanList)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox fs-3 mb-1"></i>
                                    <span>Belum ada data hewan.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($hewanList as $h): ?>
                            <tr>
                                <td class="text-muted"><?= $counter++; ?></td>
                                <td><?= htmlspecialchars($h['nama'] ?? ''); ?></td>
                                <td>
                                    <?php 
                                    $jenis = $h['jenis'] ?? '';
                                    $badgeClass = $jenis === 'Kucing' ? 'bg-info' : 'bg-warning';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($jenis); ?></span>
                                </td>
                                <td><?= htmlspecialchars($h['ras'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $ukuran = $h['ukuran'] ?? '';
                                    $ukuranClass = '';
                                    if ($ukuran === 'Kecil') $ukuranClass = 'bg-success';
                                    if ($ukuran === 'Sedang') $ukuranClass = 'bg-warning';
                                    if ($ukuran === 'Besar') $ukuranClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $ukuranClass ?>"><?= htmlspecialchars($ukuran); ?></span>
                                </td>
                                <td><?= htmlspecialchars($h['warna'] ?? '-'); ?></td>
                                <td class="small"><?= htmlspecialchars($h['catatan'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $status = $h['status'] ?? '';
                                    $statusClass = 'bg-secondary';
                                    if ($status === 'tersedia') $statusClass = 'bg-success';
                                    if ($status === 'sedang_dititipan') $statusClass = 'bg-warning';
                                    if ($status === 'sudah_diambil') $statusClass = 'bg-info';
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($status); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/template/footer.php'; ?>