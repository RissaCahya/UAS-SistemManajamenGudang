<?php 
session_start();
include 'koneksi.php'; 

// 1. PROTEKSI HALAMAN
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { 
    header("location:dashboard.php"); 
    exit(); 
}

// 2. PROSES TAMBAH USER
if(isset($_POST['tambah_user'])){
    $id       = mysqli_real_escape_string($conn, $_POST['idUser']); // Sesuai kolom idUser
    $username = mysqli_real_escape_string($conn, $_POST['namaUser']); // Sesuai kolom namaUser
    $password = $_POST['password']; // Disarankan dienkripsi, namun sesuaikan dengan sistem login Anda
    $role     = $_POST['role'];

    // Query insert ke tabel 'user' (Sesuai screenshot)
    $q = mysqli_query($conn, "INSERT INTO user (idUser, namaUser, password, role) 
                              VALUES ('$id', '$username', '$password', '$role')");
    if($q){
        header("location:adminUser.php?status=sukses");
        exit();
    } else {
        die("Gagal Simpan: " . mysqli_error($conn));
    }
}

// 3. PROSES HAPUS USER
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM user WHERE idUser = '$id'");
    header("location:adminUser.php?status=terhapus");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User - Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f4f6f9; margin: 0; }
        .main-content { flex: 1; padding: 40px; margin-left: 260px; }
        .glass-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="fw-bold mb-4">Manajemen Pengguna</h2>

        <?php if(isset($_GET['status'])): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <?= $_GET['status'] == 'sukses' ? '✅ User berhasil ditambahkan!' : '🗑️ User telah dihapus.' ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Tambah User</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">ID User (max 9 char)</label>
                            <input type="text" name="idUser" class="form-control" placeholder="Contoh: USR001" maxlength="9" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Username (namaUser)</label>
                            <input type="text" name="namaUser" class="form-control" placeholder="Nama pengguna" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Role</label>
                            <select name="role" class="form-select">
                                <option value="admin">Admin</option>
                                <option value="staf">Staf</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                        <button type="submit" name="tambah_user" class="btn btn-primary w-100 fw-bold">Daftarkan</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="glass-card">
                    <h5 class="fw-bold mb-3">Daftar User</h5>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $data = mysqli_query($conn, "SELECT * FROM user");
                            while($row = mysqli_fetch_assoc($data)): 
                            ?>
                            <tr>
                                <td><?= $row['idUser'] ?></td>
                                <td class="fw-bold"><?= $row['namaUser'] ?></td>
                                <td>
                                    <span class="badge <?= $row['role'] == 'admin' ? 'bg-primary' : ($row['role'] == 'owner' ? 'bg-dark' : 'bg-info') ?>">
                                        <?= strtoupper($row['role']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="adminUser.php?hapus=<?= $row['idUser'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Hapus user ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>