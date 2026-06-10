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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - The Krusty Krab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="auth-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="19" y1="8" x2="19" y2="14"></line>
                <line x1="22" y1="11" x2="16" y2="11"></line>
            </svg>
            Register
        </div>

        <form action="register.php" method="POST" id="regForm" onsubmit="return validateForm()">
            <div class="radio-group">
                <label>
                    <input type="radio" name="role" value="pelanggan" required checked>
                    Customer
                </label>
                <label>
                    <input type="radio" name="role" value="karyawan" required>
                    Worker
                </label>
            </div>

            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" id="username" placeholder="Minimal 3 karakter" required onkeyup="checkUsername()">
                <div id="usernameHint" class="field-hint"></div>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required onkeyup="checkPassword()">
                <div id="passwordHint" class="field-hint"></div>
            </div>

            <div class="auth-footer">
                <a href="login.php">Already have an account? Sign in here.</a>
                <div class="auth-footer-right">
                    Continuing opening account for the Krusty Krab Database...
                    <button type="submit" class="btn-continue">Continue</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function checkUsername() {
            const el = document.getElementById('username');
            const hint = document.getElementById('usernameHint');
            
            if (el.value.length === 0) {
                el.className = '';
                hint.className = 'field-hint';
            } else if (el.value.length < 3) {
                el.className = 'input-invalid';
                hint.className = 'field-hint show invalid';
                hint.innerText = "Username terlalu pendek (min. 3 karakter).";
            } else {
                el.className = 'input-valid';
                hint.className = 'field-hint show valid';
                hint.innerText = "Username terlihat bagus!";
            }
        }

        function checkPassword() {
            const el = document.getElementById('password');
            const hint = document.getElementById('passwordHint');
            
            if (el.value.length === 0) {
                el.className = '';
                hint.className = 'field-hint';
            } else if (el.value.length < 6) {
                el.className = 'input-invalid';
                hint.className = 'field-hint show invalid';
                hint.innerText = "Password terlalu pendek (min. 6 karakter).";
            } else {
                el.className = 'input-valid';
                hint.className = 'field-hint show valid';
                hint.innerText = "Password memenuhi syarat!";
            }
        }

        function validateForm() {
            const user = document.getElementById('username').value;
            const pass = document.getElementById('password').value;
            
            if (user.length < 3) {
                alert("Gagal submit: Username minimal 3 karakter!");
                return false;
            }
            if (pass.length < 6) {
                alert("Gagal submit: Password minimal 6 karakter!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>