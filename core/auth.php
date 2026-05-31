<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function login($username, $password) {
    global $conn;
    $username = trim(mysqli_real_escape_string($conn, $username));
    $password = trim(mysqli_real_escape_string($conn, $password)); 
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['login']    = true;
        $_SESSION['user_id']  = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];
        return true;
    }
    return false;
}


function register($username, $password, $role) {
    global $conn;

    $username = trim(mysqli_real_escape_string($conn, $username));
    $password = trim(mysqli_real_escape_string($conn, $password)); 

    if (strlen($username) < 3) {
        return ['success' => false, 'message' => 'Username minimal 3 karakter!'];
    }

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        return ['success' => false, 'message' => 'Username sudah dipakai!'];
    }

    $role = in_array($role, ['pelanggan', 'karyawan']) ? $role : 'pelanggan';

    // Role karyawan bisa dibatasi atau ditambahkan manual, untuk tugas ini dibebaskan
    $query = mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");

    if ($query) {
        return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
    }
    return ['success' => false, 'message' => 'Gagal mendaftar!'];
}

// Fungsi Pengecekan Akses
function cekLogin() {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function cekRole($role) {
    cekLogin();
    if ($_SESSION['role'] !== $role) {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}
?>