<?php
session_start();
include 'config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Check petugas table first (only for admin and petugas, not siswa level)
$query = mysqli_query($conn, "SELECT * FROM tb_petugas WHERE username = '$username' AND password = '$password' AND level != 'siswa'");
$cek = mysqli_num_rows($query);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($query);
    $_SESSION['id_petugas'] = $data['id_petugas'];
    $_SESSION['nama_petugas'] = $data['nama_petugas'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['level'] = $data['level'];
    $_SESSION['login'] = true;
    
    header("Location: dashboard.php");
} else {
    // Check siswa table if not found in petugas
    // Try NISN first, then NIS
    $query_siswa = mysqli_query($conn, "SELECT * FROM tb_siswa WHERE nisn = '$username' OR nis = '$username'");
    $cek_siswa = mysqli_num_rows($query_siswa);
    
    if ($cek_siswa > 0) {
        $data_siswa = mysqli_fetch_assoc($query_siswa);
        $_SESSION['nisn'] = $data_siswa['nisn'];
        $_SESSION['nama'] = $data_siswa['nama'];
        $_SESSION['username'] = $data_siswa['nis'];
        $_SESSION['level'] = 'siswa';
        $_SESSION['login'] = true;
        
        header("Location: pembayaran.php");
    } else {
        header("Location: index.php?pesan=gagal");
    }
}
?>
