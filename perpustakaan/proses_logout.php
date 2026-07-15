<?php 
session_start();
session_destroy(); // Menghancurkan session agar tidak bisa masuk lagi tanpa login
header("location:login.php?pesan=logout");
?>