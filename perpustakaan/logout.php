<?php 
// 1. Mulai session untuk mengenali siapa yang sedang login
session_start();

// 2. Hancurkan semua data session (mengunci kembali sistem)
session_destroy(); 

// 3. Arahkan (redirect) kembali ke halaman login dengan membawa pesan
header("location:login.php?pesan=logout");
exit;
?>