<?php 
include 'koneksi.php';

$id = $_POST['id'];

// 1. Ambil data batas waktu pengembalian dari database
$query_cek = mysqli_query($koneksi, "SELECT tanggal_kembali FROM peminjaman WHERE id_peminjaman = '$id'");
$data = mysqli_fetch_assoc($query_cek);

$tgl_kembali = $data['tanggal_kembali']; 
$tgl_sekarang = date('Y-m-d'); // Tanggal hari ini saat tombol diklik

$denda = 0; 
$pesan = "Buku berhasil dikembalikan tepat waktu.";

// 2. Logika Menghitung Keterlambatan & Denda
if (strtotime($tgl_sekarang) > strtotime($tgl_kembali)) {
    // Menghitung selisih hari
    $selisih = strtotime($tgl_sekarang) - strtotime($tgl_kembali);
    $hari_telat = floor($selisih / (60 * 60 * 24)); 
    
    // Tarif denda Rp 5.000 per hari
    $denda = $hari_telat * 5000; 
    $pesan = "Buku dikembalikan. Anda telat $hari_telat hari! Denda: Rp " . number_format($denda, 0, ',', '.');
}

// 3. Update status menjadi 'Selesai'
$update = mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Selesai' WHERE id_peminjaman = '$id'");

// 4. Kirim balasan ke AJAX (ke tampilan web)
if($update) { 
    echo json_encode(['status' => 'sukses', 'pesan' => $pesan, 'denda' => $denda]); 
} else { 
    echo json_encode(['status' => 'gagal', 'pesan' => 'Terjadi kesalahan pada database.']); 
} 
?>