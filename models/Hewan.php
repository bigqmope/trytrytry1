<?php
require_once __DIR__ . '/../core/Database.php';

class Hewan 
{
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }


    /**
     * Ambil semua data hewan (READ)
     */
    public function getAll()
{
    try {
        $sql = "SELECT 
                    h.id_hewan as id,
                    h.nama_hewan as nama, 
                    h.jenis,
                    h.ras,
                    h.ukuran,
                    h.warna,
                    h.catatan,
                    h.status,
                    p.nama_pelanggan as pemilik
                FROM hewan h
                LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
                ORDER BY h.id_hewan DESC"; // URUTKAN
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log("Error get all hewan: " . $e->getMessage());
        return [];
    }
}

    /**
     * Hitung statistik hewan (READ)
     */
    public function getSummary()
    {
        $sql = "SELECT COUNT(*) as total_hewan, SUM(CASE WHEN jenis = 'Kucing' THEN 1 ELSE 0 END) as total_kucing, SUM(CASE WHEN jenis = 'Anjing' THEN 1 ELSE 0 END) as total_anjing FROM hewan";
        $stmt = $this->db->query($sql); // FIX: Hapus $stmt->execute()
        return $stmt->fetch();
    }

    /**
     * Tambah hewan baru (CREATE)
     */
    public function create($data) {
    try {
        $sql = "INSERT INTO hewan (id_pelanggan, nama_hewan, jenis, ras, ukuran, warna, catatan, status) 
                VALUES (:id_pelanggan, :nama_hewan, :jenis, :ras, :ukuran, :warna, :catatan, :status)";
        
        // VALIDASI nilai ukuran sebelum insert
        $ukuran = $data['ukuran'] ?? '';
        $allowedUkuran = ['Kecil', 'Sedang', 'Besar']; // Sesuaikan dengan ENUM di database
        
        if (!in_array($ukuran, $allowedUkuran) && !empty($ukuran)) {
            error_log("Warning: Ukuran tidak valid: " . $ukuran);
            $ukuran = ''; // Set ke empty string atau default value
        }
        
        $params = [
            "id_pelanggan" => $data['id_pelanggan'],
            "nama_hewan" => $data['nama_hewan'],
            "jenis" => $data['jenis'],
            "ras" => $data['ras'] ?? '',
            "ukuran" => $ukuran, // Gunakan nilai yang sudah divalidasi
            "warna" => $data['warna'] ?? '',
            "catatan" => $data['catatan'] ?? '',
            "status" => $data['status'] ?? 'tersedia'
        ];
        
        error_log("Data hewan untuk INSERT: " . print_r($params, true));
        
        $result = $this->db->execute($sql, $params);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
        
    } catch (Exception $e) {
        error_log("Error create hewan: " . $e->getMessage());
        return false;
    }
}
    
    /**
     * Update data hewan (UPDATE)
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE hewan SET id_pelanggan = :id_pelanggan, nama_hewan = :nama_hewan, jenis = :jenis, ras = :ras, ukuran = :ukuran, warna = :warna, catatan = :catatan, status = :status WHERE id_hewan = :id";
            // FIX: Gunakan $this->db->execute()
            return $this->db->execute($sql, [/* ... params ... */]);

        } catch (Exception $e) { error_log("Error update hewan: " . $e->getMessage()); return false; }
    }

    /**
     * Cari hewan berdasarkan keyword (READ)
     */
    public function search($keyword)
    {
        $sql = "SELECT h.id_hewan as id, h.nama_hewan as nama, h.jenis, h.ras, p.nama_pelanggan as pemilik, p.no_hp as no_telp, h.catatan FROM hewan h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan WHERE h.nama_hewan LIKE :key OR h.jenis LIKE :key OR h.ras LIKE :key OR p.nama_pelanggan LIKE :key ORDER BY h.created_at DESC";
        $stmt = $this->db->query($sql, ["key" => "%{$keyword}%"]); // FIX: Hapus $stmt->execute()
        return $stmt->fetchAll();
    }
    
    /**
     * Ambil hewan berdasarkan jenis (READ)
     */
    public function getByJenis($jenis)
    {
        $sql = "SELECT h.id_hewan as id, h.nama_hewan as nama, h.jenis, h.ras, p.nama_pelanggan as pemilik, p.no_hp as no_telp, h.keterangan as catatan FROM hewan h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan WHERE h.jenis = :jenis ORDER BY h.nama_hewan";
        $params = ["jenis" => $jenis];
        $stmt = $this->db->query($sql, $params); // FIX: Hapus $stmt->execute()
        return $stmt->fetchAll();
    }

    /**
     * Ambil hewan berdasarkan pemilik (READ)
     */
    public function getByPemilik($id_pelanggan)
    {
        $sql = "SELECT h.id_hewan as id, h.nama_hewan as nama, h.jenis, h.ras, p.nama_pelanggan as pemilik, p.no_hp as no_telp, h.keterangan as catatan FROM hewan h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan WHERE h.id_pelanggan = :id_pelanggan ORDER BY h.nama_hewan";
        $params = ["id_pelanggan" => $id_pelanggan];
        $stmt = $this->db->query($sql, $params); // FIX: Hapus $stmt->execute()
        return $stmt->fetchAll();
    }

    /**
     * Ambil hewan yang tersedia (READ)
     */
    public function getAvailable()
    {
        $sql = "SELECT h.id_hewan as id, h.nama_hewan as nama, h.jenis, h.ras, p.nama_pelanggan as pemilik, p.no_hp as no_telp, h.keterangan as catatan FROM hewan h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan WHERE h.status = 'tersedia' ORDER BY h.nama_hewan";
        $stmt = $this->db->query($sql); // FIX: Hapus $stmt->execute()
        return $stmt->fetchAll();
    }

    /**
     * Ambil hewan yang sedang dititipkan (READ)
     */
    public function getInCare()
    {
        $sql = "SELECT h.id_hewan as id, h.nama_hewan as nama, h.jenis, h.ras, p.nama_pelanggan as pemilik, p.no_hp as no_telp, h.keterangan as catatan FROM hewan h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan WHERE h.status = 'sedang_dititipkan' ORDER BY h.nama_hewan";
        $stmt = $this->db->query($sql); // FIX: Hapus $stmt->execute()
        return $stmt->fetchAll();
    }

    /**
     * Hitung total hewan per status (READ)
     */
    public function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) as total FROM hewan WHERE status = :status";
        $stmt = $this->db->query($sql, ["status" => $status]); // FIX: Hapus $stmt->execute()
        return $stmt->fetch()['total'] ?? 0;
    }

    /**
     * Ambil data hewan dengan pagination (READ)
     */
    public function getWithPagination($limit = 10, $offset = 0)
    {
        $sql = "SELECT h.id_hewan as id, h.nama_hewan as nama, h.jenis, h.ras, p.nama_pelanggan as pemilik, p.no_hp as no_telp, h.keterangan as catatan FROM hewan h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan ORDER BY h.created_at DESC LIMIT :limit OFFSET :offset";

        $params = ["limit" => (int)$limit, "offset" => (int)$offset];
        $stmt = $this->db->query($sql, $params); // FIX: Hapus $stmt->execute()
        return $stmt->fetchAll();
    }

    /**
     * Hitung total hewan untuk pagination (READ)
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM hewan";
        $stmt = $this->db->query($sql); // FIX: Hapus $stmt->execute()
        return $stmt->fetch()['total'] ?? 0;
    }

    public function updateStatus($id, $status) {
        $allowed = ["tersedia", "sedang_dititipkan", "sudah_diambil"];
        if (!in_array($status, $allowed)) { $status = "tersedia"; }

        $sql = "UPDATE hewan SET status = :status WHERE id_hewan = :id";
        return $this->db->execute($sql, ["id" => $id, "status" => $status ]);
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}