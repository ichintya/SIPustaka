<?php
include 'koneksi.php';

$id_anggota = $_POST['id_anggota'];
$id_buku = $_POST['id_buku'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_kembali = $_POST['tanggal_kembali'];
$status = 'Dipinjam';

$query = mysqli_query($koneksi, "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status) 
                                 VALUES ('$id_anggota', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali', '$status')");

if($query) {
    // Kurangi stok di tabel buku
    mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
    echo "sukses"; 
} else {
    echo "gagal";
}
?>