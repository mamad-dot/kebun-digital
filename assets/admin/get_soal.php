<?php
session_start();
require_once('../includes/config.php');

if (!isset($_SESSION['admin'])) {
    die('Unauthorized');
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM soal WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    $soal = mysqli_fetch_assoc($result);
    echo json_encode($soal);
}