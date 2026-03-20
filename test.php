<?php
require_once 'includes/config.php';

echo "<h2>Users in Database:</h2>";
$result = $pdo->query("SELECT id, nome, email, password, role FROM utilizadores");
$users = $result->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "<p style='color:red;'><strong>NO USERS FOUND!</strong> You need to import the SQL file.</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password Hash</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['nome'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . substr($user['password'], 0, 30) . "...</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>Test Login:</h2>";
$email = 'aluno@teste.com';
$password = 'teste123';
$role = 'aluno';

$stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE email = ? AND role = ?");
$stmt->execute([$email, $role]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "<p>User found: " . $user['nome'] . "</p>";
    echo "<p>Hash: " . $user['password'] . "</p>";
    if (password_verify($password, $user['password'])) {
        echo "<p style='color:green;'><strong>✓ Password is correct!</strong></p>";
    } else {
        echo "<p style='color:red;'><strong>✗ Password verification failed!</strong></p>";
        echo "<p>Trying hash validation...</p>";
        // Try to generate a correct hash
        $correctHash = password_hash($password, PASSWORD_BCRYPT);
        echo "<p>Correct hash for 'teste123': " . $correctHash . "</p>";
    }
} else {
    echo "<p style='color:red;'><strong>User not found with email: $email and role: $role</strong></p>";
}
?>