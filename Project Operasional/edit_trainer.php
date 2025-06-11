<?php
require_once 'config.php';
&nbsp;
&nbsp;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: data_trainer.php?error=Anda Tidak Memiliki Akses');
    exit;
}
&nbsp;
&nbsp;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: data_trainer.php?error=ID tidak valid');
    exit;
}
&nbsp;
&nbsp;

$id = (int)$_GET['id'];
$error = '';
&nbsp;
&nbsp;

$stmt = $pdo->prepare("SELECT * FROM trainer1 WHERE id = ?");
$stmt->execute([$id]);
$trainer = $stmt->fetch();
&nbsp;
&nbsp;

if (!$trainer) {
    header('Location: data_trainer.php?error=Data tidak ditemukan');
    exit;
}
&nbsp;
&nbsp;

$NIK = $trainer['NIK'];
$nama_lengkap = $trainer['nama_lengkap'];
$no_HP = $trainer['no_HP'];
&nbsp;
&nbsp;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $NIK = trim($_POST['NIK'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_HP = trim($_POST['no_HP'] ?? '');
&nbsp;
&nbsp;

    if ($NIK == '' || $nama_lengkap == '' || $no_HP == '') {
        $error = 'Semua field wajib diisi!';
    } else {
        $sql = "UPDATE trainer1 SET NIK = ?, nama_lengkap = ?, no_HP = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$NIK, $nama_lengkap, $no_HP, $id])) {
            header('Location: data_trainer.php?success=Data Trainer Berhasil Diperbarui');
            exit;
        } else {
            $error = 'Gagal memperbarui data trainer!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Trainer - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container mt-5">
    <h1>Edit Trainer</h1>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="edit_trainer.php?id=<?= $id ?>">
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
        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="data_trainer.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>