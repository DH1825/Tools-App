<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$isAdmin = ($user['role'] === 'admin'); // Cek apakah pengguna adalah admin
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            margin-bottom: 20px;
        }
        .welcome-message {
            font-weight: bold;
            color: #007bff;
        }
        .lead {
            color: #6c757d;
        }
        .btn-lg {
            font-size: 1.25rem;
            padding: 1rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="aset/logo.png" alt="Sukarobot Academy Logo" width="40" height="40" class="me-2">
            Sukarobot Academy
        </a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3">Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="dashboard-container text-center">
    <h1 class="mb-4 welcome-message">Selamat Datang di Dashboard Sukarobot Academy</h1>
    <p class="lead mb-5">Pilih menu di bawah ini untuk mengelola data alat atau data trainer dan melihat klasifikasi tingkatan alat</p>
    <div class="d-grid gap-3">
        <a href="data_alat.php" class="btn btn-primary btn-lg">
            Data Alat Pembelajaran
        </a>
        <a href="data_peminjaman.php" class="btn btn-secondary btn-lg">
            Data Peminjaman Alat
        </a>
        <a href="kebutuhan_alat.php" class="btn btn-info btn-lg">
            Pengambilan Alat Pembelajaran
        </a>
        <?php if ($isAdmin): ?>
            <a href="data_trainer.php" class="btn btn-danger btn-lg">
                Data Trainer
            </a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <a href="stok_opname.php" class="btn btn-info btn-lg">
              Stok Opname
            </a>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
