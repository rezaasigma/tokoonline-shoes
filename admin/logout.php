<?php
session_start();

// Hapus semua variabel session admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nama']);

// Hancurkan session jika tidak ada session user lain
// Ini untuk memastikan logout admin tidak menghancurkan session belanja user jika dibuka di browser yang sama
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect ke halaman login admin
header("Location: login.php");
exit;
?>