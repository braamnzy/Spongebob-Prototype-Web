<?php
require_once '../core/auth.php';
cekLogin();
if (!in_array($_SESSION['role'], ['karyawan', 'admin'])) {
    header("Location: ../dashboard_pelanggan.php");
    exit;
}
require_once '../core/functions.php';

$pageTitle = "Data Pesanan Detail - The Krusty Krab";
$activePage = 'pesanan';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM pesanan_detail WHERE id = $id");
    echo "<script>alert('Detail pesanan berhasil dihapus!'); window.location='tabel3.php';</script>";
    exit;
}

$query = "SELECT pd.*, p.nama_pelanggan, p.created_at 
          FROM pesanan_detail pd 
          JOIN pesanan p ON pd.pesanan_id = p.id 
          ORDER BY p.created_at DESC";
$res = mysqli_query($conn, $query);
$details = [];
if ($res) {
    while($r = mysqli_fetch_assoc($res)) {
        $details[] = $r;
    }
}

include 'templates/header.php';
?>

<h3>Detail Item Pesanan</h3>

<div class="info-tab">
    <h4>Info Tabel</h4>
    <p>Halaman ini menampilkan rincian barang/menu yang dipesan di dalam setiap transaksi pesanan.</p>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID Detail</th>
                <th>Waktu Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Tipe Barang</th>
                <th>Nama Item</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($details)): ?>
                <tr><td colspan="8">Belum ada detail pesanan.</td></tr>
            <?php else: ?>
                <?php foreach($details as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= date('d M Y H:i', strtotime($d['created_at'])) ?></td>
                    <td><?= htmlspecialchars($d['nama_pelanggan']) ?></td>
                    <td>
                        <span style="font-weight: 600; color: <?= $d['tipe'] == 'menu' ? '#e67e22' : '#2980b9' ?>;">
                            <?= strtoupper($d['tipe']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($d['nama_item']) ?></td>
                    <td><?= $d['jumlah'] ?>x</td>
                    <td>Rp <?= number_format($d['jumlah'] * $d['harga_satuan'], 0, ',', '.') ?></td>
                    <td>
                        <div class="crud-actions">
                            <a href="?delete=<?= $d['id'] ?>" class="btn-danger" onclick="return confirm('Hapus detail item ini dari pesanan?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'templates/footer.php'; ?>
