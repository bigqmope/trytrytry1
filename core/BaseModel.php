<?php
require_once __DIR__ . '/Database.php';

abstract class BaseModel {
    protected $db;
    protected $tableName;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = new Database();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->query($sql, ['id' => $id])->fetch();
    }

    public function getAll($limit = 1000, $offset = 0) {
        $sql = "SELECT * FROM {$this->tableName} LIMIT :offset, :limit";
        // PDO tidak mengizinkan binding parameter untuk LIMIT dengan named params dalam beberapa driver,
        // jadi kita cast ke int langsung (ensure safe)
        $offset = (int)$offset;
        $limit = (int)$limit;
        $stmt = $this->db->query("SELECT * FROM {$this->tableName} LIMIT {$offset}, {$limit}");
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    // Model spesifik harus menyediakan create/update sesuai kolom
}