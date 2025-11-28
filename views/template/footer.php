        </div> <!-- /.app-content -->
    </main>

    <!-- FOOTER -->
    <footer class="app-footer border-top small text-muted py-2 px-3">
        <div class="d-flex justify-content-between">
            <span>&copy; <?= date('Y') ?> Sistem Penitipan Hewan</span>
            <span>Ketchua Gachor</span>
        </div>
    </footer>

</div> <!-- /.app-wrapper -->

<!-- Bootstrap 5 JS (WAJIB untuk modal, dropdown, dll) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<!-- AdminLTE v4 JS -->
<script src="public/dist/js/adminlte.js"></script>

<!-- Sidebar dropdown "Data" (Hewan / Pelanggan / Layanan) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // semua link yang punya submenu
    document.querySelectorAll('.has-dropdown').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const targetSelector = link.getAttribute('data-target');
            const menu = document.querySelector(targetSelector);
            if (!menu) return;

            // tutup submenu lain
            document.querySelectorAll('.submenu.show').forEach(function (sm) {
                if (sm !== menu) sm.classList.remove('show');
            });
            document.querySelectorAll('.has-dropdown.active-dropdown').forEach(function (lnk) {
                if (lnk !== link) lnk.classList.remove('active-dropdown');
            });

            // toggle submenu yang sedang diklik
            menu.classList.toggle('show');
            link.classList.toggle('active-dropdown');
        });
    });
});
</script>

<?php
if (!empty($extraScripts)) {
    echo $extraScripts;
}
?>

</body>
</html>
