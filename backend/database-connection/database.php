<?php


class Database {
    private $db_conn;
    private $DB_HOST = 'localhost';
    private $DB_PORT = '5432';
    private $DB_NAME = 'postgres';
    private $DB_USER = 'test_user';
    private $DB_PASS = 'pruebas';


    public function getConnection() {
    if ($this->db_conn === null) {
        try {
            $url = "pgsql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_NAME}";
            $this->db_conn = new PDO($url, $this->DB_USER, $this->DB_PASS);
            $this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
        return $this->db_conn;
    }
}