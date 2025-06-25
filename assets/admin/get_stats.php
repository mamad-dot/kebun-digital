<?php
session_start();
require_once('../includes/config.php');

if (!isset($_SESSION['admin'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

// Total soal
$query = "SELECT COUNT(*) as total FROM soal";
$result = mysqli_query($conn, $query);
$totalSoal = mysqli_fetch_assoc($result)['total'];

// Soal hari ini
$query = "SELECT COUNT(*) as total FROM soal WHERE DATE(created_at) = CURDATE()";
$result = mysqli_query($conn, $query);
$soalHariIni = mysqli_fetch_assoc($result)['total'];

// Total import (jumlah hari berbeda dengan import)
$query = "SELECT COUNT(DISTINCT DATE(created_at)) as total FROM soal";
$result = mysqli_query($conn, $query);
$totalImport = mysqli_fetch_assoc($result)['total'];

echo json_encode([
    'totalSoal' => $totalSoal,
    'soalHariIni' => $soalHariIni,
    'totalImport' => $totalImport
]);