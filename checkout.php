<?php
session_start();
include 'koneksi.php';

// 1. Keamanan: User harus login dan keranjang tidak boleh kosong
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?pesan=login_dulu');
    exit();
}
if (empty($_SESSION['keranjang'])) {
    header('Location: index.php');
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Ambil data produk dari keranjang
$keranjang = $_SESSION['keranjang'];
$products = [];
$total_belanja = 0;
$product_ids = array_keys($keranjang);
$ids_string = implode(',', $product_ids);
$sql_produk = "SELECT * FROM products WHERE id IN ($ids_string)";
$result_produk = $conn->query($sql_produk);
while($row = $result_produk->fetch_assoc()) {
    $products[$row['id']] = $row;
}

// 5. Logika Proses Pesanan (jika form disubmit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        // Hitung total belanja lagi untuk keamanan
        $final_total = 0;
        foreach ($products as $product) {
            $jumlah = $keranjang[$product['id']];
            $final_total += $product['harga'] * $jumlah;
        }

        // Insert ke tabel orders
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'pending')");
        $stmt_order->bind_param("id", $user_id, $final_total);
        $stmt_order->execute();
        $order_id = $conn->insert_id;

        // Insert ke tabel order_details dan update stok
        $stmt_details = $conn->prepare("INSERT INTO order_details (order_id, product_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");

        foreach ($products as $product) {
            $jumlah = $keranjang[$product['id']];
            $harga_satuan = $product['harga'];
            $product_id = $product['id'];
            
            $stmt_details->bind_param("iiid", $order_id, $product_id, $jumlah, $harga_satuan);
            $stmt_details->execute();

            $stmt_stock->bind_param("ii", $jumlah, $product_id);
            $stmt_stock->execute();
        }

        // Jika semua berhasil, commit transaksi
        $conn->commit();

        // Kosongkan keranjang dan redirect ke halaman sukses
        unset($_SESSION['keranjang']);
        header('Location: pesanan_sukses.php?order_id=' . $order_id);
        exit();

    } catch (Exception $e) {
        // Jika ada error, rollback transaksi
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        // Sebaiknya ada halaman error yang lebih baik
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Wiragunan Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Asset/css/custom.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="bi bi-shop"></i> Wiragunan Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="keranjang.php"><i class="bi bi-cart"></i> Keranjang</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="riwayat.php"><i class="bi bi-receipt"></i> Riwayat Pesanan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i class="bi bi-person-plus"></i> Daftar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Checkout</h2>
        <form action="checkout.php" method="post">
            <div class="row">
                <!-- 2. Ringkasan Pesanan -->
                <div class="col-md-6">
                    <h4>Ringkasan Pesanan</h4>
                    <table class="table">
                        <?php foreach ($products as $product): 
                            $jumlah = $keranjang[$product['id']];
                            $subtotal = $product['harga'] * $jumlah;
                            $total_belanja += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $product['nama_produk']; ?> (x<?php echo $jumlah; ?>)</td>
                            <td class="text-end">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- 3. Informasi Pengiriman -->
                <div class="col-md-6">
                    <h4>Alamat Pengiriman</h4>
                    <div class="card p-3">
                        <h5 class="card-title"><?php echo htmlspecialchars($user['nama']); ?></h5>
                        <p class="card-text">
                            <?php echo htmlspecialchars($user['alamat']); ?><br>
                            Email: <?php echo htmlspecialchars($user['email']); ?><br>
                            HP: <?php echo htmlspecialchars($user['no_hp']); ?>
                        </p>
                        <a href="#" class="btn btn-sm btn-outline-primary">Ubah Alamat (Fitur belum tersedia)</a>
                    </div>

                    <!-- 4. Metode Pembayaran -->
                    <h4 class="mt-4">Metode Pembayaran</h4>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer" checked>
                        <label class="form-check-label" for="transfer">Transfer Bank (Dummy)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                        <label class="form-check-label" for="cod">Cash on Delivery (Dummy)</label>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Konfirmasi -->
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-lg btn-success">Konfirmasi & Buat Pesanan</button>
            </div>
        </form>
    </div>

    <footer class="bg-light text-muted text-center p-3 mt-5">
        <p class="mb-0">&copy; 2025 Wiragunan Store - Modern Design</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
