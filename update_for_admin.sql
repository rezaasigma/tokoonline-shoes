-- Perintah ini untuk menambahkan kolom is_admin ke tabel users.
-- Kolom ini akan digunakan untuk membedakan antara user biasa dan admin.
-- Default value 0 berarti user biasa.
ALTER TABLE `users` ADD `is_admin` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=user, 1=admin';

-- Perintah ini untuk menjadikan salah satu user sebagai admin.
-- Ganti '1' dengan id user yang ingin Anda jadikan admin.
-- Pastikan user dengan ID ini sudah ada di tabel users Anda setelah melakukan registrasi.
UPDATE `users` SET `is_admin` = 1 WHERE `id` = 1;
