<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit; 
}
include 'koneksi.php';

$query = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id_anggota DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Anggota - SIPustaka</title>
    
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
        
        .badge-status { background-color: #EEF2FF; color: #4F46E5; padding: 6px 15px; border-radius: 8px; font-weight: 600; font-size: 12px; }
        .btn-add { background-color: #4F46E5; color: white; border-radius: 50px; font-weight: 500; padding: 8px 22px; border: none; transition: 0.3s; }
        .btn-add:hover { background-color: #4338CA; color: white; }
        
        /* Ikon khusus user untuk menyamakan dengan icon-book */
        .icon-user { font-size: 40px; color: #4F46E5; background: #EEF2FF; padding: 10px; border-radius: 12px; margin-right: 15px; }
        
        .btn-action { width: 35px; height: 35px; display: inline-flex; justify-content: center; align-items: center; border-radius: 10px; border: none; transition: 0.2s; }
        .btn-edit { background-color: #FEF3C7; color: #D97706; }
        .btn-edit:hover { background-color: #FDE68A; }
        .btn-delete { background-color: #FEE2E2; color: #DC2626; }
        .btn-delete:hover { background-color: #FECACA; }

        .modal-content { border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.1); }
        .modal-header { border-bottom: 1px solid #f0f0f0; background-color: #ffffff; border-radius: 20px 20px 0 0; padding: 20px 25px; }
        .form-control { border-radius: 12px; padding: 12px 15px; border: 1px solid #e5e7eb; background-color: #f9fafb; transition: 0.3s; }
        .form-control:focus { border-color: #4F46E5; box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15); background-color: white; }
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
                    <li class="nav-item"><a class="nav-link" href="buku.php">Katalog Buku</a></li>
                    <li class="nav-item"><a class="nav-link active" href="anggota.php">Data Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="tampil.php">Transaksi Peminjaman</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" href="logout.php"><i class='bx bx-log-out me-1'></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" id="konten-web">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Data Anggota</h3>
                <p class="text-muted mb-0">Kelola daftar anggota yang terdaftar di sistem.</p>
            </div>
            <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota">
                <i class='bx bx-user-plus me-1'></i> Tambah Anggota Baru
            </button>
        </div>

        <div class="main-card">
            <table id="tabelAnggota" class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">ID</th>
                        <th>Informasi Anggota</th>
                        <th>No. Handphone</th>
                        <th class="text-end" width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while($row = mysqli_fetch_assoc($query)) { 
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold">#<?php echo $row['id_anggota']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class='bx bx-user icon-user'></i>
                                <div>
                                    <div class="fw-bold text-dark fs-6 mb-1"><?php echo $row['nama_anggota']; ?></div>
                                    <span class="badge-status">Anggota Aktif</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted fw-medium d-flex align-items-center">
                                <i class='bx bx-phone me-2 text-primary'></i> <?php echo $row['no_hp']; ?>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn-action btn-edit btn-edit-anggota me-1" 
                                data-id="<?php echo $row['id_anggota']; ?>" 
                                data-nama="<?php echo $row['nama_anggota']; ?>" 
                                data-hp="<?php echo $row['no_hp']; ?>" 
                                title="Edit Anggota">
                                <i class='bx bx-edit-alt fs-5'></i>
                            </button>
                            <button class="btn-action btn-delete btn-hapus-anggota" data-id="<?php echo $row['id_anggota']; ?>" title="Hapus Anggota">
                                <i class='bx bx-trash fs-5'></i>
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH ANGGOTA -->
    <div class="modal fade" id="modalTambahAnggota" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center text-primary"><i class='bx bx-user-plus fs-4 me-2'></i> Registrasi Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputAnggota">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nama Lengkap</label>
                            <input type="text" name="nama_anggota" class="form-control" required placeholder="Contoh: Budi Santoso">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nomor Handphone</label>
                            <input type="text" name="no_hp" class="form-control" required placeholder="Contoh: 08123456789">
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-add py-2 fs-6">Simpan Data Anggota</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT ANGGOTA -->
    <div class="modal fade" id="modalEditAnggota" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" style="color: #D97706;"><i class='bx bx-edit fs-4 me-2'></i> Edit Data Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditAnggota">
                        <input type="hidden" name="id_anggota" id="edit_id_anggota">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nama Lengkap</label>
                            <input type="text" name="nama_anggota" id="edit_nama_anggota" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nomor Handphone</label>
                            <input type="text" name="no_hp" id="edit_no_hp" class="form-control" required>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn py-2 fs-6" style="background-color: #D97706; color: white; border-radius: 50px; font-weight: 500;">Update Data Anggota</button>
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
            
            $('#tabelAnggota').DataTable({
                "language": { "search": "", "searchPlaceholder": "Cari nama/no.hp...", "lengthMenu": "Tampilkan _MENU_ data", "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ anggota", "paginate": { "previous": "<i class='bx bx-chevron-left'></i>", "next": "<i class='bx bx-chevron-right'></i>" } },
                "columnDefs": [ { "orderable": false, "targets": 3 } ]
            });
            $('.dataTables_filter input').addClass('form-control form-control-sm border-0 bg-light rounded-pill px-3').css('box-shadow', 'none');

            // 1. AJAX Tambah Anggota
            $("#formInputAnggota").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: 'proses_anggota.php', type: 'POST', data: $(this).serialize(),
                    success: function(response){
                        if(response.trim() == "sukses") {
                            $('#modalTambahAnggota').modal('hide');
                            Swal.fire({title: 'Berhasil!', text: 'Anggota baru didaftarkan.', icon: 'success', timer: 1500, showConfirmButton: false}).then(() => { location.reload(); });
                        } else { Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); }
                    }
                });
            });

            // 2. Tampilkan Data di Modal Edit
            $(document).on('click', '.btn-edit-anggota', function(){
                $('#edit_id_anggota').val($(this).data('id'));
                $('#edit_nama_anggota').val($(this).data('nama'));
                $('#edit_no_hp').val($(this).data('hp'));
                $('#modalEditAnggota').modal('show');
            });

            // 3. AJAX Update Anggota
            $("#formEditAnggota").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: 'proses_edit_anggota.php', type: 'POST', data: $(this).serialize(),
                    success: function(response){
                        if(response.trim() == "sukses") {
                            $('#modalEditAnggota').modal('hide');
                            Swal.fire({title: 'Berhasil!', text: 'Data anggota diperbarui.', icon: 'success', timer: 1500, showConfirmButton: false}).then(() => { location.reload(); });
                        } else { Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'); }
                    }
                });
            });

            // 4. AJAX Hapus Anggota
            $(document).on('click', '.btn-hapus-anggota', function(){
                var id_anggota = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Anggota?', text: "Data ini akan dihapus secara permanen!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#DC2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'proses_hapus_anggota.php', type: 'POST', data: { id_anggota: id_anggota },
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