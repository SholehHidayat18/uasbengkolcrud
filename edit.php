<?php
// edit.php
include 'koneksi.php';

// Ambil NIM dari URL parameter
if (!isset($_GET['nim'])) {
    die("NIM tidak ditemukan!");
}

$nim = $_GET['nim'];

// Ambil data mahasiswa berdasarkan NIM
$query = $conn->prepare("SELECT * FROM inputmhs WHERE nim = ?");
$query->bind_param("s", $nim);
$query->execute();
$mahasiswa = $query->get_result()->fetch_assoc();

if (!$mahasiswa) {
    die("Mahasiswa dengan NIM $nim tidak ditemukan!");
}

// Fetch mata kuliah available for selection
$matkul_list = $conn->query("SELECT * FROM jwl_matakuliah");

// Fetch mata kuliah already taken by the mahasiswa
$query_taken = $conn->prepare("SELECT jm.id, mk.matakuliah, mk.sks, mk.kelp, mk.ruangan 
                               FROM jwl_mhs jm
                               JOIN jwl_matakuliah mk ON jm.matakuliah_id = mk.id
                               WHERE jm.mhs_id = ?");
$query_taken->bind_param("i", $mahasiswa['id']);
$query_taken->execute();
$matkul_taken = $query_taken->get_result();

// Handle form submission for adding mata kuliah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_matkul'])) {
    $matakuliah_id = $_POST['matakuliah_id'];

    // Pastikan mata kuliah belum diambil
    $query_check = $conn->prepare("SELECT * FROM jwl_mhs WHERE mhs_id = ? AND matakuliah_id = ?");
    $query_check->bind_param("ii", $mahasiswa['id'], $matakuliah_id);
    $query_check->execute();
    if ($query_check->get_result()->num_rows > 0) {
        die("Mata kuliah sudah diambil sebelumnya!");
    }

    // Tambahkan mata kuliah ke tabel relasi
    $query_insert = $conn->prepare("INSERT INTO jwl_mhs (mhs_id, matakuliah_id) VALUES (?, ?)");
    $query_insert->bind_param("ii", $mahasiswa['id'], $matakuliah_id);
    $query_insert->execute();
    header("Location: edit.php?nim=$nim"); // Refresh halaman
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query_delete = $conn->prepare("DELETE FROM jwl_mhs WHERE id = ?");
    $query_delete->bind_param("i", $id);
    $query_delete->execute();
    header("Location: edit.php?nim=$nim"); // Refresh halaman
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit KRS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        h1 {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }

        .alert {
            background-color: #e9f7fe;
            border-color: #b6e2f3;
            color: #31708f;
            padding: 15px;
            border-radius: 5px;
        }

        .btn {
            margin: 5px 0;
        }

        .form-select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        table {
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9f7fe;
        }

        td {
            text-align: center;
        }

        .container {
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header-box {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
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


        footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Kartu Rencana Studi (KRS)</h1>

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


        <form method="POST" class="mb-3">
            <div class="mb-3">
                <label for="matakuliah" class="form-label">Pilih Mata Kuliah:</label>
                <select name="matakuliah_id" id="matakuliah" class="form-select" required>
                    <option value="" disabled selected>Pilih Mata Kuliah</option>
                    <?php while ($row = $matkul_list->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['matakuliah']) ?> (<?= $row['sks'] ?> SKS) - Kelas <?= $row['kelp'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="add_matkul" class="btn btn-primary w-100">Simpan</button>
        </form>

        <h2>Matkul yang Diambil</h2>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Kelas</th>
                    <th>Ruangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($matkul_taken->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $matkul_taken->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['matakuliah']) ?></td>
                            <td><?= htmlspecialchars($row['sks']) ?></td>
                            <td><?= htmlspecialchars($row['kelp']) ?></td>
                            <td><?= htmlspecialchars($row['ruangan']) ?></td>
                            <td>
                                <a href="edit.php?nim=<?= $nim ?>&delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada mata kuliah yang diambil.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>&copy; 2024 Sistem Informasi KRS</footer>
</body>

</html>