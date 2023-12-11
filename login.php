<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: crud.php');
        exit();
    } else {
        echo 'Invalid username or password';
    }
}
?>

<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <a href="register.php">If you don't have an account Register here</a>
    <form method="post" action="">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" id="password" required>
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </label><br>        
        <input type="submit" value="Login">
    </form>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>
