<?php
require_once '../includes/auth.php';
redirectIfNoRole('aluno');
require_once '../includes/config.php';

$id_aluno = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id FROM ficha_aluno WHERE id_aluno = ? AND estado = 'rascunho'");
$stmt->execute([$id_aluno]);
$ficha = $stmt->fetch();

if ($ficha) {
    $update = $pdo->prepare("UPDATE ficha_aluno SET estado = 'submetida', data_submissao = NOW() WHERE id = ?");
    $update->execute([$ficha['id']]);
    $_SESSION['mensagem'] = "Ficha submetida com sucesso. Aguarde validação.";
} else {
    $_SESSION['erro'] = "Não foi encontrada nenhuma ficha em rascunho.";
}

header('Location: ficha.php');
exit;