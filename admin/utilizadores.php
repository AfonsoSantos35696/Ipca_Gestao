<?php
require_once '../includes/auth.php';
redirectIfNoRole('admin');
require_once '../includes/config.php';

$mensagem = '';
$erro = '';

// Processar ações (delete, add, edit) – igual ao original
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id == $_SESSION['user_id']) {
        $erro = "Não pode eliminar a sua própria conta.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM utilizadores WHERE id = ?");
        $stmt->execute([$id]);
        $mensagem = "Utilizador eliminado com sucesso.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (empty($nome) || empty($email) || empty($password) || empty($role)) {
            $erro = "Todos os campos são obrigatórios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Email inválido.";
        } elseif (strlen($password) < 6) {
            $erro = "A palavra-passe deve ter pelo menos 6 caracteres.";
        } else {
            $check = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $erro = "Este email já está registado.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $hashed_password, $role]);
                $mensagem = "Utilizador adicionado com sucesso.";
            }
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        $password = $_POST['password'];

        if (empty($nome) || empty($email) || empty($role)) {
            $erro = "Nome, email e perfil são obrigatórios.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Email inválido.";
        } else {
            $check = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ? AND id != ?");
            $check->execute([$email, $id]);
            if ($check->fetch()) {
                $erro = "Este email já está registado por outro utilizador.";
            } else {
                if (!empty($password)) {
                    if (strlen($password) < 6) {
                        $erro = "A palavra-passe deve ter pelo menos 6 caracteres.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE utilizadores SET nome = ?, email = ?, password = ?, role = ? WHERE id = ?");
                        $stmt->execute([$nome, $email, $hashed_password, $role, $id]);
                        $mensagem = "Utilizador atualizado com sucesso.";
                    }
                } else {
                    $stmt = $pdo->prepare("UPDATE utilizadores SET nome = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->execute([$nome, $email, $role, $id]);
                    $mensagem = "Utilizador atualizado com sucesso.";
                }
            }
        }
    }
}

$utilizadores = $pdo->query("SELECT * FROM utilizadores ORDER BY role, nome")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Utilizadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-utilizadores { background: white; border-radius: 1rem; overflow: hidden; }
        .filtros {
            background: white;
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .filtros .form-check {
            display: inline-block;
            margin-right: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi bi-people me-2"></i>Gestão de Utilizadores</h2>
        <?php if ($mensagem): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>

        <!-- Botão para adicionar (abre modal) -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-2"></i>Adicionar Utilizador
        </button>

        <!-- Filtros por perfil -->
        <div class="filtros mb-4">
            <strong class="me-3">Filtrar por perfil:</strong>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="admin" id="filtroAdmin" checked onchange="filtrarTabela()">
                <label class="form-check-label" for="filtroAdmin">Administrador</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="gestor" id="filtroGestor" checked onchange="filtrarTabela()">
                <label class="form-check-label" for="filtroGestor">Gestor</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="funcionario" id="filtroFuncionario" checked onchange="filtrarTabela()">
                <label class="form-check-label" for="filtroFuncionario">Funcionário</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="aluno" id="filtroAluno" checked onchange="filtrarTabela()">
                <label class="form-check-label" for="filtroAluno">Aluno</label>
            </div>
        </div>

        <!-- Tabela de utilizadores -->
        <div class="table-responsive">
            <table class="table table-hover table-utilizadores" id="tabelaUtilizadores">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Data Registo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilizadores as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $u['role'] == 'admin' ? 'dark' : ($u['role'] == 'gestor' ? 'warning' : ($u['role'] == 'funcionario' ? 'info' : 'primary')) ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($u['data_registo'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $u['id'] ?>"><i class="bi bi-pencil"></i> Editar</button>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que pretende eliminar este utilizador?')"><i class="bi bi-trash"></i> Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div class="modal fade" id="editModal<?= $u['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Utilizador</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <div class="mb-3">
                                            <label for="edit_nome<?= $u['id'] ?>" class="form-label">Nome</label>
                                            <input type="text" class="form-control" id="edit_nome<?= $u['id'] ?>" name="nome" value="<?= htmlspecialchars($u['nome']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_email<?= $u['id'] ?>" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="edit_email<?= $u['id'] ?>" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_password<?= $u['id'] ?>" class="form-label">Nova Palavra-passe (deixe em branco para manter a atual)</label>
                                            <input type="password" class="form-control" id="edit_password<?= $u['id'] ?>" name="password" placeholder="Mínimo 6 caracteres">
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_role<?= $u['id'] ?>" class="form-label">Perfil</label>
                                            <select class="form-select" id="edit_role<?= $u['id'] ?>" name="role" required>
                                                <option value="aluno" <?= $u['role']=='aluno'?'selected':'' ?>>Aluno</option>
                                                <option value="funcionario" <?= $u['role']=='funcionario'?'selected':'' ?>>Funcionário</option>
                                                <option value="gestor" <?= $u['role']=='gestor'?'selected':'' ?>>Gestor</option>
                                                <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Administrador</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" name="edit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Adicionar -->
        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title">Adicionar Utilizador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="add_nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="add_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_password" class="form-label">Palavra-passe</label>
                                <input type="password" class="form-control" id="add_password" name="password" placeholder="Mínimo 6 caracteres" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_role" class="form-label">Perfil</label>
                                <select class="form-select" id="add_role" name="role" required>
                                    <option value="">Selecione...</option>
                                    <option value="aluno">Aluno</option>
                                    <option value="funcionario">Funcionário</option>
                                    <option value="gestor">Gestor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="add" class="btn btn-success">Adicionar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarTabela() {
            // Obter estado dos checkboxes
            const mostrarAdmin = document.getElementById('filtroAdmin').checked;
            const mostrarGestor = document.getElementById('filtroGestor').checked;
            const mostrarFuncionario = document.getElementById('filtroFuncionario').checked;
            const mostrarAluno = document.getElementById('filtroAluno').checked;

            // Percorrer linhas do corpo da tabela (ignorar cabeçalho)
            const linhas = document.querySelectorAll('#tabelaUtilizadores tbody tr');
            linhas.forEach(linha => {
                // Obter o texto do badge de perfil (coluna 4, índice 3)
                const badge = linha.querySelector('td:nth-child(4) span.badge');
                if (!badge) return; // segurança
                const perfil = badge.textContent.trim().toLowerCase();

                // Decidir se mostra ou esconde
                let mostrar = false;
                if (perfil === 'admin' && mostrarAdmin) mostrar = true;
                else if (perfil === 'gestor' && mostrarGestor) mostrar = true;
                else if (perfil === 'funcionario' && mostrarFuncionario) mostrar = true;
                else if (perfil === 'aluno' && mostrarAluno) mostrar = true;

                linha.style.display = mostrar ? '' : 'none';
            });
        }

        // Executar filtro ao carregar a página (caso algum checkbox venha desmarcado por algum motivo)
        window.addEventListener('load', filtrarTabela);
    </script>
</body>
</html>