<?php
require_once 'core/auth.php';
cekLogin();

if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: dashboard_karyawan.php");
} else {
    header("Location: dashboard_pelanggan.php");
}
exit;
?>