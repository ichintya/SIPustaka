<?php
include 'koneksi.php';

// Memastikan ada data yang dikirim lewat metode POST
if(isset($_POST['id_anggota'])) {
    
    // Menangkap data dari form
    $id_anggota  = $_POST['id_anggota'];
    $id_buku     = $_POST['id_buku'];
    $tgl_pinjam  = $_POST['tanggal_pinjam'];
    $tgl_kembali = $_POST['tanggal_kembali'];
    $status      = 'Dipinjam'; // Status awal otomatis "Dipinjam"

    // Query untuk menyimpan ke database
    $query = "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status) 
              VALUES ('$id_anggota', '$id_buku', '$tgl_pinjam', '$tgl_kembali', '$status')";
    
    // Mengeksekusi query dan memberikan balasan JSON ke AJAX
    if(mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'sukses']);
    } else {
        echo json_encode(['status' => 'gagal', 'pesan' => mysqli_error($koneksi)]);
    }
}
?>