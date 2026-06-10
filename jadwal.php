<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['id_siswa'])) { header('Location: login.php'); exit; }

$id_siswa = $_SESSION['id_siswa'];
$error    = '';

// Tambah jadwal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $hari       = $_POST['hari'] ?? '';
    $mapel      = trim($_POST['mata_pelajaran'] ?? '');
    $jam_mulai  = $_POST['jam_mulai'] ?? '';
    $jam_selesai= $_POST['jam_selesai'] ?? '';

    if ($hari && $mapel && $jam_mulai && $jam_selesai) {
        $stmt = $pdo->prepare("INSERT INTO jadwal (id_siswa, hari, mata_pelajaran, jam_mulai, jam_selesai) VALUES (?,?,?,?,?)");
        $stmt->execute([$id_siswa, $hari, $mapel, $jam_mulai, $jam_selesai]);
    }
    header('Location: jadwal.php'); exit;
}

// Hapus jadwal
if (isset($_GET['hapus'])) {
    $jid = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM jadwal WHERE id_jadwal=? AND id_siswa=?")->execute([$jid, $id_siswa]);
    header('Location: jadwal.php'); exit;
}

// Ambil jadwal urut hari
$urut = ['Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6,'Minggu'=>7];
$stmt = $pdo->prepare("SELECT * FROM jadwal WHERE id_siswa=? ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), jam_mulai");
$stmt->execute([$id_siswa]);
$jadwalList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$showForm = isset($_GET['tambah']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="home-page">
        <h1 class="page-title" style="text-align:left;">Jadwal</h1>

        <?php if (!$showForm): ?>
        <a href="?tambah=1" class="btn btn-primary" style="margin-bottom:18px;">+ Tambah Jadwal</a>
        <?php else: ?>
        <div style="background:#fff; border-radius:14px; padding:18px; margin-bottom:18px; box-shadow:0 2px 10px rgba(0,0,0,0.07);">
            <h3 style="font-size:1rem; font-weight:800; color:#222; margin-bottom:14px;">Tambah Jadwal</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Hari</label>
                    <select name="hari">
                        <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h): ?>
                        <option value="<?= $h ?>"><?= $h ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <input type="text" name="mata_pelajaran" placeholder="Nama mata pelajaran">
                </div>
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai">
                </div>
                <div class="form-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jam_selesai">
                </div>
                <button type="submit" name="tambah" class="btn btn-primary" style="margin-bottom:8px;">SIMPAN</button>
                <a href="jadwal.php" class="btn" style="background:#e4e4ea; color:#555; display:block; text-align:center; padding:12px; border-radius:14px; font-weight:700; text-decoration:none;">Batal</a>
            </form>
        </div>
        <?php endif; ?>

        <?php if (empty($jadwalList)): ?>
            <p style="color:#aaa; font-size:0.9rem; text-align:center; padding:20px 0;">Belum ada jadwal.</p>
        <?php else: ?>
            <?php $hariSekarang = ''; foreach ($jadwalList as $j): ?>
                <?php if ($j['hari'] !== $hariSekarang): $hariSekarang = $j['hari']; ?>
                    <div style="font-weight:800; color:#1a1aff; font-size:0.85rem; margin:14px 0 8px; text-transform:uppercase; letter-spacing:0.5px;">
                        <?= $j['hari'] ?>
                    </div>
                <?php endif; ?>
                <div class="jadwal-item" style="display:flex; align-items:center; gap:12px;">
                    <div style="flex:1;">
                        <div class="jadwal-mapel"><?= htmlspecialchars($j['mata_pelajaran']) ?></div>
                        <div class="jadwal-jam"><?= substr($j['jam_mulai'],0,5) ?> - <?= substr($j['jam_selesai'],0,5) ?></div>
                    </div>
                    <a href="?hapus=<?= $j['id_jadwal'] ?>" onclick="return confirm('Hapus jadwal ini?')"
                       style="color:#e74c3c; font-size:1.1rem; text-decoration:none;">🗑️</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <nav class="bottom-nav">
        <a href="home.php" class="nav-item"><span>🏠</span>Home</a>
        <a href="jadwal.php" class="nav-item active"><span>📅</span>Jadwal</a>
        <a href="profil.php" class="nav-item"><span>👤</span>Profil</a>
    </nav>
</div>
</body>
</html>
