<?php
session_start();
require_once('../includes/config.php');

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "DELETE FROM soal WHERE id = '$id'";
    mysqli_query($conn, $query);
} elseif (isset($_GET['all'])) {
    $query = "DELETE FROM soal";
    mysqli_query($conn, $query);
}

header('Location: riwayat_soal.php');