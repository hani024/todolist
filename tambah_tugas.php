<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['id_siswa'])) { header('Location: login.php'); exit; }

$id_siswa = $_SESSION['id_siswa'];
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul_tugas'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $deadline  = $_POST['deadline'] ?? '';
    $prioritas = $_POST['prioritas'] ?? 'Sedang';

    if (empty($judul)) {
        $error = 'Judul tugas wajib diisi.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO tugas (id_siswa, judul_tugas, deskripsi, deadline, prioritas, status) VALUES (?,?,?,?,'$prioritas','Belum')");
        $stmt->execute([$id_siswa, $judul, $deskripsi, $deadline ?: null]);

        $id_tugas = $pdo->lastInsertId();

        // Buat notifikasi otomatis
        if ($deadline) {
            $pesan = "Tugas \"$judul\" deadline " . date('d/m/Y', strtotime($deadline));
            $n = $pdo->prepare("INSERT INTO notifikasi (id_tugas, pesan) VALUES (?,?)");
            $n->execute([$id_tugas, $pesan]);
        }

        header('Location: home.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="form-page">
        <a href="home.php" class="back-btn">←</a>
        <h1 class="page-title">Tambah Tugas</h1>

        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Judul Tugas</label>
                <input type="text" name="judul_tugas" placeholder="Masukkan judul tugas">
            </div>
            <div class="form-group">
                <label>Deskripsi (Opsional)</label>
                <textarea name="deskripsi" placeholder="Masukkan deskripsi"></textarea>
            </div>
            <div class="form-group">
                <label style="color:#1a1aff; text-decoration:underline;">Prioritas</label>
                <select name="prioritas">
                    <option value="Rendah">Rendah</option>
                    <option value="Sedang" selected>Sedang</option>
                    <option value="Tinggi">Tinggi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline" min="<?= date('Y-m-d') ?>">

            </div>
            <button type="submit" class="btn btn-primary">SIMPAN</button>
        </form>
    </div>
</div>
</body>
</html>
