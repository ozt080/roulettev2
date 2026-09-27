<?php

class ClasseModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère la liste de toutes les classes triées par nom.
     * 
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM classe ORDER BY nom ASC");
        return $stmt->fetchAll();
    }
}