<?php
session_start();
include 'koneksi.php';

// Ambil data kategori
$kategori_result = $conn->query("SELECT * FROM kategori");
$kategori_list = [];
while ($row = $kategori_result->fetch_assoc()) {
    $kategori_list[] = $row;
}

// Filter produk berdasarkan kategori
$selected_kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$sql = "SELECT p.*, k.nama_kategori FROM products p JOIN kategori k ON p.kategori_id = k.id";
if ($selected_kategori_id > 0) {
    $sql .= " WHERE p.kategori_id = ?";
}
$sql .= " ORDER BY p.id ASC";

$stmt = $conn->prepare($sql);
if ($selected_kategori_id > 0) {
    $stmt->bind_param("i", $selected_kategori_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiragunan Store - Koleksi Sepatu Premium</title>
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
                                <input class="form-control" type="search" name="search" placeholder="Cari sepatu..." aria-label="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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

    <main>
        <!-- Hero Section -->
        <section class="hero-main-section text-center">
            <div class="container">
                <h1 class="hero-title">Wiragunan Store</h1>
                <p class="hero-subtitle">Koleksi Sepatu Terlengkap dengan Kualitas Premium</p>
                <a href="#product-section" class="btn btn-light btn-lg hero-button">Jelajahi Koleksi</a>
            </div>
        </section>

        <!-- Category & Product Section -->
        <section class="product-section" id="product-section">
            <div class="container">
                <!-- Category Filters -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div class="filters">
                        <a href="index.php" class="btn filter-btn <?php echo ($selected_kategori_id == 0) ? 'active' : ''; ?>">Semua</a>
                        <?php foreach ($kategori_list as $kategori) : ?>
                            <a href="index.php?kategori=<?php echo $kategori['id']; ?>#product-section" class="btn filter-btn <?php echo ($selected_kategori_id == $kategori['id']) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="product-count text-muted">
                        Menampilkan <?php echo $result->num_rows; ?> produk
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="row g-4">
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card product-card h-100">
                                    <div class="product-image-container">
                                        <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                        <div class="product-category-tag"><?php echo htmlspecialchars($row['nama_kategori']); ?></div>
                                        <button class="btn-wishlist"><i class="bi bi-heart"></i></button>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title flex-grow-1"><?php echo htmlspecialchars($row['nama_produk']); ?></h5>
                                        <p class="card-text text-muted small"><?php echo substr(htmlspecialchars($row['deskripsi']), 0, 50) . '...'; ?></p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <p class="card-price mb-0">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                            <span class="stock-label">Stok: <?php echo $row['stok']; ?></span>
                                        </div>
                                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-dark w-100 stretched-link product-cart-btn">Tambah ke Keranjang</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="col-12">
                            <p class="text-center text-muted">Tidak ada produk yang ditemukan untuk kategori ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
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
                        <li>Email: thorizamaali@gmail.com</li>
                        <li>Telepon: 089692877792</li>
                        <li>WhatsApp: 089692877792</li>
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