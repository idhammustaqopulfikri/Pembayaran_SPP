<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?pesan=belum_login");
    exit;
}
include 'config/database.php';

// Handle Add/Edit/Delete
if (isset($_POST['simpan'])) {
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_spp = $_POST['id_spp'];
    
    $query = mysqli_query($conn, "INSERT INTO tb_siswa (nisn, nis, nama, id_kelas, nama_kelas, alamat, no_telp, id_spp) VALUES ('$nisn', '$nis', '$nama', '$id_kelas', '$nama_kelas', '$alamat', '$no_telp', '$id_spp')");
    header("Location: data_siswa.php");
}

if (isset($_POST['update'])) {
    $nisn = $_POST['nisn'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $id_kelas = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    $id_spp = $_POST['id_spp'];
    
    $query = mysqli_query($conn, "UPDATE tb_siswa SET nis='$nis', nama='$nama', id_kelas='$id_kelas', nama_kelas='$nama_kelas', alamat='$alamat', no_telp='$no_telp', id_spp='$id_spp' WHERE nisn='$nisn'");
    header("Location: data_siswa.php");
}

if (isset($_GET['hapus'])) {
    $nisn = $_GET['hapus'];
    $query = mysqli_query($conn, "DELETE FROM tb_siswa WHERE nisn='$nisn'");
    header("Location: data_siswa.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - SPP Siswa</title>
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
                            <li><a class="dropdown-item active" href="data_siswa.php"><i class="bi bi-people"></i> Data Siswa</a></li>
                            <li><a class="dropdown-item" href="data_kelas.php"><i class="bi bi-door-open"></i> Data Kelas</a></li>
                            <li><a class="dropdown-item" href="data_spp.php"><i class="bi bi-cash"></i> Data SPP</a></li>
                            <li><a class="dropdown-item" href="data_petugas.php"><i class="bi bi-person-badge"></i> Data Petugas</a></li>
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
                <h5 class="mb-0"><i class="bi bi-people"></i> Data Siswa</h5>
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="bi bi-plus"></i> Tambah Siswa</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tableSiswa">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Alamat</th>
                                <th>No. Telp</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM tb_siswa ORDER BY nama ASC");
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nisn']; ?></td>
                                <td><?php echo $row['nis']; ?></td>
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['nama_kelas']; ?></td>
                                <td><?php echo $row['alamat']; ?></td>
                                <td><?php echo $row['no_telp']; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['nisn']; ?>"><i class="bi bi-pencil"></i></button>
                                    <a href="data_siswa.php?hapus=<?php echo $row['nisn']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?php echo $row['nisn']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Siswa</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="data_siswa.php" method="post">
                                                <input type="hidden" name="nisn" value="<?php echo $row['nisn']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">NIS</label>
                                                    <input type="text" class="form-control" name="nis" value="<?php echo $row['nis']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" class="form-control" name="nama" value="<?php echo $row['nama']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kelas</label>
                                                    <select class="form-select" name="id_kelas" required>
                                                        <?php
                                                        $query_kelas = mysqli_query($conn, "SELECT * FROM tb_kelas");
                                                        while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                                            $selected = ($kelas['id_kelas'] == $row['id_kelas']) ? 'selected' : '';
                                                            echo "<option value='".$kelas['id_kelas']."' $selected>".$kelas['nama_kelas']."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Kelas</label>
                                                    <select class="form-select" name="nama_kelas" required>
                                                        <?php
                                                        $query_kelas = mysqli_query($conn, "SELECT * FROM tb_kelas");
                                                        while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                                            $selected = ($kelas['nama_kelas'] == $row['nama_kelas']) ? 'selected' : '';
                                                            echo "<option value='".$kelas['nama_kelas']."' $selected>".$kelas['nama_kelas']."</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <textarea class="form-control" name="alamat" required><?php echo $row['alamat']; ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">No. Telp</label>
                                                    <input type="text" class="form-control" name="no_telp" value="<?php echo $row['no_telp']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">ID SPP</label>
                                                    <select class="form-select" name="id_spp" required>
                                                        <?php
                                                        $query_spp = mysqli_query($conn, "SELECT * FROM tb_spp");
                                                        while ($spp = mysqli_fetch_assoc($query_spp)) {
                                                            $selected = ($spp['id_spp'] == $row['id_spp']) ? 'selected' : '';
                                                            echo "<option value='".$spp['id_spp']."' $selected>".$spp['tahun']."</option>";
                                                        }
                                                        ?>
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
                    <h5 class="modal-title">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="data_siswa.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" class="form-control" name="nisn" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control" name="nis" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select class="form-select" name="id_kelas" required>
                                <option value="">Pilih Kelas</option>
                                <?php
                                $query_kelas = mysqli_query($conn, "SELECT * FROM tb_kelas");
                                while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                    echo "<option value='".$kelas['id_kelas']."'>".$kelas['nama_kelas']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <select class="form-select" name="nama_kelas" required>
                                <option value="">Pilih Nama Kelas</option>
                                <?php
                                $query_kelas = mysqli_query($conn, "SELECT * FROM tb_kelas");
                                while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                    echo "<option value='".$kelas['nama_kelas']."'>".$kelas['nama_kelas']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telp</label>
                            <input type="text" class="form-control" name="no_telp" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ID SPP</label>
                            <select class="form-select" name="id_spp" required>
                                <option value="">Pilih SPP</option>
                                <?php
                                $query_spp = mysqli_query($conn, "SELECT * FROM tb_spp");
                                while ($spp = mysqli_fetch_assoc($query_spp)) {
                                    echo "<option value='".$spp['id_spp']."'>".$spp['tahun']."</option>";
                                }
                                ?>
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
