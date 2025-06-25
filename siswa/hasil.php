<?php
session_start();
require_once('../includes/config.php');

// Periksa apakah user sudah login dan memiliki nilai
if (!isset($_SESSION['siswa']) || !isset($_SESSION['nilai'])) {
    header('Location: index.php');
    exit();
}

// Ambil data siswa
$user_id = $_SESSION['siswa']['id'];
$nilai = $_SESSION['nilai'];
$kategori = isset($_SESSION['kategori_ujian']) ? $_SESSION['kategori_ujian'] : 'Peserta';

$query = "SELECT * FROM siswa WHERE id = ?";
$stmt = $conn->prepare($query);

// Periksa apakah prepare statement berhasil
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param('i', $user_id);

// Periksa apakah execute berhasil
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

// Periksa apakah data siswa ditemukan
if (!$siswa) {
    die("Data siswa tidak ditemukan");
}

// Tentukan predikat berdasarkan nilai
$predikat = '';
if ($nilai >= 90) {
    $predikat = 'Sangat Baik';
} elseif ($nilai >= 80) {
    $predikat = 'Baik';
} elseif ($nilai >= 70) {
    $predikat = 'Cukup';
} else {
    $predikat = 'Kurang';
}

// Ambil semua nilai ujian siswa
// Ambil nilai ujian berdasarkan kategori yang dipilih
$query_nilai = "SELECT * FROM riwayat_ujian WHERE siswa_id = ? AND kategori = ? ORDER BY tanggal DESC LIMIT 1";
$stmt_nilai = $conn->prepare($query_nilai);
$stmt_nilai->bind_param('is', $user_id, $kategori);
$stmt_nilai->execute();
$result_nilai = $stmt_nilai->get_result();

?>
<style>
    /* Reset CSS */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Halaman Sertifikat Bagian Depan */
    .certificate-front {
        width: 800px;
        margin: 30px auto;
        padding: 40px;
        background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 100%);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        text-align: center;
        border: 3px solid #2d5fc9;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
    }

    /* Menambahkan ornamen dekoratif */
    .certificate-front:before,
    .certificate-front:after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: linear-gradient(45deg, #4481eb22 0%, #2d5fc922 100%);
        border-radius: 50%;
    }

    .certificate-front:before {
        top: -100px;
        left: -100px;
    }

    .certificate-front:after {
        bottom: -100px;
        right: -100px;
    }

    .certificate-header {
        margin-bottom: 40px;
        position: relative;
    }

    .certificate-header:after {
        content: '';
        display: block;
        width: 150px;
        height: 4px;
        background: linear-gradient(90deg, #4481eb 0%, #2d5fc9 100%);
        margin: 20px auto;
        border-radius: 2px;
    }

    .certificate-title {
        font-size: 48px;
        background: linear-gradient(45deg, #4481eb 0%, #2d5fc9 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 6px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .certificate-subtitle {
        font-size: 26px;
        color: #555;
        margin-bottom: 40px;
        font-style: italic;
    }

    .recipient-name {
        font-size: 42px;
        color: #333;
        margin: 30px 0;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 3px;
        position: relative;
        display: inline-block;
    }

    .recipient-name:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: 2px;
        background: linear-gradient(90deg, transparent 0%, #4481eb 50%, transparent 100%);
    }

    /* Halaman Sertifikat Bagian Belakang */
    .certificate-back {
        width: 800px;
        margin: 30px auto;
        padding: 40px;
        background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 100%);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        border: 3px solid #2d5fc9;
        border-radius: 15px;
    }

    .nilai-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 30px 0;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .nilai-table th {
        background: linear-gradient(45deg, #4481eb 0%, #2d5fc9 100%);
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        padding: 15px;
        font-size: 16px;
    }

    .nilai-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-size: 15px;
        color: #444;
    }

    .nilai-table tr:last-child td {
        border-bottom: none;
    }

    .print-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 15px 30px;
        background: linear-gradient(45deg, #4481eb 0%, #2d5fc9 100%);
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .print-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }

    /* Tambahan untuk tanda tangan */
    .signature-space {
        height: 80px;
        border-bottom: 2px solid #4481eb;
        margin: 15px 0;
        position: relative;
    }

    .signature-name {
        font-weight: bold;
        margin-top: 15px;
        color: #333;
        font-size: 16px;
    }
</style>

<div class="certificate-front">
    <!-- Tambahkan logo di sini, sebelum certificate-header -->
    <div class="logo-container">
        <div class="logo-circle">
            <div class="logo-text">IT-CLUB</div>
        </div>
    </div>

    <div class="certificate-header">
        <h1 class="certificate-title">SERTIFIKAT</h1>
        <p class="certificate-subtitle">Diberikan Kepada:</p>
    </div>

    <h2 class="recipient-name"><?php echo htmlspecialchars($siswa['nama']); ?></h2>

    <p class="certificate-text">
        Telah menyelesaikan ujian <?php echo htmlspecialchars(strtoupper($kategori)); ?>
    </p>

    <div class="signatures-container"
        style="display: flex; justify-content: space-between; padding: 0 50px; margin-top: 50px;">
        <!-- Tanda tangan Kepala Sekolah -->
        <div class="signature-date">
            <p>Jakarta, <?php
                        if ($row = $result_nilai->fetch_assoc()) {
                            echo date('d F Y', strtotime($row['tanggal']));
                        }
                        $result_nilai->data_seek(0); // Reset pointer hasil query
                        ?></p>
            <p style="margin-top: 20px;">Kepala Sekolah</p>
            <div class="signature-space"></div>
            <p class="signature-name">apt, Sarwan, S.Si, M.Kes</p>
        </div>

        <!-- Tanda tangan Pengajar -->
        <div class="signature-date">
            <p>Jakarta, <?php
                        if ($row = $result_nilai->fetch_assoc()) {
                            echo date('d F Y', strtotime($row['tanggal']));
                        }
                        $result_nilai->data_seek(0); // Reset pointer hasil query
                        ?></p>
            <p style="margin-top: 20px;">Pengajar</p>
            <div class="signature-space"></div>
            <p class="signature-name">Moch Ikhsan Rahadian S.Kom</p>
        </div>
    </div>
</div>

<div class="certificate-back">
    <h1 class="detail-title">DAFTAR NILAI</h1>
    <div class="detail-info">
        <table class="info-table">
            <tr>
                <td>Nama</td>
                <td>: <?php echo htmlspecialchars($siswa['nama']); ?></td>
            </tr>
            <tr>
                <td>Tanggal Ujian</td>
                <td>: <?php
                        if ($row = $result_nilai->fetch_assoc()) {
                            echo date('d/m/Y', strtotime($row['tanggal']));
                        } else {
                            echo '-';
                        }
                        $result_nilai->data_seek(0); // Reset pointer hasil query
                        ?></td>
            </tr>
        </table>
    </div>

    <table class="nilai-table">
        <tr>
            <th>NO</th>
            <th>KOMPETENSI YANG DI UJIKAN</th>
            <th>NILAI</th>
            <th>TANGGAL</th>
        </tr>
        <?php
        $no = 1;
        while ($row = $result_nilai->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo $no++; ?>.</td>
                <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                <td><?php echo $row['nilai']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
            </tr>
        <?php } ?>
    </table>

    <div class="signature-section">
        <div class="signature-date" style="text-align: right;">
            <p>Jakarta, <?php
                        if ($row = $result_nilai->fetch_assoc()) {
                            echo date('d F Y', strtotime($row['tanggal']));
                        }
                        $result_nilai->data_seek(0); // Reset pointer hasil query
                        ?></p>
            <p>Pengajar</p>
            <div class="signature-space"></div>
            <p class="signature-name">Moch Ikhsan Rahadian S.Kom</p>
        </div>
    </div>
</div>

<style>
    .action-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        gap: 15px;
        z-index: 1000;
    }

    .back-button {
        padding: 12px 24px;
        background: #fff;
        color: #4481eb;
        border: 2px solid #4481eb;
        border-radius: 25px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .back-button:hover {
        background: #4481eb;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .print-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 15px 30px;
        background: linear-gradient(45deg, #4481eb 0%, #2d5fc9 100%);
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .print-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }
</style>

<!-- Di bagian bawah file, ganti tombol yang ada dengan div baru -->
<div class="action-buttons">
    <a href="pilih_kategori.php" class="back-button">Kembali</a>
    <button onclick="window.print()" class="print-button">Cetak Sertifikat</button>
</div>