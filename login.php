<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password) || empty($role)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ? AND role = ?");
            $stmt->execute([$email, $role]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_role'] = $user['role'];

                // Redireciona conforme o perfil
                if ($user['role'] === 'aluno') header('Location: aluno/dashboard.php');
                elseif ($user['role'] === 'funcionario') header('Location: funcionario/dashboard.php');
                elseif ($user['role'] === 'gestor') header('Location: gestor/dashboard.php');
                elseif ($user['role'] === 'admin') header('Location: admin/dashboard.php');
                exit;
            } else {
                $erro = "Email, palavra-passe ou perfil inválidos.";
            }
        } catch (PDOException $e) {
            $erro = "Erro: ". $e->getMessage();
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IPCA Gestão</title>
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
        .card-login {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
        }
        .card-login .card-header {
            background: transparent;
            border-bottom: none;
            text-align: center;
            padding-top: 2rem;
        }
        .card-login .card-header h2 {
            color: #4a4a4a;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .card-login .card-body {
            padding: 2rem;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }
        .footer-links {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-login">
                    <div class="card-header">
                        <h2><i class="bi bi-mortarboard-fill me-2"></i>IPCA Gestão</h2>
                        <p class="text-muted">Aceda ao sistema</p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($erro)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($erro) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="">
                            <div class="mb-4">
                                <label for="role" class="form-label"><i class="bi bi-person-badge me-1"></i>Perfil *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" disabled selected>Selecione o seu perfil</option>
                                    <option value="aluno" <?= ($_POST['role'] ?? '') === 'aluno' ? 'selected' : '' ?>>Aluno</option>
                                    <option value="funcionario" <?= ($_POST['role'] ?? '') === 'funcionario' ? 'selected' : '' ?>>Funcionário</option>
                                    <option value="gestor" <?= ($_POST['role'] ?? '') === 'gestor' ? 'selected' : '' ?>>Gestor Pedagógico</option>
                                    <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="exemplo@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label"><i class="bi bi-lock me-1"></i>Palavra-passe</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar <i class="bi bi-box-arrow-in-right ms-2"></i></button>
                        </form>
                        <div class="footer-links">
                            <small class="text-muted">Acesso restrito ao pessoal autorizado.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>