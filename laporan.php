<?php 
session_start();
include 'koneksi.php'; 

// Proteksi akses
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'owner') { 
    header("location:dashboard.php"); 
    exit(); 
}

// Set Tanggal Default (Awal bulan sampai hari ini)
$tgl_awal = $_POST['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_POST['tgl_akhir'] ?? date('Y-m-d');

// Query UNION yang sudah menyertakan kolom SATUAN
$query = "
    SELECT tanggal, idBarang, jumlah, satuan, tipe, keterangan, 'MASUK' as jenis 
    FROM barang_masuk 
    WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
    UNION ALL
    SELECT tanggal, idBarang, jumlah, satuan, tipe, keterangan, 'KELUAR' as jenis 
    FROM barang_keluar 
    WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY tanggal DESC
";

$sql = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; margin: 0; }
        .sidebar { width: 260px; background: #2c3e50; color: white; padding: 20px; position: fixed; height: 100vh; z-index: 100; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; }
        .main-content { flex: 1; padding: 40px; margin-left: 260px; width: calc(100% - 260px); }
        
        .card-report { background: white; border-radius: 15px; border: none; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }

        @media print {
            .sidebar, .no-print, .btn-print-action { display: none !important; }
            .main-content { margin-left: 0; width: 100%; padding: 0; }
            .card-report { box-shadow: none; border: none; }
            body { background-color: white; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h2 class="fw-bold m-0">Laporan Transaksi</h2>
            <button onclick="window.print()" class="btn btn-dark fw-bold shadow-sm">
                <i class="bi bi-printer me-2"></i>Cetak PDF
            </button>
        </div>
        
        <div class="card card-report p-4 mb-4 no-print">
            <form action="" method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Dari Tanggal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir; ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-filter me-1"></i> Filter Data
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-report p-5">
            <div class="text-center mb-5">
                <h3 class="fw-bold mb-1">GUDANG ROTI</h3>
                <h5 class="text-muted">Laporan Rekapitulasi Stok Masuk & Keluar</h5>
                <hr style="width: 100px; margin: 15px auto; border-top: 3px solid #212529;">
                <p class="small">Periode: <strong><?= date('d/m/Y', strtotime($tgl_awal)); ?></strong> s/d <strong><?= date('d/m/Y', strtotime($tgl_akhir)); ?></strong></p>
            </div>
            
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr class="small text-uppercase fw-bold">
                        <th width="50">No</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Satuan</th> <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    if(mysqli_num_rows($sql) > 0) {
                        while($d = mysqli_fetch_array($sql)){ 
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= date('d-m-Y', strtotime($d['tanggal'])); ?></td>
                        <td class="fw-bold"><?= $d['idBarang']; ?></td>
                        <td class="text-center"><?= strtoupper($d['tipe']); ?></td>
                        <td class="text-center">
                            <span class="badge <?= $d['jenis'] == 'MASUK' ? 'bg-success' : 'bg-danger'; ?>">
                                <?= $d['jenis']; ?>
                            </span>
                        </td>
                        <td class="text-center fw-bold">
                            <?= $d['jenis'] == 'MASUK' ? '+' : '-'; ?> <?= number_format($d['jumlah']); ?>
                        </td>
                        <td class="text-center"><?= $d['satuan']; ?></td> <td class="small text-muted"><?= $d['keterangan']; ?></td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='8' class='text-center py-4 text-muted'>Tidak ada data ditemukan untuk periode ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="mt-5 d-flex justify-content-between">
                <div class="text-center" style="width: 200px;">
                    <p class="mb-5 small">Mengetahui,</p>
                    <p class="fw-bold mb-0">___________________</p>
                    <p class="small">Kepala Gudang</p>
                </div>
                <div class="text-center" style="width: 200px;">
                    <p class="mb-5 small">Dicetak pada: <?= date('d/m/Y') ?></p>
                    <p class="fw-bold mb-0">Admin Gudang</p>
                    <p class="small">( <?= $_SESSION['username'] ?? 'Admin' ?> )</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>