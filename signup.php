<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Se já estiver logado, redireciona
if (isAuthenticated()) {
    header('Location: index.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validações
    if (empty($nome) || empty($email) || empty($password) || empty($confirmar_password) || empty($role)) {
        $erro = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } elseif ($password !== $confirmar_password) {
        $erro = "As palavras-passe não coincidem.";
    } elseif (strlen($password) < 6) {
        $erro = "A palavra-passe deve ter pelo menos 6 caracteres.";
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = "Este email já está registado.";
        } else {
            // Inserir novo utilizador
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nome, $email, $hashed_password, $role])) {
                $sucesso = "Conta criada com sucesso! Já pode fazer login.";
                // Limpar campos
                $_POST = [];
            } else {
                $erro = "Erro ao criar conta. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - IPCA Gestão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card-signup {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
        }
        .card-signup .card-header {
            background: transparent;
            border-bottom: none;
            text-align: center;
            padding-top: 2rem;
        }
        .card-signup .card-body {
            padding: 2rem;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-signup">
                    <div class="card-header">
                        <h2><i class="bi bi-person-plus-fill me-2"></i>Criar Conta</h2>
                        <p class="text-muted">Registe-se no sistema IPCA Gestão</p>
                    </div>
                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $erro ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($sucesso): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-2"></i><?= $sucesso ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="login.php" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Ir para Login</a>
                            </div>
                        <?php else: ?>
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="nome" class="form-label"><i class="bi bi-person me-1"></i>Nome completo *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label"><i class="bi bi-envelope me-1"></i>Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label"><i class="bi bi-person-badge me-1"></i>Perfil *</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="" disabled <?= !isset($_POST['role']) ? 'selected' : '' ?>>Selecione o seu perfil</option>
                                        <option value="aluno" <?= (isset($_POST['role']) && $_POST['role']=='aluno') ? 'selected' : '' ?>>Aluno</option>
                                        <option value="funcionario" <?= (isset($_POST['role']) && $_POST['role']=='funcionario') ? 'selected' : '' ?>>Funcionário</option>
                                        <option value="gestor" <?= (isset($_POST['role']) && $_POST['role']=='gestor') ? 'selected' : '' ?>>Gestor Pedagógico</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label"><i class="bi bi-lock me-1"></i>Palavra-passe * (mínimo 6 caracteres)</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="mb-4">
                                    <label for="confirmar_password" class="form-label"><i class="bi bi-lock-fill me-1"></i>Confirmar palavra-passe *</label>
                                    <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mb-3"><i class="bi bi-check-circle me-2"></i>Registar</button>
                                <div class="footer-links">
                                    <a href="login.php"><i class="bi bi-arrow-left me-1"></i>Voltar para Login</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>