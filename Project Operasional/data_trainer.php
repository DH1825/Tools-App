<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$isAdmin = ($user['role'] === 'admin');

try {
    $stmt = $pdo->query("SELECT id, NIK, nama_lengkap, no_HP, created_at FROM trainer1 ORDER BY created_at DESC");
    $trainerList = $stmt->fetchAll();
} catch (PDOException $e) {
    $trainerList = [];
    $error = "Terjadi kesalahan saat mengambil data: " . htmlspecialchars($e->getMessage());
}

$successMsg = $_GET['success'] ?? null;
$errorMsg = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Data Trainer - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="aset/logo.png" alt="Logo" width="40" height="40" class="me-2" /> 
      Sukarobot Academy
    </a>
    <div class="d-flex align-items-center ms-auto">
      <a href="index.php" class="btn btn-light btn-sm me-3">Dashboard</a>
      <span class="navbar-text me-3">Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)</span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1>Data Trainer</h1>
      <?php if ($isAdmin): ?>
      <a href="create_trainer.php" class="btn btn-primary">Tambah Trainer</a>
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

    <?php if ($trainerList): ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-striped">
        <thead class="table-primary">
          <tr>
            <th>NIK</th>
            <th>Nama Lengkap</th>
            <th>No HP</th>
            <?php if ($isAdmin): ?>
            <th>Aksi</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trainerList as $trainer): ?>
          <tr>
            <td><?= htmlspecialchars($trainer['NIK']) ?></td>
            <td><?= htmlspecialchars($trainer['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($trainer['no_HP']) ?></td>
            <?php if ($isAdmin): ?>
            <td>
              <a href="edit_trainer.php?id=<?= $trainer['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="delete_trainer.php?id=<?= $trainer['id'] ?>" onclick="return confirm('Yakin ingin menghapus?');" class="btn btn-sm btn-danger">Hapus</a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p>Tidak ada data trainer.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>