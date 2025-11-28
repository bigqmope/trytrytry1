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
        // 1. Include konfigurasi database
        require_once __DIR__ . '/../config/database.php';
        
        // 2. Ambil konfigurasi
        $config = getDatabaseConfig();
        
        // 3. Simpan properti
        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['dbname'];
        $this->port = $config['port'];
        
        $this->connect();
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage() . 
                "<br>Host: {$this->host}, DB: {$this->database}, Port: {$this->port}");
        }
    }
    
    /**
     * WRAPPER untuk SELECT/READ (Menggunakan prepare dan execute secara internal)
     */
    public function query($sql, $params = []) {
        try {
            // FIX: Menggunakan prepare dan execute di sini
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage() . " - SQL: " . $sql);
        }
    }
    
    /**
     * WRAPPER untuk CUD (CREATE, UPDATE, DELETE)
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die("Execute failed: " . $e->getMessage() . " - SQL: " . $sql);
        }
    }
    
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