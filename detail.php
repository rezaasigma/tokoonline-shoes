<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT p.*, k.nama_kategori FROM products p JOIN kategori k ON p.kategori_id = k.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $product = $result->fetch_assoc();
} else {
    $product = null;
}

// Ambil produk terkait (selain yang sedang dilihat)
$related_products = [];
if ($product) {
    $related_stmt = $conn->prepare("SELECT p.*, k.nama_kategori FROM products p JOIN kategori k ON p.kategori_id = k.id WHERE p.kategori_id = ? AND p.id != ? LIMIT 4");
    $related_stmt->bind_param("ii", $product['kategori_id'], $product_id);
    $related_stmt->execute();
    $related_result = $related_stmt->get_result();
    while ($row = $related_result->fetch_assoc()) {
        $related_products[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['nama_produk']) : 'Produk Tidak Ditemukan'; ?> - Wiragunan Store</title>
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
                            <a class="nav-link" href="keranjang.php"><i class="bi bi-cart fs-5"></i></a>
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

    <main class="product-detail-page">
        <div class="container my-5">
            <?php if ($product): ?>
                <div class="row g-5">
                    <!-- Product Image Gallery -->
                    <div class="col-lg-7">
                        <img src="<?php echo htmlspecialchars($product['gambar']); ?>" class="img-fluid main-product-image" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                    </div>

                    <!-- Product Details -->
                    <div class="col-lg-5">
                        <div class="product-details-content">
                            <h1 class="product-title-detail"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                            <div class="mb-3">
                                <span class="badge bg-light text-dark fs-6"><?php echo htmlspecialchars($product['nama_kategori']); ?></span>
                            </div>
                            <p class="product-price-detail">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                            
                            <div class="product-description mb-4">
                                <h5 class="mb-2">Deskripsi</h5>
                                <p><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
                            </div>

                            <form action="tambah_keranjang.php?id=<?php echo $product['id']; ?>" method="POST">
                                <div class="row g-3 align-items-end">
                                    <div class="col-6">
                                        <label for="quantity" class="form-label">Jumlah</label>
                                        <input type="number" id="quantity" name="quantity" class="form-control text-center" value="1" min="1" max="<?php echo $product['stok']; ?>">
                                    </div>
                                    <div class="col-6">
                                        <span class="stock-label">Stok: <?php echo $product['stok']; ?></span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <?php if ($product['stok'] > 0): ?>
                                        <button type="submit" class="btn btn-dark btn-lg product-cart-btn">Tambah ke Keranjang</button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-lg" disabled>Stok Habis</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Related Products -->
                <?php if (!empty($related_products)): ?>
                <div class="related-products mt-5 pt-5">
                    <h2 class="text-center mb-4">Anda Mungkin Juga Suka</h2>
                    <div class="row g-4">
                        <?php foreach ($related_products as $related): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card product-card h-100">
                                    <div class="product-image-container">
                                        <img src="<?php echo htmlspecialchars($related['gambar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['nama_produk']); ?>">
                                        <div class="product-category-tag"><?php echo htmlspecialchars($related['nama_kategori']); ?></div>
                                        <button class="btn-wishlist"><i class="bi bi-heart"></i></button>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title flex-grow-1"><?php echo htmlspecialchars($related['nama_produk']); ?></h5>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="card-price mb-0">Rp <?php echo number_format($related['harga'], 0, ',', '.'); ?></p>
                                            <span class="stock-label">Stok: <?php echo $related['stok']; ?></span>
                                        </div>
                                        <a href="detail.php?id=<?php echo $related['id']; ?>" class="btn btn-dark w-100 stretched-link product-cart-btn mt-2">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-danger text-center">
                    <h4>Produk tidak ditemukan!</h4>
                    <p>Produk yang Anda cari tidak ada atau telah dihapus.</p>
                    <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-section">
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