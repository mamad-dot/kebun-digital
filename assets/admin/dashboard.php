<?php
session_start();
require_once('../includes/config.php');

// Cek apakah user sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Club Admin - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #00b4db, #0083b0);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #fff;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .header a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .categories {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .category {
            flex: 1;
            min-width: 300px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .category:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .category i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 15px;
            display: inline-block;
            color: #fff;
            transition: all 0.3s ease;
        }

        .category:hover i {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.3);
        }

        .category h3 {
            font-size: 1.5rem;
            margin: 1rem 0;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .category p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            margin: 5px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .history {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .history h2 {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-detail {
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-detail:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>IT Club Admin</h1>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="categories">
            <div class="category">
                <i class="fas fa-lightbulb"></i>
                <h3>Basic</h3>
                <p>Kelola soal dasar untuk menguji pemahaman konsep fundamental</p>
                <a href="import_soal.php?category=basic" class="btn"><i class="fas fa-upload"></i> Import Soal</a>
                <a href="riwayat_soal.php?category=basic" class="btn"><i class="fas fa-list"></i> Lihat Soal</a>
            </div>

            <div class="category">
                <i class="fas fa-code"></i>
                <h3>Intermediate</h3>
                <p>Kelola soal tingkat menengah dengan soal yang lebih menantang</p>
                <a href="import_soal.php?category=intermediate" class="btn"><i class="fas fa-upload"></i> Import
                    Soal</a>
                <a href="riwayat_soal.php?category=intermediate" class="btn"><i class="fas fa-list"></i> Lihat Soal</a>
            </div>

            <div class="category">
                <i class="fas fa-star"></i>
                <h3>Advanced</h3>
                <p>Kelola soal tingkat lanjut untuk menguji kemampuan tingkat tinggi</p>
                <a href="import_soal.php?category=advanced" class="btn"><i class="fas fa-upload"></i> Import Soal</a>
                <a href="riwayat_soal.php?category=advanced" class="btn"><i class="fas fa-list"></i> Lihat Soal</a>
            </div>
        </div>

        <div class="history">
            <h2>Riwayat Ujian</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Nilai</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Periksa apakah tabel riwayat_ujian ada
                    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'riwayat_ujian'");
                    if (mysqli_num_rows($check_table) > 0) {
                        $query = "SELECT r.*, s.nama 
                                  FROM riwayat_ujian r 
                                  JOIN siswa s ON r.siswa_id = s.id 
                                  ORDER BY r.tanggal DESC";
                        $result = mysqli_query($conn, $query);

                        if ($result) {
                            $no = 1;
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nilai']) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                    echo "<td><a href='detail_ujian.php?id=" . $row['id'] . "' class='btn-detail'><i class='fas fa-eye'></i> Detail</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Belum ada riwayat ujian</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                    } else {
                        // Buat tabel riwayat_ujian jika belum ada
                        $create_table = "CREATE TABLE riwayat_ujian (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            siswa_id INT NOT NULL,
                            kategori VARCHAR(50) NOT NULL,
                            nilai INT NOT NULL,
                            tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (siswa_id) REFERENCES siswa(id)
                        )";
                        if (mysqli_query($conn, $create_table)) {
                            echo "<tr><td colspan='6' class='text-center'>Tabel riwayat ujian berhasil dibuat</td></tr>";
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Error membuat tabel: " . mysqli_error($conn) . "</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div
            style="text-align: center; margin-top: 30px; padding: 20px; color: #fff; font-size: 14px; background: rgba(255, 255, 255, 0.1); border-radius: 10px;">
            Copyright &copy; M4M4D3 2025
        </div>
    </div> <!-- penutup container -->
</body>

</html>