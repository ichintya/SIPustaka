<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIPustaka </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f4f7f6; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .icon-header {
            font-size: 50px;
            color: #4F46E5;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        .form-control:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
            background-color: white;
        }
        .btn-login {
            background-color: #4F46E5;
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-login:hover { background-color: #4338CA; color: white; }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <i class='bx bx-book-reader icon-header'></i>
        <h4 class="fw-bold mb-1">SIPustaka Admin</h4>
        <p class="text-muted small mb-4">Silakan masuk ke akun Anda</p>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal"){
                echo "<script>Swal.fire('Gagal Login!', 'Username atau Password salah!', 'error');</script>";
            } else if($_GET['pesan'] == "logout"){
                echo "<script>Swal.fire('Berhasil!', 'Anda telah logout.', 'success');</script>";
            } else if($_GET['pesan'] == "belum_login"){
                echo "<script>Swal.fire('Akses Ditolak!', 'Anda harus login untuk mengakses dashboard.', 'warning');</script>";
            }
        }
        ?>

        <form action="proses_login.php" method="POST">
            <div class="mb-3 text-start">
                <label class="fw-semibold small mb-1">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class='bx bx-user'></i></span>
                    <input type="text" name="username" class="form-control border-start-0" required placeholder="Masukkan username">
                </div>
            </div>
            
            <div class="mb-4 text-start">
                <label class="fw-semibold small mb-1">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class='bx bx-lock-alt'></i></span>
                    <input type="password" name="password" class="form-control border-start-0" required placeholder="Masukkan password">
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 shadow-sm">Masuk Sistem</button>
        </form>
    </div>

</body>
</html>