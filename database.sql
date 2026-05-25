-- Database Creation
CREATE DATABASE IF NOT EXISTS pembayaran_spp_siswa;
USE pembayaran_spp_siswa;

-- Table: tb_spp (Master Table)
CREATE TABLE tb_spp (
    id_spp VARCHAR(11) PRIMARY KEY,
    tahun INT(11),
    nominal VARCHAR(40)
);

-- Table: tb_petugas (Master Table)
CREATE TABLE tb_petugas (
    id_petugas VARCHAR(11) PRIMARY KEY,
    username VARCHAR(25),
    password VARCHAR(32),
    nama_petugas VARCHAR(35),
    level ENUM('admin', 'petugas', 'siswa')
);

-- Table: tb_kelas (Master Table)
CREATE TABLE tb_kelas (
    id_kelas VARCHAR(11) PRIMARY KEY,
    nama_kelas VARCHAR(10) UNIQUE,
    komp_keahlian VARCHAR(50)
);

-- Table: tb_siswa (Master Table)
CREATE TABLE tb_siswa (
    nisn VARCHAR(10) PRIMARY KEY,
    nis VARCHAR(8),
    nama VARCHAR(50) UNIQUE,
    id_kelas VARCHAR(11),
    nama_kelas VARCHAR(10),
    alamat TEXT,
    no_telp VARCHAR(13) UNIQUE,
    id_spp VARCHAR(40),
    FOREIGN KEY (id_kelas) REFERENCES tb_kelas(id_kelas),
    FOREIGN KEY (nama_kelas) REFERENCES tb_kelas(nama_kelas)
);

-- Table: tb_pembayaran (Transaction Table)
CREATE TABLE tb_pembayaran (
    id_pembayaran VARCHAR(11) PRIMARY KEY,
    status ENUM('Belum Lunas', 'Sudah Lunas'),
    nisn VARCHAR(10),
    tgl_bayar DATE,
    tgl_terakhir_bayar DATE,
    batas_pembayaran DATE,
    jumlah_bulan VARCHAR(10),
    id_spp VARCHAR(40),
    nominal_bayar VARCHAR(100),
    jumlah_bayar VARCHAR(40),
    kembalian VARCHAR(100),
    FOREIGN KEY (nisn) REFERENCES tb_siswa(nisn),
    FOREIGN KEY (id_spp) REFERENCES tb_spp(id_spp)
);

-- Table: cek_pembayaran (Transaction Table)
CREATE TABLE cek_pembayaran (
    nisn VARCHAR(10),
    tgl_terakhir_bayar DATE,
    tgl_sekarang DATE,
    status_pembayaran ENUM('Belum Lunas', 'Sudah Lunas'),
    jumlah_bulan VARCHAR(5),
    nama VARCHAR(50),
    no_telp VARCHAR(13),
    FOREIGN KEY (nisn) REFERENCES tb_siswa(nisn),
    FOREIGN KEY (nama) REFERENCES tb_siswa(nama),
    FOREIGN KEY (no_telp) REFERENCES tb_siswa(no_telp)
);

-- Insert Test Data for Master Tables (5 rows each)

-- Insert data for tb_spp
INSERT INTO tb_spp (id_spp, tahun, nominal) VALUES
('SPP001', 2023, '250000'),
('SPP002', 2024, '300000'),
('SPP003', 2025, '350000'),
('SPP004', 2026, '400000'),
('SPP005', 2027, '450000');

-- Insert data for tb_petugas
INSERT INTO tb_petugas (id_petugas, username, password, nama_petugas, level) VALUES
('PET001', 'admin', 'admin123', 'Administrator', 'admin'),
('PET002', 'petugas1', 'petugas123', 'Budi Santoso', 'petugas'),
('PET003', 'petugas2', 'petugas123', 'Siti Rahayu', 'petugas'),
('PET004', 'petugas3', 'petugas123', 'Ahmad Wijaya', 'petugas'),
('PET005', 'siswa1', 'siswa123', 'Dewi Lestari', 'siswa');

-- Insert data for tb_kelas
INSERT INTO tb_kelas (id_kelas, nama_kelas, komp_keahlian) VALUES
('KLS001', 'X RPL 1', 'Rekayasa Perangkat Lunak'),
('KLS002', 'X TKJ 1', 'Teknik Komputer Jaringan'),
('KLS003', 'XI RPL 1', 'Rekayasa Perangkat Lunak'),
('KLS004', 'XI TKJ 1', 'Teknik Komputer Jaringan'),
('KLS005', 'XII RPL 1', 'Rekayasa Perangkat Lunak');

-- Insert data for tb_siswa
INSERT INTO tb_siswa (nisn, nis, nama, id_kelas, nama_kelas, alamat, no_telp, id_spp) VALUES
('1234567890', '1001', 'Andi Pratama', 'KLS001', 'X RPL 1', 'Jl. Merdeka No. 10, Jakarta', '081234567890', 'SPP001'),
('1234567891', '1002', 'Budi Hartono', 'KLS002', 'X TKJ 1', 'Jl. Sudirman No. 20, Jakarta', '081234567891', 'SPP002'),
('1234567892', '1003', 'Citra Dewi', 'KLS003', 'XI RPL 1', 'Jl. Gatot Subroto No. 30, Jakarta', '081234567892', 'SPP003'),
('1234567893', '1004', 'Dian Kusuma', 'KLS004', 'XI TKJ 1', 'Jl. Thamrin No. 40, Jakarta', '081234567893', 'SPP004'),
('1234567894', '1005', 'Eko Saputra', 'KLS005', 'XII RPL 1', 'Jl. Rasuna Said No. 50, Jakarta', '081234567894', 'SPP005');

-- Insert Test Data for Transaction Tables (2 rows each)

-- Insert data for tb_pembayaran
INSERT INTO tb_pembayaran (id_pembayaran, status, nisn, tgl_bayar, tgl_terakhir_bayar, batas_pembayaran, jumlah_bulan, id_spp, nominal_bayar, jumlah_bayar, kembalian) VALUES
('BYR001', 'Sudah Lunas', '1234567890', '2024-01-15', '2024-01-15', '2024-02-15', 'Januari', 'SPP001', '250000', '300000', '50000'),
('BYR002', 'Sudah Lunas', '1234567891', '2024-02-20', '2024-02-20', '2024-03-20', 'Februari', 'SPP002', '300000', '300000', '0');

-- Insert data for cek_pembayaran
INSERT INTO cek_pembayaran (nisn, tgl_terakhir_bayar, tgl_sekarang, status_pembayaran, jumlah_bulan, nama, no_telp) VALUES
('1234567890', '2024-01-15', '2024-05-25', 'Sudah Lunas', 'Januari', 'Andi Pratama', '081234567890'),
('1234567891', '2024-02-20', '2024-05-25', 'Sudah Lunas', 'Februari', 'Budi Hartono', '081234567891');
