<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Gudang Roti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* CSS yang diselaraskan dengan Dashboard */
        body { 
            background-color: #f4f6f9; /* Warna background utama dashboard */
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card { 
            width: 100%;
            max-width: 400px; 
            padding: 40px; 
            border-radius: 15px; 
            background: white; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); /* Shadow halus */
            border: none;
        }
        .login-card h3 {
            color: #2c3e50; /* Warna sidebar */
            font-weight: 800;
            letter-spacing: 1px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #2c3e50;
            background-color: #fff;
        }
        .btn-login {
            background-color: #2c3e50; /* Selaras dengan tema gelap */
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            color: white;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
            color: white;
        }
        .brand-icon {
            font-size: 3rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-card text-center">
        <div class="brand-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <h3>GUDANG ROTI</h3>
        <p class="text-muted small mb-4">Silakan masuk ke akun Anda</p>
        
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="namaUser" class="form-control" placeholder="Username" required autocomplete="off">
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn btn-login w-100 shadow-sm">
                LOGIN
            </button>
        </form>

        <?php
        // Bagian logika PHP di dalam index.php
        if(isset($_POST['login'])){
            $user = mysqli_real_escape_string($conn, $_POST['namaUser']);
            $pass = mysqli_real_escape_string($conn, $_POST['password']);

            $query = mysqli_query($conn, "SELECT * FROM user WHERE namaUser='$user' AND password='$pass'");
    
         if(mysqli_num_rows($query) > 0){
            $data = mysqli_fetch_assoc($query);
            $_SESSION['role'] = $data['role'];
            $_SESSION['nama'] = $data['namaUser'];
            $_SESSION['id']   = $data['idUser']; // Menggunakan idUser
        
            header("location:dashboard.php");
            exit();
        } else {
            echo "<script>alert('User atau Password Salah!');</script>";
        }
        }
        ?>
        
        <div class="mt-4">
            <small class="text-muted">&copy; 2025 Sistem Inventaris Roti</small>
        </div>
    </div>
</body>
</html>