<?php
require_once __DIR__ . '/core/auth.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } elseif (login($username, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pemweb</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="main-container" style="max-width: 500px; margin-top: 50px;">
        <h2>Login Sistem</h2>

        <?php if ($error): ?>
            <p class="alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div>
                <label>Username:</label><br>
                <input type="text" name="username" required>
            </div>
            <div>
                <label>Password:</label><br>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Masuk</button>
        </form>
        
        <p style="margin-top:20px;">
            <a href="register.php">Belum punya akun? Daftar di sini.</a>
        </p>
    </div>
</body>
</html>