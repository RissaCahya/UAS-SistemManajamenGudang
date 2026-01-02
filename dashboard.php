<?php 
include 'koneksi.php'; 

// Proteksi halaman: Cek login
if(!isset($_SESSION['nama'])){
    header("location:index.php");
    exit();
}

// 1. Ambil Data Statistik untuk Kartu
$totalBahan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlahStok) as total FROM bahan_baku"))['total'] ?? 0;
$totalRoti  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlahStok) as total FROM barang_jadi"))['total'] ?? 0;
$varianRoti = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang_jadi"))['total'] ?? 0;

// 2. Hitung Antrean Verifikasi (Khusus untuk tampilan Admin)
$antrean = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM stok_opname WHERE statusVerifikasi='pending'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; }
        .main-content { flex: 1; padding: 30px; margin-left: 250px; }
        .card-stats { border: none; border-radius: 12px; transition: 0.3s; border-bottom: 5px solid transparent; }
        .card-stats:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .bg-bahan { border-bottom-color: #3498db; }
        .bg-roti { border-bottom-color: #2ecc71; }
        .bg-varian { border-bottom-color: #f1c40f; }
        .bg-antrean { border-bottom-color: #e74c3c; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Dashboard</h2>
                <p class="text-muted">Ringkasan aktivitas gudang hari ini</p>
            </div>
            <div class="text-end">
                <span class="badge bg-dark p-2"><?= date('l, d F Y'); ?></span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stats bg-white p-3 bg-bahan">
                    <small class="text-muted">Total Bahan Baku</small>
                    <h3 class="fw-bold"><?= number_format($totalBahan); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-white p-3 bg-roti">
                    <small class="text-muted">Total Roti Jadi</small>
                    <h3 class="fw-bold"><?= number_format($totalRoti); ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-white p-3 bg-varian">
                    <small class="text-muted">Varian Produk</small>
                    <h3 class="fw-bold"><?= $varianRoti; ?> Item</h3>
                </div>
            </div>
            
            <?php if($_SESSION['role'] == 'admin'): ?>
            <div class="col-md-3">
                <div class="card card-stats bg-white p-3 bg-antrean">
                    <small class="text-muted">Antrean Verifikasi</small>
                    <h3 class="fw-bold text-danger"><?= $antrean; ?> <small style="font-size: 1rem;">Perlu Cek</small></h3>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold mb-4">Perbandingan Stok Gudang</h5>
                    <canvas id="chartGudang" height="120"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="fw-bold">Status Sistem</h5>
                    <hr>
                    <div class="mb-3">
                        <label class="small text-muted d-block">User Aktif:</label>
                        <span class="fw-bold text-primary"><?= $_SESSION['nama']; ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Hak Akses:</label>
                        <span class="badge bg-success"><?= strtoupper($_SESSION['role']); ?></span>
                    </div>
                    <?php if($_SESSION['role'] == 'admin' && $antrean > 0): ?>
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Ada <strong><?= $antrean; ?></strong> data opname yang menunggu verifikasi Anda.
                            <a href="verifikasi_admin.php" class="d-block mt-2 fw-bold text-decoration-none">Lihat Sekarang &rarr;</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('chartGudang').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Bahan Baku', 'Roti Jadi'],
                datasets: [{
                    label: 'Jumlah Stok (Unit)',
                    data: [<?= $totalBahan; ?>, <?= $totalRoti; ?>],
                    backgroundColor: ['#3498db', '#2ecc71'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>