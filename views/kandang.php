<?php
$pageTitle = 'Data Kandang';
$activeMenu = 'kandang';
include __DIR__ . '/template/header.php';

require_once __DIR__ . '/../models/Kandang.php';
$kandangModel = new Kandang();
$kandangList = $kandangModel->getAllKandang(); // Method baru untuk ambil semua kandang
?>

<h2 class="mb-3">Data Kandang</h2>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Kandang</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kandangList)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-inbox fs-3 mb-1"></i>
                                    <span>Belum ada data kandang.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($kandangList as $k): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($k['kode_kandang']); ?></td>
                                <td>
                                    <?php 
                                    $tipe = $k['tipe'] ?? '';
                                    $tipeClass = '';
                                    if ($tipe === 'Kecil') $tipeClass = 'bg-success';
                                    if ($tipe === 'Sedang') $tipeClass = 'bg-warning';
                                    if ($tipe === 'Besar') $tipeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $tipeClass ?>"><?= htmlspecialchars($tipe); ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $status = $k['status'] ?? '';
                                    $statusClass = '';
                                    if ($status === 'tersedia') $statusClass = 'bg-success';
                                    if ($status === 'terpakai') $statusClass = 'bg-warning';
                                    if ($status === 'maintenance') $statusClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($status); ?></span>
                                </td>
                                <td class="small"><?= htmlspecialchars($k['catatan'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/template/footer.php'; ?>