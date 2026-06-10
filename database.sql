CREATE DATABASE IF NOT EXISTS todolist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE todolist;

CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL,
    nama_admin VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS siswa (
    id_siswa INT AUTO_INCREMENT PRIMARY KEY,
    nama_siswa VARCHAR(100) NOT NULL,
    kelas VARCHAR(20),
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS tugas (
    id_tugas INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    judul_tugas VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    deadline DATETIME,
    prioritas ENUM('Rendah','Sedang','Tinggi') DEFAULT 'Sedang',
    status ENUM('Belum','Selesai') DEFAULT 'Belum',
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS jadwal (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    hari VARCHAR(20) NOT NULL,
    mata_pelajaran VARCHAR(100) NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifikasi (
    id_notifikasi INT AUTO_INCREMENT PRIMARY KEY,
    id_tugas INT NOT NULL,
    pesan VARCHAR(255) NOT NULL,
    tanggal_notif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tugas) REFERENCES tugas(id_tugas) ON DELETE CASCADE
);

-- Data contoh admin
INSERT INTO admin (username, password, nama_admin) VALUES ('admin', 'admin123', 'Administrator');

-- Data contoh siswa
INSERT INTO siswa (nama_siswa, kelas, email, password) VALUES
('hani', 'XI RPL 1', 'aiasti@student.smkn1rongga.sch.id', 'hani123');

-- Data contoh tugas
INSERT INTO tugas (id_siswa, judul_tugas, deskripsi, deadline, prioritas, status) VALUES
(1, 'Belajar React', 'Pelajari hooks dan component', DATE_ADD(NOW(), INTERVAL 1 DAY), 'Tinggi', 'Belum'),
(1, 'Mengerjakan PR', 'PR Matematika halaman 45', NOW(), 'Sedang', 'Belum'),
(1, 'Olahraga', 'Lari pagi 30 menit', NOW(), 'Rendah', 'Selesai');

-- Data contoh jadwal
INSERT INTO jadwal (id_siswa, hari, mata_pelajaran, jam_mulai, jam_selesai) VALUES
(1, 'Senin', 'Pemrograman Web', '07:00:00', '09:00:00'),
(1, 'Selasa', 'Basis Data', '09:00:00', '11:00:00'),
(1, 'Rabu', 'Matematika', '07:00:00', '08:30:00');

-- Data contoh notifikasi
INSERT INTO notifikasi (id_tugas, pesan) VALUES
(1, 'Tugas Belajar React deadline besok!'),
(2, 'Tugas Mengerjakan PR deadline hari ini!');
