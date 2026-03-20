<?php
require_once '../includes/auth.php';
redirectIfNoRole('gestor');
require_once '../includes/config.php';

$mensagem = '';
$erro = '';

// Adicionar associação
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $curso = $_POST['curso'];
    $disciplina = $_POST['disciplina'];
    $ano = $_POST['ano'];
    $semestre = $_POST['semestre'];

    if (empty($curso) || empty($disciplina) || empty($ano) || empty($semestre)) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        // Verificar duplicação
        $check = $pdo->prepare("SELECT id FROM plano_estudos WHERE cursos = ? AND disciplinas = ? AND ano = ? AND semestre = ?");
        $check->execute([$curso, $disciplina, $ano, $semestre]);
        if ($check->fetch()) {
            $erro = "Esta disciplina já está associada a este curso no mesmo ano/semestre.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO plano_estudos (cursos, disciplinas, ano, semestre) VALUES (?, ?, ?, ?)");
            $stmt->execute([$curso, $disciplina, $ano, $semestre]);
            $mensagem = "Associação adicionada com sucesso.";
        }
    }
}

// Remover associação
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM plano_estudos WHERE id = ?");
    $stmt->execute([$id]);
    $mensagem = "Associação removida.";
}

// Listar planos
$planos = $pdo->query("
    SELECT pe.*, c.nome_cursos, d.nome_disciplina 
    FROM plano_estudos pe
    JOIN cursos c ON pe.cursos = c.Id_cursos
    JOIN disciplinas d ON pe.disciplinas = d.Id_disciplina
    ORDER BY c.nome_cursos, pe.ano, pe.semestre
")->fetchAll();

$cursos = $pdo->query("SELECT Id_cursos, nome_cursos FROM cursos ORDER BY nome_cursos")->fetchAll();
$disciplinas = $pdo->query("SELECT Id_disciplina, nome_disciplina FROM disciplinas ORDER BY nome_disciplina")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano de Estudos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-diagram-3 me-2"></i>Plano de Estudos</h2>
        <?php if ($mensagem): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Adicionar Associação Curso-Disciplina</h5>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-3">
                        <label for="curso" class="form-label">Curso</label>
                        <select class="form-select" id="curso" name="curso" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= $c['Id_cursos'] ?>"><?= htmlspecialchars($c['nome_cursos']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="disciplina" class="form-label">Disciplina</label>
                        <select class="form-select" id="disciplina" name="disciplina" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($disciplinas as $d): ?>
                                <option value="<?= $d['Id_disciplina'] ?>"><?= htmlspecialchars($d['nome_disciplina']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ano" class="form-label">Ano</label>
                        <input type="number" class="form-control" id="ano" name="ano" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label for="semestre" class="form-label">Semestre</label>
                        <select class="form-select" id="semestre" name="semestre" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" name="add" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i> Adicionar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Associações Atuais</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Disciplina</th>
                            <th>Ano</th>
                            <th>Semestre</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($planos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nome_cursos']) ?></td>
                            <td><?= htmlspecialchars($p['nome_disciplina']) ?></td>
                            <td><?= $p['ano'] ?></td>
                            <td><?= $p['semestre'] ?></td>
                            <td>
                                <a href="?remove=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remover esta associação?')"><i class="bi bi-trash"></i> Remover</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>