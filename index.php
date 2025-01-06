<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Input Kartu Rencana Studi (KRS)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Sistem Input Kartu Rencana Studi (KRS)</h1>
        <p class="text-center">Input data Mahasiswa di sini!</p>

        <!-- Form Input Mahasiswa -->
        <form id="inputMahasiswa" method="POST" action="process.php" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama Mahasiswa" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" class="form-control" id="ipk" name="ipk" placeholder="Masukkan IPK" required>
            </div>
            <div class="col-12 text-center mt-3">
                <button type="submit" class="btn btn-primary w-100">Input Mahasiswa</button>
            </div>
        </form>

        <!-- Tabel Mahasiswa -->
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>IPK</th>
                    <th>SKS Maksimal</th>
                    <th>Matkul yang diambil</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
    <?php
    // Modifikasi query untuk mengambil mata kuliah yang diambil
    $sql = "SELECT i.*, 
            (SELECT GROUP_CONCAT(m.matakuliah SEPARATOR ', ') 
             FROM jwl_mhs j 
             JOIN jwl_matakuliah m ON j.matakuliah_id = m.id 
             WHERE j.mhs_id = i.id) as mata_kuliah
            FROM inputmhs i";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $sks_maksimal = ($row['ipk'] >= 3) ? 24 : 20;
            // Gunakan coalesce untuk menampilkan '-' jika tidak ada mata kuliah
            $mata_kuliah = $row['mata_kuliah'] ? $row['mata_kuliah'] : '-';

            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['ipk']}</td>
                    <td>{$sks_maksimal}</td>
                    <td>{$mata_kuliah}</td>
                    <td>
                        <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm'>Hapus</a>
                        <a href='edit.php?nim={$row['nim']}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='view.php?id={$row['id']}' class='btn btn-secondary btn-sm'>Lihat</a>
                    </td>
                  </tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>Tidak ada data</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
