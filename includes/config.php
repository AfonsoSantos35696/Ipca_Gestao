<?php

$host = 'localhost';
$database = 'ipca_gestao';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL server without selecting database initially
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Explicitly select the database
    $pdo->exec("USE `$database`");
    
} catch (PDOException $e) {
    die("Erro na ligação: " . $e->getMessage());
}
?>