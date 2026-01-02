<?php 
session_start();
include 'koneksi.php'; 
if($_SESSION['role'] != 'admin') { header("location:dashboard.php"); exit(); }

// LOGIKA ALL-IN-ONE CRUD
if(isset($_POST['simpan_roti'])){
    $id = $_POST['id_roti']; $nama = $_POST['nama_roti'];
    $tglP = $_POST['tgl_produksi']; $tglK = $_POST['tgl_kadaluarsa'];
    $stok = $_POST['stok']; $lokasi = $_POST['lokasi'];
    
    mysqli_query($conn, "INSERT INTO barang_jadi VALUES ('$id', '$nama', '$tglP', '$tglK', '$stok', '$lokasi')");
    header("location:adminBarangJadi.php");
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang_jadi WHERE idBarangJadi='$id'");
    header("location:adminBarangJadi.php");
}

if(isset($_POST['edit_roti'])){
    $id = $_POST['id_roti']; $nama = $_POST['nama_roti'];
    $tglP = $_POST['tgl_produksi']; $tglK = $_POST['tgl_kadaluarsa'];
    $stok = $_POST['stok']; $lokasi = $_POST['lokasi'];
    
    mysqli_query($conn, "UPDATE barang_jadi SET namaRoti='$nama', tanggalProduksi='$tglP', tanggalKadaluarsa='$tglK', jumlahStok='$stok', lokasiSimpan='$lokasi' WHERE idBarangJadi='$id'");
    header("location:adminBarangJadi.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Master Data - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Hanya mengganti warna background utama menjadi keabuan */
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; } 
        .main-content { flex: 1; padding: 30px; margin-left: 250px; width: calc(100% - 250px); }
        
        /* Menjaga area konten tetap putih di atas background abu-abu */
        .card-container { 
            background-color: #ffffff; 
            border-radius: 15px; 
            padding: 25px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        .nav-tabs .nav-link.active { background-color: white; border-bottom: 3px solid #0d6efd; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="fw-bold">Master Data Persediaan</h2>
        <p class="text-muted small">Kelola stok bahan dan produk jadi</p>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active fw-bold" href="adminBarangJadi.php">Barang Jadi (Roti)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-muted" href="adminBahanBaku.php">Bahan Baku</a>
            </li>
        </ul>

        <div class="card shadow-sm border-0 p-4 bg-white">
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <h5 class="fw-bold m-0">Daftar Produk Roti</h5>
                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Roti
                </button>
            </div>
            
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th>ID Roti</th>
                        <th>Nama Roti</th>
                        <th class="text-center">Stok</th>
                        <th>Kadaluarsa</th>
                        <th>Lokasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM barang_jadi");
                    while($row = mysqli_fetch_assoc($res)){ ?>
                    <tr>
                        <td class="fw-bold"><?= $row['idBarangJadi']; ?></td>
                        <td><?= $row['namaRoti']; ?></td>
                        <td class="text-center"><span class="badge bg-info text-dark"><?= $row['jumlahStok']; ?> Unit</span></td>
                        <td><?= date('d M Y', strtotime($row['tanggalKadaluarsa'])); ?></td>
                        <td><i class="bi bi-geo-alt text-danger me-1"></i><?= $row['lokasiSimpan']; ?></td>
                        <td class="text-center">
                            <button class="btn btn-outline-warning btn-sm border-0" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['idBarangJadi']; ?>">
                                 <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="adminBarangJadi.php?hapus=<?= $row['idBarangJadi']; ?>" class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('Hapus data?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

            <div class="modal fade" id="modalEdit<?= $row['idBarangJadi']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold">Edit Barang Jadi (Roti)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="id_roti" value="<?= $row['idBarangJadi']; ?>">
                                <label class="small fw-bold">Nama Roti</label>
                                <input type="text" name="nama_roti" class="form-control mb-3" value="<?= $row['namaRoti']; ?>" required>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="small fw-bold">Tgl Produksi</label>
                                        <input type="date" name="tgl_produksi" class="form-control" value="<?= $row['tanggalProduksi']; ?>" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="small fw-bold">Tgl Kadaluarsa</label>
                                        <input type="date" name="tgl_kadaluarsa" class="form-control" value="<?= $row['tanggalKadaluarsa']; ?>" required>
                                    </div>
                                </div>
                                <label class="small fw-bold">Stok</label>
                                <input type="number" name="stok" class="form-control mb-3" value="<?= $row['jumlahStok']; ?>" required>
                                <label class="small fw-bold">Lokasi Simpan</label>
                                <input type="text" name="lokasi" class="form-control mb-3" value="<?= $row['lokasiSimpan']; ?>">
                                <button type="submit" name="edit_roti" class="btn btn-warning w-100 fw-bold py-2">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Tambah Barang Jadi (Roti)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="id_roti" class="form-control mb-3" placeholder="ID Barang (Max 12 Karakter)" required>
                        <input type="text" name="nama_roti" class="form-control mb-3" placeholder="Nama Roti" required>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small fw-bold">Tgl Produksi</label>
                                <input type="date" name="tgl_produksi" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small fw-bold">Tgl Kadaluarsa</label>
                                <input type="date" name="tgl_kadaluarsa" class="form-control" required>
                            </div>
                        </div>
                        <input type="number" name="stok" class="form-control mb-3" placeholder="Stok Awal" required>
                        <input type="text" name="lokasi" class="form-control mb-3" placeholder="Lokasi Simpan">
                        <button type="submit" name="simpan_roti" class="btn btn-primary w-100 fw-bold py-2">Simpan Roti</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>