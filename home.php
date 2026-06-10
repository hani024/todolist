<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['id_siswa'])) { header('Location: login.php'); exit; }

$id_siswa   = $_SESSION['id_siswa'];
$nama_siswa = $_SESSION['nama_siswa'];

// Quick add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_add'])) {
    $judul = trim($_POST['judul_quick'] ?? '');
    if (!empty($judul)) {
        $stmt = $pdo->prepare("INSERT INTO tugas (id_siswa, judul_tugas, deadline, prioritas, status) VALUES (?,?,CURDATE(),'Sedang','Belum')");
        $stmt->execute([$id_siswa, $judul]);
    }
    header('Location: home.php'); exit;
}

// Toggle status
if (isset($_GET['toggle'])) {
    $tid  = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE tugas SET status = IF(status='Belum','Selesai','Belum') WHERE id_tugas=? AND id_siswa=?");
    $stmt->execute([$tid, $id_siswa]);
    header('Location: home.php'); exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $tid  = (int)$_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM tugas WHERE id_tugas=? AND id_siswa=?");
    $stmt->execute([$tid, $id_siswa]);
    header('Location: home.php'); exit;
}

// Ambil tugas
$stmt = $pdo->prepare("SELECT * FROM tugas WHERE id_siswa=? ORDER BY status ASC, deadline ASC");
$stmt->execute([$id_siswa]);
$tugasList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Notifikasi (tugas belum selesai & deadline hari ini / lewat)
$stmt = $pdo->prepare("
SELECT *
FROM tugas
WHERE id_siswa=?
AND status='Belum'
AND deadline <= CURDATE()
ORDER BY deadline ASC
");

$stmt->execute([$id_siswa]);
$notifList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$jumlah_notif = count($notifList);
$jumlah_notif = count($notifList);
$badgeClass   = ['Tinggi' => 'badge-Tinggi', 'Sedang' => 'badge-Sedang', 'Rendah' => 'badge-Rendah'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Overlay -->
<div id="overlay" class="overlay" onclick="closeSidebar()"></div>
<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        👋 Halo, <?= htmlspecialchars($nama_siswa) ?>
    </div>
    <a href="home.php">🏠 Home</a>
    <a href="jadwal.php">📅 Jadwal</a>
    <a href="profil.php">👤 Profil</a>
    <a href="logout.php">🚪 Logout</a>
</div>
<div class="container">
    <div class="home-page">
        <!-- Top Bar -->
       <div class="top-bar">
    <span class="hamburger" onclick="toggleSidebar()">
        ☰
    </span>
            
            <div class="notif-wrap">
                <div class="notif-icon" onclick="toggleNotif()">
                    🔔
                    <?php if ($jumlah_notif > 0): ?>
                        <span class="notif-badge"><?= $jumlah_notif ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($jumlah_notif > 0): ?>
                    <span class="notif-label"><?= $jumlah_notif ?> aktif</span>
                <?php endif; ?>
            </div>
        </div>

        <h1 class="page-title" style="text-align:left; margin-bottom:4px;">To Do List</h1>
        <p class="greeting">Halo, <?= htmlspecialchars($nama_siswa) ?>!</p>

        <!-- Notifikasi Box -->
    <?php if($jumlah_notif > 0): ?>
<div class="notif-box" id="notifBox">
    <h3>🔔 Deadline Tugas</h3>
    <?php foreach($notifList as $n): ?>
    <div class="notif-item">
        <strong>
            <?= htmlspecialchars($n['judul_tugas']) ?>
        </strong>
        <br>
        Deadline:
        <?= date('d/m/Y', strtotime($n['deadline'])) ?>
    </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

        <!-- Quick Add -->
        <form method="POST">
            <div class="quick-add">
                <input type="text" name="judul_quick" placeholder="Tambah tugas cepat...">
                <button type="submit" name="quick_add" class="quick-add-btn">+</button>
            </div>
        </form>

        <!-- Daftar Tugas -->
        <div class="section-title">Daftar Tugas</div>
        <?php if (empty($tugasList)): ?>
            <p style="color:#aaa; font-size:0.9rem; text-align:center; padding:20px 0;">Belum ada tugas.</p>
        <?php else: ?>
            <?php foreach ($tugasList as $t): ?>
            <div class="task-item">
                <input type="checkbox" class="task-checkbox"
                    <?= $t['status'] === 'Selesai' ? 'checked' : '' ?>
                    onclick="window.location='?toggle=<?= $t['id_tugas'] ?>'">
                <div style="flex:1;">
                    <a href="edit_tugas.php?id=<?= $t['id_tugas'] ?>"
                       class="task-name <?= $t['status'] === 'Selesai' ? 'done' : '' ?>">
                        <?= htmlspecialchars($t['judul_tugas']) ?>
                        <span class="badge <?= $badgeClass[$t['prioritas']] ?? '' ?>"><?= $t['prioritas'] ?></span>
                    </a>
                    <?php if ($t['deadline']): ?>
                    <div class="task-meta">📅 <?= date('d/m/Y', strtotime($t['deadline'])) ?></div>
                    <?php endif; ?>
                </div>
                <a href="deadline.php?id=<?= $t['id_tugas'] ?>" class="task-deadline"></a>
                <a href="edit_tugas.php?id=<?= $t['id_tugas'] ?>" class="task-edit">✏️</a>
                <a href="?hapus=<?= $t['id_tugas'] ?>" onclick="return confirm('Hapus tugas ini?')" class="task-delete">🗑️</a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="tambah_tugas.php" class="btn btn-primary tambah-btn">+ Tambah Tugas</a>
    </div>

    <nav class="bottom-nav">
        <a href="home.php" class="nav-item active"><span>🏠</span>Home</a>
        <a href="jadwal.php" class="nav-item"><span>📅</span>Jadwal</a>
        <a href="profil.php" class="nav-item"><span>👤</span>Profil</a>
    </nav>
</div>
<script>
function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.getElementById("overlay");

    sidebar.classList.toggle("active");
    overlay.classList.toggle("show");
}

function closeSidebar() {
    document.querySelector(".sidebar").classList.remove("active");
    document.getElementById("overlay").classList.remove("show");
}
</script>
</body>
</html>
