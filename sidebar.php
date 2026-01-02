<div class="sidebar">
    <div class="sidebar-header">
        <h4 class="fw-bold">Gudang Roti</h4>
        <div class="user-info">
            <small>Role: <strong><?= strtoupper($_SESSION['role']); ?></strong></small><br>
            <small>
                <?= isset($_SESSION['namaUser']) ? $_SESSION['namaUser'] : 'User' ?>
            </small>

        </div>
    </div>
    <hr>
    <nav class="nav-menu">
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>

        <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="adminBarangJadi.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'adminBarangJadi.php' || basename($_SERVER['PHP_SELF']) == 'adminBahanBaku.php') ? 'active' : ''; ?>">
                <i class="bi bi-box-seam me-2"></i> Master Barang
            </a>
        <?php endif; ?>

        <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staf'): ?>
            <a href="barang_masuk.php" class="<?= basename($_SERVER['PHP_SELF']) == 'barang_masuk.php' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-down-left-circle me-2"></i> Barang Masuk
            </a>
        <?php endif; ?>

        <?php if($_SESSION['role'] == 'admin'): ?>
            <a href="barang_keluar.php" class="<?= basename($_SERVER['PHP_SELF']) == 'barang_keluar.php' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-up-right-circle me-2"></i> Barang Keluar
            </a>
            <a href="adminStokOpname.php" class="<?= basename($_SERVER['PHP_SELF']) == 'adminStokOpname.php' ? 'active' : ''; ?>">
                <i class="bi bi-patch-check me-2"></i> Verifikasi Stok
            </a>
            <a href="adminUser.php" class="<?= basename($_SERVER['PHP_SELF']) == 'adminUser.php' ? 'active' : ''; ?>">
                <i class="bi bi-people me-2"></i> Manajemen User
            </a>
        <?php endif; ?>

        <?php if($_SESSION['role'] == 'staf'): ?>
            <a href="adminStokOpname.php" class="<?= basename($_SERVER['PHP_SELF']) == 'adminStokOpname.php' ? 'active' : ''; ?>">
                <i class="bi bi-clipboard-check me-2"></i> Stok Opname
            </a>
        <?php endif; ?>

        <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'owner'): ?>
            <a href="laporan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan
            </a>
        <?php endif; ?>
        
        <a href="logout.php" class="logout-link text-danger mt-3 d-block px-3 text-decoration-none">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </nav>
</div>

<style>
    .sidebar { width: 250px; background: #2c3e50; color: white; position: fixed; height: 100vh; padding: 20px 0; z-index: 1000; }
    .sidebar-header { padding: 0 20px; margin-bottom: 20px; }
    .nav-menu a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
    .nav-menu a:hover, .nav-menu a.active { background: #34495e; color: white; border-left: 4px solid #1abc9c; }
    .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
</style>