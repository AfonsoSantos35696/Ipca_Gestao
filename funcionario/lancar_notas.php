<?php
require_once '../includes/auth.php';
redirectIfNoRole('funcionario');
require_once '../includes/config.php';

$id_pauta = $_GET['id_pauta'] ?? 0;

$stmt = $pdo->prepare("SELECT p.*, d.nome_disciplina FROM pauta p JOIN disciplinas d ON p.id_disciplina = d.Id_disciplina WHERE p.id = ?");
$stmt->execute([$id_pauta]);
$pauta = $stmt->fetch();
if (!$pauta) {
    header('Location: pautas.php');
    exit;
}

// Processar notas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['nota'] as $id_nota => $valor) {
        if ($valor === '') continue;
        $stmt = $pdo->prepare("UPDATE nota SET nota = ? WHERE id = ?");
        $stmt->execute([$valor, $id_nota]);
    }
    $mensagem = "Notas atualizadas.";
}

// Garantir registos de nota para todos os alunos (simplificação)
$alunos = $pdo->query("SELECT u.id, u.nome FROM utilizadores u WHERE u.role = 'aluno' ORDER BY u.nome")->fetchAll();
foreach ($alunos as $aluno) {
    $check = $pdo->prepare("SELECT id FROM nota WHERE id_pauta = ? AND id_aluno = ?");
    $check->execute([$id_pauta, $aluno['id']]);
    if (!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO nota (id_pauta, id_aluno) VALUES (?, ?)");
        $insert->execute([$id_pauta, $aluno['id']]);
    }
}

$notas = $pdo->prepare("
    SELECT n.*, u.nome AS aluno_nome 
    FROM nota n
    JOIN utilizadores u ON n.id_aluno = u.id
    WHERE n.id_pauta = ?
    ORDER BY u.nome
");
$notas->execute([$id_pauta]);
$notas = $notas->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançar Notas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Lançar Notas - <?= htmlspecialchars($pauta['nome_disciplina']) ?> (<?= $pauta['ano_letivo'] ?> - <?= $pauta['epoca'] ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (isset($mensagem)): ?>
                    <div class="alert alert-success"><?= $mensagem ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notas as $n): ?>
                                <tr>
                                    <td><?= htmlspecialchars($n['aluno_nome']) ?></td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control" name="nota[<?= $n['id'] ?>]" value="<?= $n['nota'] ?>" min="0" max="20">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Guardar Notas</button>
                    <a href="pautas.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>