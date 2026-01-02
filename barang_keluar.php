<?php 
session_start();
include 'koneksi.php'; 

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("location:dashboard.php"); 
    exit(); 
}

$error = "";
if(isset($_POST['simpan_keluar'])){
    $tgl = $_POST['tanggal'];
    $kat = $_POST['kategori'];
    $id  = $_POST['id_barang'];
    $jml = $_POST['jumlah'];
    $sat = mysqli_real_escape_string($conn, $_POST['satuan']); // Ambil input satuan
    $ket = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // CEK STOK DULU
    $cek = ($kat == 'roti') ? mysqli_query($conn, "SELECT jumlahStok FROM barang_jadi WHERE idBarangJadi = '$id'") : mysqli_query($conn, "SELECT jumlahStok FROM bahan_baku WHERE idBahan = '$id'");
    $ds = mysqli_fetch_assoc($cek);

    if($ds['jumlahStok'] < $jml){
        $error = "Gagal! Stok tidak cukup. Tersisa: " . $ds['jumlahStok'];
    } else {
        // 1. Potong Stok
        if($kat == 'roti'){
            mysqli_query($conn, "UPDATE barang_jadi SET jumlahStok = jumlahStok - $jml WHERE idBarangJadi = '$id'");
        } else {
            mysqli_query($conn, "UPDATE bahan_baku SET jumlahStok = jumlahStok - $jml WHERE idBahan = '$id'");
        }
        
        // 2. Simpan ke Riwayat (Sesuai kolom satuan)
        mysqli_query($conn, "INSERT INTO barang_keluar (idBarang, jumlah, satuan, tanggal, tipe, keterangan) 
                              VALUES ('$id', '$jml', '$sat', '$tgl', '$kat', '$ket')");
        
        header("location:barang_keluar.php?status=sukses");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Barang Keluar - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; margin: 0; }
        .main-content { flex: 1; padding: 40px; margin-left: 260px; }
        .card-custom { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2 class="fw-bold mb-4">Input Barang Keluar</h2>
        <?php if($error != "") echo "<div class='alert alert-danger border-0 shadow-sm'>$error</div>"; ?>
        <?php if(isset($_GET['status'])) echo "<div class='alert alert-success border-0 shadow-sm'>✅ Berhasil mengeluarkan barang.</div>"; ?>

        <div class="card-custom mb-5">
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
                            <?php 
                            $r = mysqli_query($conn, "SELECT idBarangJadi as id, namaRoti as n, jumlahStok as s FROM barang_jadi");
                            while($row = mysqli_fetch_assoc($r)) echo "<option value='".$row['id']."'>".$row['n']." (Stok: ".$row['s'].")</option>";
                            $b = mysqli_query($conn, "SELECT idBahan as id, namaBahan as n, jumlahStok as s FROM bahan_baku");
                            while($row = mysqli_fetch_assoc($b)) echo "<option value='".$row['id']."'>".$row['n']." (Stok: ".$row['s'].")</option>";
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold mb-2">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold mb-2">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="pcs/kg" required>
                    </div>
                    <div class="col-md-8">
                        <label class="small fw-bold mb-2">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Tujuan pengeluaran">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="simpan_keluar" class="btn btn-danger w-100 fw-bold py-2">Keluar</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-custom">
            <h5 class="fw-bold mb-3">Riwayat Transaksi Keluar</h5>
            <table class="table table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>ID Barang</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody class="text-center small">
                    <?php 
                    $data = mysqli_query($conn, "SELECT * FROM barang_keluar ORDER BY id_keluar DESC LIMIT 5");
                    while($row = mysqli_fetch_assoc($data)){ ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td class="fw-bold"><?= $row['idBarang'] ?></td>
                        <td class="text-danger fw-bold">- <?= $row['jumlah'] ?></td>
                        <td><?= $row['satuan'] ?></td>
                        <td><span class="badge bg-secondary"><?= strtoupper($row['tipe']) ?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>