<?php
$pageTitle  = 'Data Layanan';
$activeMenu = 'layanan';
include __DIR__ . '/template/header.php';

// Load data dari database
require_once __DIR__ . '/../models/Layanan.php';
$layananModel = new Layanan();
$layananList = $layananModel->getAll();
?>

<h2 class="mb-3">Data Layanan</h2>

<!-- PAKET PENITIPAN -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="me-3">
                <h5 class="mb-1">Paket Penitipan</h5>
                <small class="text-white-50">
                    Paket utama yang dipilih saat pendaftaran penitipan.
                </small>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($layananList as $l): ?>
                <?php
                    $id       = $l['id_layanan'];
                    $nama     = htmlspecialchars($l['nama_layanan']);
                    $harga    = (int)$l['harga'];
                    $deskripsi = $l['deskripsi'] ?? '';
                    $detailList = explode("\n", $deskripsi);
                    $modalId  = 'modal_' . $id;
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="fw-semibold mb-0"><?= $nama; ?></h5>
                                <span class="badge bg-primary ms-2"><?= $id; ?></span>
                            </div>

                            <p class="fw-semibold mt-2 mb-1">
                                Rp <?= number_format($harga, 0, ',', '.'); ?>
                                <span class="small text-muted">/ hari</span>
                            </p>

                            <ul class="text-muted small ps-3 mb-3 flex-grow-1">
                                <?php foreach ($detailList as $index => $d): ?>
                                    <?php if (!empty(trim($d)) && $index < 3): ?>
                                        <li><?= htmlspecialchars($d); ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (count($detailList) > 3): ?>
                                    <li class="text-primary">... dan lainnya</li>
                                <?php endif; ?>
                            </ul>

                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-outline-primary btn-sm flex-fill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#<?= $modalId; ?>">
                                    <i class="bi bi-eye me-1"></i> Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Lihat Detail -->
                <div class="modal fade" id="<?= $modalId; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Layanan: <?= $id; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Layanan</label>
                                    <p class="form-control-plaintext"><?= $nama; ?></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Harga</label>
                                    <p class="form-control-plaintext">Rp <?= number_format($harga, 0, ',', '.'); ?> / hari</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Deskripsi Lengkap</label>
                                    <div class="border rounded p-3 bg-light">
                                        <ul class="mb-0">
                                            <?php foreach ($detailList as $d): ?>
                                                <?php if (!empty(trim($d))): ?>
                                                    <li><?= htmlspecialchars($d); ?></li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/template/footer.php'; ?>