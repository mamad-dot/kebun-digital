<?php
session_start();
require_once('../includes/config.php');

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

function parseQuestions($text)
{
    $questions = [];
    $lines = explode("\n", $text);
    $current_question = null;

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;

        // Deteksi pertanyaan baru (biasanya dimulai dengan nomor)
        if (preg_match('/^\d+[.)]\s+(.+)/', $line, $matches)) {
            if ($current_question) {
                $questions[] = $current_question;
            }
            $current_question = [
                'pertanyaan' => $matches[1],
                'pilihan_a' => '',
                'pilihan_b' => '',
                'pilihan_c' => '',
                'pilihan_d' => '',
                'jawaban_benar' => ''
            ];
        }
        // Deteksi pilihan jawaban
        elseif (preg_match('/^[A-D][.)]\s+(.+)/', $line, $matches)) {
            $option = strtolower(substr($line, 0, 1));
            $current_question['pilihan_' . $option] = $matches[1];
        }
        // Deteksi jawaban benar
        elseif (preg_match('/^Jawaban\s*:\s*([A-D])/i', $line, $matches)) {
            $current_question['jawaban_benar'] = strtoupper($matches[1]);
        }
    }

    // Tambahkan pertanyaan terakhir
    if ($current_question) {
        $questions[] = $current_question;
    }

    return $questions;
}

// Inisialisasi variabel statistik
$total_soal = 0;
$soal_hari_ini = 0;
$total_import = 0;

// Query untuk mengambil statistik
try {
    // Total soal
    $query_total = "SELECT COUNT(*) as total FROM soal";
    $result_total = mysqli_query($conn, $query_total);
    if ($result_total) {
        $total_soal = mysqli_fetch_assoc($result_total)['total'];
    }

    // Soal hari ini
    $query_today = "SELECT COUNT(*) as total FROM soal WHERE DATE(created_at) = CURDATE()";
    $result_today = mysqli_query($conn, $query_today);
    if ($result_today) {
        $soal_hari_ini = mysqli_fetch_assoc($result_today)['total'];
    }

    // Total import batch
    $query_import = "SELECT COUNT(DISTINCT import_batch) as total FROM soal";
    $result_import = mysqli_query($conn, $query_import);
    if ($result_import) {
        $total_import = mysqli_fetch_assoc($result_import)['total'];
    }
} catch (Exception $e) {
    error_log('Error in statistics queries: ' . $e->getMessage());
}

if (isset($_POST['import'])) {
    if (!empty($_FILES['file']['name']) && !empty($_POST['kategori'])) {
        $kategori = $_POST['kategori'];
        $file = $_FILES['file'];
        $filename = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $imported_count = 0;

        if ($file['error'] !== 0) {
            $error = "Error saat upload file: " . $file['error'];
        } elseif (!file_exists($filename)) {
            $error = "File tidak ditemukan setelah upload.";
        } else {
            if ($file_ext == 'pdf') {
                require_once('../vendor/autoload.php');
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($filename);
                    $text = $pdf->getText();
                    $questions = parseQuestions($text);

                    foreach ($questions as $q) {
                        $sql = "INSERT INTO soal (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, kategori) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param(
                            $stmt,
                            'sssssss',
                            $q['pertanyaan'],
                            $q['pilihan_a'],
                            $q['pilihan_b'],
                            $q['pilihan_c'],
                            $q['pilihan_d'],
                            $q['jawaban_benar'],
                            $kategori
                        );
                        if (mysqli_stmt_execute($stmt)) {
                            $imported_count++;
                        } else {
                            error_log('MySQL error: ' . mysqli_stmt_error($stmt));
                        }
                        mysqli_stmt_close($stmt);
                    }
                } catch (Exception $e) {
                    $error = "Gagal parsing PDF: " . $e->getMessage();
                }
            } elseif (in_array($file_ext, ['doc', 'docx'])) {
                require_once('../vendor/autoload.php');
                try {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filename);
                    $text = '';
                    foreach ($phpWord->getSections() as $section) {
                        foreach ($section->getElements() as $element) {
                            if (method_exists($element, 'getText')) {
                                $text .= $element->getText() . "\n";
                            }
                        }
                    }
                    $questions = parseQuestions($text);

                    foreach ($questions as $q) {
                        $sql = "INSERT INTO soal (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, kategori) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param(
                            $stmt,
                            'sssssss',
                            $q['pertanyaan'],
                            $q['pilihan_a'],
                            $q['pilihan_b'],
                            $q['pilihan_c'],
                            $q['pilihan_d'],
                            $q['jawaban_benar'],
                            $kategori
                        );
                        if (mysqli_stmt_execute($stmt)) {
                            $imported_count++;
                        } else {
                            error_log('MySQL error: ' . mysqli_stmt_error($stmt));
                        }
                        mysqli_stmt_close($stmt);
                    }
                } catch (Exception $e) {
                    $error = "Gagal parsing DOC/DOCX: " . $e->getMessage();
                }
            } else {
                $error = "Format file tidak didukung. Gunakan PDF, DOC, atau DOCX.";
            }
        }

        if (!isset($error)) {
            if ($imported_count > 0) {
                $success = "Berhasil mengimport $imported_count soal!";
            } else {
                $error = "Tidak ada soal yang berhasil diimport. Pastikan format soal sudah benar.";
            }
        }
    } else {
        $error = "Kategori dan file wajib dipilih.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Soal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #8B5CF6;
        min-height: 100vh;
    }

    .container {
        padding-top: 2rem;
    }

    .card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .stats-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #8B5CF6;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Import Soal <?php echo ucfirst($_GET['category'] ?? ''); ?></h2>

                        <!-- Statistik -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="stats-card text-center">
                                    <div class="stats-number"><?php echo $total_soal; ?></div>
                                    <div>Total Soal</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card text-center">
                                    <div class="stats-number"><?php echo $soal_hari_ini; ?></div>
                                    <div>Soal Hari Ini</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card text-center">
                                    <div class="stats-number"><?php echo $total_import; ?></div>
                                    <div>Total Import</div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="kategori" value="<?php echo $_GET['category'] ?? ''; ?>">

                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File Soal (PDF/DOC/DOCX)</label>
                                <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx"
                                    required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="import" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Import Soal
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>