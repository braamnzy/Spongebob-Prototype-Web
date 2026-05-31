<?php
require_once __DIR__ . '/../config/database.php';

function bersihkanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function getAllMenu() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM menu ORDER BY kategori, nama");
    $rows   = [];
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function getMenuById($id) {
    global $conn;
    $id     = (int) $id;
    $result = mysqli_query($conn, "SELECT * FROM menu WHERE id = $id");
    return $result ? mysqli_fetch_assoc($result) : null;
}

function tambahMenu($nama, $harga, $deskripsi, $kategori) {
    global $conn;
    $nama      = bersihkanInput($nama);
    $harga     = (float) $harga;
    $deskripsi = bersihkanInput($deskripsi);
    $kategori  = in_array($kategori, ['makanan', 'minuman']) ? $kategori : 'makanan';
    return mysqli_query($conn, "INSERT INTO menu (nama, harga, deskripsi, kategori) VALUES ('$nama', $harga, '$deskripsi', '$kategori')");
}

function ubahMenu($id, $nama, $harga, $deskripsi, $kategori, $tersedia) {
    global $conn;
    $id        = (int) $id;
    $nama      = bersihkanInput($nama);
    $harga     = (float) $harga;
    $deskripsi = bersihkanInput($deskripsi);
    $kategori  = in_array($kategori, ['makanan', 'minuman']) ? $kategori : 'makanan';
    $tersedia  = $tersedia ? 1 : 0;
    return mysqli_query($conn, "UPDATE menu SET nama='$nama', harga=$harga, deskripsi='$deskripsi', kategori='$kategori', tersedia=$tersedia WHERE id=$id");
}

function hapusMenu($id) {
    global $conn;
    $id = (int) $id;
    return mysqli_query($conn, "DELETE FROM menu WHERE id = $id");
}


function getAllMerchandise() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM merchandise ORDER BY nama");
    $rows   = [];
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}


function getAllPesanan() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY created_at DESC");
    $rows = [];
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    } else {
        die("Error Query getAllPesanan: " . mysqli_error($conn));
    }
    return $rows;
}

function getPesananByUser($nama_pelanggan) {
    global $conn;
    $nama = bersihkanInput($nama_pelanggan);
    $result  = mysqli_query($conn, "
        SELECT * FROM pesanan
        WHERE nama_pelanggan = '$nama'
        ORDER BY created_at DESC
    ");
    $rows = [];
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function getDetailPesanan($pesanan_id) {
    global $conn;
    $pesanan_id = (int) $pesanan_id;
    $result     = mysqli_query($conn, "SELECT * FROM pesanan_detail WHERE pesanan_id = $pesanan_id");
    $rows       = [];
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function buatPesanan($nama_pelanggan, $menu_jumlah, $merch_jumlah, $catatan = '') {
    global $conn;
    $nama_pelanggan = bersihkanInput($nama_pelanggan);
    $catatan_clean  = bersihkanInput($catatan);
    $valid_items    = [];
    $total          = 0;

    // Proses item menu
    if (!empty($menu_jumlah)) {
        foreach ($menu_jumlah as $id => $jumlah) {
            $jumlah = (int) $jumlah;
            if ($jumlah <= 0) continue;
            $menu = getMenuById((int) $id);
            if (!$menu || !$menu['tersedia']) continue;
            $total        += $menu['harga'] * $jumlah;
            $valid_items[] = [
                'tipe'         => 'menu',
                'item_id'      => (int) $id,
                'nama_item'    => $menu['nama'],
                'harga_satuan' => $menu['harga'],
                'jumlah'       => $jumlah,
            ];
        }
    }

    // Proses item merchandise
    if (!empty($merch_jumlah)) {
        foreach ($merch_jumlah as $id => $jumlah) {
            $jumlah = (int) $jumlah;
            if ($jumlah <= 0) continue;
            $id_int = (int) $id;
            $result = mysqli_query($conn, "SELECT * FROM merchandise WHERE id = $id_int");
            $merch  = $result ? mysqli_fetch_assoc($result) : null;
            if (!$merch || $merch['stok'] < $jumlah) continue;
            $total        += $merch['harga'] * $jumlah;
            $valid_items[] = [
                'tipe'         => 'merchandise',
                'item_id'      => $id_int,
                'nama_item'    => $merch['nama'],
                'harga_satuan' => $merch['harga'],
                'jumlah'       => $jumlah,
            ];
        }
    }

    if (empty($valid_items)) {
        return ['success' => false, 'message' => 'Pilih minimal satu item untuk dipesan!'];
    }

    // Insert header pesanan
    $ins = mysqli_query($conn, "INSERT INTO pesanan (nama_pelanggan, catatan, total_harga) VALUES ('$nama_pelanggan', '$catatan_clean', $total)");
    if (!$ins) {
        return ['success' => false, 'message' => 'Gagal membuat pesanan! Error: ' . mysqli_error($conn)];
    }
    $pesanan_id = mysqli_insert_id($conn);

    // Insert detail & kurangi stok merchandise
    foreach ($valid_items as $vi) {
        $nama_esc = mysqli_real_escape_string($conn, $vi['nama_item']);
        mysqli_query($conn, "INSERT INTO pesanan_detail (pesanan_id, tipe, item_id, nama_item, harga_satuan, jumlah)
            VALUES ($pesanan_id, '{$vi['tipe']}', {$vi['item_id']}, '$nama_esc', {$vi['harga_satuan']}, {$vi['jumlah']})");

        if ($vi['tipe'] === 'merchandise') {
            mysqli_query($conn, "UPDATE merchandise SET stok = stok - {$vi['jumlah']} WHERE id = {$vi['item_id']}");
        }
    }

    return ['success' => true, 'message' => 'Pesanan berhasil dibuat!', 'pesanan_id' => $pesanan_id];
}

function updateStatusPesanan($id, $status) {
    global $conn;
    $id           = (int) $id;
    $valid_status = ['menunggu', 'diproses', 'selesai'];
    if (!in_array($status, $valid_status)) {
        return false;
    }
    return mysqli_query($conn, "UPDATE pesanan SET status = '$status' WHERE id = $id");
}
?>