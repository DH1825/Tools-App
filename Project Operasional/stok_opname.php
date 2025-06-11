<?php
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

$stmt = $pdo->query("SELECT DISTINCT tingkatan_alat FROM alat ORDER BY tingkatan_alat ASC");
$tingkatanList = $stmt->fetchAll(PDO::FETCH_COLUMN);

$selectedTingkatan = $_GET['tingkatan_alat'] ?? $tingkatanList[0];

$stmt = $pdo->prepare("SELECT * FROM alat WHERE tingkatan_alat = ? ORDER BY nama_alat ASC");
$stmt->execute([$selectedTingkatan]);
$alatList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $data = json_decode(file_get_contents('php://input'), true);
    foreach ($data as $item) {
        $alat_id = $item['id'];
        $jumlah_opname = $item['jumlah'];

        if ($jumlah_opname !== '' && is_numeric($jumlah_opname) && $jumlah_opname >= 0) {
            $stmt = $pdo->prepare("
                INSERT INTO stok_opname (nama_alato, jumlah_alat_opname, tanggal_pengecekan)
                VALUES (?, ?, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$alat_id, $jumlah_opname]);

            $stmtUpdate = $pdo->prepare("UPDATE alat SET jumlah_alat = ? WHERE id = ?");
            $stmtUpdate->execute([$jumlah_opname, $alat_id]);
        }
    }
    echo json_encode(['status' => 'success']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stok Opname AJAX - Sukarobot Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .navbar-custom { background-color: #0d6efd !important; }
        .container-wrapper { margin-top: 80px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="aset/logo.png" alt="Logo" width="40" height="40" class="me-1" />
            Sukarobot Academy
        </a>
        <div class="d-flex ms-auto align-items-center">
            <a href="index.php" class="btn btn-light btn-sm me-2">Dashboard</a>
            <span class="text-white me-3">Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container container-wrapper">
    <h2 class="mb-4">Form Input Stok Opname Alat</h2>

    <!-- Filter Tingkatan -->
    <form id="tingkatanForm" method="get" class="mb-4">
        <label for="tingkatanFilter" class="form-label"><strong>Tingkatan Alat:</strong></label>
        <select name="tingkatan_alat" id="tingkatanFilter" class="form-select w-auto d-inline" onchange="changeTingkatan(this)">
            <?php foreach ($tingkatanList as $tingkatan): ?>
                <option value="<?= htmlspecialchars($tingkatan) ?>" <?= $tingkatan === $selectedTingkatan ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ucfirst($tingkatan)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <form id="formOpname">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Alat</th>
                    <th>Tingkatan</th>
                    <th>Jumlah Awal</th>
                    <th>Update Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alatList as $i => $alat): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($alat['nama_alat']) ?></td>
                        <td><?= htmlspecialchars($alat['tingkatan_alat']) ?></td>
                        <td><strong><?= $alat['jumlah_alat'] ?></strong></td>
                        <td>
                            <input type="number" class="form-control opname-input"
                                   name="jumlah_alat_opname"
                                   data-id="<?= $alat['id'] ?>"
                                   data-tingkatan="<?= $alat['tingkatan_alat'] ?>"
                                   placeholder="Input jumlah">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <a href="rekap_pengecekan.php" class="btn btn-primary">Rekap</a>
            <button type="button" onclick="submitOpname()" class="btn btn-success">Simpan Data</button>
        </div>
    </form>
</div>

<script>
const tempData = {}; // Menyimpan inputan berdasarkan tingkatan

// Muat ulang form berdasarkan tingkatan
function changeTingkatan(select) {
    saveCurrentInputs();
    const tingkatan = select.value;
    const url = new URL(window.location.href);
    url.searchParams.set('tingkatan_alat', tingkatan);
    window.location.href = url;
}

// Simpan input saat ini
function saveCurrentInputs() {
    const inputs = document.querySelectorAll('.opname-input');
    const currentTingkatan = document.getElementById('tingkatanFilter').value;
    tempData[currentTingkatan] = [];

    inputs.forEach(input => {
        tempData[currentTingkatan].push({
            id: input.dataset.id,
            jumlah: input.value
        });
    });
}

// Restore input saat load halaman
window.onload = function() {
    const currentTingkatan = document.getElementById('tingkatanFilter').value;
    if (tempData[currentTingkatan]) {
        const inputs = document.querySelectorAll('.opname-input');
        inputs.forEach(input => {
            const match = tempData[currentTingkatan].find(item => item.id === input.dataset.id);
            if (match) input.value = match.jumlah;
        });
    }
}

// Submit menggunakan AJAX
function submitOpname() {
    saveCurrentInputs();
    let allData = [];
    for (const key in tempData) {
        allData = allData.concat(tempData[key]);
    }

    fetch('stok_opname.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(allData.concat({ ajax: 1 }))
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            alert("Data berhasil disimpan!");
            location.reload();
        } else {
            alert("Gagal menyimpan data.");
        }
    });
}
</script>
</body>
</html>
