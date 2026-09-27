<?php

class StudentModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Récupère les étudiants d'une classe avec leur moyenne calculée dynamiquement.
     */
    public function getByClasse(int $classeId): array {
        $stmt = $this->db->prepare("
            SELECT s.*, AVG(o.note) AS average 
            FROM student s 
            LEFT JOIN obtenir o ON s.id = o.id_etudiant 
            WHERE s.id_classe = :id_classe 
            GROUP BY s.id, s.nom, s.prenom 
            ORDER BY s.nom ASC, s.prenom ASC
        ");
        $stmt->execute(['id_classe' => $classeId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les évaluations d'une classe.
     */
    public function getEvaluationsByClasse(int $classeId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM evaluation 
            WHERE id_classe = :id_classe 
            ORDER BY date_evaluation DESC
        ");
        $stmt->execute(['id_classe' => $classeId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère uniquement les étudiants éligibles pour le tirage au sort.
     */
    public function getEligibleStudents(int $classeId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM student 
            WHERE id_classe = :id_classe 
              AND absence = 0 
              AND bool = 0
        ");
        $stmt->execute(['id_classe' => $classeId]);
        return $stmt->fetchAll();
    }

    /**
     * Enregistre une note et met à jour l'état de passage de l'étudiant.
     */
    public function addGrade(int $studentId, int $evaluationId, float $note): bool {
        // Enregistre ou met à jour la note dans la table 'obtenir'
        $stmt = $this->db->prepare("
            INSERT INTO obtenir (id_etudiant, id_evaluation, note) 
            VALUES (:id_etudiant, :id_evaluation, :note)
            ON DUPLICATE KEY UPDATE note = VALUES(note)
        ");
        $stmt->execute([
            'id_etudiant' => $studentId,
            'id_evaluation' => $evaluationId,
            'note' => $note
        ]);

        // Marque l'étudiant comme "passé" (bool = 1) et incrémente son compteur
        $updateBool = $this->db->prepare("
            UPDATE student 
            SET bool = 1, passage = passage + 1 
            WHERE id = :id
        ");
        return $updateBool->execute(['id' => $studentId]);
    }

    /**
     * Met à jour une note existante pour un étudiant et une évaluation.
     */
    public function updateGrade(int $studentId, int $evaluationId, float $note): bool {
        $stmt = $this->db->prepare("
            UPDATE obtenir 
            SET note = :note 
            WHERE id_etudiant = :id_etudiant AND id_evaluation = :id_evaluation
        ");
        return $stmt->execute([
            'note' => $note,
            'id_etudiant' => $studentId,
            'id_evaluation' => $evaluationId
        ]);
    }

    /**
     * Supprime une note/passage et réinitialise le statut de passage de l'étudiant.
     */
    public function deleteGrade(int $studentId, int $evaluationId): bool {
        // 1. Supprimer l'entrée dans la table 'obtenir'
        $stmt = $this->db->prepare("
            DELETE FROM obtenir 
            WHERE id_etudiant = :id_etudiant AND id_evaluation = :id_evaluation
        ");
        $deleted = $stmt->execute([
            'id_etudiant' => $studentId,
            'id_evaluation' => $evaluationId
        ]);

        // 2. Remet l'étudiant disponible (bool = 0) et décrémente son compteur de passage
        if ($deleted) {
            $updateStudent = $this->db->prepare("
                UPDATE student 
                SET bool = 0, passage = GREATEST(0, passage - 1) 
                WHERE id = :id
            ");
            $updateStudent->execute(['id' => $studentId]);
        }

        return $deleted;
    }

    /**
     * Modifie l'état de présence/absence d'un étudiant.
     */
    public function toggleAbsence(int $studentId, int $absenceStatus): bool {
        $stmt = $this->db->prepare("UPDATE student SET absence = :absence WHERE id = :id");
        return $stmt->execute([
            'absence' => $absenceStatus,
            'id' => $studentId
        ]);
    }

    /**
     * Réinitialise le statut de passage des étudiants d'une classe.
     */
    public function resetPassages(int $classeId): bool {
        $stmt = $this->db->prepare("UPDATE student SET bool = 0 WHERE id_classe = :id_classe");
        return $stmt->execute(['id_classe' => $classeId]);
    }

    public function addStudent($nom, $prenom, $idClasse) {
        $stmt = $this->db->prepare("INSERT INTO student (nom, prenom, id_classe, absence, passage) VALUES (?, ?, ?, 0, 0)");
        return $stmt->execute([$nom, $prenom, $idClasse]);
    }
}