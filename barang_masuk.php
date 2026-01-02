<?php 
session_start();
include 'koneksi.php'; 

// 1. PROTEKSI HALAMAN & LOGIKA PROSES
if($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staf') { 
    header("location:dashboard.php"); 
    exit(); 
}

if(isset($_POST['simpan_masuk'])){
    $tgl = $_POST['tanggal'];
    $kat = $_POST['kategori'];
    $id  = $_POST['id_barang'];
    $jml = $_POST['jumlah'];
    $sat = mysqli_real_escape_string($conn, $_POST['satuan']); // Menambah variabel satuan
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // INSERT KE TABEL barang_masuk (Sesuai kolom satuan yang Anda tambah di database)
    $q = mysqli_query($conn, "INSERT INTO barang_masuk (idBarang, jumlah, satuan, tanggal, tipe, keterangan) 
                              VALUES ('$id', '$jml', '$sat', '$tgl', '$kat', '$ket')");

    if($q){
        // UPDATE STOK MASTER
        if($kat == 'roti'){
            mysqli_query($conn, "UPDATE barang_jadi SET jumlahStok = jumlahStok + $jml WHERE idBarangJadi = '$id'");
        } else {
            mysqli_query($conn, "UPDATE bahan_baku SET jumlahStok = jumlahStok + $jml WHERE idBahan = '$id'");
        }
        
        header("Location: barang_masuk.php?status=sukses");
        exit;
    } else {
        die("Error Database: " . mysqli_error($conn));
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Barang Masuk - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; margin: 0; }
        .main-content { flex: 1; padding: 40px; margin-left: 260px; }
        .glass-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
        .table-dark { background-color: #212529 !important; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="fw-bold mb-4">Input Barang Masuk</h2>
        
        <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4">✅ Stok berhasil ditambah dan satuan tercatat!</div>
        <?php endif; ?>

        <div class="glass-card mb-5">
            <form action="" method="POST">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="small fw-bold mb-2">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold mb-2">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="roti">Barang Jadi (Roti)</option>
                            <option value="bahan">Bahan Baku</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold mb-2">Pilih Barang</label>
                        <select name="id_barang" class="form-select" required>
                            <optgroup label="Roti">
                                <?php 
                                $r = mysqli_query($conn, "SELECT idBarangJadi, namaRoti FROM barang_jadi");
                                while($row = mysqli_fetch_assoc($r)) echo "<option value='".$row['idBarangJadi']."'>".$row['namaRoti']."</option>";
                                ?>
                            </optgroup>
                            <optgroup label="Bahan">
                                <?php 
                                $b = mysqli_query($conn, "SELECT idBahan, namaBahan FROM bahan_baku");
                                while($row = mysqli_fetch_assoc($b)) echo "<option value='".$row['idBahan']."'>".$row['namaBahan']."</option>";
                                ?>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold mb-2">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold mb-2">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="kg/pcs/butir" required>
                    </div>
                    <div class="col-md-8">
                        <label class="small fw-bold mb-2">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Kiriman supplier">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="simpan_masuk" class="btn btn-primary w-100 fw-bold py-2">Simpan</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <h5 class="fw-bold mb-3">Riwayat Transaksi Masuk</h5>
            <table class="table table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>ID Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody class="text-center small">
                    <?php 
                    $data = mysqli_query($conn, "SELECT * FROM barang_masuk ORDER BY tanggal DESC, id_masuk DESC LIMIT 5");
                    
                    if(mysqli_num_rows($data) > 0){
                        while($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td class="fw-bold"><?= $row['idBarang'] ?></td>
                            <td><span class="badge bg-secondary"><?= strtoupper($row['tipe']) ?></span></td>
                            <td class="text-success fw-bold">+ <?= $row['jumlah'] ?></td>
                            <td><?= $row['satuan'] ?></td> <td class="text-start"><?= $row['keterangan'] ?></td>
                        </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='6' class='text-muted py-3'>Belum ada riwayat masuk.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>