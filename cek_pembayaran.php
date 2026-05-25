<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?pesan=belum_login");
    exit;
}
include 'config/database.php';

$search_result = null;
if (isset($_POST['cari'])) {
    $nisn = $_POST['nisn'];
    // For students, only allow searching their own NISN
    if ($_SESSION['level'] == 'siswa') {
        $nisn = $_SESSION['nisn'];
    }
    $query = mysqli_query($conn, "SELECT * FROM cek_pembayaran WHERE nisn='$nisn'");
    $search_result = mysqli_fetch_assoc($query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Pembayaran - SPP Siswa</title>
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
                        <a class="nav-link active" href="cek_pembayaran.php"><i class="bi bi-search"></i> Cek Pembayaran</a>
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
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-search"></i> Cek Status Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form action="cek_pembayaran.php" method="post">
                            <?php if ($_SESSION['level'] == 'siswa'): ?>
                            <div class="mb-3">
                                <label class="form-label">NISN Anda</label>
                                <input type="text" class="form-control" name="nisn" value="<?php echo isset($_SESSION['nisn']) ? $_SESSION['nisn'] : ''; ?>" readonly>
                            </div>
                            <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label">Masukkan NISN</label>
                                <input type="text" class="form-control" name="nisn" placeholder="Contoh: 1234567890" required>
                            </div>
                            <?php endif; ?>
                            <button type="submit" name="cari" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <?php if ($search_result): ?>
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hasil Pencarian</h5>
                        <button onclick="window.print()" class="btn btn-light btn-sm"><i class="bi bi-printer"></i> Cetak</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">NISN</th>
                                <td><?php echo $search_result['nisn']; ?></td>
                            </tr>
                            <tr>
                                <th>Nama Siswa</th>
                                <td><?php echo $search_result['nama']; ?></td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td><?php echo $search_result['no_telp']; ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Terakhir Bayar</th>
                                <td><?php echo date('d-m-Y', strtotime($search_result['tgl_terakhir_bayar'])); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Sekarang</th>
                                <td><?php echo date('d-m-Y', strtotime($search_result['tgl_sekarang'])); ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah Bulan</th>
                                <td><?php echo $search_result['jumlah_bulan']; ?> Bulan</td>
                            </tr>
                            <tr>
                                <th>Status Pembayaran</th>
                                <td>
                                    <?php
                                    if ($search_result['status_pembayaran'] == 'Sudah Lunas') {
                                        echo "<span class='badge bg-success fs-6'>Sudah Lunas</span>";
                                    } else {
                                        echo "<span class='badge bg-danger fs-6'>Belum Lunas</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php elseif (isset($_POST['cari'])): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Data tidak ditemukan untuk NISN tersebut.
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi</h5>
                    </div>
                    <div class="card-body">
                        <p>Masukkan NISN siswa untuk mengecek status pembayaran SPP.</p>
                        <p class="text-muted">Pastikan NISN yang dimasukkan benar dan terdaftar dalam sistem.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> Semua Data Cek Pembayaran</h5>
                        <button onclick="window.print()" class="btn btn-light btn-sm"><i class="bi bi-printer"></i> Cetak</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Tgl Terakhir Bayar</th>
                                        <th>Jumlah Bulan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // For students, only show their own data
                                    if ($_SESSION['level'] == 'siswa' && isset($_SESSION['nisn'])) {
                                        $query = mysqli_query($conn, "SELECT * FROM cek_pembayaran WHERE nisn='".$_SESSION['nisn']."' ORDER BY tgl_terakhir_bayar DESC");
                                    } else {
                                        $query = mysqli_query($conn, "SELECT * FROM cek_pembayaran ORDER BY tgl_terakhir_bayar DESC");
                                    }
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nisn']; ?></td>
                                        <td><?php echo $row['nama']; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($row['tgl_terakhir_bayar'])); ?></td>
                                        <td><?php echo $row['jumlah_bulan']; ?></td>
                                        <td>
                                            <?php
                                            if ($row['status_pembayaran'] == 'Sudah Lunas') {
                                                echo "<span class='badge bg-success'>Sudah Lunas</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>Belum Lunas</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
