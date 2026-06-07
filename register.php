<?php
require_once 'core/auth.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username        = bersihkanInput($_POST['username']);
    $password        = $_POST['password'];
    $role            = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: Gagal mendaftar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register - Bikini Bottom</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Pilih Role:</label><br>
        <select name="role" required>
            <option value="pelanggan">Pelanggan</option>
            <option value="karyawan">Karyawan (Krusty Krabs)</option>
        </select><br><br>

        <button type="submit">Daftar Akun</button>
    </form>
    <br>
    <a href="login.php">Sudah punya akun? Login di sini.</a>
</body>
</html>