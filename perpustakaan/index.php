<?php
session_start();
// Cek apakah pengguna sudah login
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit; // Hentikan eksekusi script jika belum login
}
include 'koneksi.php';

// Menghitung jumlah data
$jml_buku = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku"));
$jml_anggota = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM anggota"));
$jml_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE status='Dipinjam'"));

// Mengambil 4 Buku Terbaru untuk Katalog Dashboard
$q_buku_terbaru = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori FROM buku b INNER JOIN kategori k ON b.id_kategori = k.id_kategori ORDER BY b.id_buku DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SIPustaka</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f4f7f6; /* Warna latar abu-abu sangat muda yang bersih */
            color: #333;
        }
        /* Navbar Modern */
        .navbar { background-color: #ffffff; border-bottom: 1px solid #eaeaea; }
        .navbar-brand { color: #4F46E5 !important; font-weight: 700; letter-spacing: 0.5px; }
        .nav-link { color: #6b7280 !important; font-weight: 500; margin-left: 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #4F46E5 !important; }
        
        /* Card Statistik Modern */
        .card-stat { 
            border: none; 
            border-radius: 20px; 
            padding: 25px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        .card-stat:hover { transform: translateY(-7px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .bg-gradient-1 { background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); }
        .bg-gradient-2 { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
        .bg-gradient-3 { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
        
        .icon-stat { font-size: 3.5rem; opacity: 0.8; }
        
        /* Card Katalog Dashboard */
        .card-buku {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            transition: 0.3s;
            background: white;
        }
        .card-buku:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .kategori-badge { background-color: #EEF2FF; color: #4F46E5; font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 20px; }
        
        /* KUNCI ANIMASI: Sembunyikan konten awal untuk efek FadeIn jQuery */
        #konten-web { display: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class='bx bx-book-reader fs-3 me-2'></i> SIPustaka
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="buku.php">Katalog Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="anggota.php">Data Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="tampil.php">Transaksi Peminjaman</a></li>
                    
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="btn btn-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" href="logout.php">
                            <i class='bx bx-log-out me-1'></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5" id="konten-web">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Overview</h3>
                <p class="text-muted mb-0">Selamat Datang, Admin!</p>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card-stat bg-gradient-1 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 fw-medium">Total Katalog Buku</p>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $jml_buku; ?></h2>
                        <a href="buku.php" class="text-white text-decoration-none small mt-2 d-inline-block border-bottom border-light">Kelola Buku <i class='bx bx-right-arrow-alt'></i></a>
                    </div>
                    <i class='bx bx-book icon-stat'></i>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card-stat bg-gradient-2 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 fw-medium">Anggota Aktif</p>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $jml_anggota; ?></h2>
                        <a href="anggota.php" class="text-white text-decoration-none small mt-2 d-inline-block border-bottom border-light">Kelola Anggota <i class='bx bx-right-arrow-alt'></i></a>
                    </div>
                    <i class='bx bx-user-circle icon-stat'></i>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card-stat bg-gradient-3 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75 fw-medium">Buku Sedang Dipinjam</p>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $jml_pinjam; ?></h2>
                        <a href="tampil.php" class="text-white text-decoration-none small mt-2 d-inline-block border-bottom border-light">Lihat Transaksi <i class='bx bx-right-arrow-alt'></i></a>
                    </div>
                    <i class='bx bx-transfer icon-stat'></i>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class='bx bx-library text-primary me-2'></i>Katalog Buku Terbaru</h5>
            <a href="buku.php" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-medium">Lihat Semua Buku</a>
        </div>
        
        <div class="row">
            <?php while($b = mysqli_fetch_assoc($q_buku_terbaru)) { ?>
            <div class="col-md-3 mb-4">
                <div class="card card-buku h-100 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="kategori-badge"><?php echo $b['nama_kategori']; ?></span>
                        <i class='bx bx-bookmark-alt text-muted fs-4'></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark"><?php echo $b['judul_buku']; ?></h6>
                    <p class="text-muted small mb-3">Oleh: <?php echo $b['pengarang']; ?></p>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Memunculkan konten perlahan (durasi 1 detik / 1000 milidetik)
            $("#konten-web").fadeIn(1000);
        });
    </script>
</body>
</html>