<?php
require_once '../core/auth.php';
cekLogin();
if (!in_array($_SESSION['role'], ['karyawan', 'admin'])) {
    header("Location: ../dashboard_pelanggan.php");
    exit;
}
require_once '../core/functions.php';

$pageTitle = "Data Menu - The Krusty Krab";
$activePage = 'menu';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_menu'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = (float)$_POST['harga'];

    if ($id > 0) {
        ubahMenu($id, $nama, $deskripsi, $harga);
        $msg = "Menu berhasil diubah!";
    } else {
        tambahMenu($nama, $deskripsi, $harga);
        $msg = "Menu berhasil ditambahkan!";
    }
    echo "<script>alert('$msg'); window.location='tabel1_crud.php';</script>";
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    hapusMenu($id);
    echo "<script>alert('Menu berhasil dihapus!'); window.location='tabel1_crud.php';</script>";
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $editData = getMenuById($_GET['edit']);
}

$menus = getAllMenu();

include 'templates/header.php';
?>

<h3>Manajemen Menu Krusty Krab</h3>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Menu</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($menus)): ?>
                <tr><td colspan="5">Belum ada menu.</td></tr>
            <?php else: ?>
                <?php foreach($menus as $m): ?>
                <tr>
                    <td><?= sprintf("MN%03d", $m['id']) ?></td>
                    <td><?= htmlspecialchars($m['nama']) ?></td>
                    <td><?= htmlspecialchars($m['deskripsi']) ?></td>
                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                    <td>
                        <div class="crud-actions">
                            <a href="?edit=<?= $m['id'] ?>" class="btn-edit">Edit</a>
                            <a href="?delete=<?= $m['id'] ?>" class="btn-danger" onclick="return confirm('Krabby Patty ini mau dihapus beneran?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="add-form">
    <h4><?= $editData ? 'Edit Menu' : 'Tambah Menu Baru' ?></h4>
    <form action="tabel1_crud.php" method="POST">
        <?php if($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div>
                <label>Nama Menu</label>
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
            <button type="submit" name="save_menu" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Tambahkan Menu' ?></button>
            <?php if($editData): ?>
                <a href="tabel1_crud.php" class="btn-edit" style="background:#555;">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'templates/footer.php'; ?>
