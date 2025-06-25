<?php
session_start();
require_once('../includes/config.php');

// Inisialisasi variabel
$error = '';
$login_message = '';

// Cek apakah ada pesan dari halaman registrasi
if (isset($_SESSION['login_message'])) {
    $login_message = $_SESSION['login_message'];
    unset($_SESSION['login_message']);
}

if (isset($_SESSION['siswa'])) {
    header('Location: pilih_kategori.php');  // Ubah path menjadi relatif
    exit();
}

if (isset($_POST['login'])) {
    // Konversi NIS ke huruf kecil untuk pengecekan
    $nis = strtolower(trim(mysqli_real_escape_string($conn, $_POST['nis'])));
    $nama = trim(mysqli_real_escape_string($conn, $_POST['nama']));

    $query = "SELECT * FROM siswa WHERE nis = ? AND nama = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $nis, $nama);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $siswa = mysqli_fetch_assoc($result);
            $_SESSION['siswa'] = $siswa;
            header('Location: pilih_kategori.php');  // Ubah path menjadi relatif
            exit();
        } else {
            $error = 'NIS atau Nama tidak valid';
        }
    } else {
        $error = 'Terjadi kesalahan database';
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header .icon {
            background: #007bff;
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
        }

        .login-header h2 {
            color: #333;
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: #555;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .input-group {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .input-group-prepend .input-group-text {
            background: #f8f9fa;
            border: none;
            color: #007bff;
            padding: 0.75rem 1rem;
        }

        .form-control {
            border: none;
            padding: 0.75rem;
            height: auto;
        }

        .form-control:focus {
            box-shadow: none;
            background: #fff;
        }

        .input-group:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .btn-login {
            background: #007bff;
            color: white;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 5px;
        }

        .alert-danger {
            background-color: #fff2f2;
            color: #dc3545;
        }

        .alert i {
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h2>Login Siswa</h2>
        </div>

        <?php if ($login_message) { ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <?php echo htmlspecialchars($login_message); ?>
            </div>
        <?php } ?>

        <?php if ($error) { ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nis">Nomor Induk Siswa (NIS)</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-id-card"></i>
                        </span>
                    </div>
                    <input type="text" id="nis" name="nis" class="form-control" placeholder="Masukkan NIS" required
                        autofocus>
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
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap"
                        required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-login">Masuk</button>

            <div class="text-center mt-3">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </form>
    </div>
</body>

</html>