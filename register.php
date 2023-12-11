<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username is already taken
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Password verification checks
    $isPasswordValid = (
        strlen($password) >= 6 &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[@#$]/', $password)
    );

    if ($existingUser) {
        echo 'Username is already taken. Please choose a different username.';
    } elseif (!$isPasswordValid) {
        echo 'Password must be at least 6 characters long and include at least one lowercase letter, one uppercase letter, one digit, and one special character (@, #, $).';
    } else {
        // Hash the password and insert a new record if the username is unique and password is valid
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        header('Location: login.php');
        exit();
    }
}
?>

<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" id="password" required>
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </label><br>
        <input type="submit" value="Register">
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
