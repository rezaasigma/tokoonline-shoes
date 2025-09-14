<?php
session_start();

// Pastikan ada product id yang dikirim dan keranjang ada
if (isset($_GET['id']) && isset($_SESSION['keranjang'])) {
    $product_id = $_GET['id'];

    // Hapus item dari array keranjang
    if (isset($_SESSION['keranjang'][$product_id])) {
        unset($_SESSION['keranjang'][$product_id]);
    }
}

// Redirect kembali ke halaman keranjang
header('Location: keranjang.php');
exit();
?>