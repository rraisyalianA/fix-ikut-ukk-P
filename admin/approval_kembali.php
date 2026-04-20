<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$list = mysqli_query($conn, "SELECT transaksi.*, users.nama_lengkap, buku.judul 
                             FROM transaksi JOIN users ON transaksi.user_id = users.id 
                             JOIN buku ON transaksi.book_id = buku.id WHERE transaksi.status = 'returning'");
?>
<!DOCTYPE html>
<html>
<head><title>Approval Kembali</title></head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1><i class="fas fa-file-export"></i> Konfirmasi Pengembalian</h1>
        <div class="card">
            <table>
                <thead>
                    <tr><th>Nama Anggota</th><th>Judul Buku</th><th>Batas Kembali</th><th style="text-align:center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($list) > 0): while($t = mysqli_fetch_assoc($list)): ?>
                    <tr>
                        <td><?= $t['nama_lengkap'] ?></td>
                        <td><strong><?= $t['judul'] ?></strong></td>
                        <td><span style="color:var(--danger)"><?= date('d M Y', strtotime($t['tgl_kembali_seharusnya'])) ?></span></td>
                        <td style="text-align:center">
                            <a href="proses_transaksi.php?id=<?= $t['id'] ?>&aksi=approve_kembali" class="btn btn-approve">Buku Diterima</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4" align="center">Tidak ada pengembalian yang perlu dicek.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>