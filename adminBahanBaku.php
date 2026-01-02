<?php 
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa akses
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("location:dashboard.php"); 
    exit(); 
}

// --- LOGIKA EDIT BAHAN BAKU ---
if(isset($_POST['edit_bahan'])){
    $id = mysqli_real_escape_string($conn, $_POST['id_bahan']); 
    $nama = mysqli_real_escape_string($conn, $_POST['nama_bahan']);
    $stok = $_POST['stok']; 
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    
    $query = mysqli_query($conn, "UPDATE bahan_baku SET 
        namaBahan='$nama', 
        jumlahStok='$stok', 
        lokasiSimpan='$lokasi' 
        WHERE idBahan='$id'");
    
    if($query) {
        header("location:adminBahanBaku.php?status=update_sukses");
        exit();
    }
}

// --- LOGIKA TAMBAH BAHAN BAKU ---
if(isset($_POST['simpan_bahan'])){
    $id = mysqli_real_escape_string($conn, $_POST['id_bahan']); 
    $nama = mysqli_real_escape_string($conn, $_POST['nama_bahan']);
    $stok = $_POST['stok']; 
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    
    $query = mysqli_query($conn, "INSERT INTO bahan_baku (idBahan, namaBahan, jumlahStok, lokasiSimpan) VALUES ('$id', '$nama', '$stok', '$lokasi')");
    
    if($query) {
        header("location:adminBahanBaku.php?status=tambah_sukses");
        exit();
    } else {
        // Jika error (misal ID duplikat), tampilkan pesan
        $error_msg = mysqli_error($conn);
    }
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM bahan_baku WHERE idBahan='$id'");
    header("location:adminBahanBaku.php?status=hapus_sukses");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Master Data - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; } 
        .main-content { flex: 1; padding: 30px; margin-left: 250px; width: calc(100% - 250px); }
        .card-shadow { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .nav-tabs .nav-link { color: #6c757d; }
        .nav-tabs .nav-link.active { background-color: white; border-bottom: 3px solid #0d6efd; font-weight: bold; color: #0d6efd; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="fw-bold">Master Data Persediaan</h2>
        <p class="text-muted small">Kelola stok bahan dan produk jadi</p>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg; ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="adminBarangJadi.php">Barang Jadi (Roti)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="adminBahanBaku.php">Bahan Baku</a>
            </li>
        </ul>

        <div class="card card-shadow p-4 bg-white">
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <h5 class="fw-bold m-0 text-dark">Daftar Bahan Baku</h5>
                <button class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBahan">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Bahan
                </button>
            </div>
            
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th>ID Bahan</th>
                        <th>Nama Bahan</th>
                        <th class="text-center">Stok</th>
                        <th>Lokasi Simpan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM bahan_baku");
                    while($row = mysqli_fetch_assoc($res)){ ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= $row['idBahan']; ?></td>
                        <td><?= $row['namaBahan']; ?></td>
                        <td class="text-center"><span class="badge bg-warning text-dark px-3 py-2"><?= $row['jumlahStok']; ?> Unit</span></td>
                        <td><i class="bi bi-geo-alt text-danger me-1"></i><?= $row['lokasiSimpan']; ?></td>
                        <td class="text-center">
                            <button class="btn btn-outline-warning btn-sm border-0" data-bs-toggle="modal" data-bs-target="#modalEditBahan<?= $row['idBahan']; ?>">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <a href="adminBahanBaku.php?hapus=<?= $row['idBahan']; ?>" class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('Hapus data?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditBahan<?= $row['idBahan']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-bold">Edit Bahan Baku</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_bahan" value="<?= $row['idBahan']; ?>">
                                        <label class="small fw-bold">Nama Bahan</label>
                                        <input type="text" name="nama_bahan" class="form-control mb-3" value="<?= $row['namaBahan']; ?>" required>
                                        <label class="small fw-bold">Stok</label>
                                        <input type="number" name="stok" class="form-control mb-3" value="<?= $row['jumlahStok']; ?>" required>
                                        <label class="small fw-bold">Lokasi Simpan</label>
                                        <input type="text" name="lokasi" class="form-control mb-3" value="<?= $row['lokasiSimpan']; ?>">
                                        <button type="submit" name="edit_bahan" class="btn btn-warning w-100 fw-bold py-2 text-white shadow-sm">Simpan Perubahan</button>
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

    <div class="modal fade" id="modalTambahBahan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Tambah Bahan Baku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="text" name="id_bahan" class="form-control mb-3" placeholder="ID Bahan (Contoh: BHN01)" required>
                        <input type="text" name="nama_bahan" class="form-control mb-3" placeholder="Nama Bahan" required>
                        <input type="number" name="stok" class="form-control mb-3" placeholder="Stok Awal" required>
                        <input type="text" name="lokasi" class="form-control mb-3" placeholder="Lokasi Simpan">
                        <button type="submit" name="simpan_bahan" class="btn btn-success w-100 fw-bold py-2 shadow-sm">Simpan Bahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>