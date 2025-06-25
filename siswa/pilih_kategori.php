<?php
session_start();
require_once('../includes/config.php');

// Cek login
if (!isset($_SESSION['siswa'])) {
    header('Location: /soal/siswa/index.php');
    exit();
}

// Handle pemilihan kategori
if (isset($_POST['kategori'])) {
    $_SESSION['kategori'] = $_POST['kategori'];
    header('Location: /soal/siswa/ujian.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pilih Kategori Ujian</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%);
        min-height: 100vh;
        padding: 40px 0;
    }

    .container {
        padding: 20px;
    }

    .club-title {
        color: #fff;
        text-align: center;
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 4px;
        text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.3);
        background: linear-gradient(to right, #fff 0%, #f9f9f9 50%, #fff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    h2 {
        color: white;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        margin-bottom: 40px;
    }

    .category-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.4s ease;
        height: 100%;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    .category-card:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 20px 35px rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .category-icon {
        font-size: 2.5rem;
        margin-bottom: 20px;
        transition: all 0.4s ease;
    }

    .category-card:hover .category-icon {
        transform: scale(1.2) rotate(10deg);
    }

    .basic {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.3) 0%, rgba(26, 188, 156, 0.3) 100%);
    }

    .intermediate {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.3) 0%, rgba(41, 128, 185, 0.3) 100%);
    }

    .advanced {
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.3) 0%, rgba(142, 68, 173, 0.3) 100%);
    }

    .basic:hover {
        background: linear-gradient(135deg, rgba(46, 204, 113, 0.5) 0%, rgba(26, 188, 156, 0.5) 100%);
    }

    .intermediate:hover {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.5) 0%, rgba(41, 128, 185, 0.5) 100%);
    }

    .advanced:hover {
        background: linear-gradient(135deg, rgba(155, 89, 182, 0.5) 0%, rgba(142, 68, 173, 0.5) 100%);
    }

    .category-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: 1px;
    }

    .category-description {
        color: rgba(255, 255, 255, 0.95);
        font-size: 16px;
        line-height: 1.6;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    }

    .logout-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
    }
    </style>

<body>
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt mr-2"></i>Logout
    </a>
    <div class="container">
        <h1 class="club-title">IT-CLUB</h1>
        <h2 class="text-center">Pilih Kategori Ujian Anda</h2>
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <form method="POST" action="">
                    <button type="submit" name="kategori" value="basic" class="btn w-100">
                        <div class="category-card basic">
                            <div class="category-icon">
                                <i class="far fa-lightbulb"></i>
                            </div>
                            <div class="category-title">Basic</div>
                            <div class="category-description">
                                Ujian dasar untuk menguji pemahaman konsep fundamental
                            </div>
                        </div>
                    </button>
                </form>
            </div>
            <div class="col-md-4 mb-4">
                <form method="POST" action="">
                    <button type="submit" name="kategori" value="intermediate" class="btn w-100">
                        <div class="category-card intermediate">
                            <div class="category-icon">
                                <i class="fas fa-bars"></i>
                            </div>
                            <div class="category-title">Intermediate</div>
                            <div class="category-description">
                                Ujian tingkat menengah dengan soal yang lebih menantang
                            </div>
                        </div>
                    </button>
                </form>
            </div>
            <div class="col-md-4 mb-4">
                <form method="POST" action="">
                    <button type="submit" name="kategori" value="advanced" class="btn w-100">
                        <div class="category-card advanced">
                            <div class="category-icon">
                                <i class="far fa-star"></i>
                            </div>
                            <div class="category-title">Advanced</div>
                            <div class="category-description">
                                Ujian tingkat lanjut untuk menguji kemampuan tingkat tinggi
                            </div>
                        </div>
                    </button>
                </form>
            </div>
        </div>
        <div
            style="text-align: center; margin-top: 50px; padding: 20px; color: #fff; font-size: 14px; background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);">
            Copyright &copy; M4M4D3 2025
        </div>
    </div> <!-- penutup container -->
</body>

</html>