<?php
// File koneksi.php - Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'db_uas');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>