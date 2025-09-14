<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?pesan=login_dulu');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY tanggal_pesanan DESC");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders = $stmt_orders->get_result();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Wiragunan Store</title>
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
                                    <li><a class="dropdown-item active" href="riwayat.php">Riwayat Pesanan</a></li>
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
            <h2 class="mb-4">Riwayat Pesanan Anda</h2>

            <?php if ($orders->num_rows > 0): ?>
                <div class="accordion" id="accordionRiwayat">
                    <?php while($order = $orders->fetch_assoc()): ?>
                        <div class="accordion-item mb-3 border rounded-3">
                            <h2 class="accordion-header" id="heading-<?php echo $order['id']; ?>">
                                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $order['id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $order['id']; ?>">
                                    <div class="d-flex flex-wrap justify-content-between w-100 pe-3">
                                        <span class="me-3 mb-1"><strong>ID Pesanan:</strong> #<?php echo $order['id']; ?></span>
                                        <span class="me-3 mb-1"><strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($order['tanggal_pesanan'])); ?></span>
                                        <span class="me-3 mb-1"><strong>Total:</strong> Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></span>
                                        <span><strong>Status:</strong> <span class="badge bg-dark"><?php echo htmlspecialchars($order['status']); ?></span></span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-<?php echo $order['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $order['id']; ?>" data-bs-parent="#accordionRiwayat">
                                <div class="accordion-body">
                                    <h6 class="mb-3">Detail Item:</h6>
                                    <?php
                                    $stmt_details = $conn->prepare("SELECT p.nama_produk, p.gambar, od.jumlah, od.harga_satuan FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = ?");
                                    $stmt_details->bind_param("i", $order['id']);
                                    $stmt_details->execute();
                                    $details = $stmt_details->get_result();
                                    ?>
                                    <ul class="list-group list-group-flush">
                                        <?php while($item = $details->fetch_assoc()): ?>
                                            <li class="list-group-item d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['gambar']); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="me-3 rounded">
                                                <div class="flex-grow-1">
                                                    <?php echo htmlspecialchars($item['nama_produk']); ?><br>
                                                    <small class="text-muted">Jumlah: <?php echo $item['jumlah']; ?></small>
                                                </div>
                                                <span>Rp <?php echo number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.'); ?></span>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                    <?php $stmt_details->close(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-receipt" style="font-size: 4rem; color: var(--text-secondary);"></i>
                    <h3 class="mt-3">Anda Belum Punya Riwayat Pesanan</h3>
                    <p class="text-muted">Semua pesanan Anda akan muncul di sini.</p>
                    <a href="index.php" class="btn btn-dark mt-2">Mulai Belanja</a>
                </div>
            <?php endif; ?>
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
<?php
$stmt_orders->close();
$conn->close();
?>
