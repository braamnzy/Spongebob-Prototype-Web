<?php
require_once __DIR__ . '/../core/auth.php';
cekRole('karyawan');

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $pesanan_id = $_POST['pesanan_id'] ?? 0;
    $status     = $_POST['status'] ?? '';

    if (updateStatusPesanan($pesanan_id, $status)) {
        $success = 'Status pesanan berhasil diperbarui!';
    } else {
        $error = 'Gagal memperbarui status pesanan!';
    }
}

$semua_pesanan = getAllPesanan();
$semua_menu    = getAllMenu();

$total_menunggu = count(array_filter($semua_pesanan, fn($p) => $p['status'] === 'menunggu'));
$total_diproses = count(array_filter($semua_pesanan, fn($p) => $p['status'] === 'diproses'));
$total_selesai  = count(array_filter($semua_pesanan, fn($p) => $p['status'] === 'selesai'));

$pageTitle = 'Dashboard Karyawan';
require_once __DIR__ . '/templates/header.php';
?>

<h3>Beranda Karyawan</h3>

<?php if ($success) echo "<p class='alert-success'>$success</p>"; ?>
<?php if ($error) echo "<p class='alert-error'>$error</p>"; ?>

<div style="margin-bottom: 20px;">
    <b>Ringkasan:</b>
    <ul>
        <li>Total Pesanan: <?= count($semua_pesanan) ?></li>
        <li>Menunggu: <?= $total_menunggu ?></li>
        <li>Diproses: <?= $total_diproses ?></li>
        <li>Selesai: <?= $total_selesai ?></li>
    </ul>
</div>

<h3>Data Pesanan Terbaru</h3>
<?php if (empty($semua_pesanan)): ?>
    <p>Belum ada data pesanan.</p>
<?php else: ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Item</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($semua_pesanan as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
            <td>
                <?php 
                $details = getDetailPesanan($p['id']); 
                foreach ($details as $d) {
                    echo htmlspecialchars($d['nama_item']) . " (x" . $d['jumlah'] . ")<br>";
                }
                ?>
                <?php if ($p['catatan']): ?>
                    <i>Catatan: <?= htmlspecialchars($p['catatan']) ?></i>
                <?php endif; ?>
            </td>
            <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
            <td><?= strtoupper($p['status']) ?></td>
            <td>
                <?php if ($p['status'] === 'menunggu'): ?>
                <form method="POST">
                    <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="status" value="diproses">
                    <button type="submit" name="update_status">Proses</button>
                </form>
                <?php elseif ($p['status'] === 'diproses'): ?>
                <form method="POST">
                    <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" name="update_status">Selesai</button>
                </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<br>
<h3>Data Menu Aktif</h3>
<table border="1">
    <tr>
        <th>Nama Menu</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Status</th>
    </tr>
    <?php foreach ($semua_menu as $m): ?>
    <tr>
        <td><?= htmlspecialchars($m['nama']) ?></td>
        <td><?= $m['kategori'] ?></td>
        <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
        <td><?= $m['tersedia'] ? 'Tersedia' : 'Habis' ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
