<?php
session_start();

// Pastikan request adalah POST dan ada data jumlah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jumlah'])) {

    // Loop melalui setiap produk yang dikirim dari form
    foreach ($_POST['jumlah'] as $product_id => $jumlah) {
        // Pastikan jumlah adalah angka positif
        $jumlah = (int)$jumlah;
        $product_id = (int)$product_id;

        if ($jumlah > 0 && isset($_SESSION['keranjang'][$product_id])) {
            $_SESSION['keranjang'][$product_id] = $jumlah;
        } elseif ($jumlah <= 0 && isset($_SESSION['keranjang'][$product_id])) {
            // Jika jumlah 0 atau kurang, hapus item dari keranjang
            unset($_SESSION['keranjang'][$product_id]);
        }
    }
}

// Redirect kembali ke halaman keranjang
header('Location: keranjang.php');
exit();
?>