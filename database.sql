

CREATE DATABASE IF NOT EXISTS spongebob_db;
USE spongebob_db;

DROP TABLE IF EXISTS pesanan_detail;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS merchandise;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('pelanggan', 'karyawan') NOT NULL DEFAULT 'pelanggan'
);


CREATE TABLE menu (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    harga       INT NOT NULL,
    deskripsi   TEXT,
    kategori    VARCHAR(50),
    tersedia    INT NOT NULL DEFAULT 1
);


CREATE TABLE merchandise (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    harga       INT NOT NULL,
    deskripsi   TEXT,
    stok        INT NOT NULL DEFAULT 0
);

CREATE TABLE pesanan (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan  VARCHAR(100) NOT NULL,
    catatan         TEXT,
    total_harga     INT NOT NULL DEFAULT 0,
    status          VARCHAR(50) NOT NULL DEFAULT 'menunggu',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE pesanan_detail (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id      INT NOT NULL,
    tipe            VARCHAR(50) NOT NULL,
    item_id         INT NOT NULL,
    nama_item       VARCHAR(100) NOT NULL,
    harga_satuan    INT NOT NULL,
    jumlah          INT NOT NULL DEFAULT 1
);


INSERT INTO users (username, password, role) VALUES
('spongebob', 'password123', 'pelanggan'),
('mrkrabs', 'password123', 'karyawan');
INSERT INTO menu (nama, harga, deskripsi, kategori, tersedia) VALUES
('Krabby Patty', 25000, 'Burger legendaris', 'makanan', 1),
('Kelp Shake', 12000, 'Minuman segar', 'minuman', 1);

INSERT INTO merchandise (nama, harga, deskripsi, stok) VALUES
('Topi Krusty Krab', 45000, 'Topi koki', 30);
