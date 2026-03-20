<?php
// auth.php
session_start();

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotAuthenticated() {
    if (!isAuthenticated()) {
        header('Location: ../login.php');
        exit;
    }
}

function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function redirectIfNoRole($requiredRole) {
    redirectIfNotAuthenticated();
    if (!hasRole($requiredRole)) {
        // Redireciona para o dashboard adequado se não tiver permissão
        if (hasRole('aluno')) header('Location: ../aluno/dashboard.php');
        elseif (hasRole('funcionario')) header('Location: ../funcionario/dashboard.php');
        elseif (hasRole('gestor')) header('Location: ../gestor/dashboard.php');
        else header('Location: ../login.php');
        exit;
    }
}
?>