<?php
require_once '../includes/auth.php';
redirectIfNoRole('funcionario');
require_once '../includes/config.php';

$mensagem = '';

if (isset($_POST['criar_pauta'])) {
    $id_disciplina = $_POST['id_disciplina'];
    $ano_letivo = $_POST['ano_letivo'];
    $epoca = $_POST['epoca'];

    $stmt = $pdo->prepare("INSERT INTO pauta (id_disciplina, ano_letivo, epoca, id_funcionario_criador) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_disciplina, $ano_letivo, $epoca, $_SESSION['user_id']]);
    $mensagem = "Pauta criada com sucesso.";
}

$pautas = $pdo->query("
    SELECT pauta.*, disciplinas.nome_disciplina 
    FROM pauta 
    JOIN disciplinas ON pauta.id_disciplina = disciplinas.Id_disciplina
    ORDER BY pauta.data_criacao DESC
")->fetchAll();

$disciplinas = $pdo->query("SELECT Id_disciplina, nome_disciplina FROM disciplinas")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pautas de Avaliação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-table me-2"></i>Pautas de Avaliação</h2>
        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?= $mensagem ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Criar Nova Pauta</h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-4">
                        <label for="id_disciplina" class="form-label">Disciplina</label>
                        <select class="form-select" id="id_disciplina" name="id_disciplina" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($disciplinas as $d): ?>
                                <option value="<?= $d['Id_disciplina'] ?>"><?= htmlspecialchars($d['nome_disciplina']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="ano_letivo" class="form-label">Ano Letivo</label>
                        <input type="text" class="form-control" id="ano_letivo" name="ano_letivo" placeholder="2024/2025" required>
                    </div>
                    <div class="col-md-3">
                        <label for="epoca" class="form-label">Época</label>
                        <input type="text" class="form-control" id="epoca" name="epoca" placeholder="Normal" required>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" name="criar_pauta" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Criar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-list me-2"></i>Pautas Existentes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Disciplina</th>
                                <th>Ano Letivo</th>
                                <th>Época</th>
                                <th>Criada em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pautas as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['nome_disciplina']) ?></td>
                                <td><?= $p['ano_letivo'] ?></td>
                                <td><?= $p['epoca'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['data_criacao'])) ?></td>
                                <td>
                                    <a href="lancar_notas.php?id_pauta=<?= $p['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Lançar Notas</a>
                                    <a href="ver_pauta.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Ver</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>