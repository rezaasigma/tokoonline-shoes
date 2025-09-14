<?php 
include 'header.php';

// Ambil semua data produk
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");

?>

<h1 class="mb-4">Manajemen Produk</h1>

<a href="produk_tambah.php" class="btn btn-primary mb-3"><i class="bi bi-plus-circle"></i> Tambah Produk Baru</a>

<div class="card">
    <div class="card-header">
        <h5>Daftar Produk</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><img src="../<?php echo htmlspecialchars($product['gambar']); ?>" alt="" width="50" height="50" style="object-fit: cover;"></td>
                                <td><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                                <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $product['stok']; ?></td>
                                <td>
                                    <a href="produk_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                    <a href="produk_hapus.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus produk ini?');"><i class="bi bi-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada produk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
