<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['id_siswa'])) { header('Location: login.php'); exit; }

$id_siswa = $_SESSION['id_siswa'];
$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa=?");
$stmt->execute([$id_siswa]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

// Hitung total tugas
$stmtTotal = $pdo->prepare("SELECT COUNT(*) as total, SUM(status='Selesai') as selesai FROM tugas WHERE id_siswa=?");
$stmtTotal->execute([$id_siswa]);
$stat = $stmtTotal->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="profil-page">
        <h2 style="font-family:'Fredoka One',cursive; color:#1a1aff; font-size:1.5rem; margin-bottom:4px;">Profil</h2>

        <div class="profil-avatar">
            <div class="avatar-circle">👤</div>
            <div class="profil-name"><?= htmlspecialchars($siswa['nama_siswa']) ?></div>
            <div class="profil-email"><?= htmlspecialchars($siswa['email']) ?></div>
        </div>

        <div class="profil-info">
            <div class="info-row">
                <span class="info-label">Nama</span>
                <span class="info-value"><?= htmlspecialchars($siswa['nama_siswa']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelas</span>
                <span class="info-value"><?= htmlspecialchars($siswa['kelas'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?= htmlspecialchars($siswa['email']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Tugas</span>
                <span class="info-value"><?= $stat['total'] ?? 0 ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tugas Selesai</span>
                <span class="info-value" style="color:#27ae60;"><?= $stat['selesai'] ?? 0 ?></span>
            </div>
        </div>

        <a href="logout.php" class="btn" style="background:#e74c3c; color:white; border-radius:30px; letter-spacing:1px;">
            LOGOUT
        </a>
    </div>

    <nav class="bottom-nav">
        <a href="home.php" class="nav-item"><span>🏠</span>Home</a>
        <a href="jadwal.php" class="nav-item"><span>📅</span>Jadwal</a>
        <a href="profil.php" class="nav-item active"><span>👤</span>Profil</a>
    </nav>
</div>
</body>
</html>
