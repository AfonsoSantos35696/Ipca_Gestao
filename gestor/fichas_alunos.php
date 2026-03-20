<?php
require_once '../includes/auth.php';
redirectIfNoRole('gestor');
require_once '../includes/config.php';

if (isset($_POST['acao'])) {
    $id_ficha = $_POST['id_ficha'];
    $estado = $_POST['acao']; // 'aprovada' ou 'rejeitada'
    $observacoes = $_POST['observacoes'] ?? '';

    $sql = "UPDATE ficha_aluno SET estado=?, observacoes_gestor=?, data_validacao=NOW(), id_gestor=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estado, $observacoes, $_SESSION['user_id'], $id_ficha]);
    $mensagem = "Ficha atualizada com sucesso.";
}

// Listar fichas submetidas
$fichas = $pdo->query("
    SELECT f.*, u.nome AS nome_aluno, c.nome_cursos 
    FROM ficha_aluno f
    JOIN utilizadores u ON f.id_aluno = u.id
    JOIN cursos c ON f.id_curso = c.Id_cursos
    WHERE f.estado IN ('submetida', 'aprovada', 'rejeitada')
    ORDER BY f.data_submissao DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Fichas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-files me-2"></i>Validação de Fichas de Aluno</h2>
        <?php if (isset($mensagem)): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>

        <?php foreach ($fichas as $f): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <span><i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($f['nome_aluno']) ?> (ID: <?= $f['id_aluno'] ?>)</span>
                    <span class="badge bg-<?= $f['estado'] == 'aprovada' ? 'success' : ($f['estado'] == 'rejeitada' ? 'danger' : 'warning') ?> fs-6"><?= $f['estado'] ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <?php if ($f['fotografia']): ?>
                                <img src="../<?= $f['fotografia'] ?>" class="img-fluid rounded" alt="Foto">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-radius: 50%;"><i class="bi bi-person"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-10">
                            <p><strong>Nome Completo:</strong> <?= htmlspecialchars($f['nome_completo']) ?></p>
                            <p><strong>Data Nascimento:</strong> <?= $f['data_nascimento'] ?></p>
                            <p><strong>Morada:</strong> <?= htmlspecialchars($f['morada']) ?></p>
                            <p><strong>Telefone:</strong> <?= htmlspecialchars($f['telefone']) ?></p>
                            <p><strong>Email Contacto:</strong> <?= htmlspecialchars($f['email_contato']) ?></p>
                            <p><strong>Curso Pretendido:</strong> <?= htmlspecialchars($f['nome_cursos']) ?></p>
                            <?php if ($f['estado'] == 'submetida'): ?>
                                <form method="post" class="mt-2">
                                    <input type="hidden" name="id_ficha" value="<?= $f['id'] ?>">
                                    <div class="mb-2">
                                        <label for="obs<?= $f['id'] ?>" class="form-label">Observações (opcional):</label>
                                        <textarea class="form-control" id="obs<?= $f['id'] ?>" name="observacoes" rows="2"></textarea>
                                    </div>
                                    <button type="submit" name="acao" value="aprovada" class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Aprovar</button>
                                    <button type="submit" name="acao" value="rejeitada" class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i> Rejeitar</button>
                                </form>
                            <?php else: ?>
                                <p><strong>Observações do Gestor:</strong> <?= nl2br(htmlspecialchars($f['observacoes_gestor'] ?? '')) ?></p>
                                <p><strong>Validado em:</strong> <?= $f['data_validacao'] ? date('d/m/Y H:i', strtotime($f['data_validacao'])) : '-' ?> pelo gestor ID <?= $f['id_gestor'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>