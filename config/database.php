<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "spongebob_db";

define('BASE_URL', '/Spongebob-Prototype-Web');

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>