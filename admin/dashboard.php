<?php
require_once '../includes/auth.php';
redirectIfNoRole('admin');
require_once '../includes/config.php';

// Estatísticas
$total_utilizadores = $pdo->query("SELECT COUNT(*) FROM utilizadores")->fetchColumn();
$total_alunos = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE role='aluno'")->fetchColumn();
$total_funcionarios = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE role='funcionario'")->fetchColumn();
$total_gestores = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE role='gestor'")->fetchColumn();
$total_admins = $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE role='admin'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
        <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard do Administrador</h2>
        <p class="lead">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>.</p>

        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Utilizadores</h5>
                        <h2><?= $total_utilizadores ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Alunos</h5>
                        <h2><?= $total_alunos ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Funcionários</h5>
                        <h2><?= $total_funcionarios ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Gestores</h5>
                        <h2><?= $total_gestores ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Gestão de Utilizadores</h5>
                    </div>
                    <div class="card-body">
                        <a href="utilizadores.php" class="btn btn-primary"><i class="bi bi-pencil-square me-2"></i>Gerir Utilizadores</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>