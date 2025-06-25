<?php
session_start();
require_once('../includes/config.php');

// Cek login dan kategori
if (!isset($_SESSION['siswa'])) {
    header('Location: /soal/siswa/index.php');
    exit();
}

if (!isset($_SESSION['kategori'])) {
    header('Location: /soal/siswa/pilih_kategori.php');
    exit();
}

// Ambil data soal sesuai kategori
$kategori = mysqli_real_escape_string($conn, $_SESSION['kategori']);
$query = "SELECT * FROM soal WHERE kategori = ? ORDER BY RAND()";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $kategori);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die('Error query: ' . mysqli_error($conn));
}

// Debug jumlah soal
$num_rows = mysqli_num_rows($result);
echo "Jumlah soal: " . $num_rows . "<br>";

$soal_array = [];
while ($row = mysqli_fetch_assoc($result)) {
    $soal_array[] = $row;
}

// Simpan jawaban
if (isset($_POST['submit'])) {
    $jawaban = $_POST['jawaban'];
    $nilai = 0;
    $total_soal = count($soal_array);

    foreach ($jawaban as $id_soal => $jawaban_siswa) {
        foreach ($soal_array as $soal) {
            if ($soal['id'] == $id_soal && $soal['jawaban_benar'] == $jawaban_siswa) {
                $nilai++;
            }
        }
    }

    $nilai_akhir = ($nilai / $total_soal) * 100;
    $siswa_id = $_SESSION['siswa']['id'];

    // Cek dan buat tabel jika belum ada
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'riwayat_ujian'");
    if (mysqli_num_rows($check_table) == 0) {
        $create_table = "CREATE TABLE riwayat_ujian (
            id INT AUTO_INCREMENT PRIMARY KEY,
            siswa_id INT NOT NULL,
            kategori VARCHAR(50) NOT NULL,
            nilai INT NOT NULL,
            tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (siswa_id) REFERENCES siswa(id)
        )";
        mysqli_query($conn, $create_table);
    }

    // Simpan hasil ujian
    $query = "INSERT INTO riwayat_ujian (siswa_id, kategori, nilai, tanggal) VALUES ('$siswa_id', '$kategori', '$nilai_akhir', NOW())";
    if (!mysqli_query($conn, $query)) {
        die('Error menyimpan hasil: ' . mysqli_error($conn));
    }

    // Set session untuk halaman hasil
    $_SESSION['nilai'] = $nilai_akhir;
    $_SESSION['kategori_ujian'] = $kategori;

    // Redirect ke halaman hasil
    header('Location: hasil.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Ujian</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%);
            min-height: 100vh;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .btn-danger {
            background: linear-gradient(to right, #ff416c, #ff4b2b);
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 65, 108, 0.4);
        }

        .container {
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        .soal-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .soal-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .font-weight-bold {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .pilihan-group {
            padding-left: 20px;
        }

        .form-check {
            margin-bottom: 12px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .form-check:hover {
            background: rgba(75, 108, 183, 0.05);
        }

        .form-check-input {
            margin-top: 6px;
        }

        .form-check-label {
            color: #34495e;
            padding-left: 10px;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(to right, #4b6cb7, #182848);
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(75, 108, 183, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .card-body {
                padding: 20px;
            }

            .soal-item {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Ujian Online</a>
            <div class="ml-auto">
                <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Selamat datang, <?php echo $_SESSION['siswa']['nama']; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" id="formUjian">
                    <?php foreach ($soal_array as $index => $soal) { ?>
                        <div class="soal-item mb-4">
                            <p class="font-weight-bold">
                                <?php echo ($index + 1) . ". " . htmlspecialchars($soal['pertanyaan']); ?></p>
                            <div class="pilihan-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban[<?php echo $soal['id']; ?>]"
                                        value="A" required>
                                    <label
                                        class="form-check-label"><?php echo htmlspecialchars($soal['pilihan_a']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban[<?php echo $soal['id']; ?>]"
                                        value="B">
                                    <label
                                        class="form-check-label"><?php echo htmlspecialchars($soal['pilihan_b']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban[<?php echo $soal['id']; ?>]"
                                        value="C">
                                    <label
                                        class="form-check-label"><?php echo htmlspecialchars($soal['pilihan_c']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban[<?php echo $soal['id']; ?>]"
                                        value="D">
                                    <label
                                        class="form-check-label"><?php echo htmlspecialchars($soal['pilihan_d']); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <button type="submit" name="submit" class="btn btn-primary"
                        onclick="return confirm('Apakah Anda yakin ingin mengakhiri ujian?');">Selesai Ujian</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>