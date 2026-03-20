<?php
require_once 'includes/auth.php';
if (isAuthenticated()) {
    // Redirecionar conforme o perfil
    if ($_SESSION['user_role'] === 'aluno') header('Location: aluno/dashboard.php');
    elseif ($_SESSION['user_role'] === 'funcionario') header('Location: funcionario/dashboard.php');
    elseif ($_SESSION['user_role'] === 'gestor') header('Location: gestor/dashboard.php');
    else header('Location: login.php');
} else {
    header('Location: login.php');
}
exit;