<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php?pesan=belum_login");
    exit;
}
include 'config/database.php';

if (isset($_POST['simpan'])) {
    // Auto-generate ID pembayaran (format: BYRXXX)
    $query_max_id = mysqli_query($conn, "SELECT MAX(id_pembayaran) as max_id FROM tb_pembayaran");
    $result_max_id = mysqli_fetch_assoc($query_max_id);
    $max_id = $result_max_id['max_id'];
    
    if ($max_id == null || $max_id == '') {
        $id_pembayaran = 'BYR001';
    } else {
        // Extract numeric part from existing ID (e.g., 'BYR001' -> 1)
        $numeric_part = intval(substr($max_id, 3));
        $new_numeric = $numeric_part + 1;
        // Format with leading zeros (e.g., 2 -> '002')
        $id_pembayaran = 'BYR' . str_pad($new_numeric, 3, '0', STR_PAD_LEFT);
    }
    
    $nisn = $_POST['nisn'];
    $tgl_bayar = $_POST['tgl_bayar'];
    $bulan_bayar = $_POST['bulan_bayar'];
    $tahun_bayar = $_POST['tahun_bayar'];
    $id_spp = $_POST['id_spp'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $id_petugas = $_SESSION['id_petugas'];
    
    // Get student data
    $query_siswa = mysqli_query($conn, "SELECT * FROM tb_siswa WHERE nisn='$nisn'");
    $data_siswa = mysqli_fetch_assoc($query_siswa);
    
    // Get SPP nominal
    $query_spp = mysqli_query($conn, "SELECT * FROM tb_spp WHERE id_spp='$id_spp'");
    $data_spp = mysqli_fetch_assoc($query_spp);
    
    $nominal_bayar = $data_spp['nominal'];
    $kembalian = $jumlah_bayar - $nominal_bayar;
    
    // Check if payment is sufficient
    if ($jumlah_bayar >= $nominal_bayar) {
        $status = 'Sudah Lunas';
    } else {
        $status = 'Belum Lunas';
    }
    
    // Calculate batas pembayaran (1 month from payment date)
    $batas_pembayaran = date('Y-m-d', strtotime($tgl_bayar . ' +1 month'));
    
    $query = mysqli_query($conn, "INSERT INTO tb_pembayaran (id_pembayaran, status, nisn, tgl_bayar, tgl_terakhir_bayar, batas_pembayaran, jumlah_bulan, id_spp, nominal_bayar, jumlah_bayar, kembalian) VALUES ('$id_pembayaran', '$status', '$nisn', '$tgl_bayar', '$tgl_bayar', '$batas_pembayaran', '$bulan_bayar', '$id_spp', '$nominal_bayar', '$jumlah_bayar', '$kembalian')");
    
    // Update cek_pembayaran table
    $tgl_sekarang = date('Y-m-d');
    $query_cek = mysqli_query($conn, "INSERT INTO cek_pembayaran (nisn, tgl_terakhir_bayar, tgl_sekarang, status_pembayaran, jumlah_bulan, nama, no_telp) VALUES ('$nisn', '$tgl_bayar', '$tgl_sekarang', '$status', '$bulan_bayar', '".$data_siswa['nama']."', '".$data_siswa['no_telp']."')");
    
    header("Location: pembayaran.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - SPP Siswa</title>
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
                        <a class="nav-link active" href="pembayaran.php"><i class="bi bi-credit-card"></i> Pembayaran</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cek_pembayaran.php"><i class="bi bi-search"></i> Cek Pembayaran</a>
                    </li>
                    <?php if ($_SESSION['level'] == 'siswa'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="pembayaran.php"><i class="bi bi-credit-card"></i> Pembayaran</a>
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
            <?php if ($_SESSION['level'] != 'siswa'): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cash"></i> Form Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form action="pembayaran.php" method="post">
                            <div class="mb-3">
                                <label class="form-label">NISN Siswa</label>
                                <select class="form-select" name="nisn" id="nisn" required onchange="getSiswaInfo()">
                                    <option value="">Pilih Siswa</option>
                                    <?php
                                    $query_siswa = mysqli_query($conn, "SELECT * FROM tb_siswa ORDER BY nama ASC");
                                    while ($row = mysqli_fetch_assoc($query_siswa)) {
                                        echo "<option value='".$row['nisn']."'>".$row['nisn']." - ".$row['nama']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input type="text" class="form-control" id="nama_siswa" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" class="form-control" id="kelas_siswa" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Bayar</label>
                                <input type="date" class="form-control" name="tgl_bayar" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bulan Bayar</label>
                                <select class="form-select" name="bulan_bayar" required>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Bayar</label>
                                <input type="number" class="form-control" name="tahun_bayar" value="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ID SPP</label>
                                <select class="form-select" name="id_spp" id="id_spp" required onchange="getSppNominal()">
                                    <option value="">Pilih SPP</option>
                                    <?php
                                    $query_spp = mysqli_query($conn, "SELECT * FROM tb_spp ORDER BY tahun ASC");
                                    while ($row = mysqli_fetch_assoc($query_spp)) {
                                        echo "<option value='".$row['id_spp']."'>".$row['tahun']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nominal SPP</label>
                                <input type="text" class="form-control" id="nominal_spp" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Bayar</label>
                                <input type="number" class="form-control" name="jumlah_bayar" id="jumlah_bayar" required oninput="hitungKembalian()">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kembalian</label>
                                <input type="text" class="form-control" id="kembalian_display" readonly>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-primary w-100"><i class="bi bi-save"></i> Simpan Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
            <?php else: ?>
            <div class="col-md-12">
            <?php endif; ?>
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>Bulan</th>
                                        <th>Nominal</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Kembalian</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // For students, only show their own payment history
                                    if ($_SESSION['level'] == 'siswa' && isset($_SESSION['nisn'])) {
                                        $query = mysqli_query($conn, "SELECT p.*, s.nama FROM tb_pembayaran p JOIN tb_siswa s ON p.nisn = s.nisn WHERE p.nisn='".$_SESSION['nisn']."' ORDER BY p.tgl_bayar DESC");
                                    } else {
                                        $query = mysqli_query($conn, "SELECT p.*, s.nama FROM tb_pembayaran p JOIN tb_siswa s ON p.nisn = s.nisn ORDER BY p.tgl_bayar DESC");
                                    }
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($row['tgl_bayar'])); ?></td>
                                        <td><?php echo $row['nama']; ?></td>
                                        <td><?php echo $row['jumlah_bulan']; ?></td>
                                        <td>Rp <?php echo number_format($row['nominal_bayar'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php
                                            if ($row['kembalian'] >= 0) {
                                                echo "Rp " . number_format($row['kembalian'], 0, ',', '.');
                                            } else {
                                                echo "<span class='text-danger'>Rp " . number_format(abs($row['kembalian']), 0, ',', '.') . " (Kurang)</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row['status'] == 'Sudah Lunas') {
                                                echo "<span class='badge bg-success'>Sudah Lunas</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>Belum Lunas</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row['status'] == 'Belum Lunas') {
                                                echo "<a href='lunaskan_pembayaran.php?id_pembayaran=" . $row['id_pembayaran'] . "' class='btn btn-warning btn-sm'><i class='bi bi-cash'></i> Lunaskan</a>";
                                            } else {
                                                echo "-";
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

    <script>
        function getSiswaInfo() {
            var nisn = document.getElementById('nisn').value;
            <?php
            $query_siswa = mysqli_query($conn, "SELECT * FROM tb_siswa");
            $siswa_data = array();
            while ($row = mysqli_fetch_assoc($query_siswa)) {
                $siswa_data[] = $row;
            }
            echo "var siswaData = " . json_encode($siswa_data) . ";";
            ?>
            
            for (var i = 0; i < siswaData.length; i++) {
                if (siswaData[i].nisn == nisn) {
                    document.getElementById('nama_siswa').value = siswaData[i].nama;
                    document.getElementById('kelas_siswa').value = siswaData[i].nama_kelas;
                    break;
                }
            }
        }
        
        function getSppNominal() {
            var id_spp = document.getElementById('id_spp').value;
            <?php
            $query_spp = mysqli_query($conn, "SELECT * FROM tb_spp");
            $spp_data = array();
            while ($row = mysqli_fetch_assoc($query_spp)) {
                $spp_data[] = $row;
            }
            echo "var sppData = " . json_encode($spp_data) . ";";
            ?>
            
            for (var i = 0; i < sppData.length; i++) {
                if (sppData[i].id_spp == id_spp) {
                    document.getElementById('nominal_spp').value = "Rp " + sppData[i].nominal;
                    break;
                }
            }
            hitungKembalian();
        }
        
        function hitungKembalian() {
            var nominal_spp_text = document.getElementById('nominal_spp').value;
            var jumlah_bayar = document.getElementById('jumlah_bayar').value;
            
            // Extract numeric value from nominal_spp (remove "Rp " and format)
            var nominal_spp = 0;
            if (nominal_spp_text) {
                nominal_spp = parseInt(nominal_spp_text.replace(/[^0-9]/g, ''));
            }
            
            if (jumlah_bayar && nominal_spp > 0) {
                var kembalian = parseInt(jumlah_bayar) - nominal_spp;
                if (kembalian >= 0) {
                    document.getElementById('kembalian_display').value = "Rp " + kembalian.toLocaleString('id-ID');
                } else {
                    document.getElementById('kembalian_display').value = "Kurang Rp " + Math.abs(kembalian).toLocaleString('id-ID');
                }
            } else {
                document.getElementById('kembalian_display').value = "";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
