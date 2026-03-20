<?php
require_once '../includes/auth.php';
redirectIfNoRole('funcionario');
require_once '../includes/config.php';

// Contagens para o dashboard
$pendentes = $pdo->query("SELECT COUNT(*) FROM pedido_matricula WHERE estado='pendente'")->fetchColumn();
$total_pautas = $pdo->query("SELECT COUNT(*) FROM pauta")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-dashboard { border: none; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .card-dashboard:hover { transform: translateY(-5px); }
        .icon-circle { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard do Funcionário</h2>
        <p class="lead">Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>.</p>

        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card card-dashboard">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-warning-light me-3">
                                <i class="bi bi-envelope-paper"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Pedidos Pendentes</h5>
                                <h2 class="text-warning"><?= $pendentes ?></h2>
                            </div>
                        </div>
                        <a href="pedidos_matricula.php" class="btn btn-outline-warning mt-3 w-100">Gerir Pedidos</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card card-dashboard">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-info-light me-3">
                                <i class="bi bi-table"></i>
                            </div>
                            <div>
                                <h5 class="card-title">Pautas</h5>
                                <h2 class="text-info"><?= $total_pautas ?></h2>
                            </div>
                        </div>
                        <a href="pautas.php" class="btn btn-outline-info mt-3 w-100">Gerir Pautas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>