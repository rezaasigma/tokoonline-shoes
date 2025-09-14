<?php 
include 'auth.php'; 
include '../koneksi.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Main Custom CSS -->
    <link rel="stylesheet" href="../Asset/css/custom.css">
    <!-- Admin-specific CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 0; /* Override user-facing padding */
        }
        .sidebar {
            width: 260px;
            background: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding-top: 1.5rem;
            box-shadow: 0 0 20px rgba(0,0,0,.05);
            z-index: 1000;
        }
        .sidebar .admin-brand {
            color: #333;
            font-weight: 600;
            font-size: 1.2rem;
            padding-bottom: 1.5rem;
        }
        .sidebar a {
            color: #555;
            text-decoration: none;
            display: block;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar a:hover {
            background: #f0f0f0;
            color: var(--primary-color);
        }
        .sidebar a.active {
            color: var(--primary-color);
            background-color: rgba(13, 110, 253, 0.08);
            border-right: 4px solid var(--primary-color);
            font-weight: 600;
        }
        .sidebar a.active i {
            color: var(--primary-color);
        }
        .sidebar a i {
            margin-right: 12px;
            color: #888;
            width: 20px;
        }
        .content {
            margin-left: 260px;
            padding: 0;
            width: calc(100% - 260px);
        }
        .top-nav {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
            padding: 1rem 2rem;
        }
        .main-content {
            padding: 2rem;
        }
        /* Override card style for admin */
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a class="navbar-brand admin-brand text-center d-block" href="index.php">
        <i class="bi bi-shield-shaded"></i> Admin Panel
    </a>
    
    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="produk.php" class="<?php echo in_array($current_page, ['produk.php', 'produk_tambah.php', 'produk_edit.php']) ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Produk</a>
    <a href="pesanan.php" class="<?php echo in_array($current_page, ['pesanan.php', 'pesanan_detail.php']) ? 'active' : ''; ?>"><i class="bi bi-receipt"></i> Pesanan</a>
    <a href="users.php" class="<?php echo ($current_page == 'users.php') ? 'active' : ''; ?>"><i class="bi bi-people"></i> Users</a>
    
    <hr class="mx-3">
    
    <a href="../index.php" target="_blank"><i class="bi bi-eye"></i> Lihat Toko</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <nav class="top-nav mb-4">
        <span class="navbar-brand">Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>!</span>
    </nav>

    <div class="main-content">