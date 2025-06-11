<?php
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    // Hitung jumlah total tanggal unik
    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT DATE(tanggal_pengecekan)) AS total
        FROM stok_opname
    ");
    $totalRows = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRows / $limit);

    // Ambil data dengan paginasi
    $stmt = $pdo->prepare("
        SELECT so.nama_alato,
               a.tingkatan_alat,
               DATE(so.tanggal_pengecekan) AS tgl,
               SUM(so.jumlah_alat_opname) AS total_opname
        FROM stok_opname so
        JOIN alat a ON so.nama_alato = a.id
        GROUP BY so.nama_alato, DATE(so.tanggal_pengecekan)
        ORDER BY DATE(so.tanggal_pengecekan) DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rekapList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rekapList = [];
    $error = "Gagal mengambil data rekap: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pengecekan - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        .navbar-custom { background-color: #0d6efd !important; }
        .container-wrapper { margin-top:80px; }
    </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
    <div class="container-fluid">
      <a href="index.php" class="navbar-brand d-flex align-items-center ms-1">
        <img src="aset/logo.png" alt="Logo" width="40" height="40" class="me-1">
        Sukarobot Academy
      </a>
      <div class="d-flex align-items-center ms-auto">
        <a href="index.php" class="btn btn-light btn-sm me-2">Dashboard</a>
        <span class="navbar-text me-3 text-white">
          Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container container-wrapper">
    <h2>Rekap Pengecekan Stok</h2>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>No</th>
            <th>Nama Alat</th>
            <th>Tingkatan Alat</th>
            <th>Total Jumlah Opname</th>
            <th>Tanggal Pengecekan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rekapList)): ?>
            <tr><td colspan="5" class="text-center">Belum ada data rekap</td></tr>
          <?php else: foreach($rekapList as $i => $rekap): ?>
            <tr>
              <td><?= $offset + $i + 1 ?></td>
              <td>
                <?php
                  $stmt2 = $pdo->prepare("SELECT nama_alat FROM alat WHERE id = ?");
                  $stmt2->execute([$rekap['nama_alato']]);
                  $nama = $stmt2->fetchColumn();
                ?>
                <?= htmlspecialchars($nama) ?>
              </td>
              <td><?= htmlspecialchars($rekap['tingkatan_alat']) ?></td>
              <td><?= htmlspecialchars($rekap['total_opname']) ?></td>
              <td><?= date('d-m-Y', strtotime($rekap['tgl'])) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-center">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Sebelumnya</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Berikutnya</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>

      <div class="d-flex justify-content-between">
        <a href="stok_opname.php" class="btn btn-primary">Kembali</a>
      </div>
    </div>
  </div>
</body>
</html>
