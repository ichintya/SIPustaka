<?php 
session_start(); // Memulai session
include 'koneksi.php';

$username = $_POST['username'];
$password = md5($_POST['password']); // Mengenkripsi password inputan menjadi MD5

// Cek data di database
$cek = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
$jumlah = mysqli_num_rows($cek);

if($jumlah > 0){
    $data = mysqli_fetch_assoc($cek);
    
    // Menyimpan data ke dalam session
    $_SESSION['username'] = $username;
    $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
    $_SESSION['status'] = "login";
    
    header("location:index.php"); // Pindah ke dashboard jika sukses
} else {
    header("location:login.php?pesan=gagal"); // Kembali ke form jika gagal
}
?>