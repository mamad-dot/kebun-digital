<?php
session_start();
require_once('../includes/config.php');

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } else {
        // Cek apakah username sudah ada
        $check_query = "SELECT * FROM admin WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Buat tabel admin jika belum ada
            $create_table = "CREATE TABLE IF NOT EXISTS admin (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($conn, $create_table);

            // Insert data admin baru
            $query = "INSERT INTO admin (username, password) VALUES ('$username', '$hashed_password')";

            if (mysqli_query($conn, $query)) {
                $success = 'Registrasi berhasil! Silakan login.';
                header('refresh:2;url=index.php');
            } else {
                $error = 'Terjadi kesalahan: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .register-title {
            text-align: center;
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 30px;
            padding: 12px 20px;
            height: auto;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #e3e3e3;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4b6cb7;
            box-shadow: 0 0 15px rgba(75, 108, 183, 0.1);
        }

        .btn-register {
            background: linear-gradient(to right, #4b6cb7, #182848);
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            letter-spacing: 1px;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(75, 108, 183, 0.4);
        }

        .alert {
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #4b6cb7;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #182848;
            text-decoration: underline;
        }

        .input-group-text {
            border-radius: 30px 0 0 30px;
            background: #f8f9fa;
            border: 2px solid #e3e3e3;
            border-right: none;
        }

        .input-group .form-control {
            border-radius: 0 30px 30px 0;
            border-left: none;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2 class="register-title">Register Admin</h2>

        <?php if ($error) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" name="confirm_password" class="form-control"
                        placeholder="Konfirmasi Password" required>
                </div>
            </div>

            <button type="submit" name="register" class="btn btn-register">Register</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="index.php">Login di sini</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>