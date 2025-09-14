<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "tokoonline";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}
?>