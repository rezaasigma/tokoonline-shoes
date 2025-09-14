<?php 
include 'header.php';

// Ambil semua data pesanan, join dengan tabel user untuk mendapatkan nama
$orders = $conn->query("SELECT o.*, u.nama as user_nama FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.tanggal_pesanan DESC");

// Fungsi untuk mendapatkan warna badge bootstrap berdasarkan status
function get_status_badge($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'paid':
        case 'processing':
            return 'bg-info text-dark';
        case 'shipped':
            return 'bg-success';
        case 'canceled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

?>

<h1 class="mb-4">Manajemen Pesanan</h1>

<div class="card">
    <div class="card-header">
        <h5>Daftar Semua Pesanan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Nama User</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_nama']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                                <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge <?php echo get_status_badge($order['status']); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="pesanan_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
