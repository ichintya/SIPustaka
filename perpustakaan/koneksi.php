<?php
// Konfigurasi Database Lokal (XAMPP)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_perpustakaan"; // Pastikan namanya sesuai dengan yang kita buat tadi

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>