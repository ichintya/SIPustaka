<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit; 
}
include 'koneksi.php';

// Mengambil data peminjaman
$query = "SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali, p.status, p.id_anggota, p.id_buku, 
                 a.nama_anggota, b.judul_buku, k.nama_kategori 
          FROM peminjaman p
          INNER JOIN anggota a ON p.id_anggota = a.id_anggota
          INNER JOIN buku b ON p.id_buku = b.id_buku
          INNER JOIN kategori k ON b.id_kategori = k.id_kategori
          ORDER BY p.id_peminjaman DESC";
$result = mysqli_query($koneksi, $query);

// Mengambil data anggota dan buku untuk Dropdown di Modal
$q_anggota = mysqli_query($koneksi, "SELECT id_anggota, nama_anggota FROM anggota ORDER BY nama_anggota ASC");
$q_buku = mysqli_query($koneksi, "SELECT id_buku, judul_buku FROM buku ORDER BY judul_buku ASC");

// Simpan ke array agar bisa dipakai di modal tambah & edit tanpa query ulang
$data_anggota = [];
while($row = mysqli_fetch_assoc($q_anggota)) { $data_anggota[] = $row; }

$data_buku = [];
while($row = mysqli_fetch_assoc($q_buku)) { $data_buku[] = $row; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Peminjaman - SIPustaka </title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #333; }
        
        /* Navbar */
        .navbar { background-color: #ffffff; border-bottom: 1px solid #eaeaea; }
        .navbar-brand { color: #4F46E5 !important; font-weight: 700; }
        .nav-link { color: #6b7280 !important; font-weight: 500; margin-left: 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #4F46E5 !important; }
        
        /* Area Konten Utama */
        .main-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); background: white; padding: 25px; }
        
        /* Desain Tabel Modern */
        table.dataTable { border-collapse: collapse !important; width: 100%; margin-top: 20px !important; }
        table.dataTable thead th { border-bottom: 2px solid #f0f0f0; background-color: #ffffff; color: #6b7280; font-weight: 600; font-size: 13px; text-transform: uppercase; padding: 15px; }
        table.dataTable tbody td { border-bottom: 1px solid #f8f9fa; padding: 18px 15px; vertical-align: middle; color: #4b5563; font-size: 14px; }
        table.dataTable tbody tr:hover { background-color: #f8fafc; }
        
        /* Badges / Lencana */
        .badge-status-dipinjam { background-color: #FEF3C7; color: #D97706; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 12px; }
        .badge-status-selesai { background-color: #D1FAE5; color: #059669; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 12px; }
        .badge-kategori { background-color: #EEF2FF; color: #4F46E5; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px; }
        
        /* Action Buttons */
        .btn-add { background-color: #4F46E5; color: white; border-radius: 50px; font-weight: 500; padding: 8px 22px; border: none; transition: 0.3s; }
        .btn-add:hover { background-color: #4338CA; color: white; }
        .btn-action { width: 35px; height: 35px; display: inline-flex; justify-content: center; align-items: center; border-radius: 10px; border: none; transition: 0.2s; }
        .btn-kembali { background-color: #10B981; color: white; border-radius: 8px; font-weight: 500; font-size: 13px; padding: 6px 15px; }
        .btn-kembali:hover { background-color: #059669; }
        .btn-edit { background-color: #FEF3C7; color: #D97706; }
        .btn-edit:hover { background-color: #FDE68A; }
        .btn-delete { background-color: #FEE2E2; color: #DC2626; }
        .btn-delete:hover { background-color: #FECACA; }
        
        /* Modal Form */
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
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class='bx bx-book-reader fs-3 me-2'></i> SIPustaka
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="buku.php">Katalog Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="anggota.php">Data Anggota</a></li>
                    <li class="nav-item"><a class="nav-link active" href="tampil.php">Transaksi Peminjaman</a></li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" href="logout.php"><i class='bx bx-log-out me-1'></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" id="konten-web">
        
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Peminjaman</h3>
                <p class="text-muted mb-0">Kelola peminjaman dan pengembalian buku.</p>
            </div>
            <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPeminjaman">
                <i class='bx bx-plus me-1'></i> Peminjaman Baru
            </button>
        </div>

        <div class="main-card">
            <table id="tabelData" class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">ID</th>
                        <th>Peminjam</th>
                        <th>Detail Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Waktu</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" width="22%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr id="baris-<?php echo $row['id_peminjaman']; ?>">
                        <td class="text-center text-muted fw-bold">#<?php echo $row['id_peminjaman']; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo $row['nama_anggota']; ?></div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1"><?php echo $row['judul_buku']; ?></div>
                            <span class="badge-kategori"><?php echo $row['nama_kategori']; ?></span>
                        </td>
                        <td><div class="text-muted"><i class='bx bx-calendar me-1'></i><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></div></td>
                        <td><div class="text-muted"><i class='bx bx-calendar-exclamation me-1'></i><?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?></div></td>
                        <td class="text-center">
                            <?php if($row['status'] == 'Dipinjam') { ?>
                                <span class="badge-status-dipinjam">Dipinjam</span>
                            <?php } else { ?>
                                <span class="badge-status-selesai">Selesai</span>
                            <?php } ?>
                        </td>
                        <td class="text-end">
                            <?php if($row['status'] == 'Dipinjam') { ?>
                                <button class="btn btn-kembali me-1 btn-sm" data-id="<?php echo $row['id_peminjaman']; ?>"><i class='bx bx-refresh me-1'></i>Kembali</button>
                            <?php } ?>
                            
                            <!-- Tombol Edit dengan Modal -->
                            <button class="btn-action btn-edit btn-edit-peminjaman me-1" 
                                data-id="<?php echo $row['id_peminjaman']; ?>"
                                data-idanggota="<?php echo $row['id_anggota']; ?>"
                                data-idbuku="<?php echo $row['id_buku']; ?>"
                                data-tglpinjam="<?php echo $row['tanggal_pinjam']; ?>"
                                data-tglkembali="<?php echo $row['tanggal_kembali']; ?>"
                                title="Edit">
                                <i class='bx bx-edit-alt fs-5'></i>
                            </button>
                            
                            <button class="btn-action btn-delete btn-hapus" data-id="<?php echo $row['id_peminjaman']; ?>" title="Hapus"><i class='bx bx-trash fs-5'></i></button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL (POP-UP) TAMBAH PEMINJAMAN -->
    <div class="modal fade" id="modalTambahPeminjaman" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center text-primary"><i class='bx bx-plus-circle fs-4 me-2'></i> Transaksi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formInputPeminjaman">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Anggota</label>
                            <select name="id_anggota" class="form-select" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php foreach($data_anggota as $anggota) { ?>
                                    <option value="<?php echo $anggota['id_anggota']; ?>"><?php echo $anggota['nama_anggota']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Buku</label>
                            <select name="id_buku" class="form-select" required>
                                <option value="">-- Pilih Buku --</option>
                                <?php foreach($data_buku as $buku) { ?>
                                    <option value="<?php echo $buku['id_buku']; ?>"><?php echo $buku['judul_buku']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Tgl Pinjam</label>
                                <input type="date" name="tanggal_pinjam" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Tgl Kembali</label>
                                <input type="date" name="tanggal_kembali" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-add py-2 fs-6">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL (POP-UP) EDIT PEMINJAMAN -->
    <div class="modal fade" id="modalEditPeminjaman" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" style="color: #D97706;"><i class='bx bx-edit fs-4 me-2'></i> Edit Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditPeminjaman">
                        <input type="hidden" name="id_peminjaman" id="edit_id_peminjaman">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Anggota</label>
                            <select name="id_anggota" id="edit_id_anggota" class="form-select" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php foreach($data_anggota as $anggota) { ?>
                                    <option value="<?php echo $anggota['id_anggota']; ?>"><?php echo $anggota['nama_anggota']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Pilih Buku</label>
                            <select name="id_buku" id="edit_id_buku" class="form-select" required>
                                <option value="">-- Pilih Buku --</option>
                                <?php foreach($data_buku as $buku) { ?>
                                    <option value="<?php echo $buku['id_buku']; ?>"><?php echo $buku['judul_buku']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Tgl Pinjam</label>
                                <input type="date" name="tanggal_pinjam" id="edit_tanggal_pinjam" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Tgl Kembali</label>
                                <input type="date" name="tanggal_kembali" id="edit_tanggal_kembali" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn py-2 fs-6" style="background-color: #D97706; color: white; border-radius: 50px; font-weight: 500;">Update Transaksi</button>
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
            
            // Konfigurasi DataTable
            $('#tabelData').DataTable({
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari data peminjaman...",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": { "previous": "<i class='bx bx-chevron-left'></i>", "next": "<i class='bx bx-chevron-right'></i>" }
                },
                "columnDefs": [ { "orderable": false, "targets": [5, 6] } ] // Nonaktifkan sorting pada Status dan Aksi
            });
            $('.dataTables_filter input').addClass('form-control form-control-sm border-0 bg-light rounded-pill px-3').css('box-shadow', 'none');

					  
			$("#formInputPeminjaman").on('submit', function(e){
				e.preventDefault();
				$.ajax({
					url: 'input.php', 
					type: 'POST', 
					data: $(this).serialize(),
					success: function(response){
						if(response.trim() == "sukses") {
							$('#modalTambahPeminjaman').modal('hide');
							Swal.fire({
								title: 'Berhasil!', 
								text: 'Peminjaman berhasil dicatat.', 
								icon: 'success', 
								timer: 1500, 
								showConfirmButton: false
							}).then(() => { location.reload(); });
						} else { 
							Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'); 
						}
					}
				});
			});

            // 2. Isi Data ke Modal Edit Peminjaman
            $(document).on('click', '.btn-edit-peminjaman', function(){
                $('#edit_id_peminjaman').val($(this).data('id'));
                $('#edit_id_anggota').val($(this).data('idanggota'));
                $('#edit_id_buku').val($(this).data('idbuku'));
                $('#edit_tanggal_pinjam').val($(this).data('tglpinjam'));
                $('#edit_tanggal_kembali').val($(this).data('tglkembali'));
                $('#modalEditPeminjaman').modal('show');
            });

            // 3. AJAX Update Transaksi Peminjaman
            $("#formEditPeminjaman").on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: 'proses_edit_peminjaman.php', 
                    type: 'POST', 
                    data: $(this).serialize(),
                    success: function(response){
                        if(response.trim() == "sukses") {
                            $('#modalEditPeminjaman').modal('hide');
                            Swal.fire({title: 'Diperbarui!', text: 'Data transaksi berhasil diubah.', icon: 'success', timer: 1500, showConfirmButton: false}).then(() => { location.reload(); });
                        } else { 
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengubah data.', 'error'); 
                        }
                    }
                });
            });

            // 4. AJAX Tombol Kembali
            $(document).on('click', '.btn-kembali', function(){
                var id_peminjaman = $(this).data('id');
                Swal.fire({
                    title: 'Kembalikan Buku?', text: "Sistem akan otomatis menghitung denda.", icon: 'question',
                    showCancelButton: true, confirmButtonColor: '#4F46E5', cancelButtonColor: '#d33', confirmButtonText: 'Ya, Kembalikan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'proses_kembali.php', type: 'POST', data: { id: id_peminjaman }, dataType: 'json',
                            success: function(response) {
                                if(response.status == 'sukses') {
                                    Swal.fire({ title: (response.denda > 0) ? 'Kena Denda!' : 'Berhasil!', text: response.pesan, icon: (response.denda > 0) ? 'warning' : 'success' }).then(() => { location.reload(); });
                                } else { Swal.fire('Error!', response.pesan, 'error'); }
                            }
                        });
                    }
                })
            });

            // 5. AJAX Tombol Hapus
            $(document).on('click', '.btn-hapus', function(){
                var id_data = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Transaksi?', text: "Data tidak bisa dikembalikan!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#DC2626', cancelButtonColor: '#6b7280', confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'proses_hapus.php', type: 'POST', data: { id: id_data },
                            success: function(response){
                                if(response.trim() == "sukses") {
                                    Swal.fire({title:'Terhapus!', text:'Data berhasil dihapus.', icon:'success', timer:1500, showConfirmButton:false});
                                    $('#baris-' + id_data).fadeOut(800, function(){ $('#tabelData').DataTable().row($(this)).remove().draw(); });
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