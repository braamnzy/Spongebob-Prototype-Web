<?php
require_once __DIR__ . '/../core/auth.php';
cekRole('pelanggan');

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $menu_jumlah  = $_POST['menu_jumlah'] ?? [];
    $merch_jumlah = $_POST['merch_jumlah'] ?? [];
    $catatan      = $_POST['catatan'] ?? '';

    $result = buatPesanan($_SESSION['username'], $menu_jumlah, $merch_jumlah, $catatan);

    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

$daftar_menu  = getAllMenu();
$daftar_merch = getAllMerchandise();
$pesanan_saya = getPesananByUser($_SESSION['username']);

$pageTitle = 'Dashboard Pelanggan';
require_once __DIR__ . '/templates/header.php';
?>

<h3>Beranda Pelanggan</h3>

<?php if ($success) echo "<p class='alert-success'>$success</p>"; ?>
<?php if ($error) echo "<p class='alert-error'>$error</p>"; ?>

<form action="dashboard_role2.php" method="POST">

    <h4>Daftar Menu Makanan & Minuman</h4>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Nama Menu</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Jumlah Beli</th>
        </tr>
        <?php $no = 1; foreach ($daftar_menu as $item): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($item['nama']) ?></td>
            <td><?= $item['kategori'] ?></td>
            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
            <td><?= $item['tersedia'] ? 'Tersedia' : 'Kosong' ?></td>
            <td>
                <?php if ($item['tersedia']): ?>
                    <input type="number" name="menu_jumlah[<?= $item['id'] ?>]" min="0" max="100" value="0">
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h4>Daftar Merchandise</h4>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Jumlah Beli</th>
        </tr>
        <?php $no = 1; foreach ($daftar_merch as $item): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($item['nama']) ?></td>
            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
            <td><?= $item['stok'] ?></td>
            <td>
                <?php if ($item['stok'] > 0): ?>
                    <input type="number" name="merch_jumlah[<?= $item['id'] ?>]" min="0" max="<?= $item['stok'] ?>" value="0">
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <div>
        <label>Catatan Pesanan:</label><br>
        <textarea name="catatan" rows="3" cols="40"></textarea>
    </div>
    <br>
    <button type="submit" name="pesan">Pesan Sekarang</button>
</form>

<br>
<hr>

<h3>Riwayat Pesanan Anda</h3>
<?php
$tampil_pesanan = array_slice($pesanan_saya, 0, 5);
if (empty($tampil_pesanan)):
?>
    <p>Data tidak ditemukan.</p>
<?php else: ?>
    <table border="1">
        <tr>
            <th>No Pesanan</th>
            <th>Waktu</th>
            <th>Total Pembayaran</th>
            <th>Status</th>
            <th>Detail Barang</th>
        </tr>
        <?php foreach ($tampil_pesanan as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['created_at'] ?></td>
            <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
            <td><?= strtoupper($p['status']) ?></td>
            <td>
                <?php 
                $details = getDetailPesanan($p['id']); 
                foreach ($details as $d) {
                    echo htmlspecialchars($d['nama_item']) . " (x" . $d['jumlah'] . ")<br>";
                }
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
