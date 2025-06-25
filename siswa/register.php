<?php
session_start();
require_once('../includes/config.php');

if (isset($_SESSION['siswa'])) {
    header('Location: /soal/siswa/pilih_kategori.php');
    exit();
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $nis = trim(mysqli_real_escape_string($conn, $_POST['nis']));
    $nama = trim(mysqli_real_escape_string($conn, $_POST['nama']));
    
    // Validasi format NIS
    if (!preg_match('/^[0-9]+$/', $nis)) {
        $error = 'NIS hanya boleh berisi angka';
    } else {
        // Cek NIS dengan case-insensitive
        $check_query = "SELECT * FROM siswa WHERE LOWER(nis) = LOWER(?)";
        $check_stmt = $conn->prepare($check_query);
        
        if ($check_stmt) {
            $check_stmt->bind_param('s', $nis);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Cek apakah nama juga sama
                $existing_user = $result->fetch_assoc();
                if (strtolower($existing_user['nama']) === strtolower($nama)) {
                    // Jika NIS dan nama sama, arahkan ke login
                    $_SESSION['login_message'] = 'Akun Anda sudah terdaftar. Silakan login.';
                    header('Location: login.php');
                    exit();
                } else {
                    $error = 'NIS sudah digunakan oleh siswa lain.';
                }
            } else {
                // NIS belum terdaftar, lakukan insert
                $insert_query = "INSERT INTO siswa (nis, nama) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                
                if ($insert_stmt) {
                    $insert_stmt->bind_param('ss', $nis, $nama);
                    if ($insert_stmt->execute()) {
                        $success = 'Registrasi berhasil! Silakan login.';
                    } else {
                        $error = 'Gagal mendaftar. Silakan coba lagi.';
                    }
                    $insert_stmt->close();
                }
            }
            $check_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Siswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    body {
        background-color: #3498db;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .register-box {
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        max-width: 350px;
        padding: 0;
    }

    .register-header {
        background: #2980b9;
        color: white;
        padding: 20px;
        text-align: center;
    }

    .register-header .icon {
        font-size: 50px;
        margin-bottom: 10px;
        color: white;
    }

    .register-header h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .register-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        color: #34495e;
        margin-bottom: 5px;
    }

    .input-group {
        border: 2px solid #e3e3e3;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .input-group:focus-within {
        border-color: #3498db;
    }

    .input-group-prepend .input-group-text {
        background: none;
        border: none;
        color: #3498db;
        padding-left: 15px;
    }

    .form-control {
        border: none;
        padding: 10px;
        height: auto;
        font-size: 15px;
    }

    .form-control:focus {
        box-shadow: none;
    }

    .btn-register {
        background: #3498db;
        color: white;
        padding: 12px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 5px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-register:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .alert {
        border: none;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .alert i {
        margin-right: 10px;
    }

    .alert-danger {
        background-color: #fff2f2;
        color: #dc3545;
    }

    .alert-success {
        background-color: #f0fff4;
        color: #28a745;
    }
    </style>
</head>

<body>
    <div class="register-box">
        <div class="register-header">
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3>Registrasi Siswa</h3>
        </div>

        <div class="register-body">
            <?php if ($error) { ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
            <?php } ?>

            <?php if ($success) { ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
            <?php } ?>

            <!-- Hapus bagian form tanggal lahir -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nis">NIS</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                        </div>
                        <input type="text" id="nis" name="nis" class="form-control" placeholder="Masukkan NIS" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" id="nama" name="nama" class="form-control"
                            placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>

                <button type="submit" name="register" class="btn btn-register">Daftar</button>
            </form>
        </div>
    </div>
</body>

</html>