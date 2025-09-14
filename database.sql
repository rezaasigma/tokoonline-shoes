-- Membuat Database `tokoonline` jika belum ada
CREATE DATABASE IF NOT EXISTS tokoonline;
USE tokoonline;

-- Hapus tabel jika sudah ada untuk menghindari error
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `kategori`;

-- Tabel Kategori
CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mengisi Tabel Kategori
INSERT INTO `kategori` (`nama_kategori`) VALUES
('Casual'),
('Formal'),
('Olahraga'),
('Sneakers');

-- Tabel Pengguna (users)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text,
  `no_hp` varchar(20),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Produk (products)
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mengisi Tabel Produk dengan data awal
INSERT INTO `products` (`nama_produk`, `deskripsi`, `harga`, `stok`, `gambar`, `kategori_id`) VALUES
('Gray Shoe', 'Sepatu kasual berwarna abu-abu yang nyaman untuk kegiatan sehari-hari.', 450000.00, 20, 'Asset/img/gray_shoe.jpg', 1),
('Green Shoe', 'Sepatu lari dengan warna hijau cerah, memberikan performa maksimal.', 550000.00, 15, 'Asset/img/green_shoe.jpg', 3),
('Maroon Shoe', 'Sepatu elegan berwarna merah marun, cocok untuk acara formal.', 750000.00, 10, 'Asset/img/maroon_shoe.jpg', 2),
('Red Shoe', 'Sepatu sneakers merah yang stylish dan menarik perhatian.', 620000.00, 18, 'Asset/img/red_shoe.jpg', 4),
('White Shoe', 'Sepatu putih klasik yang wajib dimiliki, mudah dipadukan dengan gaya apapun.', 500000.00, 25, 'Asset/img/white_shoe.jpg', 1);

-- Tabel Pesanan (orders)
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tanggal_pesanan` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Detail Pesanan (order_details)
CREATE TABLE `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;