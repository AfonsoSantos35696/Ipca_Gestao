<?php
require_once '../includes/auth.php';
redirectIfNoRole('gestor');
require_once '../includes/config.php';

$total_cursos = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
$total_disciplinas = $pdo->query("SELECT COUNT(*) FROM disciplinas")->fetchColumn();
$fichas_submetidas = $pdo->query("SELECT COUNT(*) FROM ficha_aluno WHERE estado='submetida'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gestor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-dashboard { border: none; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .card-dashboard:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard do Gestor Pedagógico</h2>
        <p class="lead">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>.</p>

        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card card-dashboard text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-book me-2"></i>Cursos</h5>
                        <h2><?= $total_cursos ?></h2>
                        <a href="cursos.php" class="btn btn-light mt-3">Gerir Cursos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card card-dashboard text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-journal-bookmark-fill me-2"></i>Disciplinas</h5>
                        <h2><?= $total_disciplinas ?></h2>
                        <a href="disciplinas.php" class="btn btn-light mt-3">Gerir Disciplinas</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card card-dashboard text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-files me-2"></i>Fichas Submetidas</h5>
                        <h2><?= $fichas_submetidas ?></h2>
                        <a href="fichas_alunos.php" class="btn btn-light mt-3">Validar Fichas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>