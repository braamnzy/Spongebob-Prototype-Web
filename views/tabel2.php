<?php
require_once __DIR__ . '/../core/auth.php';
cekLogin();

$daftar_merch = getAllMerchandise();
$pageTitle    = 'Daftar Merchandise';
require_once __DIR__ . '/templates/header.php';
?>

<h3>Data Merchandise</h3>

<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Deskripsi</th>
        <th>Stok Tersedia</th>
    </tr>
    <?php 
    $no = 1; 
    if (empty($daftar_merch)): 
    ?>
    <tr>
        <td colspan="5" align="center">Data kosong</td>
    </tr>
    <?php 
    else:
        foreach ($daftar_merch as $m): 
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($m['nama']) ?></td>
        <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
        <td><?= htmlspecialchars($m['deskripsi']) ?></td>
        <td><?= $m['stok'] ?></td>
    </tr>
    <?php 
        endforeach;
    endif;
    ?>
</table>

<?php if ($_SESSION['role'] === 'pelanggan'): ?>
    <br>
    <a href="<?= BASE_URL ?>/views/dashboard_role2.php">[+] Pergi ke halaman pemesanan</a>
<?php endif; ?>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
