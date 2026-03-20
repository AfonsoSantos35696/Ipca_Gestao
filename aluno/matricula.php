<?php
require_once '../includes/auth.php';
redirectIfNoRole('aluno');
require_once '../includes/config.php';
include '../includes/navbar.php';

$id_aluno = $_SESSION['user_id'];
$mensagem = '';
$erro = '';

$stmt = $pdo->prepare("SELECT id, estado FROM pedido_matricula WHERE id_aluno = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$id_aluno]);
$pedido = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$pedido) {
    $id_curso = $_POST['id_curso'] ?? '';
    if (empty($id_curso)) {
        $erro = "Selecione um curso.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO pedido_matricula (id_aluno, id_curso) VALUES (?, ?)");
        $stmt->execute([$id_aluno, $id_curso]);
        $mensagem = "Pedido de matrícula efetuado com sucesso. Ficará pendente de aprovação.";
        // Recarregar
        $stmt = $pdo->prepare("SELECT id, estado FROM pedido_matricula WHERE id_aluno = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$id_aluno]);
        $pedido = $stmt->fetch();
    }
}

$cursos = $pdo->query("SELECT Id_cursos, nome_cursos FROM cursos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de Matrícula</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-matricula { border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card card-matricula">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Pedido de Matrícula</h4>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-success"><?= $mensagem ?></div>
                <?php endif; ?>
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <?php if ($pedido): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>Já existe um pedido de matrícula. Estado atual: <strong><?= $pedido['estado'] ?></strong>
                    </div>
                    <a href="consultar.php" class="btn btn-primary"><i class="bi bi-search me-1"></i>Consultar Estado</a>
                <?php else: ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="id_curso" class="form-label">Selecione o Curso *</label>
                            <select class="form-select" id="id_curso" name="id_curso" required>
                                <option value="">Escolha...</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= $curso['Id_cursos'] ?>"><?= htmlspecialchars($curso['nome_cursos']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>Efetuar Pedido</button>
                        <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Cancelar</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>