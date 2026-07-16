<?php
include 'koneksi.php';

$id_buku = $_POST['id'];

$query = "DELETE FROM buku WHERE id_buku = '$id_buku'";
$hapus = mysqli_query($koneksi, $query);

if($hapus) {
    echo "sukses"; 
} else {
    echo "gagal";
}
?>