<?php
require_once __DIR__ . '/core/auth.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'pelanggan';

    if (empty($username) || empty($password)) {
        $error = 'Semua field wajib diisi!';
    } else {
        $result = register($username, $password, $role);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Pemweb</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="main-container" style="max-width: 500px; margin-top: 50px;">
        <h2>Daftar Akun Baru</h2>

        <?php if ($error): ?>
            <p class="alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="alert-success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div>
                <label>Username:</label><br>
                <input type="text" name="username" required>
            </div>
            <div>
                <label>Password:</label><br>
                <input type="password" name="password" required>
            </div>
            <div>
                <label>Daftar Sebagai:</label><br>
                <select name="role">
                    <option value="pelanggan">Pelanggan</option>
                    <option value="karyawan">Karyawan</option>
                </select>
            </div>
            <button type="submit">Daftar</button>
        </form>

        <p style="margin-top:20px;">
            <a href="login.php">Sudah punya akun? Kembali ke Login.</a>
        </p>
    </div>
</body>
</html>