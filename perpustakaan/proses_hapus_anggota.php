<?php
include 'koneksi.php';


$id_anggota = $_POST['id_anggota'];


$query = "DELETE FROM anggota WHERE id_anggota = '$id_anggota'";
$hapus = mysqli_query($koneksi, $query);

if($hapus) {
    echo "sukses"; 
} else {
    echo "gagal";
}
?>