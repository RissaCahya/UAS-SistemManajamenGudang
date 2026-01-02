<?php 
ob_start(); // Mencegah error "headers already sent"
session_start();
include 'koneksi.php';

// Atur Zona Waktu agar ID SO sesuai dengan jam lokal
date_default_timezone_set('Asia/Jakarta');

// 1. PROTEKSI: Hanya Admin dan Staf yang bisa buka
if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staf')) { 
    header("location:dashboard.php"); 
    exit(); 
}

// 2. LOGIKA STAF: SIMPAN OPNAME
if(isset($_POST['simpan_opname'])){
    $data_split = explode('|', $_POST['id_barang']);
    $id_barang  = $data_split[0]; 
    $sistem     = $data_split[1]; 
    $fisik      = $_POST['stok_fisik'];
    
    // FORMAT ID: SO + YYMMDDHHII (Total 12 Karakter sesuai database)
    $idOpname   = "SO" . date('y') . date('m') . date('d') . date('H') . date('i'); 
    
    $selisih    = $fisik - $sistem; 
    $tgl        = date('Y-m-d'); 
    $ket        = mysqli_real_escape_string($conn, $_POST['ket']);

    // INSERT sesuai urutan 9 kolom (8 kolom awal + 1 kolom idBarang)
    $query = "INSERT INTO stok_opname VALUES (
        '$idOpname', '$sistem', '$fisik', '$selisih', '$tgl', 'pending', '$ket', 'PENDING', '$id_barang'
    )";

    if(mysqli_query($conn, $query)){
        // Redirect ke file ini sendiri
        header("location:adminStokOpname.php?status=sukses");
        exit(); 
    } else {
        die("Error Database: " . mysqli_error($conn));
    }
}

// 3. LOGIKA ADMIN: VERIFIKASI
if(isset($_GET['setuju']) && $_SESSION['role'] == 'admin'){
    $id = mysqli_real_escape_string($conn, $_GET['setuju']);
    
    // Ambil data opname untuk proses update stok master
    $data_opn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM stok_opname WHERE idOpname='$id'"));
    $fisik_opn = $data_opn['stokFisik'];
    $id_item   = $data_opn['idBarang'];
    
    // Update status di kedua kolom enum yang ada di database
    mysqli_query($conn, "UPDATE stok_opname SET statusVerifikasi='approved', status_verifikasi='DISETUJUI' WHERE idOpname='$id'");

    // Sinkronisasi Stok ke Master (Bahan Baku atau Barang Jadi)
    $cekBahan = mysqli_query($conn, "SELECT * FROM bahan_baku WHERE idBahan = '$id_item'");
    if(mysqli_num_rows($cekBahan) > 0){
        mysqli_query($conn, "UPDATE bahan_baku SET jumlahStok = '$fisik_opn' WHERE idBahan = '$id_item'");
    } else {
        mysqli_query($conn, "UPDATE barang_jadi SET jumlahStok = '$fisik_opn' WHERE idBarangJadi = '$id_item'");
    }
    
    header("location:adminStokOpname.php?status=verifikasi_berhasil");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stok Opname - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; margin: 0; }
        .main-content { flex: 1; padding: 40px; margin-left: 250px; }
        .glass-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2 class="fw-bold mb-4">Stok Opname</h2>

        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> 
                <?= $_GET['status'] == 'sukses' ? 'Laporan Opname berhasil dikirim ke Admin!' : 'Data stok berhasil diperbarui!' ?>
            </div>
        <?php endif; ?>

        <?php if($_SESSION['role'] == 'staf'): ?>
            <div class="glass-card mb-4">
                <h5 class="fw-bold mb-3">Input Hasil Opname Fisik</h5>
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold mb-1">Pilih Barang (Bahan/Roti)</label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                <optgroup label="Barang Jadi (Roti)">
                                    <?php 
                                    $r = mysqli_query($conn, "SELECT * FROM barang_jadi"); 
                                    while($row = mysqli_fetch_assoc($r)){ 
                                        echo "<option value='".$row['idBarangJadi']."|".$row['jumlahStok']."'>".$row['namaRoti']." (Sistem: ".$row['jumlahStok'].")</option>"; 
                                    } ?>
                                </optgroup>
                                <optgroup label="Bahan Baku">
                                    <?php 
                                    $b = mysqli_query($conn, "SELECT * FROM bahan_baku"); 
                                    while($row = mysqli_fetch_assoc($b)){ 
                                        echo "<option value='".$row['idBahan']."|".$row['jumlahStok']."'>".$row['namaBahan']." (Sistem: ".$row['jumlahStok'].")</option>"; 
                                    } ?>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-1">Jumlah Fisik di Gudang</label>
                            <input type="number" name="stok_fisik" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold mb-1">Keterangan / Alasan Selisih</label>
                            <textarea name="ket" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" name="simpan_opname" class="btn btn-warning fw-bold px-4">Kirim Laporan</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <h5 class="fw-bold mb-3">Riwayat Opname</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Opname</th>
                            <th>ID Barang</th>
                            <th>Sistem</th>
                            <th>Fisik</th>
                            <th>Selisih</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php 
                        $res = mysqli_query($conn, "SELECT * FROM stok_opname ORDER BY tanggalOpname DESC, idOpname DESC");
                        while($row = mysqli_fetch_assoc($res)){ 
                            $badge = ($row['status_verifikasi'] == 'PENDING') ? 'bg-secondary' : (($row['status_verifikasi'] == 'DISETUJUI') ? 'bg-success' : 'bg-danger');
                        ?>
                        <tr>
                            <td><?= $row['idOpname']; ?></td>
                            <td class="fw-bold"><?= $row['idBarang']; ?></td>
                            <td><?= $row['stokSistem']; ?></td>
                            <td><?= $row['stokFisik']; ?></td>
                            <td class="fw-bold <?= $row['selisih'] < 0 ? 'text-danger' : 'text-primary'; ?>">
                                <?= ($row['selisih'] > 0 ? '+' : '') . $row['selisih']; ?>
                            </td>
                            <td><span class="badge <?= $badge; ?>"><?= $row['status_ver_teks'] ?? $row['status_verifikasi']; ?></span></td>
                            <td>
                                <?php if($row['status_verifikasi'] == 'PENDING' && $_SESSION['role'] == 'admin'): ?>
                                    <a href="adminStokOpname.php?setuju=<?= $row['idOpname']; ?>" class="btn btn-success btn-sm fw-bold shadow-sm" onclick="return confirm('Verifikasi data ini?')">Verifikasi</a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>