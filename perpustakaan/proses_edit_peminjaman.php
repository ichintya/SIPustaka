<?php
include 'koneksi.php';

$id_peminjaman = $_POST['id_peminjaman'];
$id_anggota = $_POST['id_anggota'];
$id_buku = $_POST['id_buku'];
$tanggal_pinjam = $_POST['tanggal_pinjam'];
$tanggal_kembali = $_POST['tanggal_kembali'];

$query = mysqli_query($koneksi, "UPDATE peminjaman SET id_anggota='$id_anggota', id_buku='$id_buku', tanggal_pinjam='$tanggal_pinjam', tanggal_kembali='$tanggal_kembali' WHERE id_peminjaman='$id_peminjaman'");

if($query) {
    echo "sukses";
} else {
    echo "gagal";
}
?>