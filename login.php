<?php
require_once 'core/auth.php';

if (isset($_SESSION['login'])) {
    $redirect = ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') ? 'dashboard_karyawan.php' : 'dashboard_pelanggan.php';
    header("Location: $redirect");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        $redirect = ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') ? 'dashboard_karyawan.php' : 'dashboard_pelanggan.php';
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
    <title>Login - Bikini Bottom</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Masuk</button>
    </form>
    <br>
    <a href="register.php">Belum punya akun? Daftar di sini.</a>
</body>
</html>