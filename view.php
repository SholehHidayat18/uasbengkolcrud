<?php
include 'koneksi.php';

$id = $_GET['id'];

// Get student information
$sql = "SELECT * FROM inputmhs WHERE id = $id";
$result = $conn->query($sql);
$mahasiswa = $result->fetch_assoc();

// Get enrolled courses
$sql = "SELECT jm.id, mk.matakuliah, mk.sks, mk.kelp, mk.ruangan 
        FROM jwl_mhs jm 
        JOIN jwl_matakuliah mk ON jm.matakuliah_id = mk.id 
        WHERE jm.mhs_id = $id";
$matakuliah_result = $conn->query($sql);

// Calculate total SKS
$total_sks = 0;
if ($matakuliah_result->num_rows > 0) {
    while($row = $matakuliah_result->fetch_assoc()) {
        $total_sks += $row['sks'];
    }
}
$matakuliah_result->data_seek(0); // Reset pointer to beginning

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Input KRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header-box {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .back-button {
            background-color: #ffc107;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            color: black;
            font-size: 14px;
        }
        .print-button {
            background-color: #198754;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 3px;
            text-decoration: none;
        }
        /* CSS untuk menyembunyikan elemen saat print */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-2">Kartu Rencana Studi</h2>
        <p class="text-center text-muted mb-4">Lihat jadwal matakuliah yang telah diinputkan disini!</p>

        <div class="header-box">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold">Mahasiswa:</span> <?= htmlspecialchars($mahasiswa['nama']) ?> | 
                    <span class="fw-bold">NIM:</span> <?= htmlspecialchars($mahasiswa['nim']) ?> | 
                    <span class="fw-bold">IPK:</span> <?= number_format($mahasiswa['ipk'], 2) ?>
                </div>
                <!-- Tambahkan kelas "no-print" pada tombol -->
                <a href="index.php" class="back-button no-print">Kembali ke data mahasiswa</a>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Matakuliah</th>
                    <th>SKS</th>
                    <th>Kelp</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($matakuliah_result->num_rows > 0) {
                    $no = 1;
                    while($row = $matakuliah_result->fetch_assoc()) { 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['matakuliah']) ?></td>
                        <td><?= $row['sks'] ?></td>
                        <td><?= htmlspecialchars($row['kelp']) ?></td>
                        <td><?= htmlspecialchars($row['ruangan']) ?></td>
                    </tr>
                <?php 
                    }
                } 
                ?>
                <tr>
                    <td colspan="2" class="text-end fw-bold">Total SKS</td>
                    <td colspan="3"><?= $total_sks ?></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-3">
            <!-- Tambahkan kelas "no-print" pada tombol Cetak PDF -->
            <button class="print-button no-print" onclick="window.print()">Cetak PDF</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

