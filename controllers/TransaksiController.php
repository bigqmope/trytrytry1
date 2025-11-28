<?php
require_once __DIR__ . '/../core/BaseController.php'; 
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/DetailTransaksi.php';
require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Hewan.php';
require_once __DIR__ . '/../models/Kandang.php';
require_once __DIR__ . '/../models/Layanan.php';

class TransaksiController extends BaseController {
    
    private $transaksiModel;
    private $detailTransaksiModel;
    private $pelangganModel;
    private $hewanModel;
    private $kandangModel;
    private $layananModel;

    public function __construct() {
        $this->transaksiModel = new Transaksi();
        $this->pelangganModel = new Pelanggan();
        $this->hewanModel = new Hewan(); 
        $this->detailTransaksiModel = new DetailTransaksi();
        $this->kandangModel = new Kandang();
        $this->layananModel = new Layanan();
    }
        
    public function index() {
        error_log("=== TRANSAKSI CONTROLLER INDEX DIPANGGIL ===");
        
        $tab = $_GET['tab'] ?? 'pendaftaran';
        
        // DEBUG: Cek data pelanggan
        $pelangganList = $this->pelangganModel->getAll();
        error_log("Jumlah pelanggan: " . count($pelangganList));
        
        $data = [
            'tab' => $tab,
            'pelangganList' => $pelangganList, 
            'paketList' => $this->layananModel->getAll(),
            'kandangTersedia' => $this->kandangModel->getAll(),
            'hewanMenginap' => $this->transaksiModel->getActiveTransactions(),
        ];

        $this->view('transaksi', $data); 
    }


/**
 * Menangani proses Check-in (CRUD → Create)
 */
public function create() {
    try {
        // Validasi data 
        if (empty($_POST['id_layanan']) || empty($_POST['id_kandang']) || empty($_POST['nama_hewan'])) {
            throw new Exception("Data required tidak lengkap");
        }

        // Handle Pelanggan
        $id_pelanggan = $this->handlePelanggan($_POST);
        
        // Handle Hewan
        $id_hewan = $this->handleHewan($_POST, $id_pelanggan);
        
        // Hitung biaya
        $biayaData = $this->hitungBiaya($_POST);
        
        // 4. Prepare transaksi data
        $transaksiData = [
            'id_pelanggan' => $id_pelanggan,
            'id_hewan' => $id_hewan,
            'id_kandang' => $_POST['id_kandang'],
            'id_layanan' => $_POST['id_layanan'],
            'biaya_paket' => $biayaData['biaya_paket'],
            'tanggal_masuk' => $_POST['tanggal_masuk'] ?? date('Y-m-d'),
            'durasi' => $_POST['durasi'] ?? 1,
            'total_biaya' => $biayaData['total_biaya']
        ];


        // Create transaksi
        $id_transaksi = $this->transaksiModel->create($transaksiData);
        if (!$id_transaksi) {
            throw new Exception("Gagal membuat transaksi utama");
        }

        // BUAT DETAIL TRANSAKSI (paket utama + layanan tambahan)
        $this->handleDetailTransaksi($_POST, $id_transaksi, $biayaData);
        
        // Update status kandang dan hewan
        $this->kandangModel->updateStatus($transaksiData['id_kandang'], 'terpakai');
        $this->hewanModel->updateStatus($id_hewan, 'sedang_dititipan');
        

        // custom message
        $this->redirect('index.php?page=transaksi&status=success&tab=pendaftaran&id=' . $id_transaksi . '&message=' . urlencode('Penitipan hewan berhasil didaftarkan'));
    } catch (Exception $e) {
        error_log("Error in create transaksi: " . $e->getMessage());
        $this->redirect('index.php?page=transaksi&status=error&message=' . urlencode($e->getMessage()) . '&tab=pendaftaran');
    }
}

/**
 * Handle detail transaksi (PAKET UTAMA + layanan tambahan)
 */
private function handleDetailTransaksi($data, $id_transaksi, $biayaData) {
    error_log("=== MEMBUAT DETAIL TRANSAKSI ===");
    
    // A. DETAIL PAKET UTAMA (selalu dibuat)
    $paket = $this->layananModel->getById($data['id_layanan']);
    if ($paket) {
        $detailPaket = [
            'id_transaksi' => $id_transaksi,
            'kode_layanan' => 'PAKET', // atau kode khusus untuk paket
            'nama_layanan' => $paket['nama_layanan'] . ' (' . ($data['durasi'] ?? 1) . ' hari)',
            'harga' => $paket['harga'],
            'quantity' => $data['durasi'] ?? 1,
            'subtotal' => $biayaData['biaya_paket']
        ];
        
        $this->detailTransaksiModel->create($detailPaket);
        error_log("Detail paket dibuat: " . print_r($detailPaket, true));
    }
    
    // B. DETAIL LAYANAN TAMBAHAN (jika ada)
    if (!empty($data['layanan_tambahan'])) {
        $layananTambahanList = [
            'G001' => ['nama' => 'Grooming Dasar', 'harga' => 100000],
            'G002' => ['nama' => 'Grooming Lengkap', 'harga' => 170000],
            'L003' => ['nama' => 'Vitamin / Suplemen', 'harga' => 50000],
            'L004' => ['nama' => 'Vaksin', 'harga' => 260000]
        ];
        
        foreach ($data['layanan_tambahan'] as $kodeLayanan) {
            if (isset($layananTambahanList[$kodeLayanan])) {
                $layanan = $layananTambahanList[$kodeLayanan];
                
                $detailData = [
                    'id_transaksi' => $id_transaksi,
                    'kode_layanan' => $kodeLayanan,
                    'nama_layanan' => $layanan['nama'],
                    'harga' => $layanan['harga'],
                    'quantity' => 1,
                    'subtotal' => $layanan['harga']
                ];
                
                $this->detailTransaksiModel->create($detailData);
                error_log("Detail layanan tambahan dibuat: " . print_r($detailData, true));
            }
        }
    }
    
    error_log("Total detail transaksi dibuat untuk ID: " . $id_transaksi);
}

    /**
    * Hitung biaya transaksi
    */
    private function hitungBiaya($data) {
        // Ambil harga paket dari database
        $paket = $this->layananModel->getById($data['id_layanan']);
        
        if (!$paket) {
            throw new Exception("Paket layanan tidak ditemukan dengan ID: " . $data['id_layanan']);
        }
        
        $hargaPaket = floatval($paket['harga']);
        $durasi = intval($data['durasi']) ?: 1;
        $biayaPaket = $hargaPaket * $durasi;
        
        // Hitung biaya layanan tambahan
        $biayaTambahan = 0;
        if (!empty($data['layanan_tambahan'])) {
            $layananTambahanList = [
                'G001' => 100000, 
                'G002' => 170000, 
                'L003' => 50000, 
                'L004' => 260000 
            ];
            
            foreach ($data['layanan_tambahan'] as $kodeLayanan) {
                if (isset($layananTambahanList[$kodeLayanan])) {
                    $biayaTambahan += $layananTambahanList[$kodeLayanan];
                }
            }
        }
        
        $totalBiaya = $biayaPaket + $biayaTambahan;
        
        return [
            'biaya_paket' => $biayaPaket,
            'biaya_tambahan' => $biayaTambahan,
            'total_biaya' => $totalBiaya
        ];
    }


    private function handlePelanggan($data) {
    $id_pelanggan = $data['id_pelanggan'] ?? null;
    
    // Jika id_pelanggan ada dan bukan 'new', gunakan pelanggan existing
    if (!empty($id_pelanggan) && $id_pelanggan !== 'new') {
        return $id_pelanggan;
    }
    
    // Buat pelanggan baru - PERBAIKI: Ambil nama dari field yang benar
    $nama_pelanggan = '';
    
    if (!empty($data['nama_pelanggan_baru'])) {
        // Jika menggunakan form "Tambah Pemilik Baru"
        $nama_pelanggan = $data['nama_pelanggan_baru'];
    } else if (!empty($data['search_pemilik'])) {
        // Jika menggunakan autocomplete/search (fallback)
        $nama_pelanggan = $data['search_pemilik'];
    } else {
        // Jika kedua-duanya kosong, throw error
        throw new Exception("Nama pelanggan harus diisi");
    }
    
    $pelangganData = [
        'nama_pelanggan' => $nama_pelanggan,
        'no_hp' => $data['no_hp'] ?? '',
        'alamat' => $data['alamat'] ?? ''
    ];

    error_log("Data pelanggan baru: " . print_r($pelangganData, true));

    // Model harus mengembalikan ID baru jika berhasil
    $newPelangganId = $this->pelangganModel->create($pelangganData); 
    if ($newPelangganId) {
        return $newPelangganId;
    } else {
        throw new Exception("Gagal membuat pelanggan baru");
    }
}

    private function handleHewan($data, $id_pelanggan) {
        // PERBAIKAN: Gunakan 'jenis_hewan' sesuai dengan form
        $hewanData = [
            'id_pelanggan' => $id_pelanggan,
            'nama_hewan' => $data['nama_hewan'] ?? '',
            'jenis' => $data['jenis_hewan'] ?? '', // PERBAIKAN: dari 'jenis' jadi 'jenis_hewan'
            'ras' => $data['ras'] ?? '',
            'ukuran' => $data['ukuran'] ?? '',
            'warna' => $data['warna'] ?? '',
            'catatan' => $data['catatan'] ?? '',
            'status' => 'tersedia' 
        ];

        // Create hewan dan ambil ID-nya
        $result = $this->hewanModel->create($hewanData);
        if ($result) {
            return $this->hewanModel->getLastInsertId();
        } else {
            throw new Exception("Gagal membuat data hewan");
        }
    }

/**
 * Menangani proses Check-out (CRUD → Update Status)
 */
public function checkout() {
    $id_transaksi = $_GET['id'] ?? $_POST['id'] ?? null;
        
    if (!$id_transaksi) {
        $this->redirect('index.php?page=transaksi&status=error&tab=pengembalian&message=ID+transaksi+tidak+valid');
        return;
    }

    try {
        // 1. Update status transaksi menjadi 'completed'
        $result = $this->transaksiModel->checkout($id_transaksi);
        
        if ($result) {            
            // 2. Ambil data transaksi untuk update kandang dan hewan
            $transaksi = $this->transaksiModel->getById($id_transaksi);
            
            if ($transaksi) {
                // 3. Update status kandang menjadi 'tersedia'
                if (!empty($transaksi['id_kandang'])) {
                    $this->kandangModel->updateStatus($transaksi['id_kandang'], 'tersedia');
                }
                
                // 4. Update status hewan menjadi 'sudah_diambil'
                if (!empty($transaksi['id_hewan'])) {
                    $this->hewanModel->updateStatus($transaksi['id_hewan'], 'sudah_diambil');
                }
            }
            
            // Redirect ke halaman sukses
            $this->redirect('index.php?page=transaksi&status=success&tab=pengembalian&message=' . urlencode('Checkout berhasil - Hewan sudah dikembalikan'));   
        } else {
            throw new Exception("Gagal melakukan checkout di database");
        }

    } catch (Exception $e) {
        error_log("Error in checkout transaksi: " . $e->getMessage());
        $this->redirect('index.php?page=transaksi&status=error&tab=pengembalian&message=' . urlencode($e->getMessage()));
    }
}


}

?>