<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/core/auth.php';


cekLogin();

if ($_SESSION['role'] === 'karyawan') {
    header("Location: " . BASE_URL . "/views/dashboard_role1.php");
} else {
    header("Location: " . BASE_URL . "/views/dashboard_role2.php");
}
exit;