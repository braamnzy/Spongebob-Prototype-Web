<?php
require_once 'core/auth.php';
cekLogin();
if ($_SESSION['role'] !== 'karyawan' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard_pelanggan.php");
    exit;
}
require_once 'core/functions.php';

// Proses update status pesanan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id = (int)$_POST['pesanan_id'];
    $status = bersihkanInput($_POST['status']);
    
    // update db
    $query = "UPDATE pesanan SET status = '$status' WHERE id = $id";
    mysqli_query($conn, $query);
    
    echo "<script>alert('Status pesanan berhasil diupdate!'); window.location='dashboard_karyawan.php';</script>";
}

// Ambil data pesanan (urutkan dari yang terbaru)
$pesanan_result = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY created_at DESC");
$list_pesanan = [];
if ($pesanan_result) {
    while ($r = mysqli_fetch_assoc($pesanan_result)) {
        $list_pesanan[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Karyawan - Krusty Krab</title>
</head>
<body>
    <h2>Selamat Datang Karyawan, <?= $_SESSION['username']; ?>!</h2>
    <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    <hr>
    
    <h3>Daftar Pesanan Pelanggan</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Pelanggan</th>
                <th>Total Harga</th>
                <th>Catatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list_pesanan)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada pesanan.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($list_pesanan as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $p['created_at'] ?></td>
                    <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
                    <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($p['catatan']) ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td>
                        <form action="dashboard_karyawan.php" method="POST" style="display:inline;">
                            <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                            <select name="status">
                                <option value="menunggu" <?= $p['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="dibuat" <?= $p['status'] == 'dibuat' ? 'selected' : '' ?>>Dibuat</option>
                                <option value="disajikan" <?= $p['status'] == 'disajikan' ? 'selected' : '' ?>>Disajikan</option>
                            </select>
                            <button type="submit" name="update_status">Update Status</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
