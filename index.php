<?php
// Autoload untuk load class otomatis dari /models dan /controllers
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/models/' . $className . '.php',
        __DIR__ . '/controllers/' . $className . '.php',
        __DIR__ . '/core/' . $className . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            if (!class_exists($className)) {
                require_once $path;
            }
            return;
        }
    }
});

// Mulai session untuk login
session_start();

// Cek 
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action) {
    // Routing untuk backend (controllers)
    switch ($action) {
        case 'login':
            $controller = new AuthController();
            $controller->login();
            break;
        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case 'searchPelanggan':
            require_once __DIR__ . '/models/Pelanggan.php';
            $pelangganModel = new Pelanggan();
            
            $keyword = $_GET['q'] ?? '';
            
            // Gunakan method getAll() yang sudah ada dan filter manual
            $allPelanggan = $pelangganModel->getAll();
            $results = [];
            
            foreach ($allPelanggan as $pelanggan) {
                if (stripos($pelanggan['nama'], $keyword) !== false || 
                    stripos($pelanggan['hp'], $keyword) !== false) {
                    $results[] = $pelanggan;
                }
            }
            
            header('Content-Type: application/json');
            echo json_encode($results);
            exit;

        case 'getKandangTersedia':
            require_once __DIR__ . '/models/Kandang.php';
            $kandangModel = new Kandang();
            
            $jenis = $_GET['jenis'] ?? '';
            $ukuran = $_GET['ukuran'] ?? '';
            
            $kandangTersedia = $kandangModel->getAvailableKandang($jenis, $ukuran);
            
            header('Content-Type: application/json');
            echo json_encode($kandangTersedia);
            exit;

        // TRANSAKSI ACTIONS     
        case 'createTransaksi':
            $controller = new TransaksiController();
            $controller->create();
            break;
        case 'checkoutTransaksi':
            $controller = new TransaksiController();
            $controller->checkout();
            break;

        default:
            echo json_encode(['error' => 'Action not found']);
            break;
    }
    exit;
}

// Jika tidak ada action, lanjut ke frontend routing (page)
$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'dashboard':
        include 'views/dashboard.php';
        break;
    case 'transaksi':
        $controller = new TransaksiController();
        $controller->index();
        break;
    case 'layanan':
        include 'views/layanan.php';
        break;
    case 'hewan':
        include 'views/hewan.php';
        break;
    case 'pemilik':
        include 'views/pelanggan.php';
        break;
    case 'laporan':
        include 'views/laporan.php';
        break;
    case 'login':
        include 'views/login.php';
        break;
    // Di section $page, tambahkan:
    case 'kandang':
        include 'views/kandang.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    default:
        include 'views/404.php';
        break;
}