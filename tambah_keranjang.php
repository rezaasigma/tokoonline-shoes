<?php
session_start();

// User harus login untuk menambahkan ke keranjang
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?pesan=login_dulu');
    exit();
}

// Pastikan ada product id yang dikirim
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Cek apakah produk sudah ada di keranjang
    if (isset($_SESSION['keranjang'][$product_id])) {
        // Jika sudah ada, tambah jumlahnya
        $_SESSION['keranjang'][$product_id]++;
    } else {
        // Jika belum ada, tambahkan ke keranjang dengan jumlah 1
        $_SESSION['keranjang'][$product_id] = 1;
    }

    // Redirect kembali ke halaman utama dengan notifikasi
    header('Location: index.php?status=added');
    exit();

} else {
    // Jika tidak ada id, redirect ke halaman utama
    header('Location: index.php');
    exit();
}
?>