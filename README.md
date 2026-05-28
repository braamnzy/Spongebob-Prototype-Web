Sebelum menjalankanya di localhost kalian perlu menambahkan database dan 3 tabel dengan query berikut

CREATE DATABASE spongebob_db;
USE spongebob_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL -- Memenuhi 2 jenis role akun berbeda
);

CREATE TABLE karakter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    pekerjaan VARCHAR(100),
    deskripsi TEXT
);

CREATE TABLE lokasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tempat VARCHAR(100) NOT NULL,
    pemilik VARCHAR(100)
);

CREATE TABLE episode (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    musim INT
);