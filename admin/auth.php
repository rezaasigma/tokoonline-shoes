<?php
session_start();

// Cek apakah session admin ada
if (!isset($_SESSION['admin_id'])) {
    // Jika tidak ada, redirect ke halaman login
    header('Location: login.php');
    exit();
}
?>