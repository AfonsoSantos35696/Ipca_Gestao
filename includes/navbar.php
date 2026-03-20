<?php
// navbar.php
// Assume que a sessão já foi iniciada em auth.php
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="bi bi-mortarboard-fill me-2"></i>IPCA Gestão</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($_SESSION['user_role'] === 'aluno'): ?>
                    <li class="nav-item"><a class="nav-link" href="../aluno/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../aluno/ficha.php"><i class="bi bi-file-person me-1"></i>Ficha de Aluno</a></li>
                    <li class="nav-item"><a class="nav-link" href="../aluno/matricula.php"><i class="bi bi-pencil-square me-1"></i>Pedido de Matrícula</a></li>
                    <li class="nav-item"><a class="nav-link" href="../aluno/consultar.php"><i class="bi bi-search me-1"></i>Consultar Estado</a></li>
                <?php elseif ($_SESSION['user_role'] === 'funcionario'): ?>
                    <li class="nav-item"><a class="nav-link" href="../funcionario/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../funcionario/pedidos_matricula.php"><i class="bi bi-envelope-paper me-1"></i>Pedidos de Matrícula</a></li>
                    <li class="nav-item"><a class="nav-link" href="../funcionario/pautas.php"><i class="bi bi-table me-1"></i>Pautas</a></li>
                <?php elseif ($_SESSION['user_role'] === 'gestor'): ?>
                    <li class="nav-item"><a class="nav-link" href="../gestor/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../gestor/cursos.php"><i class="bi bi-book me-1"></i>Cursos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../gestor/disciplinas.php"><i class="bi bi-journal-bookmark-fill me-1"></i>Unidades Curriculares</a></li>
                    <li class="nav-item"><a class="nav-link" href="../gestor/plano_estudos.php"><i class="bi bi-diagram-3 me-1"></i>Plano de Estudos</a></li>
                    <li class="nav-item"><a class="nav-link" href="../gestor/fichas_alunos.php"><i class="bi bi-files me-1"></i>Fichas de Aluno</a></li>
                <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="../admin/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/utilizadores.php"><i class="bi bi-people me-1"></i>Utilizadores</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white me-3">
                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Sair</a>
        </div>
    </div>
</nav>