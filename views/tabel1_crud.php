<?php
require_once __DIR__ . '/../core/auth.php';
cekRole('karyawan');

$success = '';
$error   = '';
$edit_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama     = $_POST['nama'] ?? '';
    $harga    = $_POST['harga'] ?? 0;
    $deskripsi= $_POST['deskripsi'] ?? '';
    $kategori = $_POST['kategori'] ?? 'makanan';

    if (empty($nama) || $harga <= 0) {
        $error = 'Nama dan harga wajib diisi!';
    } elseif (tambahMenu($nama, $harga, $deskripsi, $kategori)) {
        $success = 'Data menu berhasil ditambahkan!';
    } else {
        $error = 'Gagal menambahkan data!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah'])) {
    $id       = $_POST['id'] ?? 0;
    $nama     = $_POST['nama'] ?? '';
    $harga    = $_POST['harga'] ?? 0;
    $deskripsi= $_POST['deskripsi'] ?? '';
    $kategori = $_POST['kategori'] ?? 'makanan';
    $tersedia = isset($_POST['tersedia']) ? 1 : 0;

    if (empty($nama) || $harga <= 0) {
        $error = 'Nama dan harga wajib diisi!';
    } elseif (ubahMenu($id, $nama, $harga, $deskripsi, $kategori, $tersedia)) {
        $success = 'Data menu berhasil diperbarui!';
    } else {
        $error = 'Gagal memperbarui data!';
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus' && isset($_GET['id'])) {
    if (hapusMenu($_GET['id'])) {
        $success = 'Data menu berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus data!';
    }
    header("Location: tabel1_crud.php?success=" . urlencode($success));
    exit;
}

if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

if (isset($_GET['aksi']) && $_GET['aksi'] === 'edit' && isset($_GET['id'])) {
    $edit_data = getMenuById($_GET['id']);
}

$daftar_menu = getAllMenu();
$pageTitle   = 'Manajemen Menu';
require_once __DIR__ . '/templates/header.php';
?>

<h3>Manajemen Data Menu</h3>

<?php if ($success) echo "<p class='alert-success'>$success</p>"; ?>
<?php if ($error) echo "<p class='alert-error'>$error</p>"; ?>

<h4><?= $edit_data ? 'Edit Data Menu' : 'Tambah Data Menu' ?></h4>
<?php if ($edit_data): ?>
    <a href="tabel1_crud.php">[Batal Edit]</a><br><br>
<?php endif; ?>

<form action="tabel1_crud.php" method="POST">
    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
    <?php endif; ?>

    <label>Nama Menu:</label><br>
    <input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" required><br><br>

    <label>Harga (Rp):</label><br>
    <input type="number" name="harga" value="<?= $edit_data['harga'] ?? '' ?>" required><br><br>

    <label>Kategori:</label><br>
    <select name="kategori">
        <option value="makanan" <?= (($edit_data['kategori'] ?? '') === 'makanan') ? 'selected' : '' ?>>Makanan</option>
        <option value="minuman" <?= (($edit_data['kategori'] ?? '') === 'minuman') ? 'selected' : '' ?>>Minuman</option>
    </select><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" rows="3" cols="30"><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea><br><br>

    <?php if ($edit_data): ?>
        <label>
            <input type="checkbox" name="tersedia" value="1" <?= $edit_data['tersedia'] ? 'checked' : '' ?>>
            Tersedia
        </label><br><br>
    <?php endif; ?>

    <?php if ($edit_data): ?>
        <button type="submit" name="ubah">Simpan Perubahan</button>
    <?php else: ?>
        <button type="submit" name="tambah">Tambah Data</button>
    <?php endif; ?>
</form>

<hr>

<h4>Daftar Menu</h4>
<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Menu</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Deskripsi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php 
    $no = 1; 
    if (empty($daftar_menu)): 
    ?>
    <tr>
        <td colspan="7" align="center">Data kosong</td>
    </tr>
    <?php 
    else:
        foreach ($daftar_menu as $m): 
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($m['nama']) ?></td>
        <td><?= $m['kategori'] ?></td>
        <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
        <td><?= htmlspecialchars($m['deskripsi']) ?></td>
        <td><?= $m['tersedia'] ? 'Tersedia' : 'Habis' ?></td>
        <td>
            <a href="tabel1_crud.php?aksi=edit&id=<?= $m['id'] ?>">Edit</a> | 
            <a href="tabel1_crud.php?aksi=hapus&id=<?= $m['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
    </tr>
    <?php 
        endforeach; 
    endif;
    ?>
</table>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
