<?php 
include 'koneksi.php';
$id = $_POST['id_buku']; 
$judul = $_POST['judul_buku']; 
$pengarang = $_POST['pengarang']; 
$id_kat = $_POST['id_kategori'];
$stok = $_POST['stok']; // Menangkap perubahan jumlah stok

if(mysqli_query($koneksi, "UPDATE buku SET judul_buku='$judul', pengarang='$pengarang', id_kategori='$id_kat', stok='$stok' WHERE id_buku='$id'")) { 
    echo "sukses"; 
} else { 
    echo "gagal"; 
} 
?>