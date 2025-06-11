<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

// Define pertemuan counts for each tingkatan
$kebutuhanPertemuan = [
    'Beginner 1' => 16,
    'Beginner 2' => 16,
    'Basic 1 Junior' => 16,
    'Basic 1 Senior' => 16,
    'Basic 2' => 24,
    'Basic 3' => 24,
    'Intermediate' => 24,
    'Advance' => 24,
];




function getAlat($pdo) {
    $sqlBasic1Junior = "SELECT nama_alat FROM alat WHERE tingkatan_alat = 'Huna'";
    $stmtBasic1Junior = $pdo->query($sqlBasic1Junior);
    $resultsBasic1Junior = $stmtBasic1Junior->fetchAll(PDO::FETCH_COLUMN);

    $sqlBasic1Senior = "SELECT nama_alat FROM alat WHERE tingkatan_alat = 'Huna'";
    $stmtBasic1Senior = $pdo->query($sqlBasic1Senior);
    $resultsBasic1Senior = $stmtBasic1Senior->fetchAll(PDO::FETCH_COLUMN);

    $sqlBasic2 = "SELECT nama_alat FROM alat WHERE tingkatan_alat = 'Huna'";
    $stmtBasic2 = $pdo->query($sqlBasic2);
    $resultsBasic2 = $stmtBasic2->fetchAll(PDO::FETCH_COLUMN);

    $sqlBasic3 = "SELECT nama_alat FROM alat WHERE tingkatan_alat = 'Basic 3'";
    $stmtBasic3 = $pdo->query($sqlBasic3);
    $resultsBasic3 = $stmtBasic3->fetchAll(PDO::FETCH_COLUMN);

    $sqlIntermediate = "SELECT nama_alat FROM alat WHERE tingkatan_alat IN ('Basic 3', 'Intermediate')";
    $stmtIntermediate = $pdo->query($sqlIntermediate);
    $resultsIntermediate = $stmtIntermediate->fetchAll(PDO::FETCH_COLUMN);

    $sqlAdvance = "SELECT nama_alat FROM alat WHERE tingkatan_alat IN ('Advance', 'Intermediate')";
    $stmtAdvance = $pdo->query($sqlAdvance);
    $resultsAdvance = $stmtAdvance->fetchAll(PDO::FETCH_COLUMN);

    $result = [
        'Basic1Junior' => [],
        'Basic1Senior' => [],
        'Basic2' => [],
        'Basic3' => [],
        'Intermediate' => [],
        'Advance' => []
    ];

    for ($i = 1; $i <= 16; $i++) {
        $result['Basic1Senior'][$i] = $resultsBasic1Junior;
    }

    for ($i = 1; $i <= 16; $i++) {
        $result['Basic1Senior'][$i] = $resultsBasic1Senior;
    }

    for ($i = 1; $i <= 24; $i++) {
        $result['Basic2'][$i] = $resultsBasic2;
    }
    for ($i = 1; $i <= 24; $i++) {
        $result['Basic3'][$i] = $resultsBasic3;
    }

    for ($i = 1; $i <= 24; $i++) {
        $result['Intermediate'][$i] = $resultsIntermediate;
    }

    for ($i = 1; $i <= 24; $i++) {
          $result['Advance'][$i] = $resultsAdvance;
    }
    return $result;
}

$alatRequirements = getAlat($pdo);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Kebutuhan Alat - Sukarobot Academy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .header-top-right {
      position: fixed;
      top: 10px;
      right: 20px;
      font-weight: 700;
      font-size: 1.2rem;
      color: #0d6efd;
      z-index: 1050;
    }
    .sidebar {
      width: 280px;
      background-color:rgb(13, 110, 253);
      border-right: 1px solid #ddd;
      padding: 1rem;
      height: 100vh;
      box-sizing: border-box;
      position: fixed;
      top: 0px;
      left: 0;
      overflow-y: auto;
    }
    .sidebar h5 {
      font-weight: bold;
      color:rgb(255, 255, 255);
      margin-bottom: 1rem;
      margin-top: 1rem;
    }
    .sidebar .tingkatan-link {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
      padding: 10px 15px;
      border-radius: 8px;
      background-color:rgb(255, 255, 255);
      color: #000;
      font-weight: 600;
      cursor: pointer;
      user-select: none;
      transition: background-color 0.2s ease, color 0.2s ease;
    }
    .sidebar .tingkatan-link:hover, .sidebar .tingkatan-link.active {
      background-color:rgb(0, 0, 0);
      color: white;
      text-decoration: none;
    }
    .sidebar .tingkatan-link img {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }
    .main-content {
      margin-left: 280px;
      padding: 20px;
    }
    .pertemuan-list {
      list-style: none;
      padding-left: 0;
      max-width: 600px;
    }
    .pertemuan-item {
      cursor: pointer;
      padding: 10px 15px;
      margin-bottom: 8px;
      background-color: #e9ecef;
      border-radius: 8px;
      display: flex;
      align-items: center;
      font-weight: 600;
      color: #000;
      user-select: none;
      transition: background-color 0.2s ease;
      text-decoration: none;
    }
    .pertemuan-item:hover {
      background-color: #0d6efd;
      color: white;
      text-decoration: none;
    }
    .alat-list {
      margin-top: 1rem;
      max-width: 600px;
    }
    .alat-item {
      margin-bottom: 0.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .alat-name {
      font-weight: 500;
    }
    .alat-actions input[type="number"] {
      width: 70px;
      margin-right: 8px;
    }
    .btn-group-bottom {
      margin-top: 10px;
      text-align: center;
    }
    .btn-group-bottom button {
      margin: 0 5px;
      min-width: 120px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4" style="margin-left:280px;">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="aset/logo.png" alt="Logo Sukarobot Academy" width="40" height="40" class="me-2" />
      Sukarobot Academy
    </a>
    <div class="d-flex align-items-center ms-auto">
      <button class="btn btn-light btn-sm me-3" id="btnAddTingkatan" data-bs-toggle="modal" data-bs-target="#addTingkatanModal">Tambah Tingkatan</button>
      <a href="index.php" class="btn btn-light btn-sm me-3">Kembali ke Dashboard</a>
      <span class="navbar-text me-3">Halo, <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)</span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="sidebar" id="sidebar">
  <h5>Kebutuhan alat per level</h5>
  <?php foreach ($kebutuhanPertemuan as $tingkatan => $count): ?>
    <div class="tingkatan-link" data-tingkatan="<?= htmlspecialchars($tingkatan) ?>">
      <span><?= htmlspecialchars($tingkatan) ?></span>
    </div>
  <?php endforeach; ?>
</div>

<div class="main-content">
  <h3 id="selectedTingkatan">   </h3>
  <ul id="pertemuanList" class="pertemuan-list"></ul>
</div>

<!-- Modal for alat kebutuhan -->
<div class="modal fade" id="alatModal" tabindex="-1" aria-labelledby="alatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alatModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body d-flex flex-column">
        <div class="input-group mb-3">
          <input type="text" id="searchAlat" class="form-control" placeholder="Cari alat..." />
          <button class="btn btn-primary" id="btnSearchAlat" type="button">Cari</button>
        </div>
        <div class="mb-3">
          <label for="trainerSelect" class="form-label">Pilih Trainer</label>
          <select class="form-select" id="trainerSelect" required>
            <option value="">Pilih Trainer</option>
            <!-- Daftar trainer akan diisi di sini -->
          </select>
        </div>
        <div class="mb-3">
          <label for="projectName" class="form-label">Nama Project</label>
          <input type="text" class="form-control" id="projectName" placeholder="Masukkan nama proyek" required>
        </div>
        <div class="mb-3">
          <label for="teachingLocation" class="form-label">Lokasi Ngajar</label>
          <input type="text" class="form-control" id="teachingLocation" placeholder="Masukkan lokasi pengajaran" required>
        </div>
        <div id="modalAlatBody"></div>
      </div>
      <div class="modal-footer">
        <div class="btn-group-bottom">
          <button type="button" class="btn btn-success" id="btnAmbilSemua">Ambil</button>
          <button type="button" class="btn btn-warning" id="btnKembalikanSemua">Kembalikan</button>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const kebutuhanPertemuan = <?php echo json_encode($kebutuhanPertemuan); ?>;
const alatRequirements = <?php echo json_encode($alatRequirements); ?>;

const tingkatanLinks = document.querySelectorAll('.tingkatan-link');
const tingkatanTitle = document.getElementById('selectedTingkatan');
const pertemuanList = document.getElementById('pertemuanList');
const alatModal = new bootstrap.Modal(document.getElementById('alatModal'));
const modalAlatLabel = document.getElementById('alatModalLabel');
const modalAlatBody = document.getElementById('modalAlatBody');
const btnAmbilSemua = document.getElementById('btnAmbilSemua');
const btnKembalikanSemua = document.getElementById('btnKembalikanSemua');
const searchInput = document.getElementById('searchAlat');
const btnSearchAlat = document.getElementById('btnSearchAlat');

let currentTingkatan = null;
let currentAlatInputs = [];
let currentAlatList = [];
let originalAlatList = [];

const borrowListKey = 'borrowedAlat';

function loadBorrowList() {
  let data = localStorage.getItem(borrowListKey);
  return data ? JSON.parse(data) : {};
}

function saveBorrowList(borrowList) {
  localStorage.setItem(borrowListKey, JSON.stringify(borrowList));
}

function addToBorrowList(alat, qty) {
  let borrowList = loadBorrowList();
  if(borrowList[alat]){
    borrowList[alat] += qty;
  } else {
    borrowList[alat] = qty;
  }
  saveBorrowList(borrowList);
}

function removeFromBorrowList(alat) {
  let borrowList = loadBorrowList();
  if(borrowList[alat]) {
    delete borrowList[alat];
    saveBorrowList(borrowList);
  }
}

function renderBorrowList() {
  const borrowListContainer = document.getElementById('borrowListContainer');
  if(!borrowListContainer) return;
  
  let borrowList = loadBorrowList();
  borrowListContainer.innerHTML = '';
  if(Object.keys(borrowList).length === 0) {
    borrowListContainer.innerHTML = '<p>Belum ada alat di daftar pinjaman.</p>';
    return;
  }
  let ul = document.createElement('ul');
  ul.classList.add('list-group');
  for(const [alat, qty] of Object.entries(borrowList)) {
    let li = document.createElement('li');
    li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
    li.textContent = alat;
    let span = document.createElement('span');
    span.classList.add('badge', 'bg-primary', 'rounded-pill');
    span.textContent = qty;
    li.appendChild(span);
    ul.appendChild(li);
  }
  borrowListContainer.appendChild(ul);
}

tingkatanLinks.forEach(link => {
  link.addEventListener('click', () => {
    tingkatanLinks.forEach(l => l.classList.remove('active'));
    link.classList.add('active');

    currentTingkatan = link.dataset.tingkatan;
    tingkatanTitle.textContent = currentTingkatan;

    const count = kebutuhanPertemuan[currentTingkatan] || 0;
    pertemuanList.innerHTML = '';

    for(let i=1; i<=count; i++) {
      const li = document.createElement('li');
      li.classList.add('pertemuan-item');
      li.textContent = 'Pertemuan ke-'+i;
      li.dataset.pertemuan = i;
      li.style.userSelect = 'none';
      li.addEventListener('click', () => openAlatModal(i));
      pertemuanList.appendChild(li);
    }
  });
});

function renderAlatList(list) {
  modalAlatBody.innerHTML = '';

  if (list.length === 0) {
    modalAlatBody.innerHTML = "<p>Tidak ada data alat tersedia untuk pertemuan ini.</p>";
    return;
  }

  const ul = document.createElement('ul');
  ul.classList.add('list-group');

  list.forEach(alat => {
    const li = document.createElement('li');
    li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');

    const spanName = document.createElement('span');
    spanName.textContent = alat;

    const inputJumlah = document.createElement('input');
    inputJumlah.type = 'number';
    inputJumlah.min = '1';
    inputJumlah.placeholder = 'Jumlah';
    inputJumlah.style.width = '70px';

    const btnAdd = document.createElement('button');
    btnAdd.type = 'button';
    btnAdd.textContent = 'Tambah ke Daftar';
    btnAdd.classList.add('btn', 'btn-success', 'btn-sm', 'ms-2');
    btnAdd.addEventListener('click', () => {
      const qty = parseInt(inputJumlah.value);
      if(!qty || qty <= 0) {
        alert('Masukkan jumlah valid!');
        return;
      }
      addToBorrowList(alat, qty);
      renderBorrowList();
    });

    const controlsDiv = document.createElement('div');
    controlsDiv.appendChild(inputJumlah);
    controlsDiv.appendChild(btnAdd);

    li.appendChild(spanName);
    li.appendChild(controlsDiv);
    ul.appendChild(li);
  });

  const borrowListContainer = document.createElement('div');
  borrowListContainer.id = 'borrowListContainer';
  borrowListContainer.classList.add('mt-4');

  modalAlatBody.appendChild(ul);
  modalAlatBody.appendChild(borrowListContainer);

  renderBorrowList();
}

function openAlatModal(pertemuan) {
  modalAlatLabel.textContent = `Alat yang diperlukan ${currentTingkatan} pertemuan ${pertemuan} yaitu:`;
  searchInput.value = '';

  const tKey = currentTingkatan.replace(/\s+/g,'');
  currentAlatList = alatRequirements[tKey]?.[pertemuan] || [];

  // Simpan daftar alat asli supaya search selalu filter dari daftar lengkap
  originalAlatList = [...currentAlatList];

  renderAlatList(currentAlatList);

  // Ambil daftar trainer
  fetch('data_trainer.php')
    .then(response => response.text())
    .then(data => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(data, 'text/html');
      const trainerNames = Array.from(doc.querySelectorAll('tr td:nth-child(2)')).map(td => td.textContent.trim());
      window.trainerNames = trainerNames; // Simpan daftar trainer di global

      const trainerSelect = document.getElementById('trainerSelect');
      trainerSelect.innerHTML = '<option value="">Pilih Trainer</option>'; // Reset dropdown
      trainerNames.forEach(name => {
        const option = document.createElement('option');
        option.value = name;
        option.textContent = name;
        trainerSelect.appendChild(option);
      });
      console.log(trainerNames)
    });

  alatModal.show();
}



// Fitur pencarian dengan tombol "Cari"
btnSearchAlat.addEventListener('click', () => {
  const keyword = searchInput.value.toLowerCase().trim();
  const filtered = originalAlatList.filter(alat => alat.toLowerCase().includes(keyword));
  renderAlatList(filtered);
});



btnAmbilSemua.addEventListener('click', () => {
  const borrowList = loadBorrowList();
  const trainerName = document.getElementById('trainerSelect').value.trim();
  const projectName = document.getElementById('projectName').value.trim();
  const teachingLocation = document.getElementById('teachingLocation').value.trim();

  if (!trainerName) {
    alert('Nama trainer harus diisi.');
    return;
  }

  if (!projectName) {
    alert('Nama proyek harus diisi.');
    return;
  }

  if (!teachingLocation) {
    alert('Lokasi pengajaran harus diisi.');
    return;
  }

  if (Object.keys(borrowList).length === 0) {
    alert('Daftar pinjaman masih kosong.');
    return;
  }
  // Kirim semua data borrowList ke server
  fetch('update_alat.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      action: 'ambil_semua',
      daftar: borrowList,
      trainer: trainerName,
      project: projectName,
      location: teachingLocation,
      currentTingkatan: currentTingkatan
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Berhasil meminjam semua alat.');
      localStorage.removeItem(borrowListKey);
      renderBorrowList();
      if (typeof alatModal !== 'undefined') {
        alatModal.hide();
      }
    } else {
      alert('Gagal meminjam alat: ' + (data.message || 'Terjadi kesalahan.'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan komunikasi dengan server.');
  });
});



// Similarly for “Kembalikan Semua” button, adjust accordingly
btnKembalikanSemua.addEventListener('click', () => {
  const borrowList = loadBorrowList();
  const trainerName = document.getElementById('trainerSelect').value.trim();
  const projectName = document.getElementById('projectName')?.value.trim() || '';
  const teachingLocation = document.getElementById('teachingLocation')?.value.trim() || '';
  const currentTingkatan = window.currentTingkatan || '';

  if (!trainerName) {
    alert('Nama trainer harus diisi.');
    return;
  }

  if (!window.trainerNames.includes(trainerName)) {
    alert('Nama trainer tidak valid. Silakan masukkan nama trainer yang terdaftar.');
    return;
  }

  if (Object.keys(borrowList).length === 0) {
    alert('Daftar pengembalian masih kosong.');
    return;
  }

  fetch('update_alat.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'kembalikan_semua',
      daftar: borrowList,
      trainer: trainerName,
      project: projectName,
      location: teachingLocation,
      currentTingkatan: currentTingkatan
    })
  })
  .then(async res => {
    const text = await res.text();
    try {
      const data = JSON.parse(text);
      if (data.success) {
        alert('Berhasil mengembalikan semua alat.');
        localStorage.removeItem(borrowListKey);
        renderBorrowList();
        if (typeof alatModal !== 'undefined') {
          alatModal.hide();
        }
      } else {
        alert('Gagal mengembalikan alat: ' + (data.message || 'Terjadi kesalahan.'));
      }
    } catch (err) {
      console.error('Respon server bukan JSON:', text);
      alert('Terjadi kesalahan komunikasi (respon tidak valid).');
    }
  })
  .catch(error => {
    console.error('Fetch error:', error);
    alert('Terjadi kesalahan komunikasi.');
  });
});



</script>
</body>
</html>




