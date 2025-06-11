<?php
require_once 'config.php';

// Only allow logged in users
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, nama_alat, tingkatan_alat, jumlah_alat, tanggal_input FROM alat1 ORDER BY id ASC");
    $alatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error querying data');
}

// Set headers to download file rather than display
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data_alat.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output column headings
fputcsv($output, ['ID', 'Nama Alat', 'Tingkatan Alat', 'Jumlah Alat', 'Tanggal Input']);

// Output rows
foreach ($alatList as $row) {
    fputcsv($output, $row);
}

// Close output stream
fclose($output);
exit;
