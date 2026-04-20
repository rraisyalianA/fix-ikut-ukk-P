<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$list = mysqli_query($conn, "SELECT transaksi.*, users.nama_lengkap, buku.judul 
                             FROM transaksi JOIN users ON transaksi.user_id = users.id 
                             JOIN buku ON transaksi.book_id = buku.id WHERE transaksi.status = 'pending'");
?>
<!DOCTYPE html>
<html>
<head><title>Approval Pinjam</title></head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h1><i class="fas fa-file-import"></i> Persetujuan Peminjaman</h1>
        <div class="card">
            <table>
                <thead>
                    <tr><th>Nama Peminjam</th><th>Buku</th><th>Tgl Pengajuan</th><th style="text-align:center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($list) > 0): while($t = mysqli_fetch_assoc($list)): ?>
                    <tr>
                        <td><?= $t['nama_lengkap'] ?></td>
                        <td><strong><?= $t['judul'] ?></strong></td>
                        <td><?= $t['tgl_pengajuan'] ?></td>
                        <td style="text-align:center">
                            <a href="proses_transaksi.php?id=<?= $t['id'] ?>&aksi=approve_pinjam\" class="btn btn-approve">Setujui</a>
                            <a href="proses_transaksi.php?id=<?= $t['id'] ?>&aksi=reject_pinjam\" class="btn btn-reject">Tolak</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4" align="center">Tidak ada antrian pengajuan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>