<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$isAdmin = ($user['role'] === 'admin');

try {
    $stmt = $pdo->query("
        SELECT 
            id,
            nama_trainer,
            nama_project,
            tingkatan_alat,
            Tempat_mengajar,
            tanggal_ngajar,
            Status_alat,
            GROUP_CONCAT(CONCAT(alat_yang_dipinjam, ' (', jumlah_alat_yang_dipinjam, ')') SEPARATOR ', ') AS daftar_alat
        FROM data_peminjaman_alat
        GROUP BY 
            nama_trainer,
            nama_project,
            tingkatan_alat,
            Tempat_mengajar,
            tanggal_ngajar
        ORDER BY tanggal_ngajar DESC
    ");
    $peminjamanList = $stmt->fetchAll();
} catch (PDOException $e) {
    $peminjamanList = [];
    $error = "Terjadi kesalahan saat mengambil data: " . htmlspecialchars($e->getMessage());
}

$successMsg = $_GET['success'] ?? null;
$errorMsg = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Data Peminjaman Alat - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="aset/logo.png" alt="Logo Sukarobot Academy" width="40" height="40" class="me-2" />
            Sukarobot Academy
        </a>
        <div class="d-flex align-items-center ms-auto">
            <a href="index.php" class="btn btn-light btn-sm me-3">Kembali ke Dashboard</a>
            <span class="navbar-text me-3">Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Data Peminjaman Alat</h1>
        <?php if ($isAdmin): ?>
            <form action="delete_all_peminjaman.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua data peminjaman?');">
                <button type="submit" class="btn btn-danger">Hapus Semua Data</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($peminjamanList)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Tingkatan Alat</th>
                        <th>Nama Project</th>
                        <th>Nama Trainer</th>
                        <th>Tempat Mengajar</th>
                        <th>Tanggal Ngajar</th>
                        <th>Alat yang Dipinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                   <?php foreach ($peminjamanList as $peminjaman): ?>
                        <tr>
                            <td><?= htmlspecialchars($peminjaman['tingkatan_alat']) ?></td>
                            <td><?= htmlspecialchars($peminjaman['nama_project']) ?></td>
                            <td><?= htmlspecialchars($peminjaman['nama_trainer']) ?></td>
                            <td><?= htmlspecialchars($peminjaman['Tempat_mengajar']) ?></td>
                            <td><?= htmlspecialchars($peminjaman['tanggal_ngajar']) ?></td>
                            <td>
                                <ul class="mb-0">
                                    <?php 
                                    $alatList = explode(', ', $peminjaman['daftar_alat']);
                                    foreach ($alatList as $alat): ?>
                                        <li><?= htmlspecialchars($alat) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?= htmlspecialchars($peminjaman['Status_alat']) ?></td>
                        </tr>
                   <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Tidak ada data peminjaman alat.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>