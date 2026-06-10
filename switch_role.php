<?php
require_once 'core/auth.php';
cekLogin();

if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    $_SESSION['role'] = 'pelanggan';
    header("Location: dashboard_pelanggan.php");
} else {
    $_SESSION['role'] = 'karyawan';
    header("Location: dashboard_karyawan.php");
}
exit;
?>
