<?php
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion LDAP - Roulette</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 0.6rem;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        }
    </style>
</head>
<body>

<div class="login-card p-4">
    <div class="text-center mb-4">
        <div class="display-5 text-primary mb-2">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h4 class="fw-bold">Connexion LDAP</h4>
        <p class="text-muted small">Veuillez vous identifier pour accéder à l'application</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small fw-semibold mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <input type="hidden" name="action" value="login">
        
        <div class="mb-3">
            <label class="form-label fw-bold text-secondary small">Identifiant LDAP</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control" placeholder="ex: admin" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold text-secondary small">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom text-white w-100 mb-2">
            <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
        </button>
    </form>
</div>

</body>
</html>