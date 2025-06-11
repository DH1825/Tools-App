<?php
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: data_trainer.php?error=Anda Tidak Memiliki Akses');
    exit;
}

$error = '';
$NIK = '';
$nama_lengkap = '';
$no_HP = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $NIK = trim($_POST['NIK'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_HP = trim($_POST['no_HP'] ?? '');

    if ($NIK == '' || $nama_lengkap == '' || $no_HP == '') {
        $error = 'Semua field wajib diisi!';
    } else {
        $sql = "INSERT INTO trainer1 (NIK, nama_lengkap, no_HP, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$NIK, $nama_lengkap, $no_HP])) {
            header('Location: data_trainer.php?success=Data Trainer Berhasil Ditambahkan');
            exit;
        } else {
            $error = 'Gagal menambah data trainer!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Trainer - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container mt-5">
    <h1>Tambah Trainer</h1>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="create_trainer.php">
        <div class="mb-3">
            <label for="NIK" class="form-label">NIK</label>
            <input type="text" class="form-control" id="NIK" name="NIK" value="<?= htmlspecialchars($NIK) ?>" required />
        </div>
        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($nama_lengkap) ?>" required />
        </div>
        <div class="mb-3">
            <label for="no_HP" class="form-label">No HP</label>
            <input type="text" class="form-control" id="no_HP" name="no_HP" value="<?= htmlspecialchars($no_HP) ?>" required />
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="data_trainer.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>