<?php
require_once __DIR__ . '/../config/database.php';

function bersihkanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function getAllData($tabel) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM `$tabel`");
    $rows   = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function getUserByUsername($username) {
    global $conn;
    $username = bersihkanInput($username);
    $result   = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    return mysqli_fetch_assoc($result);
}

function registerUser($username, $password, $role = 'pelanggan') {
    global $conn;
    $username = bersihkanInput($username);
    $role     = bersihkanInput($role);
    $hash     = password_hash($password, PASSWORD_DEFAULT);
    return mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$hash', '$role')");
}

function getAllMenu() {
    return getAllData('menu');
}

function getMenuById($id) {
    global $conn;
    $id     = (int) $id;
    $result = mysqli_query($conn, "SELECT * FROM menu WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

function tambahMenu($nama, $deskripsi, $harga) {
    global $conn;
    $nama      = bersihkanInput($nama);
    $deskripsi = bersihkanInput($deskripsi);
    $harga     = (float) $harga;
    return mysqli_query($conn, "INSERT INTO menu (nama, deskripsi, harga) VALUES ('$nama', '$deskripsi', $harga)");
}

function ubahMenu($id, $nama, $deskripsi, $harga) {
    global $conn;
    $id        = (int) $id;
    $nama      = bersihkanInput($nama);
    $deskripsi = bersihkanInput($deskripsi);
    $harga     = (float) $harga;
    return mysqli_query($conn, "UPDATE menu SET nama='$nama', deskripsi='$deskripsi', harga=$harga WHERE id=$id");
}

function hapusMenu($id) {
    global $conn;
    $id = (int) $id;
    return mysqli_query($conn, "DELETE FROM menu WHERE id = $id");
}

function getAllMerchandise() {
    return getAllData('merchandise');
}

function getMerchandiseById($id) {
    global $conn;
    $id     = (int) $id;
    $result = mysqli_query($conn, "SELECT * FROM merchandise WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

function tambahMerchandise($nama, $deskripsi, $harga) {
    global $conn;
    $nama      = bersihkanInput($nama);
    $deskripsi = bersihkanInput($deskripsi);
    $harga     = (float) $harga;
    return mysqli_query($conn, "INSERT INTO merchandise (nama, deskripsi, harga) VALUES ('$nama', '$deskripsi', $harga)");
}

function ubahMerchandise($id, $nama, $deskripsi, $harga) {
    global $conn;
    $id        = (int) $id;
    $nama      = bersihkanInput($nama);
    $deskripsi = bersihkanInput($deskripsi);
    $harga     = (float) $harga;
    return mysqli_query($conn, "UPDATE merchandise SET nama='$nama', deskripsi='$deskripsi', harga=$harga WHERE id=$id");
}

function hapusMerchandise($id) {
    global $conn;
    $id = (int) $id;
    return mysqli_query($conn, "DELETE FROM merchandise WHERE id = $id");
}

function getAllPesanan() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY created_at DESC");
    $rows   = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function getPesananById($id) {
    global $conn;
    $id     = (int) $id;
    $result = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

function getPesananDetail($pesanan_id) {
    global $conn;
    $pesanan_id = (int) $pesanan_id;
    $result     = mysqli_query($conn, "SELECT * FROM pesanan_detail WHERE pesanan_id = $pesanan_id");
    $rows       = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function ubahStatusPesanan($id, $status) {
    global $conn;
    $id     = (int) $id;
    $status = bersihkanInput($status);
    return mysqli_query($conn, "UPDATE pesanan SET status='$status' WHERE id=$id");
}

function hapusPesanan($id) {
    global $conn;
    $id = (int) $id;
    return mysqli_query($conn, "DELETE FROM pesanan WHERE id = $id");
}

function buatPesanan($nama_pelanggan, $catatan, $total_harga, $items) {
    global $conn;
    $nama_pelanggan = bersihkanInput($nama_pelanggan);
    $catatan        = bersihkanInput($catatan);
    $total_harga    = (float) $total_harga;

    $ok = mysqli_query($conn, "INSERT INTO pesanan (nama_pelanggan, catatan, total_harga) VALUES ('$nama_pelanggan', '$catatan', $total_harga)");
    if (!$ok) return false;

    $pesanan_id = mysqli_insert_id($conn);

    foreach ($items as $item) {
        $tipe      = bersihkanInput($item['tipe']); // nilai: 'menu' atau 'merchandise'
        $item_id   = (int) $item['item_id'];
        $nama_item = bersihkanInput($item['nama_item']);
        $harga     = (float) $item['harga_satuan'];
        $jumlah    = (int) $item['jumlah'];
        mysqli_query($conn, "INSERT INTO pesanan_detail (pesanan_id, tipe, item_id, nama_item, harga_satuan, jumlah)
                             VALUES ($pesanan_id, '$tipe', $item_id, '$nama_item', $harga, $jumlah)");
    }

    return $pesanan_id;
}
?>