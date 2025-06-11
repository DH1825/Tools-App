<?php
require_once 'config.php';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = null;

$users = [
    'Admin' => [ 'password' => 'admin', 'role' => 'admin' ],
    'Trainer' => [ 'password' => 'trainer', 'role' => 'trainer' ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username dan password harus diisi.";
    } elseif (!isset($users[$username]) || $users[$username]['password'] !== $password) {
        $error = "Username atau password salah.";
    } else {
        // Login sukses
        $_SESSION['user'] = [
            'username' => $username,
            'role' => $users[$username]['role'],
        ];
        header('Location: index.php');
        exit;
    }
}
?>
<!-- ... bagian PHP tetap sama ... -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login - Sukarobot Academy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(to right, #0d6efd, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 1rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .form-label {
            font-weight: bold;
            color: #333;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .logo-header img {
            width: 100px;
            margin-bottom: 20px;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            text-align: center;
            width: 100%;
            color: #ffffff;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="logo-header">
        <img src="aset/logo.png" alt="Logo Sukarobot">
    </div>
    <h2>Login Sukarobot Academy</h2>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="login.php" novalidate>
        <div class="mb-3 text-start">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required autofocus value="<?= htmlspecialchars($username ?? '') ?>" />
        </div>
        <div class="mb-3 text-start">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100">Masuk</button>
    </form>
</div>
<div class="footer">
    <p>&copy; 2025 Sukarobot Academy. Created by Dzikri.</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
