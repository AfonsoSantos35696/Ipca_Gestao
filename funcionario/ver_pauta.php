<?php
require_once '../includes/auth.php';
redirectIfNoRole('funcionario');
require_once '../includes/config.php';

$id_pauta = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT p.*, d.nome_disciplina 
    FROM pauta p 
    JOIN disciplinas d ON p.id_disciplina = d.Id_disciplina 
    WHERE p.id = ?
");
$stmt->execute([$id_pauta]);
$pauta = $stmt->fetch();
if (!$pauta) {
    header('Location: pautas.php');
    exit;
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
    <title>Ver Pauta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-eye me-2"></i>Pauta: <?= htmlspecialchars($pauta['nome_disciplina']) ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Ano Letivo:</strong> <?= $pauta['ano_letivo'] ?> | <strong>Época:</strong> <?= $pauta['epoca'] ?></p>
                <p><strong>Criada em:</strong> <?= date('d/m/Y H:i', strtotime($pauta['data_criacao'])) ?> pelo funcionário ID <?= $pauta['id_funcionario_criador'] ?></p>
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
                                <td><?= $n['nota'] !== null ? $n['nota'] : '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="pautas.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>