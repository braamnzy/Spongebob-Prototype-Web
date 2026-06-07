<?php
require_once 'core/auth.php';
cekLogin();
if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: dashboard_karyawan.php");
    exit;
}
require_once 'core/functions.php';

// Proses pesanan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pesan'])) {
    $catatan = bersihkanInput($_POST['catatan']);
    $nama_pelanggan = $_SESSION['username'];
    
    $total_harga = 0;
    $pesanan_items = [];
    
    // hitung menu
    if (isset($_POST['menu'])) {
        foreach ($_POST['menu'] as $id => $item) {
            if ($item['jumlah'] > 0) {
                // ambil detail menu
                $id = (int)$id;
                $res = mysqli_query($conn, "SELECT nama, harga FROM menu WHERE id = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    $subtotal = $row['harga'] * (int)$item['jumlah'];
                    $total_harga += $subtotal;
                    
                    $pesanan_items[] = [
                        'tipe' => 'menu',
                        'item_id' => $id,
                        'nama_item' => $row['nama'],
                        'harga_satuan' => $row['harga'],
                        'jumlah' => (int)$item['jumlah']
                    ];
                }
            }
        }
    }
    
    // hitung merch
    if (isset($_POST['merch'])) {
        foreach ($_POST['merch'] as $id => $item) {
            if ($item['jumlah'] > 0) {
                // ambil detail merch
                $id = (int)$id;
                $res = mysqli_query($conn, "SELECT nama, harga FROM merchandise WHERE id = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    $subtotal = $row['harga'] * (int)$item['jumlah'];
                    $total_harga += $subtotal;
                    
                    $pesanan_items[] = [
                        'tipe' => 'merchandise',
                        'item_id' => $id,
                        'nama_item' => $row['nama'],
                        'harga_satuan' => $row['harga'],
                        'jumlah' => (int)$item['jumlah']
                    ];
                }
            }
        }
    }
    
    if ($total_harga > 0) {
        $q_pesanan = "INSERT INTO pesanan (nama_pelanggan, catatan, total_harga, status) VALUES ('$nama_pelanggan', '$catatan', $total_harga, 'menunggu')";
        if (mysqli_query($conn, $q_pesanan)) {
            $pesanan_id = mysqli_insert_id($conn);
            foreach ($pesanan_items as $pi) {
                $q_detail = "INSERT INTO pesanan_detail (pesanan_id, tipe, item_id, nama_item, harga_satuan, jumlah) 
                             VALUES ($pesanan_id, '{$pi['tipe']}', {$pi['item_id']}, '{$pi['nama_item']}', {$pi['harga_satuan']}, {$pi['jumlah']})";
                mysqli_query($conn, $q_detail);
            }
            echo "<script>alert('Pesanan berhasil dibuat! Menunggu diproses.'); window.location='dashboard_pelanggan.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih minimal satu pesanan!');</script>";
    }
}

$menu_list = getAllData('menu');
$merch_list = getAllData('merchandise');

// ambil info pesanan pengguna ini
$q_pesanan_saya = "SELECT * FROM pesanan WHERE nama_pelanggan = '{$_SESSION['username']}' ORDER BY id DESC LIMIT 5";
$res_pesanan_saya = mysqli_query($conn, $q_pesanan_saya);
$pesanan_saya = [];
if ($res_pesanan_saya) {
    while ($r = mysqli_fetch_assoc($res_pesanan_saya)) {
        $pesanan_saya[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Pelanggan - Krusty Krab</title>
    <style>
        .container { display: flex; gap: 20px; }
        .column { flex: 1; }
        .info-tab { background: #fdfd96; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;}
    </style>
</head>
<body>
    <h2>Selamat Datang di Krusty Krab, <?= $_SESSION['username']; ?>!</h2>
    <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    <hr>
    
    <?php if (!empty($pesanan_saya)): ?>
    <div class="info-tab">
        <h4>Status Pesanan Terbaru Anda:</h4>
        <ul>
            <?php foreach($pesanan_saya as $ps): ?>
                <li>Pesanan #<?= $ps['id'] ?> (Rp <?= number_format($ps['total_harga'], 0, ',', '.') ?>) - <strong>Status: <?= strtoupper($ps['status']) ?></strong></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="dashboard_pelanggan.php" method="POST">
        <div class="container">
            <div class="column">
                <h3>Daftar Menu Makanan</h3>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Pesan (Jumlah)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($menu_list)): ?>
                            <tr><td colspan="3">Tidak ada menu.</td></tr>
                        <?php else: ?>
                            <?php foreach ($menu_list as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['nama']) ?><br><small><?= htmlspecialchars($m['deskripsi']) ?></small></td>
                                <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                                <td><input type="number" name="menu[<?= $m['id'] ?>][jumlah]" value="0" min="0" style="width: 50px;"></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="column">
                <h3>Daftar Merchandise</h3>
                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Pesan (Jumlah)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($merch_list)): ?>
                            <tr><td colspan="3">Tidak ada merchandise.</td></tr>
                        <?php else: ?>
                            <?php foreach ($merch_list as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['nama']) ?><br><small><?= htmlspecialchars($m['deskripsi']) ?></small></td>
                                <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                                <td><input type="number" name="merch[<?= $m['id'] ?>][jumlah]" value="0" min="0" style="width: 50px;"></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <br>
        <label>Catatan Pesanan:</label><br>
        <textarea name="catatan" rows="3" cols="50" placeholder="Cth: Krabby Patty tanpa bawang..."></textarea><br><br>
        <button type="submit" name="pesan" style="padding: 10px 20px; font-size: 16px;">Kirim Pesanan</button>
    </form>
</body>
</html>
