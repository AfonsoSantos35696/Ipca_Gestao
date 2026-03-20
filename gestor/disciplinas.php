<?php
require_once '../includes/auth.php';
redirectIfNoRole('gestor');
require_once '../includes/config.php';

$mensagem = '';
$erro = '';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM disciplinas WHERE Id_disciplina = ?");
        $stmt->execute([$id]);
        $mensagem = "Disciplina eliminada com sucesso.";
    } catch (PDOException $e) {
        $erro = "Não é possível eliminar esta disciplina (pode estar associada a planos de estudo ou pautas).";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] == 'add') {
        $nome = $_POST['nome_disciplina'];
        if (!empty($nome)) {
            $stmt = $pdo->prepare("INSERT INTO disciplinas (nome_disciplina) VALUES (?)");
            $stmt->execute([$nome]);
            $mensagem = "Disciplina adicionada com sucesso.";
        } else {
            $erro = "O nome da disciplina é obrigatório.";
        }
    } elseif ($_POST['acao'] == 'edit') {
        $id = $_POST['id_disciplina'];
        $nome = $_POST['nome_disciplina'];
        if (!empty($nome)) {
            $stmt = $pdo->prepare("UPDATE disciplinas SET nome_disciplina = ? WHERE Id_disciplina = ?");
            $stmt->execute([$nome, $id]);
            $mensagem = "Disciplina atualizada com sucesso.";
        } else {
            $erro = "O nome da disciplina é obrigatório.";
        }
    }
}

$disciplinas = $pdo->query("SELECT * FROM disciplinas ORDER BY nome_disciplina")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Disciplinas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-journal-bookmark-fill me-2"></i>Gestão de Disciplinas</h2>
        <?php if ($mensagem): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Adicionar Nova Disciplina</h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="acao" value="add">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="nome_disciplina" placeholder="Nome da disciplina" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i> Adicionar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Lista de Disciplinas</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disciplinas as $d): ?>
                        <tr>
                            <td><?= $d['Id_disciplina'] ?></td>
                            <td><?= htmlspecialchars($d['nome_disciplina']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $d['Id_disciplina'] ?>"><i class="bi bi-pencil"></i> Editar</button>
                                <a href="?delete=<?= $d['Id_disciplina'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')"><i class="bi bi-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                        <!-- Modal Editar -->
                        <div class="modal fade" id="editModal<?= $d['Id_disciplina'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Disciplina</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="acao" value="edit">
                                            <input type="hidden" name="id_disciplina" value="<?= $d['Id_disciplina'] ?>">
                                            <div class="mb-3">
                                                <label for="nome_disciplina<?= $d['Id_disciplina'] ?>" class="form-label">Nome da Disciplina</label>
                                                <input type="text" class="form-control" id="nome_disciplina<?= $d['Id_disciplina'] ?>" name="nome_disciplina" value="<?= htmlspecialchars($d['nome_disciplina']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>