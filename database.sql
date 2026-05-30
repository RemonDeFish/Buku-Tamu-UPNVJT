-- ============================================================
-- SIPPK - Sistem Informasi Pengunjung dan Pertemuan Kampus
-- Database: sippk_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS sippk_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sippk_db;

-- ------------------------------------------------------------
-- Tabel: users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    otp_code VARCHAR(10) NULL,
    otp_expired DATETIME NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Tabel: pertemuan (jadwal kunjungan / pertemuan)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kunjungan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_pengunjung VARCHAR(100) NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    instansi VARCHAR(150),
    keperluan VARCHAR(100) NOT NULL,
    tujuan TEXT NOT NULL,
    tanggal DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    status ENUM(
        'menunggu',
        'disetujui',
        'ditolak',
        'selesai'
    ) DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Tabel: log_aktivitas (audit trail)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    aktivitas VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id)
    REFERENCES admins(id)
    ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Data Awal: Admin Default
-- password: Admin@123  (bcrypt hash)
-- ------------------------------------------------------------
INSERT INTO admins (nama_lengkap, email, password)
VALUES (
    'Administrator SIPPK',
    'admin@sippk.ac.id',
    '$2y$12$Eq93nm/Hj4puRZolXtEGaexAqF/I7ahT63N5p4FHnVc6imNq74DYK'
);

-- Contoh user biasa
INSERT INTO kunjungan
(
    nama_pengunjung,
    no_telp,
    instansi,
    keperluan,
    tujuan,
    tanggal,
    waktu_mulai,
    waktu_selesai,
    status
)
VALUES
(
    'Raymond Harsono',
    '081234567890',
    'UPN Veteran Jawa Timur',
    'Konsultasi Akademik',
    'Menemui bu Made',
    '2026-06-01',
    '09:00:00',
    '10:00:00',
    'menunggu'
);

-- Contoh data pertemuan
INSERT INTO kunjungan (nama_pengunjung, no_telp, instansi, keperluan, tujuan, tanggal, waktu_mulai, waktu_selesai, status)
VALUES
(2, 'Dewi Rahayu',    'PT Nusantara Jaya', 'Diskusi kerja sama magang',       'Prof. Dr. Hendra Muani', '2025-07-10', '09:00:00', '10:00:00', 'disetujui'),
(2, 'Ahmad Fauzi',   'Dimas Pendidikan',  'Sosialisasi program beasiswa',    'Dr. Siti Musfota, M.Pd',   '2025-07-12', '13:00:00', '14:30:00', 'menunggu'),
(2, 'Widya Kartika',  'Yayasan Gak Peduli',    'Penyerahan bantuan beasiswa',     'Rektor',                   '2025-07-15', '10:00:00', '11:00:00', 'menunggu');
