<?php
// Pastikan file Database.php sudah di-include di sini
require_once 'Database.php'; 

/**
 * Kelas Dasar (Induk) untuk semua Model.
 * Menerapkan Pewarisan (Inheritance) dan Encapsulation.
 */
abstract class BaseModel {
    protected $db; 
    protected $tableName; 
    protected $primaryKey = 'id';

    public function __construct() {
        // Instansiasi koneksi Database (Encapsulation)
        $this->db = new Database(); 
    }

    // [Contoh metode CRUD yang diwariskan]

    public function find($id) {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }
    
    // Asumsi: Di model anak seperti Hewan.php, Anda harus menambahkan metode 'create'
    // yang menggunakan $this->db->execute() dan mengembalikan $this->db->lastInsertId()
}