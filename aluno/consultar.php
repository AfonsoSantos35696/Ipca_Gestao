<?php
require_once '../includes/auth.php';
redirectIfNoRole('aluno');
require_once '../includes/config.php';

$id_aluno = $_SESSION['user_id'];

// Buscar fichas
$stmt = $pdo->prepare("SELECT * FROM ficha_aluno WHERE id_aluno = ? ORDER BY id DESC");
$stmt->execute([$id_aluno]);
$fichas = $stmt->fetchAll();

// Buscar pedidos de matrícula
$stmt = $pdo->prepare("
    SELECT pm.*, c.nome_cursos 
    FROM pedido_matricula pm 
    JOIN cursos c ON pm.id_curso = c.Id_cursos 
    WHERE pm.id_aluno = ? 
    ORDER BY pm.id DESC
");
$stmt->execute([$id_aluno]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Estado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-custom { background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-search me-2"></i>Consultar Estado dos Pedidos</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-person me-2"></i>Ficha de Aluno</h5>
            </div>
            <div class="card-body">
                <?php if (count($fichas) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-custom">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data Submissão</th>
                                    <th>Estado</th>
                                    <th>Observações</th>
                                    <th>Validado por</th>
                                    <th>Data Validação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fichas as $f): ?>
                                <tr>
                                    <td><?= $f['id'] ?></td>
                                    <td><?= $f['data_submissao'] ? date('d/m/Y H:i', strtotime($f['data_submissao'])) : '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $f['estado'] == 'aprovada' ? 'success' : ($f['estado'] == 'rejeitada' ? 'danger' : 'warning') ?>">
                                            <?= $f['estado'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($f['observacoes_gestor'] ?? '-') ?></td>
                                    <td><?= $f['id_gestor'] ? 'ID: '.$f['id_gestor'] : '-' ?></td>
                                    <td><?= $f['data_validacao'] ? date('d/m/Y H:i', strtotime($f['data_validacao'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Ainda não preencheu a ficha.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-envelope-paper me-2"></i>Pedidos de Matrícula</h5>
            </div>
            <div class="card-body">
                <?php if (count($pedidos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-custom">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Curso</th>
                                    <th>Data Pedido</th>
                                    <th>Estado</th>
                                    <th>Observações</th>
                                    <th>Funcionário</th>
                                    <th>Data Decisão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $p): ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td><?= htmlspecialchars($p['nome_cursos']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $p['estado'] == 'aprovado' ? 'success' : ($p['estado'] == 'rejeitado' ? 'danger' : 'warning') ?>">
                                            <?= $p['estado'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($p['observacoes'] ?? '-') ?></td>
                                    <td><?= $p['id_funcionario'] ?? '-' ?></td>
                                    <td><?= $p['data_decisao'] ? date('d/m/Y H:i', strtotime($p['data_decisao'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Ainda não fez nenhum pedido de matrícula.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>