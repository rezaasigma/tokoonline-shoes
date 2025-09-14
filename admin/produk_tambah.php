<?php 
include 'header.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar_path = '';

    // Handle file upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../Asset/img/";
        // Buat nama file unik untuk menghindari tumpang tindih
        $file_name = uniqid() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek tipe file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_path = 'Asset/img/' . $file_name; // Path untuk disimpan di DB
            } else {
                $message = '<div class="alert alert-danger">Gagal memindahkan file yang diunggah.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Hanya format JPG, JPEG, PNG, GIF, & WEBP yang diizinkan.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Terjadi kesalahan saat mengunggah gambar.</div>';
    }

    // Jika path gambar berhasil dibuat, insert ke database
    if ($gambar_path != '' && $message == '') {
        $stmt = $conn->prepare("INSERT INTO products (nama_produk, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nama_produk, $deskripsi, $harga, $stok, $gambar_path);
        
        if ($stmt->execute()) {
            header("Location: produk.php?status=added");
            exit();
        } else {
            $message = '<div class="alert alert-danger">Gagal menyimpan produk ke database: ' . $conn->error . '</div>';
        }
        $stmt->close();
    }
}

?>

<h1 class="mb-4">Tambah Produk Baru</h1>

<?php echo $message; ?>

<div class="card">
    <div class="card-body">
        <form action="produk_tambah.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" required>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Produk</label>
                <input type="file" class="form-control" id="gambar" name="gambar" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
            <a href="produk.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
