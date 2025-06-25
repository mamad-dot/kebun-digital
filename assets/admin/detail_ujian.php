<?php
session_start();
require_once('../includes/config.php');

// Cek apakah admin sudah login
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: index.php');
    exit();
}

// Ambil ID ujian dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Escape ID untuk keamanan
$id = mysqli_real_escape_string($conn, $id);

// Query untuk mengambil detail ujian
$query = "SELECT r.*, s.nama as nama_siswa FROM riwayat_ujian r 
          JOIN siswa s ON r.siswa_id = s.id 
          WHERE r.id = '$id'";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if ($result === false) {
    die('Error: ' . mysqli_error($conn));
}

$ujian = mysqli_fetch_assoc($result);

// Query untuk mengambil detail jawaban
$query_jawaban = "SELECT js.*, s.pertanyaan, s.jawaban_benar 
                  FROM jawaban_siswa js
                  JOIN soal s ON js.soal_id = s.id
                  WHERE js.siswa_id = '{$ujian['siswa_id']}' 
                  AND js.created_at = '{$ujian['tanggal']}'
                  ORDER BY js.id";
$result_jawaban = mysqli_query($conn, $query_jawaban);

// Cek apakah query jawaban berhasil
if ($result_jawaban === false) {
    die('Error: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hasil Ujian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .detail-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.2s;
        }

        .back-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Detail Hasil Ujian</h1>
            <a href="riwayat_soal.php" class="back-btn">Kembali</a>
        </div>

        <?php if ($ujian): ?>
            <div class="detail-box">
                <h2>Informasi Ujian</h2>
                <p><strong>Nama Siswa:</strong> <?php echo htmlspecialchars($ujian['nama_siswa']); ?></p>
                <p><strong>Kategori:</strong> <?php echo htmlspecialchars($ujian['kategori']); ?></p>
                <p><strong>Nilai:</strong> <?php echo htmlspecialchars($ujian['nilai']); ?></p>
                <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($ujian['tanggal']); ?></p>
            </div>
        <?php else: ?>
            <div class="detail-box">
                <p>Data ujian tidak ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>