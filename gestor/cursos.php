<?php
require_once '../includes/auth.php';
redirectIfNoRole('gestor');
require_once '../includes/config.php';

$mensagem = '';
$erro = '';

// Processar ações
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM cursos WHERE Id_cursos = ?");
        $stmt->execute([$id]);
        $mensagem = "Curso eliminado com sucesso.";
    } catch (PDOException $e) {
        $erro = "Não é possível eliminar este curso (pode estar associado a planos de estudo ou fichas).";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] == 'add') {
        $nome = $_POST['nome_cursos'];
        if (!empty($nome)) {
            $stmt = $pdo->prepare("INSERT INTO cursos (nome_cursos) VALUES (?)");
            $stmt->execute([$nome]);
            $mensagem = "Curso adicionado com sucesso.";
        } else {
            $erro = "O nome do curso é obrigatório.";
        }
    } elseif ($_POST['acao'] == 'edit') {
        $id = $_POST['id_cursos'];
        $nome = $_POST['nome_cursos'];
        if (!empty($nome)) {
            $stmt = $pdo->prepare("UPDATE cursos SET nome_cursos = ? WHERE Id_cursos = ?");
            $stmt->execute([$nome, $id]);
            $mensagem = "Curso atualizado com sucesso.";
        } else {
            $erro = "O nome do curso é obrigatório.";
        }
    }
}

$cursos = $pdo->query("SELECT * FROM cursos ORDER BY nome_cursos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-book me-2"></i>Gestão de Cursos</h2>
        <?php if ($mensagem): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Adicionar Novo Curso</h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="acao" value="add">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="nome_cursos" placeholder="Nome do curso" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i> Adicionar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Lista de Cursos</h5>
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
                        <?php foreach ($cursos as $curso): ?>
                        <tr>
                            <td><?= $curso['Id_cursos'] ?></td>
                            <td><?= htmlspecialchars($curso['nome_cursos']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $curso['Id_cursos'] ?>"><i class="bi bi-pencil"></i> Editar</button>
                                <a href="?delete=<?= $curso['Id_cursos'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')"><i class="bi bi-trash"></i> Eliminar</a>
                            </td>
                        </tr>
                        <!-- Modal Editar -->
                        <div class="modal fade" id="editModal<?= $curso['Id_cursos'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Editar Curso</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="acao" value="edit">
                                            <input type="hidden" name="id_cursos" value="<?= $curso['Id_cursos'] ?>">
                                            <div class="mb-3">
                                                <label for="nome_cursos<?= $curso['Id_cursos'] ?>" class="form-label">Nome do Curso</label>
                                                <input type="text" class="form-control" id="nome_cursos<?= $curso['Id_cursos'] ?>" name="nome_cursos" value="<?= htmlspecialchars($curso['nome_cursos']) ?>" required>
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