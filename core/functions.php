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
    $result = mysqli_query($conn, "SELECT * FROM $tabel");
    $rows   = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function tambahKarakter($nama, $pekerjaan, $deskripsi) {
    global $conn;
    $nama      = bersihkanInput($nama);
    $pekerjaan = bersihkanInput($pekerjaan);
    $deskripsi = bersihkanInput($deskripsi);
    return mysqli_query($conn, "INSERT INTO karakter (nama, pekerjaan, deskripsi) VALUES ('$nama', '$pekerjaan', '$deskripsi')");
}

function ubahKarakter($id, $nama, $pekerjaan, $deskripsi) {
    global $conn;
    $id        = (int) $id;
    $nama      = bersihkanInput($nama);
    $pekerjaan = bersihkanInput($pekerjaan);
    $deskripsi = bersihkanInput($deskripsi);
    return mysqli_query($conn, "UPDATE karakter SET nama = '$nama', pekerjaan = '$pekerjaan', deskripsi = '$deskripsi' WHERE id = $id");
}

function hapusKarakter($id) {
    global $conn;
    $id = (int) $id;
    return mysqli_query($conn, "DELETE FROM karakter WHERE id = $id");
}

function getKarakterById($id) {
    global $conn;
    $id     = (int) $id;
    $result = mysqli_query($conn, "SELECT * FROM karakter WHERE id = $id");
    return mysqli_fetch_assoc($result);
}
?>