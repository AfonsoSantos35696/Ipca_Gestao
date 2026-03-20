<?php
require_once '../includes/auth.php';
redirectIfNoRole('aluno');
require_once '../includes/config.php';
include '../includes/navbar.php';

$id_aluno = $_SESSION['user_id'];
$mensagem = $_SESSION['mensagem'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['mensagem'], $_SESSION['erro']);

// Buscar ficha existente (se houver)
$stmt = $pdo->prepare("
    SELECT f.*, c.nome_cursos 
    FROM ficha_aluno f 
    LEFT JOIN cursos c ON f.id_curso = c.Id_cursos 
    WHERE f.id_aluno = ? 
    ORDER BY f.id DESC LIMIT 1
");
$stmt->execute([$id_aluno]);
$ficha = $stmt->fetch();

// Buscar cursos
$cursos = $pdo->query("SELECT Id_cursos, nome_cursos FROM cursos")->fetchAll();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_completo = $_POST['nome_completo'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $morada = $_POST['morada'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email_contato = $_POST['email_contato'] ?? '';
    $id_curso = $_POST['id_curso'] ?? '';

    if (empty($nome_completo) || empty($data_nascimento) || empty($morada) || empty($telefone) || empty($email_contato) || empty($id_curso)) {
        $erro = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email_contato, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email de contacto inválido.";
    } else {
        // Upload da fotografia
        $foto_path = $ficha['fotografia'] ?? null;
        if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['fotografia']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];
            if (!in_array($ext, $allowed)) {
                $erro = "Formato de imagem não permitido. Use JPG ou PNG.";
            } elseif ($_FILES['fotografia']['size'] > 2 * 1024 * 1024) {
                $erro = "A imagem não pode ter mais de 2MB.";
            } else {
                $novo_nome = uniqid() . '.' . $ext;
                $destino = '../assets/uploads/' . $novo_nome;
                if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $destino)) {
                    $foto_path = 'assets/uploads/' . $novo_nome;
                } else {
                    $erro = "Erro ao guardar a fotografia.";
                }
            }
        }

        if (empty($erro)) {
            if ($ficha) {
                // Atualizar ficha existente (se ainda em rascunho)
                $sql = "UPDATE ficha_aluno SET nome_completo=?, data_nascimento=?, morada=?, telefone=?, email_contato=?, id_curso=?, fotografia=COALESCE(?, fotografia) WHERE id_aluno=? AND estado='rascunho'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome_completo, $data_nascimento, $morada, $telefone, $email_contato, $id_curso, $foto_path, $id_aluno]);
                $_SESSION['mensagem'] = "Ficha atualizada com sucesso.";
            } else {
                // Inserir nova ficha em rascunho
                $sql = "INSERT INTO ficha_aluno (id_aluno, id_curso, nome_completo, data_nascimento, morada, telefone, email_contato, fotografia, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'rascunho')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_aluno, $id_curso, $nome_completo, $data_nascimento, $morada, $telefone, $email_contato, $foto_path]);
                $_SESSION['mensagem'] = "Ficha criada em rascunho.";
            }
            header('Location: ficha.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-ficha { border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .preview-img { max-width: 150px; max-height: 150px; border-radius: 0.5rem; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card card-ficha">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-file-person me-2"></i>Ficha de Aluno</h4>
            </div>
            <div class="card-body">
                <?php if ($mensagem): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= $mensagem ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if ($erro): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?= $erro ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <?php if ($ficha && $ficha['estado'] !== 'rascunho'): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>A sua ficha já foi submetida e está no estado <strong><?= $ficha['estado'] ?></strong>. Não é possível editar.
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?php if ($ficha['fotografia']): ?>
                                <img src="../<?= $ficha['fotografia'] ?>" class="preview-img img-thumbnail" alt="Foto">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <p><strong>Nome:</strong> <?= htmlspecialchars($ficha['nome_completo']) ?></p>
                            <p><strong>Data Nascimento:</strong> <?= $ficha['data_nascimento'] ?></p>
                            <p><strong>Morada:</strong> <?= htmlspecialchars($ficha['morada']) ?></p>
                            <p><strong>Telefone:</strong> <?= htmlspecialchars($ficha['telefone']) ?></p>
                            <p><strong>Email Contacto:</strong> <?= htmlspecialchars($ficha['email_contato']) ?></p>
                            <p><strong>Curso:</strong> <?= htmlspecialchars($ficha['nome_cursos'] ?? $ficha['id_curso']) ?></p>
                        </div>
                    </div>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
                <?php else: ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome_completo" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($ficha['nome_completo'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="<?= $ficha['data_nascimento'] ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="morada" class="form-label">Morada *</label>
                            <textarea class="form-control" id="morada" name="morada" rows="2" required><?= htmlspecialchars($ficha['morada'] ?? '') ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone *</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($ficha['telefone'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_contato" class="form-label">Email de Contacto *</label>
                                <input type="email" class="form-control" id="email_contato" name="email_contato" value="<?= htmlspecialchars($ficha['email_contato'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="id_curso" class="form-label">Curso Pretendido *</label>
                            <select class="form-select" id="id_curso" name="id_curso" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= $curso['Id_cursos'] ?>" <?= ($ficha['id_curso'] ?? '') == $curso['Id_cursos'] ? 'selected' : '' ?>><?= htmlspecialchars($curso['nome_cursos']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fotografia" class="form-label">Fotografia (JPG/PNG, máx 2MB)</label>
                            <input type="file" class="form-control" id="fotografia" name="fotografia" accept=".jpg,.jpeg,.png">
                            <?php if (!empty($ficha['fotografia'])): ?>
                                <p class="mt-2">Atual: <img src="../<?= $ficha['fotografia'] ?>" width="80" alt="Foto"></p>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar Rascunho</button>
                        <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Cancelar</a>
                        <?php if ($ficha && $ficha['estado'] === 'rascunho'): ?>
                            <a href="submeter_ficha.php" class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>Submeter para Validação</a>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>