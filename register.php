<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['id_siswa'])) { header('Location: home.php'); exit; }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama_siswa'] ?? '');
    $kelas    = trim($_POST['kelas'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Nama, email, dan password wajib diisi.';
    } else {
        $cek = $pdo->prepare("SELECT id_siswa FROM siswa WHERE email = ?");
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO siswa (nama_siswa, kelas, email, password) VALUES (?,?,?,?)");
            $stmt->execute([$nama, $kelas, $email, $password]);
            $success = 'Akun berhasil dibuat! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="auth-page">
        <a href="login.php" class="back-btn">←</a>
        <h1 class="page-title">Daftar Akun</h1>
        <p class="auth-subtitle">Buat akun baru untuk mulai.</p>

        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama_siswa" placeholder="Nama lengkap" value="<?= htmlspecialchars($_POST['nama_siswa'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Kelas</label>
                <input type="text" name="kelas" placeholder="Contoh: XI RPL 1" value="<?= htmlspecialchars($_POST['kelas'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-wrap">
                    <input type="password" id="pw" name="password" placeholder="Password">
                    <button type="button" class="toggle-pass" onclick="togglePw()"></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">DAFTAR</button>
        </form>
        <p class="auth-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</div>
<script>function togglePw() { const i = document.getElementById('pw'); i.type = i.type==='password'?'text':'password'; }</script>
</body>
</html>
