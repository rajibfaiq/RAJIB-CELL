-- ============================================================
-- MIGRASI REVISI SIMRS — 5 Fitur Baru
-- Database: rumah sakit (MySQL)
-- ============================================================

-- 1. Tabel baru: poli
CREATE TABLE IF NOT EXISTS poli (
    ID_POLI VARCHAR(10) PRIMARY KEY,
    NAMA_POLI VARCHAR(100) NOT NULL,
    ICON VARCHAR(50) DEFAULT 'fa-hospital',
    KETERANGAN TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data poli
INSERT IGNORE INTO poli (ID_POLI, NAMA_POLI, ICON, KETERANGAN) VALUES
('POL001', 'Poli Umum', 'fa-stethoscope', 'Pemeriksaan umum'),
('POL002', 'Poli Gigi', 'fa-tooth', 'Pemeriksaan dan perawatan gigi'),
('POL003', 'Poli Anak', 'fa-baby', 'Pemeriksaan anak-anak'),
('POL004', 'Poli Kandungan', 'fa-female', 'Pemeriksaan kandungan dan kebidanan'),
('POL005', 'Poli Mata', 'fa-eye', 'Pemeriksaan mata'),
('POL006', 'Poli THT', 'fa-head-side-cough', 'Telinga, Hidung, Tenggorokan'),
('POL007', 'Poli Bedah', 'fa-procedures', 'Konsultasi dan tindakan bedah'),
('POL008', 'Poli Penyakit Dalam', 'fa-heartbeat', 'Penyakit dalam / internis');

-- 2. ALTER tabel dokter: tambah link ke poli dan kuota
ALTER TABLE dokter ADD COLUMN IF NOT EXISTS ID_POLI VARCHAR(10) DEFAULT NULL;
ALTER TABLE dokter ADD COLUMN IF NOT EXISTS KUOTA_HARIAN INT DEFAULT 20;

-- 3. ALTER tabel pendaftaran: tambah poli, dokter, antrian, status
ALTER TABLE pendaftaran ADD COLUMN IF NOT EXISTS ID_POLI VARCHAR(10) DEFAULT NULL;
ALTER TABLE pendaftaran ADD COLUMN IF NOT EXISTS ID_DOKTER VARCHAR(10) DEFAULT NULL;
ALTER TABLE pendaftaran ADD COLUMN IF NOT EXISTS NO_ANTRIAN INT DEFAULT NULL;
ALTER TABLE pendaftaran ADD COLUMN IF NOT EXISTS STATUS_ANTRIAN VARCHAR(20) DEFAULT 'menunggu';

-- 4. ALTER tabel rontgen: tambah detail rujukan
ALTER TABLE rontgen ADD COLUMN IF NOT EXISTS JENIS_RONTGEN VARCHAR(50) DEFAULT NULL;
ALTER TABLE rontgen ADD COLUMN IF NOT EXISTS KETERANGAN_KLINIS TEXT DEFAULT NULL;
ALTER TABLE rontgen ADD COLUMN IF NOT EXISTS CATATAN TEXT DEFAULT NULL;
ALTER TABLE rontgen ADD COLUMN IF NOT EXISTS STATUS VARCHAR(20) DEFAULT 'diminta';
ALTER TABLE rontgen ADD COLUMN IF NOT EXISTS TGL_PERMINTAAN DATETIME DEFAULT NULL;

-- 5. ALTER tabel users: link ke dokter
ALTER TABLE users ADD COLUMN IF NOT EXISTS ID_DOKTER VARCHAR(10) DEFAULT NULL;

-- 6. Tabel baru: pembayaran
CREATE TABLE IF NOT EXISTS pembayaran (
    ID_PEMBAYARAN VARCHAR(10) PRIMARY KEY,
    NO_PENDAFTARAN VARCHAR(10) DEFAULT NULL,
    JENIS_LAYANAN VARCHAR(30) NOT NULL,
    ID_REFERENSI VARCHAR(10) DEFAULT NULL,
    KETERANGAN_LAYANAN VARCHAR(255) DEFAULT NULL,
    BIAYA DECIMAL(12,0) NOT NULL DEFAULT 0,
    JENIS_PEMBAYARAN VARCHAR(20) DEFAULT 'Tunai',
    STATUS VARCHAR(20) DEFAULT 'belum_bayar',
    NO_KUITANSI VARCHAR(20) DEFAULT NULL,
    TGL_BAYAR DATETIME DEFAULT NULL,
    CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. ALTER tabel pengobatan: tambah kolom HARGA_OBAT
ALTER TABLE pengobatan ADD COLUMN IF NOT EXISTS HARGA_OBAT DECIMAL(12,0) DEFAULT 0;

-- 8. ALTER tabel perawatan: tambah kolom TGL_PERAWATAN
ALTER TABLE perawatan ADD COLUMN IF NOT EXISTS TGL_PERAWATAN DATE DEFAULT NULL AFTER ID_PASIEN;

-- 9. ALTER tabel pengobatan: tambah kolom ID_FARMASI & naikkan panjang NAMA_OBAT
ALTER TABLE pengobatan ADD COLUMN IF NOT EXISTS ID_FARMASI VARCHAR(10) DEFAULT NULL AFTER ID_PERIKSA;
ALTER TABLE pengobatan MODIFY COLUMN NAMA_OBAT VARCHAR(100) NOT NULL;

