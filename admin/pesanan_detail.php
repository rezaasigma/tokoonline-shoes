<?php 
include 'header.php';

// Cek ID
if (!isset($_GET['id'])) {
    header("Location: pesanan.php");
    exit();
}
$order_id = $_GET['id'];

// Proses update status jika ada POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt_update = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt_update->bind_param("si", $new_status, $order_id);
    $stmt_update->execute();
    $stmt_update->close();
    // Redirect untuk refresh halaman dan menunjukkan status baru
    header("Location: pesanan_detail.php?id=$order_id&status_updated=1");
    exit();
}

// Ambil data pesanan dan user
$stmt_order = $conn->prepare("SELECT o.*, u.nama, u.email, u.alamat, u.no_hp FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) {
    echo "<div class='alert alert-danger'>Pesanan tidak ditemukan.</div>";
    include 'footer.php';
    exit();
}

// Ambil item pesanan
$stmt_items = $conn->prepare("SELECT p.nama_produk, od.jumlah, od.harga_satuan FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

$possible_statuses = ['pending', 'processing', 'paid', 'shipped', 'canceled'];

?>

<h1 class="mb-4">Detail Pesanan #<?php echo $order['id']; ?></h1>

<?php if(isset($_GET['status_updated'])): ?>
<div class="alert alert-success">Status pesanan berhasil diperbarui.</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">Item Pesanan</div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <?php while($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                            <td><?php echo $item['jumlah']; ?> x Rp <?php echo number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
                            <td class="text-end">Rp <?php echo number_format($item['jumlah'] * $item['harga_satuan'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <tr class="fw-bold">
                            <td colspan="2">TOTAL</td>
                            <td class="text-end">Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Detail Pelanggan</div>
            <div class="card-body">
                <strong><?php echo htmlspecialchars($order['nama']); ?></strong><br>
                <?php echo htmlspecialchars($order['email']); ?><br>
                <?php echo htmlspecialchars($order['no_hp']); ?><br>
                <hr>
                <p><?php echo nl2br(htmlspecialchars($order['alamat'])); ?></p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Status Pesanan</div>
            <div class="card-body">
                <form action="pesanan_detail.php?id=<?php echo $order_id; ?>" method="post">
                    <div class="input-group">
                        <select name="status" class="form-select">
                            <?php foreach($possible_statuses as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo ($order['status'] == $status) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($status); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<a href="pesanan.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan</a>

<?php include 'footer.php'; ?>
