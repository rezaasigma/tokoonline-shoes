<?php 
include 'header.php';

$message = '';

// Cek jika tidak ada ID, redirect
if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit();
}

$id = $_GET['id'];

// Ambil data produk yang akan diedit
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $product = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
    include 'footer.php';
    exit();
}

// Proses update data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_post = $_POST['id'];
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar_path = $_POST['gambar_lama']; // Default ke gambar lama

    // Handle upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0 && $_FILES['gambar']['size'] > 0) {
        $target_dir = "../Asset/img/";
        $file_name = uniqid() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_path = 'Asset/img/' . $file_name;
                // Hapus gambar lama jika perlu
                if (file_exists('../' . $_POST['gambar_lama'])) {
                    unlink('../' . $_POST['gambar_lama']);
                }
            } else { $message = '<div class="alert alert-danger">Gagal upload file baru.</div>'; }
        } else { $message = '<div class="alert alert-danger">Format file tidak diizinkan.</div>'; }
    }

    if ($message == '') {
        $stmt_update = $conn->prepare("UPDATE products SET nama_produk=?, deskripsi=?, harga=?, stok=?, gambar=? WHERE id=?");
        $stmt_update->bind_param("ssdisi", $nama_produk, $deskripsi, $harga, $stok, $gambar_path, $id_post);
        
        if ($stmt_update->execute()) {
            header("Location: produk.php?status=updated");
            exit();
        } else {
            $message = '<div class="alert alert-danger">Gagal mengupdate produk: ' . $conn->error . '</div>';
        }
        $stmt_update->close();
    }
}

?>

<h1 class="mb-4">Edit Produk</h1>

<?php echo $message; ?>

<div class="card">
    <div class="card-body">
        <form action="produk_edit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="gambar_lama" value="<?php echo $product['gambar']; ?>">

            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($product['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" step="0.01" value="<?php echo $product['harga']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $product['stok']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk</label><br>
                <img src="../<?php echo htmlspecialchars($product['gambar']); ?>" width="100" class="mb-2">
                <input type="file" class="form-control" id="gambar" name="gambar">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
            </div>
            <button type="submit" class="btn btn-primary">Update Produk</button>
            <a href="produk.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
