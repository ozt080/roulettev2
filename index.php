<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/src/services/LdapService.php';
require_once __DIR__ . '/src/c/StudentController.php';

// Action 1 : Traitement de la déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Action 2 : Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation directe pour ton compte de test
    if ($username === 'student1245' && $password === 'Berkancan08.') {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    }

    // Sinon, tentative de connexion LDAP classique
    $ldapService = new LdapService();

    if ($ldapService->authenticate($username, $password)) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Identifiants LDAP incorrects ou serveur indisponible.";
        require_once __DIR__ . '/src/v/loginView.php';
        exit();
    }
}

// Vérification de sécurité : si l'utilisateur n'est pas connecté, on affiche la vue de connexion
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    require_once __DIR__ . '/src/v/loginView.php';
    exit();
}

// Action 3 : Affichage par défaut de l'application via le contrôleur si connecté
$controller = new StudentController();
$controller->index();