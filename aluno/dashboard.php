<?php
require_once '../includes/auth.php';
redirectIfNoRole('aluno');
require_once '../includes/config.php';

$id_aluno = $_SESSION['user_id'];

// Verificar ficha e obter dados
$stmt = $pdo->prepare("
    SELECT f.*, c.nome_cursos 
    FROM ficha_aluno f 
    LEFT JOIN cursos c ON f.id_curso = c.Id_cursos 
    WHERE f.id_aluno = ? 
    ORDER BY f.id DESC LIMIT 1
");
$stmt->execute([$id_aluno]);
$ficha = $stmt->fetch();

// Verificar pedido de matrícula
$stmt = $pdo->prepare("SELECT estado FROM pedido_matricula WHERE id_aluno = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$id_aluno]);
$matricula = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-dashboard {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }
        .bg-info-light {
            background-color: rgba(23, 162, 184, 0.2);
            color: #0c5460;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard do Aluno</h2>
        
        <div class="card mb-4 border-0 shadow-sm rounded-4" style="background-color: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <?php if ($ficha && !empty($ficha['fotografia'])): ?>
                        <img src="../<?= htmlspecialchars($ficha['fotografia']) ?>" alt="Foto de Perfil" class="rounded-circle me-4 border shadow-sm" style="width: 90px; height: 90px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle me-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 90px; height: 90px; font-size: 2.5rem;">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="mb-1 fw-bold"><?= htmlspecialchars($ficha['nome_completo'] ?? $_SESSION['user_name']) ?></h4>
                        <?php if ($ficha && !empty($ficha['nome_cursos'])): ?>
                            <p class="mb-0 text-secondary" style="font-size: 1.1rem;"><i class="bi bi-mortarboard-fill me-2 text-primary"></i><?= htmlspecialchars($ficha['nome_cursos']) ?></p>
                        <?php else: ?>
                            <p class="mb-0 text-muted"><i class="bi bi-info-circle me-2"></i>Curso não definido (Preencha a sua Ficha)</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-info-light me-3">
                                <i class="bi bi-file-person"></i>
                            </div>
                            <h5 class="card-title mb-0">Ficha de Aluno</h5>
                        </div>
                        <?php if ($ficha): ?>
                            <p class="card-text">Estado: 
                                <span class="badge bg-<?= $ficha['estado'] == 'aprovada' ? 'success' : ($ficha['estado'] == 'rejeitada' ? 'danger' : 'warning') ?>">
                                    <?= $ficha['estado'] ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="card-text text-muted">Ainda não preencheu a ficha.</p>
                        <?php endif; ?>
                        <a href="ficha.php" class="btn btn-outline-primary mt-3"><i class="bi bi-pencil me-1"></i>Preencher/Editar Ficha</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card card-dashboard h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-warning-light me-3">
                                <i class="bi bi-envelope-paper"></i>
                            </div>
                            <h5 class="card-title mb-0">Pedido de Matrícula</h5>
                        </div>
                        <?php if ($matricula): ?>
                            <p class="card-text">Estado: 
                                <span class="badge bg-<?= $matricula['estado'] == 'aprovado' ? 'success' : ($matricula['estado'] == 'rejeitado' ? 'danger' : 'warning') ?>">
                                    <?= $matricula['estado'] ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="card-text text-muted">Ainda não fez pedido de matrícula.</p>
                        <?php endif; ?>
                        <a href="matricula.php" class="btn btn-outline-warning mt-3"><i class="bi bi-plus-circle me-1"></i>Fazer Pedido</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-dashboard">
                    <div class="card-body">
                        <h5><i class="bi bi-info-circle me-2"></i>Informações</h5>
                        <p>Consulte o estado das suas submissões e pedidos. Qualquer dúvida, contacte os serviços académicos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>