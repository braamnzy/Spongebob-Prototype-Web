<?php
require_once 'core/auth.php';

if (isset($_SESSION['login'])) {
    $redirect = ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') ? 'home_karyawan.php' : 'home.php';
    header("Location: $redirect");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        $redirect = ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') ? 'home_karyawan.php' : 'home.php';
        header("Location: $redirect");
        exit;
    } else {
        $error = "Username atau password salah, Bung!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The Krusty Krab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="auth-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Login
        </div>

        <?php if (isset($error)): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" placeholder="Enter your username here" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter your password here" required>
            </div>

            <div class="auth-footer">
                <a href="register.php">Don't have an account? Sign up here.</a>
                <div class="auth-footer-right">
                    Continuing to the Krusty Krab Database...
                    <button type="submit" class="btn-continue">Continue</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>