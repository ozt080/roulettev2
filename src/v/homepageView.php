<?php
$selectedClasseId = $selectedClasseId ?? null;
$selectedEvalId = $selectedEvalId ?? null;
$selectedStudent = $selectedStudent ?? null;
$students = $students ?? [];
$classes = $classes ?? [];
$evaluations = $evaluations ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la Roulette</title>
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
        }

        /* Header Gradient */
        .app-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #ffffff;
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Modern Cards */
        .custom-card {
            border: none;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        .card-title-custom {
            font-size: 1.1rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Roulette Button */
        .btn-roulette {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
        }

        .btn-roulette:hover:not(:disabled) {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .btn-roulette:active:not(:disabled) {
            transform: scale(0.98);
        }

        /* Student Card Animation */
        .student-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px dashed #cbd5e1;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        .selected-name {
            font-size: 1.75rem;
            font-weight: 800;
            color: #4f46e5;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Table Styling */
        .custom-table {
            margin-bottom: 0;
        }

        .custom-table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }

        .custom-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Custom Form Controls */
        .form-select-custom, .form-control-custom {
            border-radius: 0.6rem;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 1rem;
            font-weight: 500;
        }

        .form-select-custom:focus, .form-control-custom:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }
    </style>
</head>
<body>

<div class="container py-5">

    <!-- En-tête de l'application avec bouton de déconnexion -->
    <div class="app-header mb-4 text-center position-relative">
        <?php if (!empty($_SESSION['username'])): ?>
            <a href="index.php?action=logout" class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-3 fw-semibold rounded-2">
                <i class="bi bi-box-arrow-right me-1"></i> Déconnexion (<?= htmlspecialchars($_SESSION['username']) ?>)
            </a>
        <?php endif; ?>
        <h1 class="fw-bold mb-1"><i class="bi bi-dice-5-fill me-2"></i>Gestion de la Roulette</h1>
        <p class="text-white-50 mb-0">Sélection aléatoire, suivi de présence et gestion des notes</p>
    </div>

    <!-- Sélecteurs Classe & Évaluation -->
    <div class="custom-card p-4 mb-4">
        <form method="POST" action="index.php" class="row g-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary mb-1">
                    <i class="bi bi-mortarboard-fill me-1 text-primary"></i> Classe
                </label>
                <select name="classe_id" class="form-select form-select-custom" onchange="this.form.submit()">
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $selectedClasseId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary mb-1">
                    <i class="bi bi-journal-check me-1 text-primary"></i> Évaluation en cours
                </label>
                <select name="eval_id" class="form-select form-select-custom" onchange="this.form.submit()">
                    <?php if (empty($evaluations)): ?>
                        <option value="">Aucune évaluation disponible</option>
                    <?php else: ?>
                        <?php foreach ($evaluations as $e): ?>
                            <option value="<?= $e['id_evaluation'] ?>" <?= $e['id_evaluation'] == $selectedEvalId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nom_evaluation']) ?> (Coef <?= $e['coefficient'] ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Tirage au Sort & Étudiant Sélectionné -->
    <div class="row g-4 mb-4">
        <!-- Carte Lancer Roulette -->
        <div class="col-md-5">
            <div class="custom-card p-4 h-100 d-flex flex-column justify-content-between">
                <div class="card-title-custom mb-3">
                    <i class="bi bi-shuffle me-1"></i> Tirage au Sort
                </div>
                <form method="POST" action="index.php" class="my-auto">
                    <input type="hidden" name="action" value="draw">
                    <button type="submit" class="btn btn-roulette w-100 fs-5" <?= empty($evaluations) ? 'disabled' : '' ?>>
                        <i class="bi bi-controller me-2"></i> Lancer la Roulette
                    </button>
                </form>
                <small class="text-muted text-center mt-3 display-6 fs-6">
                    <i class="bi bi-info-circle me-1"></i> Ne tire que les élèves présents et non encore interrogés.
                </small>
            </div>
        </div>

        <!-- Carte Étudiant désigné -->
        <div class="col-md-7">
            <div class="custom-card p-4 h-100">
                <div class="card-title-custom mb-3">
                    <i class="bi bi-person-badge me-1"></i> Étudiant Sélectionné
                </div>
                
                <div class="student-box h-75 d-flex flex-column align-items-center justify-content-center text-center">
                    <?php if ($selectedStudent): ?>
                        <span class="badge bg-indigo-subtle text-indigo px-3 py-1 rounded-pill mb-2 fw-semibold" style="background:#e0e7ff; color:#4338ca;">
                            Tiré au sort
                        </span>
                        <div class="selected-name mb-3">
                            <?= htmlspecialchars($selectedStudent['prenom'] . ' ' . $selectedStudent['nom']) ?>
                        </div>
                        <form method="POST" action="index.php" class="d-flex gap-2 w-75">
                            <input type="hidden" name="action" value="save_grade">
                            <input type="hidden" name="student_id" value="<?= $selectedStudent['id'] ?>">
                            <input type="hidden" name="eval_id" value="<?= $selectedEvalId ?>">
                            <input type="number" step="0.25" min="0" max="20" name="note" class="form-control form-control-custom text-center fw-bold" placeholder="Note / 20" required>
                            <button type="submit" class="btn btn-success fw-bold px-4 rounded-3">
                                <i class="bi bi-check-lg me-1"></i> Valider
                            </button>
                        </form>
                    <?php else: ?>
                        <i class="bi bi-person-bounding-box text-slate-300 fs-1 mb-2" style="color:#cbd5e1;"></i>
                        <p class="text-muted fw-medium m-0">Cliquez sur "Lancer la Roulette" pour désigner un élève.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de la classe -->
    <div class="custom-card overflow-hidden">
        <div class="p-4 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-3">
            <h5 class="fw-bold m-0 text-dark">
                <i class="bi bi-people-fill me-2 text-primary"></i>Liste des Élèves
            </h5>
            <div class="d-flex gap-2">
                <!-- Bouton d'ouverture de la modale d'ajout d'élève -->
                <button type="button" class="btn btn-primary btn-sm fw-semibold rounded-2" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Ajouter un élève
                </button>

                <form method="POST" action="index.php" class="m-0">
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn btn-outline-secondary btn-sm fw-semibold rounded-2">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les passages
                    </button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table custom-table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th class="text-center">Présence</th>
                        <th class="text-center">Passages</th>
                        <th class="text-end">Moyenne Générale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($student['nom']) ?></td>
                            <td><?= htmlspecialchars($student['prenom']) ?></td>
                            <td class="text-center">
                                <form method="POST" action="index.php" class="m-0">
                                    <input type="hidden" name="action" value="toggle_absence">
                                    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                    <input type="hidden" name="absence" value="<?= $student['absence'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm px-3 rounded-pill fw-semibold <?= $student['absence'] ? 'btn-danger' : 'btn-soft-success' ?>" 
                                            style="<?= !$student['absence'] ? 'background:#dcfce7; color:#15803d; border:none;' : '' ?>">
                                        <i class="bi <?= $student['absence'] ? 'bi-x-circle' : 'bi-check-circle' ?> me-1"></i>
                                        <?= $student['absence'] ? 'Absent' : 'Présent' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <?php if ($student['passage'] > 0): ?>
                                    <!-- Bouton interactif pour modifier ou supprimer le passage -->
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-sm" 
                                            data-bs-toggle="modal" data-bs-target="#editStudentModal<?= $student['id'] ?>">
                                        <i class="bi bi-pencil-square me-1"></i> <?= $student['passage'] ?> passage(s)
                                    </button>
                                <?php else: ?>
                                    <span class="badge rounded-pill fs-6 px-3 py-2 bg-secondary">
                                        0 passage(s)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-extrabold fs-6 text-dark">
                                <?= $student['average'] !== null ? number_format($student['average'], 2) . ' <small class="text-muted fs-6">/ 20</small>' : '<span class="text-muted">-</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Ajout d'un élève -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="add_student">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="addStudentModalLabel">
                        <i class="bi bi-person-plus text-primary me-2"></i>Ajouter un nouvel élève
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nom</label>
                        <input type="text" name="nom" class="form-control form-control-custom" placeholder="ex: DURAND" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Prénom</label>
                        <input type="text" name="prenom" class="form-control form-control-custom" placeholder="ex: Thomas" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-semibold px-4">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales de modification / suppression de passage pour chaque élève concerné -->
<?php foreach ($students as $student): ?>
    <?php if ($student['passage'] > 0): ?>
    <div class="modal fade" id="editStudentModal<?= $student['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="index.php">
                    <input type="hidden" name="action" value="update_or_delete_grade">
                    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                    <input type="hidden" name="eval_id" value="<?= $selectedEvalId ?>">
                    
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-gear text-primary me-2"></i> Gérer : <?= htmlspecialchars($student['prenom'] . ' ' . $student['nom']) ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Modifiez la note attribuée pour cette évaluation ou supprimez complètement le passage pour le remettre à zéro.</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Modifier la note / 20</label>
                            <input type="number" step="0.25" min="0" max="20" name="new_note" 
                                   class="form-control form-control-custom fw-bold" 
                                   placeholder="Nouvelle note (optionnel si suppression)">
                        </div>
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between">
                        <!-- Bouton pour supprimer complètement le passage -->
                        <button type="submit" name="delete_grade" value="1" class="btn btn-outline-danger btn-sm fw-semibold" onclick="return confirm('Voulez-vous vraiment supprimer ce passage ?');">
                            <i class="bi bi-trash me-1"></i> Supprimer le passage
                        </button>
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold px-4">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Bootstrap JS (nécessaire pour faire fonctionner les modales) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>