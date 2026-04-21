<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Cek login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { 
    header("Location: ../login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];

// Query Riwayat (Sudah disesuaikan: user_id dan book_id)
$query = "SELECT t.*, b.judul 
          FROM transaksi t 
          JOIN buku b ON t.book_id = b.id 
          WHERE t.user_id = '$user_id' 
          ORDER BY t.id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pinjam - Perpus Modern</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="display: block;">
    
    <div class="sidebar">
        <div class="sidebar-header">MENU SISWA</div>
        <div class="sidebar-menu">
            <a href="index.php"><i class="fas fa-search"></i> Cari Buku</a>
            <a href="riwayat.php" class="active"><i class="fas fa-history"></i> Riwayat Pinjam</a>
            <a href="../logout.php" style="margin-top: 50px; color: #e74c3c;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Riwayat Peminjaman Buku</h1>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Status</th>
                        <th>Info</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><b><?= $row['judul']; ?></b></td>
                        <td><?= $row['tgl_pinjam']; ?></td>
                        <td>
                            <?php 
                            if($row['status'] == 'pending') {
                                echo "<span style='color:orange; font-weight:bold;'><i class='fas fa-clock'></i> Menunggu Approval</span>";
                            } elseif($row['status'] == 'approved') {
                                echo "<span style='color:green; font-weight:bold;'><i class='fas fa-check-circle'></i> Sedang Dipinjam</span>";
                            } elseif($row['status'] == 'rejected') {
                                echo "<span style='color:red; font-weight:bold;'><i class='fas fa-times-circle'></i> Ditolak</span>";
                            } else {
                                echo "<span style='color:blue; font-weight:bold;'><i class='fas fa-undo'></i> Sudah Kembali</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'approved'): ?>
                                <small>Bawa buku ke petugas untuk dikembalikan</small>
                            <?php elseif($row['status'] == 'pending'): ?>
                                <small>Tunggu admin menyetujui</small>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if(mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 20px; color: #7f8c8d;">
                                Kamu belum pernah meminjam buku.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <a href="index.php" class="btn btn-add" style="margin-top: 10px;">
            <i class="fas fa-arrow-left"></i> Kembali Cari Buku
        </a>
    </div>
</body>
</html>