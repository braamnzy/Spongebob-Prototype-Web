<?php
require_once __DIR__ . '/../core/auth.php';
cekLogin();

$success = '';
$error   = '';
$is_karyawan = $_SESSION['role'] === 'karyawan';

if ($is_karyawan && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $pesanan_id = $_POST['pesanan_id'] ?? 0;
    $status     = $_POST['status'] ?? '';

    if (updateStatusPesanan($pesanan_id, $status)) {
        $success = 'Status pesanan berhasil diperbarui!';
    } else {
        $error = 'Gagal memperbarui status!';
    }
}

// Filter mode by GET
$filter = $_GET['filter'] ?? 'all';

$daftar_pesanan = $is_karyawan ? getAllPesanan() : getPesananByUser($_SESSION['username']);

if ($filter !== 'all') {
    $daftar_pesanan = array_filter($daftar_pesanan, fn($p) => $p['status'] === $filter);
}

$pageTitle = $is_karyawan ? 'Semua Pesanan' : 'Pesanan Saya';
require_once __DIR__ . '/templates/header.php';
?>

<h3><?= $pageTitle ?></h3>

<?php if ($success) echo "<p class='alert-success'>$success</p>"; ?>
<?php if ($error) echo "<p class='alert-error'>$error</p>"; ?>

<form method="GET" action="tabel3.php">
    <label>Filter Status: </label>
    <select name="filter">
        <option value="all" <?= $filter==='all' ? 'selected' : '' ?>>Semua</option>
        <option value="menunggu" <?= $filter==='menunggu' ? 'selected' : '' ?>>Menunggu</option>
        <option value="diproses" <?= $filter==='diproses' ? 'selected' : '' ?>>Diproses</option>
        <option value="selesai" <?= $filter==='selesai' ? 'selected' : '' ?>>Selesai</option>
    </select>
    <button type="submit">Filter</button>
</form>
<br>

<table border="1">
    <tr>
        <th>Format ID</th>
        <?php if ($is_karyawan): ?><th>Nama Pelanggan</th><?php endif; ?>
        <th>Daftar Pembelian</th>
        <th>Total Bayar</th>
        <th>Catatan</th>
        <th>Tanggal Transaksi</th>
        <th>Status</th>
        <?php if ($is_karyawan): ?><th>Aksi (Update)</th><?php endif; ?>
    </tr>
    <?php if (empty($daftar_pesanan)): ?>
    <tr>
        <td colspan="<?= $is_karyawan ? 8 : 6 ?>" align="center">Tidak ada transaksi</td>
    </tr>
    <?php else: ?>
        <?php foreach ($daftar_pesanan as $p): ?>
        <tr>
            <td>INV-<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></td>
            <?php if ($is_karyawan): ?>
                <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
            <?php endif; ?>
            <td>
                <?php 
                $details = getDetailPesanan($p['id']); 
                foreach ($details as $d) {
                    echo "- " . htmlspecialchars($d['nama_item']) . " (" . $d['jumlah'] . " pcs)<br>";
                }
                ?>
            </td>
            <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
            <td><?= $p['catatan'] ? htmlspecialchars($p['catatan']) : '-' ?></td>
            <td><?= $p['created_at'] ?></td>
            <td><?= strtoupper($p['status']) ?></td>
            <?php if ($is_karyawan): ?>
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
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
