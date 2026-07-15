<?php
include 'koneksi.php';

if(isset($_POST['nama_anggota'])) {
    $nama_anggota = $_POST['nama_anggota'];
    $no_hp        = $_POST['no_hp'];

    if(mysqli_query($koneksi, "INSERT INTO anggota (nama_anggota, no_hp) VALUES ('$nama_anggota', '$no_hp')")) { 
        echo "sukses"; 
    } else { echo "gagal"; }
}
?>