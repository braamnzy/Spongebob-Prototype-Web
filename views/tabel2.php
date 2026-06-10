<?php
require_once '../core/auth.php';
cekLogin();
if (!in_array($_SESSION['role'], ['karyawan', 'admin'])) {
    header("Location: ../dashboard_pelanggan.php");
    exit;
}
require_once '../core/functions.php';

$pageTitle = "Data Merchandise - The Krusty Krab";
$activePage = 'merch';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_merch'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = (float)$_POST['harga'];

    if ($id > 0) {
        ubahMerchandise($id, $nama, $deskripsi, $harga);
        $msg = "Merchandise berhasil diubah!";
    } else {
        tambahMerchandise($nama, $deskripsi, $harga);
        $msg = "Merchandise berhasil ditambahkan!";
    }
    echo "<script>alert('$msg'); window.location='tabel2.php';</script>";
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    hapusMerchandise($id);
    echo "<script>alert('Merchandise berhasil dihapus!'); window.location='tabel2.php';</script>";
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $editData = getMerchandiseById($_GET['edit']);
}

$merchs = getAllMerchandise();

include 'templates/header.php';
?>

<h3>Manajemen Merchandise Krusty Krab</h3>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($merchs)): ?>
                <tr><td colspan="5">Belum ada merchandise.</td></tr>
            <?php else: ?>
                <?php foreach($merchs as $m): ?>
                <tr>
                    <td><?= sprintf("MC%03d", $m['id']) ?></td>
                    <td><?= htmlspecialchars($m['nama']) ?></td>
                    <td><?= htmlspecialchars($m['deskripsi']) ?></td>
                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                    <td>
                        <div class="crud-actions">
                            <a href="?edit=<?= $m['id'] ?>" class="btn-edit">Edit</a>
                            <a href="?delete=<?= $m['id'] ?>" class="btn-danger" onclick="return confirm('Yakin ingin hapus barang jualan Tuan Krab ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="add-form">
    <h4><?= $editData ? 'Edit Merchandise' : 'Tambah Merchandise Baru' ?></h4>
    <form action="tabel2.php" method="POST">
        <?php if($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div>
                <label>Nama Merchandise</label>
                <input type="text" name="nama" required value="<?= htmlspecialchars($editData['nama'] ?? '') ?>">
            </div>
            <div>
                <label>Harga (Rp)</label>
                <input type="number" name="harga" required min="0" value="<?= htmlspecialchars($editData['harga'] ?? '') ?>">
            </div>
            <div>
                <label>Deskripsi Singkat</label>
                <input type="text" name="deskripsi" required value="<?= htmlspecialchars($editData['deskripsi'] ?? '') ?>">
            </div>
        </div>
        
        <div>
            <button type="submit" name="save_merch" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Tambahkan Merchandise' ?></button>
            <?php if($editData): ?>
                <a href="tabel2.php" class="btn-edit" style="background:#555;">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'templates/footer.php'; ?>
