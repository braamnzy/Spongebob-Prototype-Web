<?php
require_once 'core/auth.php';
cekLogin();

if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: home_karyawan.php");
} else {
    header("Location: home.php");
}
exit;
?>