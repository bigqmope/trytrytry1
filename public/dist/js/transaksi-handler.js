// public/dist/js/transaksi-handler.js

document.addEventListener('DOMContentLoaded', function() {
    console.log("=== SCRIPT TRANSAKSI DIMULAI (External) ===");

    // Akses variabel global yang didefinisikan di PHP
    const kandangTersedia = (typeof PHP_DATA !== 'undefined' && PHP_DATA.kandangTersedia) ? PHP_DATA.kandangTersedia : [];
    const hewanMenginap = (typeof PHP_DATA !== 'undefined' && PHP_DATA.hewanMenginap) ? PHP_DATA.hewanMenginap : [];
    
    // ELEMEN UTAMA
    const selectPelanggan = document.getElementById('selectPelanggan');
    const noHpInput = document.getElementById('p_hp');
    const alamatInput = document.getElementById('p_alamat');
    const newCustomerFields = document.getElementById('newCustomerFields');
    const namaBaruInput = document.querySelector('[name="nama_pelanggan_baru"]');
    
    const paketSelect = document.getElementById('paketSelect');
    const lamaInapInput = document.getElementById('lamaInap');
    const totalHargaElement = document.getElementById('totalHarga');
    const totalInput = document.getElementById('totalInput');
    const detailPerhitungan = document.getElementById('detailPerhitungan');
    const paketInfo = document.getElementById('paketInfo');

    const btnPilihKandang = document.getElementById('btnPilihKandang');
    const panelKandang = document.getElementById('panelKandang');
    const kandangLabel = document.getElementById('kandangLabel');
    const idKandangInput = document.getElementById('id_kandang');
    const kandangInfo = document.getElementById('kandangInfo');
    const jenisHewanSelect = document.getElementById('jenisHewanSelect');
    const ukuranHewanSelect = document.getElementById('ukuranHewanSelect');

    const formPendaftaran = document.getElementById('formPendaftaran');

    // Elemen Pengembalian (Checkout)
    const searchCheckout = document.getElementById('searchCheckout');
    const filterKandang = document.getElementById('filterKandang');
    const btnCariCheckout = document.getElementById('btnCariCheckout');
    const tabelHewanMenginapBody = document.querySelector('.table-responsive table tbody');


    // =============================================
    // Fungsi Utility
    // =============================================
    function formatRupiah(angka) {
        return 'Rp ' + (angka || 0).toLocaleString('id-ID');
    }

    // =============================================
    // 1. AUTO-FILL & LOGIKA PELANGGAN (FIX: Masalah Dropdown)
    // =============================================
    if (selectPelanggan && newCustomerFields && namaBaruInput) {
        
        function toggleNewCustomerFields(isNew) {
            if (isNew) {
                // Tampilkan field nama baru
                newCustomerFields.style.display = 'block';
                namaBaruInput.required = true;
                namaBaruInput.disabled = false;
                
                // Kosongkan HP dan Alamat saat membuat baru
                noHpInput.value = '';
                alamatInput.value = '';

            } else {
                // Sembunyikan field nama baru
                newCustomerFields.style.display = 'none';
                namaBaruInput.required = false;
                namaBaruInput.disabled = true;
                namaBaruInput.value = ''; 
            }
            noHpInput.required = true;
            alamatInput.required = true;
        }

        selectPelanggan.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value === 'new') {
                toggleNewCustomerFields(true); 
                
            } else if (selectedOption.value) {
                toggleNewCustomerFields(false);

                // Auto-fill data pelanggan yang dipilih (Menggunakan data-attribute)
                noHpInput.value = selectedOption.dataset.hp || '';
                alamatInput.value = selectedOption.dataset.alamat || '';
                
            } else {
                toggleNewCustomerFields(false);
                noHpInput.value = '';
                alamatInput.value = '';
            }
        });
        
        // Inisialisasi awal
        toggleNewCustomerFields(selectPelanggan.value === 'new');
    }

    // =============================================
    // 2. KALKULASI TOTAL HARGA
    // =============================================
    function hitungTotal() {
        let total = 0;
        let hargaPaket = 0;
        let lamaInap = 1;
        let namaPaket = '';
        
        lamaInap = parseInt(lamaInapInput ? lamaInapInput.value : 1) || 1;
        // Pastikan lamaInap tidak kurang dari 1
        if (lamaInap < 1) {
            lamaInap = 1;
            if(lamaInapInput) lamaInapInput.value = 1;
        }

        if (paketSelect && paketSelect.value) {
            const selectedOption = paketSelect.options[paketSelect.selectedIndex];
            hargaPaket = parseInt(selectedOption.getAttribute('data-harga')) || 0;
            namaPaket = selectedOption.getAttribute('data-nama') || '';
            
            total = hargaPaket * lamaInap;
            
            if (paketInfo) {
                paketInfo.innerHTML = `
                    <strong>${namaPaket}</strong><br>
                    <small>Harga: ${formatRupiah(hargaPaket)} / hari</small>
                `;
            }
        } else {
            if (paketInfo) {
                paketInfo.textContent = 'Pilih paket untuk melihat detail';
            }
        }

        if (totalHargaElement) {
            totalHargaElement.textContent = formatRupiah(total);
        }
        if (totalInput) {
            totalInput.value = total;
        }
        if (detailPerhitungan) {
            if (hargaPaket > 0) {
                detailPerhitungan.textContent = `${formatRupiah(hargaPaket)} × ${lamaInap} hari`;
            } else {
                detailPerhitungan.textContent = '-';
            }
        }
    }

    if (paketSelect) {
        paketSelect.addEventListener('change', hitungTotal);
    }
    if (lamaInapInput) {
        lamaInapInput.addEventListener('input', hitungTotal);
    }
    hitungTotal();

// =============================================
// 3. PEMILIHAN KANDANG (FIXED - TIDAK AUTO-TRIGGER)
// =============================================
if (btnPilihKandang) {
    
    function filterKandang() {
        const jenisHewan = jenisHewanSelect.value;
        const ukuranHewan = ukuranHewanSelect ? ukuranHewanSelect.value : '';
        
        console.log("Filter kandang - Jenis:", jenisHewan, "Ukuran:", ukuranHewan);
        
        if (!jenisHewan) {
            alert('Pilih jenis hewan terlebih dahulu');
            panelKandang.classList.add('d-none');
            return;
        }
        
        // LOGICA FILTER (tetap sama seperti sebelumnya)
        let tipeKandangYangCocok = [];
        
        if (jenisHewan === 'Kucing') {
            if (ukuranHewan === 'Kecil') {
                tipeKandangYangCocok = ['Kecil', 'Sedang', 'Besar'];
            } else if (ukuranHewan === 'Sedang') {
                tipeKandangYangCocok = ['Sedang', 'Besar'];
            } else if (ukuranHewan === 'Besar') {
                tipeKandangYangCocok = ['Besar'];
            } else {
                tipeKandangYangCocok = ['Kecil', 'Sedang', 'Besar'];
            }
        } else if (jenisHewan === 'Anjing') {
            if (ukuranHewan === 'Kecil') {
                tipeKandangYangCocok = ['Sedang', 'Besar'];
            } else if (ukuranHewan === 'Sedang') {
                tipeKandangYangCocok = ['Sedang', 'Besar'];
            } else if (ukuranHewan === 'Besar') {
                tipeKandangYangCocok = ['Besar'];
            } else {
                tipeKandangYangCocok = ['Sedang', 'Besar'];
            }
        }

        console.log("Tipe kandang yang cocok:", tipeKandangYangCocok);

        // Tampilkan loading sementara
        panelKandang.innerHTML = `
            <div class="text-center py-2">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span class="text-muted">Memuat kandang tersedia...</span>
            </div>
        `;
        panelKandang.classList.remove('d-none');
        
        setTimeout(() => {
            panelKandang.innerHTML = '';
            let kandangDitemukan = false;
            
            kandangTersedia.forEach(kandang => {
                const kodeKandang = kandang.kode_kandang;
                const tipeKandang = kandang.tipe;
                const statusKandang = kandang.status;
                const idKandang = kandang.id;
                
                // Filter berdasarkan tipe kandang yang cocok DAN status tersedia
                if (statusKandang === 'tersedia' && tipeKandangYangCocok.includes(tipeKandang)) { 
                    kandangDitemukan = true;
                    
                    const kandangItem = document.createElement('div');
                    kandangItem.className = 'p-2 border-bottom';
                    kandangItem.style.cursor = 'pointer';
                    kandangItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold">${kodeKandang}</span>
                                <small class="text-muted ms-2">${tipeKandang}</small>
                            </div>
                            <span class="badge bg-success">Tersedia</span>
                        </div>
                    `;
                    
                    kandangItem.addEventListener('click', function() {
                        kandangLabel.textContent = `${kodeKandang} - ${tipeKandang}`;
                        idKandangInput.value = idKandang; 
                        panelKandang.classList.add('d-none');
                        kandangInfo.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Kandang ${kodeKandang} dipilih</span>`;
                        validateKandang();
                    });
                    
                    panelKandang.appendChild(kandangItem);
                }
            });
            
            if (!kandangDitemukan) {
                panelKandang.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-inbox display-6 opacity-50"></i>
                        <p class="mt-2 mb-0">Tidak ada kandang tersedia</p>
                        <small>Untuk ${jenisHewan} ${ukuranHewan ? 'ukuran ' + ukuranHewan : ''}</small>
                    </div>
                `;
                // Reset pilihan jika tidak ada yang ditemukan
                if (idKandangInput.value) {
                     resetKandangPilihan();
                }
            }
        }, 300);
    }

    btnPilihKandang.addEventListener('click', function(e) {
        e.preventDefault();
        filterKandang();
    });

    function validateKandang() {
        if (idKandangInput.value) {
            btnPilihKandang.classList.remove('btn-outline-secondary');
            btnPilihKandang.classList.add('btn-outline-success');
        } else {
            btnPilihKandang.classList.remove('btn-outline-success');
            btnPilihKandang.classList.add('btn-outline-secondary');
        }
    }

    function resetKandangPilihan() {
        idKandangInput.value = '';
        kandangLabel.textContent = 'Pilih kandang yang tersedia';
        kandangInfo.innerHTML = 'Pilih kandang yang sesuai dengan jenis dan ukuran hewan';
        panelKandang.classList.add('d-none');
        validateKandang();
    }

    // hanya reset pilihan, TIDAK auto-trigger filter
    if (jenisHewanSelect) {
        jenisHewanSelect.addEventListener('change', resetKandangPilihan);
    }
    if (ukuranHewanSelect) {
        ukuranHewanSelect.addEventListener('change', resetKandangPilihan);
    }
    
    validateKandang();
}
    // 4. FORM SUBMIT HANDLER & VALIDASI PENDAFTARAN

    if (formPendaftaran) {
        
        function validateForm(event) {
            // 1. Validasi bawaan browser (required fields)
            if (!formPendaftaran.checkValidity()) {
                 return; // Biarkan browser menampilkan error bawaan
            }

            // 2. Validasi Kandang
            if (!idKandangInput.value) {
                event.preventDefault(); // Hentikan submit
                alert('Pilih kandang terlebih dahulu untuk menyelesaikan pendaftaran.');
                btnPilihKandang.focus();
                return;
            }

            // 3. Validasi Total Biaya
            if (parseInt(totalInput.value) <= 0) {
                 event.preventDefault(); // Hentikan submit
                alert('Total biaya tidak valid. Pastikan Paket Utama dan Lama Inap sudah diisi dengan benar.');
                paketSelect.focus();
                return;
            }
        }
        
        formPendaftaran.addEventListener('submit', validateForm);
    }
    
    // =============================================
    // 5. FITUR CHECKOUT (PENCARIAN & FILTER)
    // =============================================
    
    // Fungsi untuk memformat tanggal ke format d/m/Y
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'});
    }
    
    // Fungsi untuk merender ulang tabel
    function renderHewanMenginap(data) {
        if (!tabelHewanMenginapBody) return;

        tabelHewanMenginapBody.innerHTML = ''; // Kosongkan tabel
        
        if (data.length === 0) {
            tabelHewanMenginapBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-4 text-muted opacity-50"></i>
                        <p class="mt-3 mb-0">Tidak ada hewan yang sesuai kriteria pencarian</p>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(hewan => {
            const hewanIcon = hewan.jenis_hewan === 'Kucing' ? 'bi-cat text-info' : 'bi-dog text-warning';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="fw-semibold">${hewan.kode_transaksi}</td>
                <td>${hewan.nama_pelanggan}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi ${hewanIcon} me-2"></i>
                        ${hewan.nama_hewan}
                    </div>
                </td>
                <td><span class="badge bg-secondary">${hewan.kode_kandang}</span></td>
                <td>${formatDate(hewan.tanggal_masuk)}</td>
                <td>${hewan.durasi} hari</td>
                <td class="fw-semibold text-primary">${formatRupiah(hewan.total_biaya)}</td>
                <td>
                    <button type="button" class="btn btn-success btn-sm" onclick="prosesCheckout('${hewan.id_transaksi}')">
                        <i class="bi bi-check-lg me-1"></i>Check-out
                    </button>
                </td>
            `;
            tabelHewanMenginapBody.appendChild(row);
        });
    }
    
    // Fungsi Pencarian/Filter
    function applyCheckoutFilter() {
        const searchTerm = searchCheckout.value.toLowerCase();
        const selectedKandang = filterKandang.value;
        
        const filteredData = hewanMenginap.filter(hewan => {
            // Filter 1: Pencarian
            const matchesSearch = hewan.nama_pelanggan.toLowerCase().includes(searchTerm) || 
                                  hewan.nama_hewan.toLowerCase().includes(searchTerm);
            
            // Filter 2: Kandang
            const matchesKandang = !selectedKandang || 
                                   (selectedKandang === 'KK' && (hewan.kode_kandang.startsWith('KK'))) ||
                                   (selectedKandang === 'KB' && (hewan.kode_kandang.startsWith('KB')));
                                   
            return matchesSearch && matchesKandang;
        });
        
        renderHewanMenginap(filteredData);
    }

    if (btnCariCheckout) {
        btnCariCheckout.addEventListener('click', applyCheckoutFilter);
    }
    
    // Juga terapkan filter saat input atau select berubah (Opsional: agar lebih interaktif)
    if (searchCheckout) {
        searchCheckout.addEventListener('input', applyCheckoutFilter);
    }
    if (filterKandang) {
        filterKandang.addEventListener('change', applyCheckoutFilter);
    }
    
    // Tambahkan fungsi global untuk proses checkout dari button di tabel
    window.prosesCheckout = function(id_transaksi) {
    console.log("Proses checkout untuk ID: " + id_transaksi);
    
    if (confirm(`Apakah Anda yakin ingin menyelesaikan transaksi (Check-out) untuk transaksi ID: ${id_transaksi}?`)) {
        // Arahkan ke action controller untuk proses check-out
        console.log("Redirect ke checkout...");
        window.location.href = `index.php?action=checkoutTransaksi&id=${id_transaksi}`;
    }
}
    
    // Panggil saat DOMContentLoaded untuk memastikan data awal ditampilkan (jika tab pengembalian aktif)
    // Cek apakah tabel hewan menginap ada di halaman.
    if(tabelHewanMenginapBody) {
         applyCheckoutFilter();
    }
    
});