<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman: cuma user yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query Cari Buku
$query = "SELECT * FROM buku WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%'";
$result = mysqli_query($conn, $query);

// --- FITUR NOTIFIKASI (Sudah diperbaiki kolomnya) ---
// Cek apakah ada buku yang BARU disetujui hari ini
$cek_notif = mysqli_query($conn, "SELECT b.judul FROM transaksi t 
                                  JOIN buku b ON t.book_id = b.id 
                                  WHERE t.user_id = '$user_id' 
                                  AND t.status = 'approved' 
                                  AND t.tgl_pinjam = CURDATE()");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Cari Buku - Perpus Modern</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="display: block;"> 
    <div class="sidebar">
        <div class="sidebar-header">MENU SISWA</div>
        <div class="sidebar-menu">
            <a href="index.php" class="active"><i class="fas fa-search"></i> Cari Buku</a>
            <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat Pinjam</a>
            <a href="../logout.php" style="margin-top: 50px; color: #e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Halo, <?= $nama_user ?>! 👋</h1>

        <?php if($cek_notif && mysqli_num_rows($cek_notif) > 0): ?>
            <?php while($n = mysqli_fetch_assoc($cek_notif)): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745;">
                    <i class="fas fa-bell"></i> <b>Notifikasi:</b> Peminjaman buku <u><?= $n['judul'] ?></u> sudah <b>DISETUJUI</b>. Silakan ambil di perpustakaan!
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <div class="card">
            <h3>🔍 Cari Koleksi Buku</h3>
            <form method="GET" style="margin-top: 15px; display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Ketik judul buku atau nama penulis..." value="<?= $search ?>" 
                       style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                <button type="submit" class="btn btn-add" style="padding: 0 25px;">Cari</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="card" style="text-align: center; padding: 15px;">
                        <img src="../assets/img/<?= $row['cover'] ?>" style="width: 100%; height: 220px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                        <h4 style="margin-bottom: 5px; color: var(--bg-dark);"><?= $row['judul'] ?></h4>
                        <p style="font-size: 13px; color: #7f8c8d;">Oleh: <?= $row['penulis'] ?></p>
                        <p style="font-size: 13px; margin: 10px 0;">📍 <b>Rak <?= $row['lokasi_rak'] ?></b></p>
                        
                        <p style="margin-bottom: 15px;">
                            <?php if($row['stok'] > 0): ?>
                                <span style="color: var(--success); font-weight: bold;">Tersedia: <?= $row['stok'] ?></span>
                            <?php else: ?>
                                <span style="color: var(--danger); font-weight: bold;">Stok Habis</span>
                            <?php endif; ?>
                        </p>

                        <?php if($row['stok'] > 0): ?>
                            <form action="proses_pinjam.php" method="POST">
                                <input type="hidden" name="id_buku" value="<?= $row['id'] ?>">
                                <button type="submit" name="pinjam" class="btn btn-approve" 
                                        style="width: 100%; justify-content: center;" 
                                        onclick="return confirm('Pinjam buku <?= $row['judul'] ?>?')">
                                    <i class="fas fa-book-reader"></i> Pinjam
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card" style="grid-column: 1 / -1; text-align: center; color: #7f8c8d;">
                    Buku yang kamu cari tidak ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>