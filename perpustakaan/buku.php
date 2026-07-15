<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit; 
}
include 'koneksi.php';

// Query canggih: Mengambil data buku sekaligus menghitung berapa yang sedang dipinjam
$query = mysqli_query($koneksi, "
    SELECT b.*, k.nama_kategori, 
    (SELECT COUNT(id_peminjaman) FROM peminjaman WHERE id_buku = b.id_buku AND status = 'Dipinjam') as dipinjam 
    FROM buku b 
    INNER JOIN kategori k ON b.id_kategori = k.id_kategori 
    ORDER BY b.id_buku DESC
");

$q_kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
$q_kategori_edit = mysqli_query($koneksi, "SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Buku - SIPustaka</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #333; }
        .navbar { background-color: #ffffff; border-bottom: 1px solid #eaeaea; }
        .navbar-brand { color: #4F46E5 !important; font-weight: 700; }
        .nav-link { color: #6b7280 !important; font-weight: 500; margin-left: 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #4F46E5 !important; }
        
        .main-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); background: white; padding: 25px; }
        table.dataTable { border-collapse: collapse !important; width: 100%; margin-top: 20px !important; }
        table.dataTable thead th { border-bottom: 2px solid #f0f0f0; background-color: #ffffff; color: #6b7280; font-weight: 600; font-size: 13px; text-transform: uppercase; padding: 15px; }
        table.dataTable tbody td { border-bottom: 1px solid #f8f9fa; padding: 18px 15px; vertical-align: middle; color: #4b5563; font-size: 14px; }
        
        .badge-kategori { background-color: #EEF2FF; color: #4F46E5; padding: 6px 15px; border-radius: 8px; font-weight: 600; font-size: 12px; }
        .btn-add { background-color: #4F46E5; color: white; border-radius: 50px; font-weight: 500; padding: 8px 22px; border: none; transition: 0.3s; }
        .btn-add:hover { background-color: #4338CA; color: white; }
        .icon-book { font-size: 40px; color: #10B981; background: #D1FAE5; padding: 10px; border-radius: 12px; margin-right: 15px; }
        
        .btn-action { width: 35px; height: 35px; display: inline-flex; justify-content: center; align-items: center; border-radius: 10px; border: none; transition: 0.2s; }
        .btn-edit { background-color: #FEF3C7; color: #D97706; }
        .btn-edit:hover { background-color: #FDE68A; }
        .btn-delete { background-color: #FEE2E2; color: #DC2626; }
        .btn-delete:hover { background-color: #FECACA; }
        .btn-disabled { background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

        .modal-content { border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.1); }
        .modal-header { border-bottom: 1px solid #f0f0f0; background-color: #ffffff; border-radius: 20px 20px 0 0; padding: 20px 25px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #e5e7eb; background-color: #f9fafb; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15); background-color: white; }
        #konten-web { display: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3 sticky-top mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php"><i class='bx bx-book-reader fs-3 me-2'></i> SIPustaka</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="buku.php">Katalog Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="anggota.php">Data Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="tampil.php">Transaksi Peminjaman</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" href="logout.php"><i class='bx bx-log-out me-1'></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" id="konten-web">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Katalog & Stok Buku</h3>
                <p class="text-muted mb-0">Kelola ketersediaan dan stok koleksi perpustakaan.</p>
            </div>
            <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBuku">
                <i class='bx bx-book-add me-1'></i> Tambah Buku Baru
            </button>
        </div>

        <div class="main-card">
            <table id="tabelBuku" class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">ID</th>
                        <th>Informasi Buku</th>
                        <th>Pengarang</th>
                        <th class="text-center">Ketersediaan Stok</th>
                        <th class="text-end" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = mysqli_fetch_assoc($query)) { 
                        // Logika Ketersediaan Stok
                        $sisa_stok = $row['stok'] - $row['dipinjam'];
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold">#<?php echo $row['id_buku']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class='bx bx-book icon-book'></i>
                                <div>
                                    <div class="fw-bold text-dark fs-6 mb-1"><?php echo $row['judul_buku']; ?></div>
                                    <span class="badge-kategori"><?php echo $row['nama_kategori']; ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted fw-medium d-flex align-items-center">
                                <i class='bx bx-pen me-2 text-primary'></i> <?php echo $row['pengarang']; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="mb-1 text-dark fw-bold">Stok Awal: <?php echo $row['stok']; ?></div>
                            <?php if($sisa_stok > 0) { ?>
                                <div class="text-success small fw-bold"><i class='bx bx-check-circle me-1'></i>Tersedia: <?php echo $sisa_stok; ?></div>
                            <?php } else { ?>
                                <div class="text-danger small fw-bold"><i class='bx bx-error-circle me-1'></i>Stok Habis</div>
                            <?php } ?>
                        </td>
                        <td class="text-end">
                            <button class="btn-action btn-edit btn-edit-buku me-1" 
                                data-id="<?php echo $row['id_buku']; ?>" 
                                data-judul="<?php echo $row['judul_buku']; ?>" 
                                data-pengarang="<?php echo $row['pengarang']; ?>" 
                                data-kategori="<?php echo $row['id_kategori']; ?>"
                                data-stok="<?php echo $row['stok']; ?>" 
                                title="Edit Buku">
                                <i class='bx bx-edit-alt fs-5'></i>
                            </button>

                            <?php if($row['dipinjam'] > 0) { ?>
                                <button class="btn-action btn-disabled" title="Ada stok yang masih dipinjam!">
                                    <i class='bx bx-trash fs-5'></i>
                                </button>
                            <?php } else { ?>
                                <button class="btn-action btn-delete btn-hapus-buku" data-id="<?php echo $row['id_buku']; ?>" title="Hapus Buku">
                                    <i class='bx bx-trash fs-5'></i>
                                </button>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalTambahBuku" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center text-primary"><i class='bx bx-book-add fs-4 me-2'></i> Tambah Koleksi Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputBuku">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Judul Buku</label>
                            <input type="text" name="judul_buku" class="form-control" required placeholder="Masukkan judul buku...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nama Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" required placeholder="Contoh: Andrea Hirata">
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while($k = mysqli_fetch_assoc($q_kategori)) { ?>
                                        <option value="<?php echo $k['id_kategori']; ?>"><?php echo $k['nama_kategori']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" min="1" value="1" required>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-add py-2 fs-6">Simpan Katalog Buku</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditBuku" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" style="color: #D97706;"><i class='bx bx-edit fs-4 me-2'></i> Edit Data Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditBuku">
                        <input type="hidden" name="id_buku" id="edit_id_buku">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Judul Buku</label>
                            <input type="text" name="judul_buku" id="edit_judul" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nama Pengarang</label>
                            <input type="text" name="pengarang" id="edit_pengarang" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Kategori</label>
                                <select name="id_kategori" id="edit_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while($k = mysqli_fetch_assoc($q_kategori_edit)) { ?>
                                        <option value="<?php echo $k['id_kategori']; ?>"><?php echo $k['nama_kategori']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Update Stok</label>
                                <input type="number" name="stok" id="edit_stok" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn py-2 fs-6" style="background-color: #D97706; color: white; border-radius: 50px; font-weight: 500;">Update Data Buku</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $("#konten-web").fadeIn(1000);
            
            $('#tabelBuku').DataTable({
                "language": { "search": "", "searchPlaceholder": "Cari judul/pengarang...", "lengthMenu": "Tampilkan _MENU_ data", "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ buku", "paginate": { "previous": "<i class='bx bx-chevron-left'></i>", "next": "<i class='bx bx-chevron-right'></i>" } }
            });
            $('.dataTables_filter input').addClass('form-control form-control-sm border-0 bg-light rounded-pill px-3').css('box-shadow', 'none');

            // 1. AJAX Tambah Buku
            $("#formInputBuku").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: 'proses_buku.php', type: 'POST', data: $(this).serialize(),
                    success: function(response){
                        if(response.trim() == "sukses") {
                            $('#modalTambahBuku').modal('hide');
                            Swal.fire({title: 'Berhasil!', text: 'Buku ditambahkan beserta stok.', icon: 'success', timer: 1500, showConfirmButton: false}).then(() => { location.reload(); });
                        } else { Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); }
                    }
                });
            });

            // 2. Tampilkan Data di Modal Edit (Dengan Stok)
            $(document).on('click', '.btn-edit-buku', function(){
                $('#edit_id_buku').val($(this).data('id'));
                $('#edit_judul').val($(this).data('judul'));
                $('#edit_pengarang').val($(this).data('pengarang'));
                $('#edit_kategori').val($(this).data('kategori'));
                $('#edit_stok').val($(this).data('stok')); // Memasukkan value stok
                $('#modalEditBuku').modal('show');
            });

            // 3. AJAX Update Buku
            $("#formEditBuku").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: 'proses_edit_buku.php', type: 'POST', data: $(this).serialize(),
                    success: function(response){
                        if(response.trim() == "sukses") {
                            $('#modalEditBuku').modal('hide');
                            Swal.fire({title: 'Berhasil!', text: 'Data buku dan stok diperbarui.', icon: 'success', timer: 1500, showConfirmButton: false}).then(() => { location.reload(); });
                        } else { Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'); }
                    }
                });
            });

            // 4. AJAX Hapus Buku
            $(document).on('click', '.btn-hapus-buku', function(){
                var id_buku = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Buku?', text: "Semua stok buku ini akan dihapus permanen!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#DC2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'proses_hapus_buku.php', type: 'POST', data: { id: id_buku },
                            success: function(response){
                                if(response.trim() == "sukses") {
                                    Swal.fire({title:'Terhapus!', icon:'success', timer:1500, showConfirmButton:false}).then(() => { location.reload(); });
                                } else { Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'); }
                            }
                        });
                    }
                })
            });
        });
    </script>
</body>
</html>