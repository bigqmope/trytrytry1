<?php
require_once __DIR__ . '/../core/Database.php';
// pelanggan
class Pelanggan
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Ambil semua data pelanggan (READ)
     * Menggunakan alias: id, nama, hp (Wajib sesuai yang dicari di View)
     */
    public function getAll() {
    $sql = "SELECT p.id_pelanggan as id, p.kode_pelanggan as kode, p.nama_pelanggan as nama, p.no_hp as hp, p.alamat 
            FROM pelanggan p 
            ORDER BY p.id_pelanggan DESC"; // URUTKAN DARI TERBARU
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}
    
    /**
     * Tambah pelanggan baru (CREATE)
     */
    public function create($data) {
    try {
        $kode = $this->generateKodePelanggan();
        
        // VALIDASI: Pastikan nama_pelanggan tidak kosong
        $nama_pelanggan = trim($data['nama_pelanggan'] ?? '');
        if (empty($nama_pelanggan)) {
            throw new Exception("Nama pelanggan tidak boleh kosong");
        }
        
        $sql = "INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, no_hp, alamat) 
                VALUES (:kode_pelanggan, :nama_pelanggan, :no_hp, :alamat)";

        $result = $this->db->execute($sql, [
            "kode_pelanggan" => $kode,
            "nama_pelanggan" => $nama_pelanggan,
            "no_hp" => $data['no_hp'] ?? '',
            "alamat" => $data['alamat'] ?? '',
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;

    } catch (Exception $e) {
        error_log("Error create pelanggan: " . $e->getMessage());
        return false;
    }
}

    
    /**
     * Generate kode pelanggan otomatis (Helper)
     */
    private function generateKodePelanggan()
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(kode_pelanggan, 4) AS UNSIGNED)) as max_number FROM pelanggan WHERE kode_pelanggan LIKE 'PLG%'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $nextNumber = ($result['max_number'] ?? 0) + 1;
        return 'PLG' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Hitung total pelanggan (READ)
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM pelanggan";
        $stmt = $this->db->query($sql); // FIX: Hapus $stmt->execute() yang berlebihan
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Ambil data untuk dropdown (READ)
     */
    public function getForDropdown()
    {
        $sql = "SELECT p.id_pelanggan as id, p.kode_pelanggan as kode, p.nama_pelanggan as nama
                FROM pelanggan p
                ORDER BY p.nama_pelanggan";

        $stmt = $this->db->query($sql); // FIX: Hapus $stmt->execute() yang berlebihan
        return $stmt->fetchAll();
    }

    /**
     * Cek apakah no HP sudah terdaftar (READ)
     */
    public function isPhoneExists($no_hp, $exclude_id = null)
    {
        $sql = "SELECT COUNT(*) as total FROM pelanggan WHERE no_hp = :no_hp";
        
        $params = ["no_hp" => $no_hp];

        if ($exclude_id) {
            $sql .= " AND id_pelanggan != :exclude_id";
            $params["exclude_id"] = $exclude_id;
        }

        $stmt = $this->db->query($sql, $params); // FIX: Gunakan query($sql, $params)
        $result = $stmt->fetch();
        return ($result['total'] ?? 0) > 0;
    }
    
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
}