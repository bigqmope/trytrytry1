<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pageTitle)) {
    $pageTitle = 'Sistem Penitipan Hewan';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- AdminLTE v4 CSS -->
    <!-- <link rel="stylesheet" href="public/dist/css/adminlte.css">
    <link rel="stylesheet" href="public/dist/css/custom.css"> -->

    <link rel="stylesheet" href="/public/dist/css/adminlte.css">
    <link rel="stylesheet" href="/public/dist/css/custom.css">
    <!-- Bootstrap Icons (ikon di sidebar/nav) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">

        <!-- HEADER / NAVBAR -->
        <nav class="app-header navbar navbar-expand bg-body border-bottom shadow-sm">
            <div class="container-fluid">

                <!-- Tombol toggle sidebar (kiri) -->
                <button class="navbar-toggler" type="button" data-lte-toggle="sidebar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Kosongkan sisi kiri, biar badge user di kanan -->
                <div class="flex-grow-1"></div>

                <!-- Badge user di pojok kanan -->
                <div class="d-flex align-items-center">
                    <div class="px-3 py-1 rounded-pill bg-primary text-white d-flex align-items-center">
                        <i class="bi bi-person-fill me-2"></i>
                        <span class="small">
                            <?= htmlspecialchars($_SESSION['user']['username'] ?? 'admin'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </nav>
        <!-- /HEADER -->

        <!-- SIDEBAR -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- MAIN CONTENT WRAPPER -->
        <main class="app-main">
            <div class="app-content p-3">
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const paketSelect = document.getElementById('paketSelect'); // select Layanan Utama
                        const dropdownBtn = document.getElementById('dropdownLayananTambahan'); // tombol dropdown
                        const labelSpan = document.getElementById('ltLabel'); // teks di tombol
                        const checkboxes = document.querySelectorAll('.lt-checkbox'); // semua checkbox
                        const hiddenContainer = document.getElementById('ltHiddenContainer'); // div untuk hidden input

                        // Kalau elemen tidak ada (misal di tab lain), jangan apa-apa
                        if (!dropdownBtn || !hiddenContainer) return;

                        // Fungsi sinkron pilihan checkbox -> hidden input + label
                        function syncLayananTambahan() {
                            hiddenContainer.innerHTML = ''; // reset

                            const selectedLabels = [];

                            checkboxes.forEach(cb => {
                                if (cb.checked) {
                                    // buat input hidden name="layanan_tambahan[]" untuk form
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'layanan_tambahan[]';
                                    input.value = cb.value;
                                    hiddenContainer.appendChild(input);

                                    // ambil teks label untuk ditampilkan
                                    const text = cb.nextElementSibling?.textContent.trim() || cb.value;
                                    selectedLabels.push(text);
                                }
                            });

                            if (selectedLabels.length === 0) {
                                labelSpan.textContent = 'Pilih layanan tambahan (opsional)';
                            } else if (selectedLabels.length === 1) {
                                labelSpan.textContent = selectedLabels[0];
                            } else {
                                labelSpan.textContent = selectedLabels.length + ' layanan dipilih';
                            }
                        }

                        // Saat checkbox berubah -> sync
                        checkboxes.forEach(cb => {
                            cb.addEventListener('change', syncLayananTambahan);
                        });

                        // ===== Hubungkan dengan Layanan Utama =====
                        if (paketSelect) {
                            // awal: kalau belum pilih layanan utama, "disable" dropdown
                            function updateDropdownState() {
                                const disabled = !paketSelect.value;

                                dropdownBtn.classList.toggle('disabled', disabled);
                                dropdownBtn.setAttribute('aria-disabled', disabled ? 'true' : 'false');

                                if (disabled) {
                                    // reset semua pilihan layanan tambahan
                                    checkboxes.forEach(cb => cb.checked = false);
                                    syncLayananTambahan();
                                }
                            }

                            paketSelect.addEventListener('change', updateDropdownState);
                            updateDropdownState(); // panggil sekali di awal
                        }

                        // Jalankan sync pertama kali
                        syncLayananTambahan();
                    });
                </script>