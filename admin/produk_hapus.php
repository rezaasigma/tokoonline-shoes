<?php 
include 'auth.php';
include '../koneksi.php';

// Cek jika tidak ada ID, redirect
if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit();
}

$id = $_GET['id'];

// 1. Ambil path gambar sebelum menghapus record DB
$stmt_select = $conn->prepare("SELECT gambar FROM products WHERE id = ?");
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$result = $stmt_select->get_result();
if ($result->num_rows == 1) {
    $product = $result->fetch_assoc();
    $gambar_path = '../' . $product['gambar'];
} else {
    // Produk tidak ditemukan, redirect saja
    header("Location: produk.php?status=notfound");
    exit();
}
$stmt_select->close();

// 2. Hapus record dari database
$stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {
    // 3. Jika record DB berhasil dihapus, hapus file gambar
    if (isset($gambar_path) && file_exists($gambar_path)) {
        unlink($gambar_path);
    }
    header("Location: produk.php?status=deleted");
    exit();
} else {
    header("Location: produk.php?status=error");
    exit();
}

$stmt_delete->close();
$conn->close();
?>
