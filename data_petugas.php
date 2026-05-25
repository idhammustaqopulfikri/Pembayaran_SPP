<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?pesan=belum_login");
    exit;
}
include 'config/database.php';

if (isset($_POST['simpan'])) {
    $id_petugas = $_POST['id_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_petugas = $_POST['nama_petugas'];
    $level = $_POST['level'];
    
    $query = mysqli_query($conn, "INSERT INTO tb_petugas (id_petugas, username, password, nama_petugas, level) VALUES ('$id_petugas', '$username', '$password', '$nama_petugas', '$level')");
    header("Location: data_petugas.php");
}

if (isset($_POST['update'])) {
    $id_petugas = $_POST['id_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_petugas = $_POST['nama_petugas'];
    $level = $_POST['level'];
    
    $query = mysqli_query($conn, "UPDATE tb_petugas SET username='$username', password='$password', nama_petugas='$nama_petugas', level='$level' WHERE id_petugas='$id_petugas'");
    header("Location: data_petugas.php");
}

if (isset($_GET['hapus'])) {
    $id_petugas = $_GET['hapus'];
    $query = mysqli_query($conn, "DELETE FROM tb_petugas WHERE id_petugas='$id_petugas'");
    header("Location: data_petugas.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Petugas - SPP Siswa</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-database"></i> Data Master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="data_siswa.php"><i class="bi bi-people"></i> Data Siswa</a></li>
                            <li><a class="dropdown-item" href="data_kelas.php"><i class="bi bi-door-open"></i> Data Kelas</a></li>
                            <li><a class="dropdown-item" href="data_spp.php"><i class="bi bi-cash"></i> Data SPP</a></li>
                            <li><a class="dropdown-item active" href="data_petugas.php"><i class="bi bi-person-badge"></i> Data Petugas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pembayaran.php"><i class="bi bi-credit-card"></i> Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cek_pembayaran.php"><i class="bi bi-search"></i> Cek Pembayaran</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> <?php echo $_SESSION['nama_petugas']; ?> (<?php echo $_SESSION['level']; ?>)
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Data Petugas</h5>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus"></i> Tambah Petugas</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Petugas</th>
                                <th>Username</th>
                                <th>Nama Petugas</th>
                                <th>Level</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM tb_petugas ORDER BY nama_petugas ASC");
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['id_petugas']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['nama_petugas']; ?></td>
                                <td>
                                    <?php
                                    if ($row['level'] == 'admin') {
                                        echo "<span class='badge bg-danger'>Admin</span>";
                                    } else if ($row['level'] == 'petugas') {
                                        echo "<span class='badge bg-primary'>Petugas</span>";
                                    } else {
                                        echo "<span class='badge bg-success'>Siswa</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['id_petugas']; ?>"><i class="bi bi-pencil"></i></button>
                                    <a href="data_petugas.php?hapus=<?php echo $row['id_petugas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?php echo $row['id_petugas']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Petugas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="data_petugas.php" method="post">
                                                <input type="hidden" name="id_petugas" value="<?php echo $row['id_petugas']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Password</label>
                                                    <input type="text" class="form-control" name="password" value="<?php echo $row['password']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Petugas</label>
                                                    <input type="text" class="form-control" name="nama_petugas" value="<?php echo $row['nama_petugas']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Level</label>
                                                    <select class="form-select" name="level" required>
                                                        <option value="admin" <?php echo ($row['level'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="petugas" <?php echo ($row['level'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                                                        <option value="siswa" <?php echo ($row['level'] == 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                                                    </select>
                                                </div>
                                                <button type="submit" name="update" class="btn btn-warning w-100">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Petugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="data_petugas.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">ID Petugas</label>
                            <input type="text" class="form-control" name="id_petugas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="text" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Petugas</label>
                            <input type="text" class="form-control" name="nama_petugas" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Level</label>
                            <select class="form-select" name="level" required>
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
