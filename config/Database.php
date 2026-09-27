<?php

class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'roulettev2';
        $username = 'root';
        $password = ''; // Laragon par défaut n'a pas de mot de passe root

        try {
            $this->connection = new PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion à la BDD : " . $e->getMessage());
        }
    }

    // Empêche le clonage de l'instance Singleton
    private function __clone() {}

    // Empêche la désérialisation de l'instance Singleton
    public function __wakeup() {
        throw new \Exception("Impossible de désérialiser un Singleton.");
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}