<?php
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: data_alat.php?error=Anda tidak memiliki akses.');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: data_alat.php?error=ID tidak valid.');
    exit;
}

$id = (int)$_GET['id'];
$errors = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM alat WHERE id = ?");
    $stmt->execute([$id]);
    $alat = $stmt->fetch();
    if (!$alat) {
        header('Location: data_alat.php?error=Data alat tidak ditemukan.');
        exit;
    }
} catch (PDOException $e) {
    header('Location: data_alat.php?error=Kesalahan mengambil data.');
    exit;
}

$nama_alat = $alat['nama_alat'];
$tingkatan_alat = $alat['tingkatan_alat'];
$jumlah_alat = $alat['jumlah_alat'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $tingkatan_alat = trim($_POST['tingkatan_alat'] ?? '');
    $jumlah_alat = trim($_POST['jumlah_alat'] ?? '');

    if ($nama_alat === '') $errors[] = "Nama Alat harus diisi.";
    if ($tingkatan_alat === '') $errors[] = "Tingkatan Alat harus diisi.";
    if (!is_numeric($jumlah_alat) || intval($jumlah_alat) < 0) $errors[] = "Jumlah Alat harus angka tidak negatif.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE alat SET nama_alat = ?, tingkatan_alat = ?, jumlah_alat = ? WHERE id = ?");
            $stmt->execute([$nama_alat, $tingkatan_alat, intval($jumlah_alat), $id]);
            header('Location: data_alat.php?success=Data alat berhasil diperbarui.');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui data: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Data Alat - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Sukarobot Academy</a>
  </div>
</nav>
<div class="container">
    <h1 class="mb-4">Edit Data Alat</h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="edit.php?id=<?= $id ?>" novalidate>
        <div class="mb-3">
            <label for="nama_alat" class="form-label">Nama Alat</label>
            <input type="text" id="nama_alat" name="nama_alat" class="form-control" required value="<?= htmlspecialchars($nama_alat) ?>" />
        </div>
        <div class="mb-3">
            <label for="tingkatan_alat" class="form-label">Tingkatan Alat</label>
            <input type="text" id="tingkatan_alat" name="tingkatan_alat" class="form-control" required value="<?= htmlspecialchars($tingkatan_alat) ?>" />
        </div>
        <div class="mb-3">
            <label for="jumlah_alat" class="form-label">Jumlah Alat</label>
            <input type="number" id="jumlah_alat" name="jumlah_alat" min="0" class="form-control" required value="<?= htmlspecialchars($jumlah_alat) ?>" />
        </div>
        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>