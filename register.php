<?php
session_start();
include 'koneksi.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $message = '<div class="alert alert-danger">Email sudah terdaftar!</div>';
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO users (nama, email, password, alamat, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssss", $nama, $email, $hashed_password, $alamat, $no_hp);

        if ($stmt_insert->execute()) {
            $message = '<div class="alert alert-success">Pendaftaran berhasil! Silakan <a href="login.php">login</a>.</div>';
        } else {
            $message = '<div class="alert alert-danger">Pendaftaran gagal: ' . $conn->error . '</div>';
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Wiragunan Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="Asset/css/custom.css">
    <style>
        body {
            background-color: var(--secondary-bg);
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .register-card {
            width: 100%;
            max-width: 550px;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="card register-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <a class="navbar-brand fs-2" href="index.php">Wiragunan Store</a>
                </div>
                <h3 class="text-center mb-1">Buat Akun Baru</h3>
                <p class="text-center text-muted mb-4">Daftar untuk mulai berbelanja.</p>
                
                <?php echo $message; ?>

                <form action="register.php" method="post">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-lg" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control form-control-lg" id="alamat" name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">Nomor HP</label>
                        <input type="text" class="form-control form-control-lg" id="no_hp" name="no_hp" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dark btn-lg">Daftar</button>
                    </div>
                </form>
                <div class="text-center mt-4">
                    <p class="text-muted">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>