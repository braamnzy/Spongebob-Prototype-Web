<?php
require_once 'core/auth.php';
cekLogin();

if ($_SESSION['role'] !== 'karyawan' && $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Krusty Krab - Employee Home</title>
    <meta name="description" content="Welcome to The Krusty Krab! Employee Portal.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="home-page">

    <nav class="navbar">
        <a href="home_karyawan.php" title="Home">
            <img src="public/img/kk_logo.png" alt="Krusty Krab Logo" class="nav-logo">
        </a>
        <div class="nav-divider"></div>
        <a href="logout.php" class="nav-icon" title="Logout" onclick="return confirm('Yakin ingin keluar?')">
            <img src="public/img/logout 1.png" alt="Logout">
        </a>
        <div class="nav-divider"></div>
        <a href="dashboard_karyawan.php" class="nav-icon" title="Workspace">
            <img src="public/img/checkout 1.png" alt="Workspace">
        </a>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Hi, <span><?= htmlspecialchars($_SESSION['username']) ?>.</span><br>Welcome to Employee Portal!</h1>
            <a href="dashboard_karyawan.php" class="btn-order">Manage Workspace &gt;&gt;</a>
        </div>
        <div class="hero-right">
            <img src="public/img/kk_sign.png" alt="The Krusty Krab Sign">
        </div>
    </section>

    <footer>
        <nav class="footer-nav">
            <a href="home_karyawan.php">Home</a>
            <a href="dashboard_karyawan.php">Orders</a>
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
        </nav>

        <div class="footer-info">
            <p>Operational Time:</p>
            <div class="hours">09.00 - 20.00</div>
            <div class="social-icons">
                <a href="#" title="Instagram">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                    </svg>
                </a>
                <a href="#" title="YouTube">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
                        <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>
                    </svg>
                </a>
                <a href="#" title="X (Twitter)">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="footer-logo">
            <img src="public/img/kk_logo.png" alt="The Krusty Krab">
        </div>
    </footer>

</body>
</html>
