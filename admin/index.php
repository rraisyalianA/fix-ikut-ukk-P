<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$total_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stok) as total FROM buku"))['total'];
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'pending'"))['total'];
$total_kembali = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'returning'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Dashboard Admin</title></head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1><i class="fas fa-th-large"></i> Dashboard Overview</h1>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="card" style="border-bottom: 4px solid var(--accent);">
                <h3>TOTAL STOK BUKU</h3>
                <p style="font-size: 28px; font-weight: bold;"><?= $total_buku ?? 0 ?></p>
            </div>
            <div class="card" style="border-bottom: 4px solid #f1c40f;">
                <h3>PERMINTAAN PINJAM</h3>
                <p style="font-size: 28px; font-weight: bold;"><?= $total_pending ?></p>
            </div>
            <div class="card" style="border-bottom: 4px solid #e67e22;">
                <h3>PERLU CEK KEMBALI</h3>
                <p style="font-size: 28px; font-weight: bold;"><?= $total_kembali ?></p>
            </div>
        </div>
    </div>
</body>
</html>