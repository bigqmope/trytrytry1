<?php
// views/transaksi.php
// FILE INI HANYA MENAMPILKAN DATA (VIEW)

$pageTitle = 'Transaksi Penitipan Hewan';
$activeMenu = 'transaksi';

// Nilai default dari Controller (Wajib)
$pelangganList = $pelangganList ?? [];
$paketList = $paketList ?? [];
$kandangTersedia = $kandangTersedia ?? [];
$hewanMenginap = $hewanMenginap ?? []; 
$tab = $tab ?? ($_GET['tab'] ?? 'pendaftaran');

include __DIR__ . '/template/header.php';
?>

<?php if (isset($_GET['status'])): ?>
    <div class='alert alert-<?= $_GET['status'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show'>
        <?php if ($_GET['status'] === 'success'): ?>
            <strong>Sukses!</strong> <?= htmlspecialchars($_GET['message'] ?? 'Transaksi berhasil dibuat') ?>
        <?php else: ?>
            <strong>Error!</strong> <?= htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan') ?>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                    <a class="nav-link <?= $tab === 'pendaftaran' ? 'active' : '' ?>" href="index.php?page=transaksi&tab=pendaftaran">
                <i class="bi bi-box-arrow-in-down me-2"></i>Pendaftaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab === 'pengembalian' ? 'active' : '' ?>"
                href="index.php?page=transaksi&tab=pengembalian">
                <i class="bi bi-box-arrow-up me-2"></i>Pengembalian
            </a>
        </li>
    </ul>

            </div>

            <div class="card-body">

                <?php if ($tab === 'pendaftaran'): ?>
                    <h5 class="mb-3">Form Pendaftaran Penitipan</h5>
                    <form method="post" action="index.php?action=createTransaksi" id="formPendaftaran">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card p-3 h-100 position-relative">
                                    <h6 class="mb-3 text-primary">Informasi Pemilik</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                                        <select name="id_pelanggan" class="form-select" id="selectPelanggan" required>
                                            <option value="">-- Pilih Pemilik --</option>
                                            <?php foreach ($pelangganList as $p): ?>
                                                <option value="<?= htmlspecialchars($p['id']) ?>" 
                                                    data-hp="<?= htmlspecialchars($p['hp']) ?>" 
                                                    data-alamat="<?= htmlspecialchars($p['alamat']) ?>">
                                                    <?= htmlspecialchars($p['nama']) ?> (<?= htmlspecialchars($p['hp']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                            <option value="new">+ Tambah Pemilik Baru</option>
                                        </select>
                                        <small class="text-muted">Pilih dari daftar pelanggan terdaftar</small>
                                    </div>

                                        <div class="mb-3" id="newCustomerFields" style="display: none;">
                                                <label class="form-label">Nama Pemilik Baru <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_pelanggan_baru" class="form-control" 
                                                    placeholder="Ketik nama pemilik baru" required>
                                            </div>

                                            <!-- Field untuk pelanggan existing (jika menggunakan search/autocomplete) -->
                                            <input type="hidden" name="search_pemilik" id="search_pemilik">
                                            
                                            <!-- Field lainnya -->
                                            <div class="mb-3">
                                                <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                                <input type="text" name="no_hp" id="p_hp" class="form-control"
                                                    placeholder="Contoh: 08123456789" required>
                                            </div>

                                    <div class="mb-3">
                                        <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                        <textarea name="alamat" id="p_alamat" class="form-control"
                                            rows="2" placeholder="Alamat lengkap pemilik" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card p-3 h-100">
                                    <h6 class="mb-3 text-primary">Informasi Hewan</h6>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Hewan <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_hewan" class="form-control"
                                            placeholder="Contoh: Mochi, Blacky" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jenis Hewan <span class="text-danger">*</span></label>
                                        <select name="jenis_hewan" class="form-select" id="jenisHewanSelect" required>
                                            <option value="">-- Pilih Hewan --</option>
                                            <option value="Kucing">Kucing</option>
                                            <option value="Anjing">Anjing</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ras</label>
                                        <input type="text" name="ras" class="form-control"
                                            placeholder="Contoh: Persia, Siberian Husky">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Ukuran</label>
                                        <select name="ukuran" class="form-select" id="ukuranHewanSelect">
                                            <option value="">-- Pilih Ukuran --</option>
                                            <option value="Kecil">Kecil</option>
                                            <option value="Sedang">Sedang</option>
                                            <option value="Besar">Besar</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Warna</label>
                                        <input type="text" name="warna" class="form-control"
                                            placeholder="Contoh: Putih, Hitam-Putih">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Catatan Khusus</label>
                                        <textarea name="catatan" class="form-control" rows="2"
                                            placeholder="Alergi, penyakit, kebiasaan khusus, dll."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card p-3">
                                    <h6 class="mb-3 text-primary">Layanan</h6>

                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label class="form-label">Paket Utama <span class="text-danger">*</span></label>
                                            <select name="id_layanan" class="form-select" id="paketSelect" required>
                                                <option value="">-- Pilih Paket --</option>
                                                <?php foreach ($paketList as $pk): ?>
                                                    <option value="<?= $pk['id_layanan'] ?>" 
                                                        data-harga="<?= $pk['harga'] ?>" 
                                                        data-nama="<?= htmlspecialchars($pk['nama_layanan']) ?>">
                                                        <?= htmlspecialchars($pk['nama_layanan']) ?>
                                                        - Rp <?= number_format($pk['harga'], 0, ',', '.'); ?>/hari
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text">Pilih salah satu paket penitipan</div>
                                        </div>

                                        <div class="col-lg-8">
                                            <div class="alert alert-info mt-4">
                                                <h6>Info Paket:</h6>
                                                <div id="paketInfo">Pilih paket untuk melihat detail</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="small text-muted">Total Estimasi Biaya</div>
                                                <h3 id="totalHarga" class="fw-bold text-primary mb-0">Rp 0</h3>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block" id="detailPerhitungan">-</small>
                                            </div>
                                        </div>
                                        <input type="hidden" name="total_biaya" id="totalInput" value="0">
                                        <div class="small text-muted mt-1">
                                            Total = (harga paket × lama inap)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="card p-3">
                                    <h6 class="mb-3 text-primary">Detail Penitipan</h6>

                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                            <input type="date" name="tanggal_masuk" class="form-control"
                                                value="<?= date('Y-m-d') ?>" required>
                                        </div>

                                        <div class="col-lg-4">
                                            <label class="form-label">Lama Inap (hari) <span class="text-danger">*</span></label>
                                            <input type="number" name="durasi" class="form-control"
                                                min="1" value="1" required id="lamaInap">
                                        </div>

                                        <div class="col-lg-4">
                                            <label class="form-label">Kandang <span class="text-danger">*</span></label>
                                            <button type="button"
                                                class="btn btn-outline-secondary text-start w-100 d-flex justify-content-between align-items-center"
                                                id="btnPilihKandang">
                                                <span id="kandangLabel">Pilih kandang yang tersedia</span>
                                                <i class="bi bi-chevron-down ms-2 small"></i>
                                            </button>

                                            <div id="panelKandang" class="border rounded p-2 mt-1 d-none"
                                                style="max-height: 200px; overflow-y: auto;">
                                                <div class="text-center">
                                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <span class="text-muted">Memuat kandang tersedia...</span>
                                                </div>
                                            </div>

                                            <input type="hidden" name="id_kandang" id="id_kandang">
                                                <small class="text-muted d-block mt-1" id="kandangInfo">
                                                    Pilih kandang: 
                                                    <span id="kandangRuleInfo">
                                                        <?php 
                                                        // Info default
                                                        echo "Kucing kecil: semua kandang | Kucing sedang: sedang & besar | Kucing besar: besar saja | ";
                                                        echo "Anjing kecil/sedang: sedang & besar | Anjing besar: besar saja";
                                                        ?>
                                                    </span>
                                                </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>

                <?php else: ?>
                    <h5 class="mb-3">Form Pengembalian Hewan</h5>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3 text-primary">Cari Hewan yang Sedang Menginap</h6>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Cari berdasarkan Nama Pemilik atau Hewan</label>
                                    <input type="text" id="searchCheckout" class="form-control" placeholder="Ketik nama pemilik atau hewan...">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Kandang</label>
                                    <select id="filterKandang" class="form-select">
                                        <option value="">Semua Kandang</option>
                                        <option value="KK">Kandang Kecil (KK)</option>
                                        <option value="KB">Kandang Besar (KB)</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" id="btnCariCheckout">
                                        <i class="bi bi-search me-2"></i>Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0 text-primary">Daftar Hewan yang Sedang Menginap</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Transaksi</th>
                                            <th>Pemilik</th>
                                            <th>Hewan</th>
                                            <th>Kandang</th>
                                            <th>Tgl Masuk</th>
                                            <th>Lama Inap</th>
                                            <th>Total Biaya</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($hewanMenginap)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox display-4 text-muted opacity-50"></i>
                                                    <p class="mt-3 mb-0">Tidak ada hewan yang sedang menginap</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($hewanMenginap as $hewan): ?>
                                                <tr>
                                                    <td class="fw-semibold"><?= htmlspecialchars($hewan['kode_transaksi']); ?></td>
                                                    <td><?= htmlspecialchars($hewan['nama_pelanggan']); ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php $hewanIcon = $hewan['jenis_hewan'] === 'Kucing' ? 'bi-cat text-info' : 'bi-dog text-warning'; ?>
                                                            <i class="bi <?= $hewanIcon; ?> me-2"></i>
                                                            <?= htmlspecialchars($hewan['nama_hewan']); ?>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($hewan['kode_kandang']); ?></span></td>
                                                    <td><?= date('d/m/Y', strtotime($hewan['tanggal_masuk'])); ?></td>
                                                    <td><?= $hewan['durasi']; ?> hari</td>
                                                    <td class="fw-semibold text-primary">Rp <?= number_format($hewan['total_biaya'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-success btn-sm" onclick="prosesCheckout('<?= $hewan['id_transaksi']; ?>')">
                                                            <i class="bi bi-check-lg me-1"></i>Check-out
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Menyediakan data PHP ke variabel JavaScript global
    const PHP_DATA = {
        kandangTersedia: <?= json_encode($kandangTersedia ?? []) ?>,
        hewanMenginap: <?= json_encode($hewanMenginap ?? []) ?>
    };
</script>
<script src="public/dist/js/transaksi-handler.js"></script> 

<?php include __DIR__ . '/template/footer.php'; ?>