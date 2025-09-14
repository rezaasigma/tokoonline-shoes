<?php
session_start();
include 'koneksi.php';

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
$products = [];
$total_belanja = 0;

if (!empty($keranjang)) {
    $product_ids = array_keys($keranjang);
    $ids_string = implode(',', $product_ids);
    
    $sql = "SELECT * FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Wiragunan Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="Asset/css/custom.css">
</head>
<body>

    <!-- Header -->
    <header class="header-section">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">Wiragunan Store</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="mx-auto">
                        <form class="d-flex search-form" action="index.php" method="GET">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input class="form-control" type="search" name="search" placeholder="Cari sepatu..." aria-label="Search">
                            </div>
                        </form>
                    </div>
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="keranjang.php"><i class="bi bi-cart fs-5"></i></a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])) { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle fs-5 me-2"></i>
                                    <span class="d-none d-lg-inline"><?php echo htmlspecialchars($_SESSION['user_nama']); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right fs-5"></i> <span class="d-none d-lg-inline">Login</span></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="mb-4">Keranjang Belanja</h2>
                    <?php if (!empty($products)): ?>
                        <form action="update_keranjang.php" method="post">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <table class="table align-middle">
                                        <tbody>
                                            <?php foreach ($products as $product): 
                                                $jumlah = $keranjang[$product['id']];
                                                $subtotal = $product['harga'] * $jumlah;
                                                $total_belanja += $subtotal;
                                            ?>
                                            <tr>
                                                <td style="width: 120px;">
                                                    <img src="<?php echo htmlspecialchars($product['gambar']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                                                </td>
                                                <td>
                                                    <h5 class="mb-0"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                                                    <small class="text-muted">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></small>
                                                </td>
                                                <td style="width: 150px;">
                                                    <div class="input-group">
                                                        <input type="number" name="jumlah[<?php echo $product['id']; ?>]" class="form-control text-center" value="<?php echo $jumlah; ?>" min="1">
                                                    </div>
                                                </td>
                                                <td class="text-end" style="width: 150px;">
                                                    <strong>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></strong>
                                                </td>
                                                <td class="text-center" style="width: 50px;">
                                                    <a href="hapus_keranjang.php?id=<?php echo $product['id']; ?>" class="text-danger" title="Hapus"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-3 d-flex justify-content-between">
                                <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Lanjutkan Belanja</a>
                                <button type="submit" class="btn btn-dark">Perbarui Keranjang</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x" style="font-size: 4rem; color: var(--text-secondary);"></i>
                            <h3 class="mt-3">Keranjang Anda Kosong</h3>
                            <p class="text-muted">Sepertinya Anda belum menambahkan produk apapun.</p>
                            <a href="index.php" class="btn btn-dark mt-2">Mulai Belanja</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Summary -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Ringkasan Belanja</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Biaya Pengiriman</span>
                                <span>Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span>Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                            </div>
                            <div class="d-grid mt-4">
                                <a href="checkout.php" class="btn btn-dark btn-lg <?php echo empty($products) ? 'disabled' : ''; ?>">Lanjutkan ke Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-section mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="footer-title">Wiragunan Store</h5>
                    <p>Toko sepatu online terpercaya dengan koleksi terlengkap dan kualitas terbaik.</p>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <h5 class="footer-subtitle">Kategori</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Casual</a></li>
                        <li><a href="#">Formal</a></li>
                        <li><a href="#">Olahraga</a></li>
                        <li><a href="#">Sneakers</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <h5 class="footer-subtitle">Customer Service</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#">Bantuan</a></li>
                        <li><a href="#">Kebijakan Return</a></li>
                        <li><a href="#">Panduan Ukuran</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-subtitle">Kontak</h5>
                    <ul class="list-unstyled footer-links">
                        <li>Email: info@wiragunan.com</li>
                        <li>Telepon: (021) 123-4567</li>
                        <li>WhatsApp: +62 812-3456-7890</li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date("Y"); ?> Wiragunan Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>