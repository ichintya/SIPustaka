<?php 
include 'koneksi.php';
$judul = $_POST['judul_buku']; 
$pengarang = $_POST['pengarang']; 
$id_kat = $_POST['id_kategori'];
$stok = $_POST['stok']; // Menangkap jumlah stok

if(mysqli_query($koneksi, "INSERT INTO buku (judul_buku, pengarang, id_kategori, stok) VALUES ('$judul', '$pengarang', '$id_kat', '$stok')")) { 
    echo "sukses"; 
} else { 
    echo "gagal"; 
} 
?>