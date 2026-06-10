<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function login($username, $password) {
    global $conn;
    $username = bersihkanInput($username);
    $result   = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            $_SESSION['login']    = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];
            return true;
        }
    }
    return false;
}

function cekLogin() {
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }
}
?>