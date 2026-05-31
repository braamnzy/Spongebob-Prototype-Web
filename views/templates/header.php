<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? $pageTitle : 'Sistem Pemesanan' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<div class="header-top">
    <h2>Aplikasi Pemesanan Makanan</h2>
    <p>Login sebagai: <b><?= htmlspecialchars($_SESSION['username'] ?? '') ?></b> (Role: <?= htmlspecialchars($_SESSION['role'] ?? '') ?>)</p>

    <div class="nav-menu">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'karyawan'): ?>
            <a href="<?= BASE_URL ?>/views/dashboard_role1.php">Dashboard</a>
            <a href="<?= BASE_URL ?>/views/tabel1_crud.php">Manajemen Menu</a>
            <a href="<?= BASE_URL ?>/views/tabel3.php">Data Pesanan</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/views/dashboard_role2.php">Dashboard</a>
            <a href="<?= BASE_URL ?>/views/tabel2.php">Daftar Merchandise</a>
            <a href="<?= BASE_URL ?>/views/tabel3.php">Pesanan Saya</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/logout.php" class="logout-btn" onclick="return confirm('Apakah anda yakin ingin keluar dari sistem?')">Logout</a>
    </div>
</div>

<div class="main-container">
