<?php
// core/Database.php

class Database {
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;
    private $port;
    private $charset = 'utf8mb4';
    
    public function __construct() {
        // Ambil konfigurasi dari config/database.php
        require_once __DIR__ . '/../config/database.php';
        $cfg = getDatabaseConfig();

        $this->host = $cfg['host'] ?? '127.0.0.1';
        $this->port = $cfg['port'] ?? '3306';
        $this->database = $cfg['dbname'] ?? '';
        $this->username = $cfg['username'] ?? 'root';
        $this->password = $cfg['password'] ?? '';

        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Jangan tunjukkan detail error di production — untuk debug tampilkan sementara
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Jalankan query SELECT (mengembalikan PDOStatement)
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Jalankan perintah INSERT/UPDATE/DELETE
     * @param string $sql
     * @param array $params
     * @return int affected rows
     */
    public function execute(string $sql, array $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Dapatkan last insert id
     * @return string
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
}