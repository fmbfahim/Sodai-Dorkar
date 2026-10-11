<?php

namespace Core;

use PDO;
use PDOException;

class Database {
    private $connection;
    private $config;

    public function __construct($config) {
        $this->config = $config;
        $this->connect();
    }

    protected function connect() {
        $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['dbname']};charset=utf8mb4";
        try {
            $this->connection = new PDO($dsn, $this->config['user'], $this->config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 30,
            ]);
            // Increase session timeout to prevent idle disconnects during long cURL or image uploads
            @$this->connection->exec("SET SESSION wait_timeout = 300");
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    protected function isConnectionLost(PDOException $e): bool {
        $code = $e->errorInfo[1] ?? 0;
        $msg = $e->getMessage();
        return $code == 2006 || $code == 2013 
            || str_contains($msg, '2006') 
            || str_contains($msg, 'server has gone away') 
            || str_contains($msg, 'Lost connection');
    }

    public function query($query, $params = []) {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            if ($this->isConnectionLost($e)) {
                // Reconnect to MySQL server and retry query once
                $this->connect();
                $statement = $this->connection->prepare($query);
                $statement->execute($params);
                return $statement;
            }
            throw $e;
        }
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function getConnection() {
        try {
            $this->connection->query('SELECT 1');
        } catch (\Throwable $e) {
            $this->connect();
        }
        return $this->connection;
    }
}

