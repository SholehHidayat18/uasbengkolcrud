<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $ipk = $_POST['ipk'];

    $mata_kuliah = ''; // Default mata kuliah kosong

    $sql = "INSERT INTO inputmhs (nim, nama, ipk) VALUES ('$nim', '$nama', '$ipk')";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.php'); // Redirect ke halaman utama
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>