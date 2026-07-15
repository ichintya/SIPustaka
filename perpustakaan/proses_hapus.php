<?php
include 'koneksi.php';

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM peminjaman WHERE id_peminjaman = '$id'";
    
    if(mysqli_query($koneksi, $query)) {
        echo "sukses";
    } else {
        echo "gagal";
    }
}
?>