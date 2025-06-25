<?php
session_start();
require_once('../includes/config.php');

// Redirect jika sudah login
if (isset($_SESSION['siswa'])) {
    header('Location: /soal/siswa/pilih_kategori.php');
    exit();
}

$error = '';
if (isset($_POST['login'])) {
    $nis = trim(mysqli_real_escape_string($conn, $_POST['nis']));
    $nama = trim(mysqli_real_escape_string($conn, $_POST['nama']));

    $query = "SELECT * FROM siswa WHERE nis = ? AND nama = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $nis, $nama);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $siswa = mysqli_fetch_assoc($result);
            $_SESSION['siswa'] = $siswa;
            header('Location: /soal/siswa/pilih_kategori.php');
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
            background-color: #3498db;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 350px;
            padding: 0;
        }

        .login-header {
            background: #2980b9;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .login-header .icon {
            font-size: 50px;
            margin-bottom: 10px;
            color: white;
        }

        .login-header h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .login-body {
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

        .btn-login {
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

        .btn-login:hover {
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

        .it-club-title {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 2px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(45deg, #2c3e50, #3498db);
            padding: 10px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: #3498db;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 100px;
            /* Menambahkan padding atas untuk ruang IT CLUB */
        }
    </style>
</head>

<body>
    <div class="it-club-title">IT CLUB</div>
    <div class="login-box">
        <div class="login-header">
            <div class="icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3>Login Siswa</h3>
        </div>

        <div class="login-body">
            <?php if ($error) { ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php } ?>

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

                <button type="submit" name="login" class="btn btn-login">Masuk</button>

                <div class="text-center mt-3">
                    <p class="text-muted">Belum punya akun?
                        <a href="register.php" style="color: #3498db; text-decoration: none; font-weight: 600;">Daftar
                            di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>