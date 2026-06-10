<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['id_siswa'])) { header('Location: login.php'); exit; }

$id_siswa = $_SESSION['id_siswa'];
$id_tugas = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM tugas WHERE id_tugas=? AND id_siswa=?");
$stmt->execute([$id_tugas, $id_siswa]);
$tugas = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tugas) { header('Location: home.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $pdo->prepare("DELETE FROM tugas WHERE id_tugas=? AND id_siswa=?")->execute([$id_tugas, $id_siswa]);
    header('Location: home.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $judul     = trim($_POST['judul_tugas'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $deadline  = $_POST['deadline'] ?? '';
    $prioritas = $_POST['prioritas'] ?? 'Sedang';
    $status    = $_POST['status'] ?? 'Belum';

    if (empty($judul)) { $error = 'Judul wajib diisi.'; }
    else {
        $stmt = $pdo->prepare("UPDATE tugas SET judul_tugas=?,deskripsi=?,deadline=?,prioritas=?,status=? WHERE id_tugas=? AND id_siswa=?");
        $stmt->execute([$judul, $deskripsi, $deadline ?: null, $prioritas, $status, $id_tugas, $id_siswa]);
        header('Location: home.php'); exit;
    }
    $tugas = array_merge($tugas, compact('judul_tugas', 'deskripsi', 'deadline', 'prioritas', 'status'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="form-page">
        <a href="home.php" class="back-btn">←</a>
        <h1 class="page-title">Edit Tugas</h1>

        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Judul Tugas</label>
                <input type="text" name="judul_tugas" value="<?= htmlspecialchars($tugas['judul_tugas']) ?>">
            </div>
            <div class="form-group">
                <label>Deskripsi (Opsional)</label>
                <textarea name="deskripsi"><?= htmlspecialchars($tugas['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Prioritas</label>
                <select name="prioritas">
                    <?php foreach (['Rendah','Sedang','Tinggi'] as $p): ?>
                    <option value="<?= $p ?>" <?= $tugas['prioritas']===$p?'selected':'' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline" value="<?= htmlspecialchars($tugas['deadline'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Belum"   <?= $tugas['status']==='Belum'?'selected':'' ?>>Belum</option>
                    <option value="Selesai" <?= $tugas['status']==='Selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary" style="margin-bottom:12px;">SIMPAN PERUBAHAN</button>
            <button type="submit" name="hapus" onclick="return confirm('Yakin hapus?')" class="btn btn-danger">HAPUS TUGAS</button>
        </form>
    </div>
</div>
</body>
</html>
