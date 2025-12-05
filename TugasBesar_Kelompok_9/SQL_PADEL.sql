DROP DATABASE IF EXISTS db_sewa_padel;
CREATE DATABASE db_sewa_padel;
USE db_sewa_padel;

-- 1. Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

-- 2. Tabel Lapangan
CREATE TABLE lapangan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lapangan VARCHAR(100),
    harga_dasar INT, 
    deskripsi TEXT
);

-- 3. Tabel Sewa (Transaksi)
CREATE TABLE sewa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    lapangan_id INT,
    nomor_lapangan INT NOT NULL DEFAULT 1,
    tanggal_main DATE,
    jam_mulai TIME,
    durasi_jam INT,
    jumlah_raket INT DEFAULT 0,
    harga_per_jam_final INT, 
    total_bayar INT,
    keterangan VARCHAR(100) NULL,
    keterangan_harga TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (lapangan_id) REFERENCES lapangan(id)
);

-- 4. Data Dummy Lapangan
INSERT INTO lapangan (nama_lapangan, harga_dasar, deskripsi) VALUES 
('Court A (Indoor)', 200000, 'Lapangan Indoor Karpet'),
('Court B (Outdoor)', 150000, 'Lapangan Outdoor Semen');
