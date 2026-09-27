<?php
require_once __DIR__ . '/../m/StudentModel.php';
require_once __DIR__ . '/../m/ClasseModel.php';

class StudentController {
    private $studentModel;
    private $classeModel;

    public function __construct() {
        $this->studentModel = new StudentModel();
        $this->classeModel = new ClasseModel();
    }

    public function index() {
        // Sécurité : Vérification de l'authentification LDAP
        if (empty($_SESSION['user_logged_in'])) {
            require_once __DIR__ . '/../v/loginView.php';
            exit();
        }

        $classes = $this->classeModel->getAll();

        // Stockage / Récupération de la classe dans la session
        if (isset($_REQUEST['classe_id'])) {
            $_SESSION['classe_id'] = (int)$_REQUEST['classe_id'];
        }
        $selectedClasseId = $_SESSION['classe_id'] ?? ($classes[0]['id'] ?? 1);

        // Récupération des évaluations de la classe sélectionnée
        $evaluations = $this->studentModel->getEvaluationsByClasse($selectedClasseId);

        // Stockage / Récupération de l'évaluation dans la session
        if (isset($_REQUEST['eval_id'])) {
            $_SESSION['eval_id'] = (int)$_REQUEST['eval_id'];
        }
        $selectedEvalId = $_SESSION['eval_id'] ?? ($evaluations[0]['id_evaluation'] ?? null);

        // Action : Tirer un étudiant au sort (Roulette)
        $selectedStudent = null;
        if (isset($_POST['action']) && $_POST['action'] === 'draw') {
            $eligibleStudents = $this->studentModel->getEligibleStudents($selectedClasseId);
            if (!empty($eligibleStudents)) {
                $randomIndex = array_rand($eligibleStudents);
                $selectedStudent = $eligibleStudents[$randomIndex];
            }
        }

        // Action : Enregistrer une note
        if (isset($_POST['action']) && $_POST['action'] === 'save_grade') {
            $studentId = (int)$_POST['student_id'];
            $evalId = (int)$_POST['eval_id'];
            $note = (float)$_POST['note'];

            // Sécurité : Vérifier si l'evalId appartient bien aux évaluations valides, sinon utiliser la première disponible
            $validEvalIds = array_column($evaluations, 'id_evaluation');
            if (!in_array($evalId, $validEvalIds) && !empty($validEvalIds)) {
                $evalId = $validEvalIds[0];
            }

            $this->studentModel->addGrade($studentId, $evalId, $note);
            header("Location: index.php");
            exit();
        }

        // Action : Mettre à jour ou supprimer un passage / une note
        if (isset($_POST['action']) && $_POST['action'] === 'update_or_delete_grade') {
            $studentId = (int)$_POST['student_id'];
            $evalId = (int)$_POST['eval_id'];

            // Si l'utilisateur a cliqué sur "Supprimer le passage"
            if (isset($_POST['delete_grade']) && $_POST['delete_grade'] === '1') {
                $this->studentModel->deleteGrade($studentId, $evalId);
            } 
            // Sinon, s'il a saisi une nouvelle note
            else {
                $newNote = trim($_POST['new_note'] ?? '');
                if ($newNote !== '') {
                    $this->studentModel->updateGrade($studentId, $evalId, (float)$newNote);
                }
            }

            header("Location: index.php");
            exit();
        }

        // Action : Basculer l'absence
        if (isset($_POST['action']) && $_POST['action'] === 'toggle_absence') {
            $studentId = (int)$_POST['student_id'];
            $newStatus = (int)$_POST['absence'];

            $this->studentModel->toggleAbsence($studentId, $newStatus);
            header("Location: index.php");
            exit();
        }

        // Action : Réinitialiser les passages
        if (isset($_POST['action']) && $_POST['action'] === 'reset') {
            $this->studentModel->resetPassages($selectedClasseId);
            header("Location: index.php");
            exit();
        }

        // Action : Ajouter un nouvel élève
        if (isset($_POST['action']) && $_POST['action'] === 'add_student') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');

            if (!empty($nom) && !empty($prenom)) {
                $this->studentModel->addStudent($nom, $prenom, $selectedClasseId);
            }
            header("Location: index.php");
            exit();
        }

        $students = $this->studentModel->getByClasse($selectedClasseId);

        require_once __DIR__ . '/../v/homepageView.php';
    }
}