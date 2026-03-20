<?php
require_once '../includes/auth.php';
redirectIfNoRole('funcionario');
require_once '../includes/config.php';

$mensagem = '';

if (isset($_POST['acao'])) {
    $id_pedido = $_POST['id_pedido'];
    $estado = $_POST['acao']; // 'aprovado' ou 'rejeitado'
    $observacoes = $_POST['observacoes'] ?? '';

    $sql = "UPDATE pedido_matricula SET estado=?, observacoes=?, data_decisao=NOW(), id_funcionario=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estado, $observacoes, $_SESSION['user_id'], $id_pedido]);
    $mensagem = "Pedido atualizado com sucesso.";
}

// Listar pendentes
$pendentes = $pdo->query("
    SELECT p.*, u.nome AS aluno_nome, c.nome_cursos 
    FROM pedido_matricula p
    JOIN utilizadores u ON p.id_aluno = u.id
    JOIN cursos c ON p.id_curso = c.Id_cursos
    WHERE p.estado = 'pendente'
    ORDER BY p.data_pedido
")->fetchAll();

// Listar outros
$outros = $pdo->query("
    SELECT p.*, u.nome AS aluno_nome, c.nome_cursos 
    FROM pedido_matricula p
    JOIN utilizadores u ON p.id_aluno = u.id
    JOIN cursos c ON p.id_curso = c.Id_cursos
    WHERE p.estado != 'pendente'
    ORDER BY p.data_decisao DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos de Matrícula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-pedido { border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 1rem; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-envelope-paper me-2"></i>Pedidos de Matrícula</h2>
        <?php if ($mensagem): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= $mensagem ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Pendentes (<?= count($pendentes) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (count($pendentes) == 0): ?>
                    <p class="text-muted">Nenhum pedido pendente.</p>
                <?php else: ?>
                    <?php foreach ($pendentes as $p): ?>
                        <div class="card card-pedido">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($p['aluno_nome']) ?></h6>
                                        <p class="mb-1"><strong>Curso:</strong> <?= htmlspecialchars($p['nome_cursos']) ?></p>
                                        <small class="text-muted">Data do pedido: <?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></small>
                                    </div>
                                    <form method="post" class="d-flex gap-2">
                                        <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                        <input type="text" class="form-control form-control-sm" name="observacoes" placeholder="Observações (opcional)" style="width: 200px;">
                                        <button type="submit" name="acao" value="aprovado" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Aprovar</button>
                                        <button type="submit" name="acao" value="rejeitado" class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i> Rejeitar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Histórico</h5>
            </div>
            <div class="card-body">
                <?php if (count($outros) == 0): ?>
                    <p class="text-muted">Nenhum pedido processado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Curso</th>
                                    <th>Data Pedido</th>
                                    <th>Estado</th>
                                    <th>Observações</th>
                                    <th>Funcionário</th>
                                    <th>Data Decisão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($outros as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['aluno_nome']) ?></td>
                                    <td><?= htmlspecialchars($p['nome_cursos']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $p['estado']=='aprovado'?'success':'danger'?>">
                                            <?= $p['estado'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($p['observacoes'] ?? '-') ?></td>
                                    <td><?= $p['id_funcionario'] ?></td>
                                    <td><?= $p['data_decisao'] ? date('d/m/Y H:i', strtotime($p['data_decisao'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>