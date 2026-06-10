<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'The Krusty Krab' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="dashboard-page">

    <nav class="navbar">
        <a href="../switch_role.php" title="Toggle Employee/Customer Role">
            <img src="../public/img/kk_logo.png" alt="Logo" class="nav-logo">
        </a>
        <div class="nav-divider"></div>
        <a href="../logout.php" class="nav-icon" title="Logout" onclick="return confirm('Yakin ingin keluar?')">
            <img src="../public/img/logout 1.png" alt="Logout">
        </a>
    </nav>

    <div class="main-content">
        <div class="card">
            <div class="page-tabs">
                <a href="../dashboard_karyawan.php" class="page-tab <?= ($activePage ?? '') === 'orders'  ? 'active' : '' ?>">📋 Orders</a>
                <a href="tabel1_crud.php"            class="page-tab <?= ($activePage ?? '') === 'menu'    ? 'active' : '' ?>">🍔 Menu</a>
                <a href="tabel2.php"                 class="page-tab <?= ($activePage ?? '') === 'merch'   ? 'active' : '' ?>">🎽 Merchandise</a>
                <a href="tabel3.php"                 class="page-tab <?= ($activePage ?? '') === 'pesanan' ? 'active' : '' ?>">📦 Pesanan Detail</a>
            </div>
