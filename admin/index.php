<?php 
include 'header.php';

// Query untuk statistik
$total_produk = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$total_pendapatan_query = $conn->query("SELECT SUM(total) as total_revenue FROM orders WHERE status != 'pending'");
$total_pendapatan = $total_pendapatan_query->num_rows > 0 ? $total_pendapatan_query->fetch_assoc()['total_revenue'] : 0;
if(is_null($total_pendapatan)) $total_pendapatan = 0;

// Query untuk pesanan terbaru
$recent_orders = $conn->query("SELECT o.*, u.nama FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.tanggal_pesanan DESC LIMIT 5");

?>

<h1 class="mb-4">Dashboard</h1>

<!-- Stat Cards -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-box-seam"></i> Total Produk</h5>
                <p class="card-text fs-4"><?php echo $total_produk; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people"></i> Total User</h5>
                <p class="card-text fs-4"><?php echo $total_users; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-receipt"></i> Total Pesanan</h5>
                <p class="card-text fs-4"><?php echo $total_pesanan; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-dark">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cash-stack"></i> Total Pendapatan</h5>
                <p class="card-text fs-4">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-clock-history"></i> 5 Pesanan Terbaru</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama User</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_orders->num_rows > 0): ?>
                    <?php while($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['nama']); ?></td>
                            <td><?php echo date('d M Y', strtotime($order['tanggal_pesanan'])); ?></td>
                            <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($order['status']); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada pesanan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
