<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?pesan=belum_login");
    exit;
}
include 'config/database.php';

$id_pembayaran = $_GET['id_pembayaran'];

// Get payment details
$query = mysqli_query($conn, "SELECT p.*, s.nama, s.nama_kelas FROM tb_pembayaran p JOIN tb_siswa s ON p.nisn = s.nisn WHERE p.id_pembayaran='$id_pembayaran'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['lunaskan'])) {
    $tambahan_bayar = $_POST['tambahan_bayar'];
    $jumlah_bayar_baru = $data['jumlah_bayar'] + $tambahan_bayar;
    $nominal_bayar = $data['nominal_bayar'];
    $kembalian_baru = $jumlah_bayar_baru - $nominal_bayar;
    
    // Check if payment is now sufficient
    if ($jumlah_bayar_baru >= $nominal_bayar) {
        $status = 'Sudah Lunas';
    } else {
        $status = 'Belum Lunas';
    }
    
    // Update payment record
    $query_update = mysqli_query($conn, "UPDATE tb_pembayaran SET jumlah_bayar='$jumlah_bayar_baru', kembalian='$kembalian_baru', status='$status' WHERE id_pembayaran='$id_pembayaran'");
    
    // Update cek_pembayaran table
    $query_cek_update = mysqli_query($conn, "UPDATE cek_pembayaran SET status_pembayaran='$status' WHERE nisn='".$data['nisn']."' AND tgl_terakhir_bayar='".$data['tgl_terakhir_bayar']."'");
    
    header("Location: pembayaran.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunaskan Pembayaran - SPP Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><i class="bi bi-wallet2"></i> SPP Siswa</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($_SESSION['level'] != 'siswa'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($_SESSION['level'] == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-database"></i> Data Master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="data_siswa.php"><i class="bi bi-people"></i> Data Siswa</a></li>
                            <li><a class="dropdown-item" href="data_kelas.php"><i class="bi bi-door-open"></i> Data Kelas</a></li>
                            <li><a class="dropdown-item" href="data_spp.php"><i class="bi bi-cash"></i> Data SPP</a></li>
                            <li><a class="dropdown-item" href="data_petugas.php"><i class="bi bi-person-badge"></i> Data Petugas</a></li>
                        </ul>
                    </li>
                    <?php elseif ($_SESSION['level'] == 'petugas'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-database"></i> Data Master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="data_siswa.php"><i class="bi bi-people"></i> Data Siswa</a></li>
                            <li><a class="dropdown-item" href="data_kelas.php"><i class="bi bi-door-open"></i> Data Kelas</a></li>
                            <li><a class="dropdown-item" href="data_spp.php"><i class="bi bi-cash"></i> Data SPP</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'petugas'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pembayaran.php"><i class="bi bi-credit-card"></i> Pembayaran</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cek_pembayaran.php"><i class="bi bi-search"></i> Cek Pembayaran</a>
                    </li>
                    <?php if ($_SESSION['level'] == 'siswa'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pembayaran.php"><i class="bi bi-credit-card"></i> Pembayaran</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> <?php echo isset($_SESSION['nama']) ? $_SESSION['nama'] : $_SESSION['nama_petugas']; ?> (<?php echo $_SESSION['level']; ?>)
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-cash"></i> Lunaskan Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Informasi Pembayaran</h6>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td>Nama Siswa</td>
                                    <td>: <?php echo $data['nama']; ?></td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>: <?php echo $data['nama_kelas']; ?></td>
                                </tr>
                                <tr>
                                    <td>Nominal SPP</td>
                                    <td>: Rp <?php echo number_format($data['nominal_bayar'], 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td>Sudah Dibayar</td>
                                    <td>: Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td>Kekurangan</td>
                                    <td class="text-danger">: Rp <?php echo number_format(abs($data['kembalian']), 0, ',', '.'); ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <form action="lunaskan_pembayaran.php?id_pembayaran=<?php echo $id_pembayaran; ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Tambahan Pembayaran</label>
                                <input type="number" class="form-control" name="tambahan_bayar" required min="1" value="<?php echo abs($data['kembalian']); ?>">
                                <small class="text-muted">Minimal: Rp <?php echo number_format(abs($data['kembalian']), 0, ',', '.'); ?></small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="lunaskan" class="btn btn-warning flex-grow-1"><i class="bi bi-cash"></i> Lunaskan</button>
                                <a href="pembayaran.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
