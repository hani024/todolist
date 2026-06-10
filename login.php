<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['id_siswa'])) { header('Location: home.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM siswa WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($siswa) {
            $_SESSION['id_siswa']   = $siswa['id_siswa'];
            $_SESSION['nama_siswa'] = $siswa['nama_siswa'];
            $_SESSION['kelas']      = $siswa['kelas'];
            $_SESSION['email']      = $siswa['email'];
            header('Location: home.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - To Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="auth-page">
        <h1 class="page-title">To Do List</h1>
        <p class="auth-subtitle">Selamat Datang!<br>Login Untuk Melanjutkan.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
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
            <button type="submit" class="btn btn-primary">LOGIN</button>
        </form>
        <p class="auth-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</div>
<script>
function togglePw() {
    const i = document.getElementById('pw');
    i.type = i.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
