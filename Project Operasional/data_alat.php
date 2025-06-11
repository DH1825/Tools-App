<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$isAdmin = ($user['role'] === 'admin');

try {
    // Ambil semua alat
    $stmt = $pdo->query("SELECT id, nama_alat, tingkatan_alat, jumlah_alat, tanggal_input FROM alat ORDER BY id DESC");
    $alatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $alatList = [];
    $error = "Terjadi kesalahan saat mengambil data: " . htmlspecialchars($e->getMessage());
}

$successMsg = $_GET['success'] ?? null;
$errorMsg = $_GET['error'] ?? null;

// Klasifikasikan alat berdasarkan tingkatan secara otomatis
$alatByTingkatan = [];

// Mengelompokkan alat berdasarkan tingkatan
foreach ($alatList as $alat) {
    $tingkatan = $alat['tingkatan_alat'];
    if (!isset($alatByTingkatan[$tingkatan])) {
        $alatByTingkatan[$tingkatan] = []; // Inisialisasi array jika tingkatan belum ada
    }
    $alatByTingkatan[$tingkatan][] = $alat; // Tambahkan alat ke tingkatan yang sesuai
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Data Alat - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .navbar-custom {
            background-color: #0f172a !important;
        }
        .header-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .sidebar {
            width: 200px;
            position: fixed;
            top: 56px; /* Tinggi navbar */
            left: 0;
            background-color: #0d6efd;
            padding: 1rem;
            height: calc(100vh - 56px);
            overflow-y: auto;
        }
        .tingkatan-link {
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: background-color 0.3s;
        }
        .tingkatan-link:hover {
            background-color: #e2e6ea;
        }
    </style>
</head>
<body>
  <!-- Navbar Fixed-Top -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center ms-1" href="index.php">
        <img src="aset/logo.png" alt="Logo Sukarobot Academy" width="40" height="40" class="me-1" />
        Sukarobot Academy
      </a>
      <div class="d-flex align-items-center ms-auto">
        <a href="index.php" class="btn btn-light btn-sm me-3">Kembali ke Dashboard</a>
        <span class="navbar-text me-3 text-white">
        Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h5>Kategori Alat</h5>
    <?php foreach (array_keys($alatByTingkatan) as $tingkatan) : ?>
      <div class="tingkatan-link" data-tingkatan="<?= htmlspecialchars($tingkatan) ?>">
        <?= htmlspecialchars($tingkatan) ?>
      </div>
    <?php endforeach; ?>
    <div class="tingkatan-link" data-tingkatan="all">Semua</div>
  </div>


    <div class="container" style="margin-left: 220px;">
        <div class="header-buttons">
            <h1 class="mb-0">Data Alat</h1>
            <?php if ($isAdmin) : ?>
                <div>
                    <a href="create.php" class="btn btn-primary me-2">Tambah Data Alat</a>
                    <a href="download_alat.php" class="btn btn-success">Download Data Alat</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($successMsg) : ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>
        <?php if ($errorMsg) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php foreach ($alatByTingkatan as $tingkatan => $alatList) : ?>
            <h2 class="tingkatan-header" data-tingkatan="<?= htmlspecialchars($tingkatan) ?>" style="display: none;"><?= htmlspecialchars($tingkatan) ?></h2>
            <?php if (count($alatList) > 0) : ?>
                <div class="table-responsive tingkatan-table" data-tingkatan="<?= htmlspecialchars($tingkatan) ?>" style="display: none;">
                    <table class="table table-bordered table-hover table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Nama Alat</th>
                                <th>Tingkatan Alat</th>
                                <th>Jumlah Alat</th>
                                <th>Tanggal Input</th>
                                <?php if ($isAdmin) : ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alatList as $alat) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($alat['nama_alat']) ?></td>
                                    <td><?= htmlspecialchars($alat['tingkatan_alat']) ?></td>
                                    <td><?= htmlspecialchars($alat['jumlah_alat']) ?></td>
                                    <td><?= htmlspecialchars($alat['tanggal_input']) ?></td>
                                    <?php if ($isAdmin) : ?>
                                        <td>
                                            <a href="edit.php?id=<?= $alat['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="delete.php?id=<?= $alat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data alat ini?');">Hapus</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p>Tidak ada data alat yang tersedia untuk tingkatan <?= htmlspecialchars($tingkatan) ?>.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.tingkatan-link').forEach(link => {
            link.addEventListener('click', function() {
                const selectedTingkatan = this.dataset.tingkatan;

                // Sembunyikan semua tabel dan header
                document.querySelectorAll('.tingkatan-table').forEach(table => {
                    table.style.display = 'none';
                });
                document.querySelectorAll('.tingkatan-header').forEach(header => {
                    header.style.display = 'none';
                });

                // Tampilkan tabel yang sesuai dengan tingkatan yang dipilih
                if (selectedTingkatan === 'all') {
                    document.querySelectorAll('.tingkatan-table').forEach(table => {
                        table.style.display = 'block';
                    });
                    document.querySelectorAll('.tingkatan-header').forEach(header => {
                        header.style.display = 'block';
                    });
                } else {
                    const targetTable = document.querySelector(`.tingkatan-table[data-tingkatan="${selectedTingkatan}"]`);
                    const targetHeader = document.querySelector(`.tingkatan-header[data-tingkatan="${selectedTingkatan}"]`);
                    if (targetTable) {
                        targetTable.style.display = 'block'; // Tampilkan tabel yang sesuai
                    }
                    if (targetHeader) {
                        targetHeader.style.display = 'block'; // Tampilkan header yang sesuai
                    }
                }
            });
        });

        // Tampilkan semua tabel secara default
        document.querySelector('.tingkatan-link[data-tingkatan="all"]').click();
    </script>
</body>
</html>
