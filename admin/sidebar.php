<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">

<?php
// Ambil jumlah permintaan pinjam yang masih pending
$q_pinjam = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'pending'");
$notif_pinjam = mysqli_fetch_assoc($q_pinjam)['total'];

// Ambil jumlah permintaan kembali yang masih returning
$q_kembali = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'returning'");
$notif_kembali = mysqli_fetch_assoc($q_kembali)['total'];
?>

<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-book-reader" style="color: var(--accent);"></i>
        <span>PerpusAdmin</span>
    </div>

    <nav class="sidebar-menu">
        <?php $page = basename($_SERVER['PHP_SELF']); ?>
        
        <a href="index.php" class="<?= $page == 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        
        <a href="kelola_buku.php" class="<?= $page == 'kelola_buku.php' ? 'active' : '' ?>">
            <i class="fas fa-book"></i> Kelola Buku
        </a>

        <a href="approval_pinjam.php" class="<?= $page == 'approval_pinjam.php' ? 'active' : '' ?>" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fas fa-file-import"></i> Approval Pinjam</span>
            <?php if($notif_pinjam > 0): ?>
                <span style="background: var(--danger); color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold;"><?= $notif_pinjam ?></span>
            <?php endif; ?>
        </a>

        <a href="approval_kembali.php" class="<?= $page == 'approval_kembali.php' ? 'active' : '' ?>" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fas fa-file-export"></i> Approval Kembali</span>
            <?php if($notif_kembali > 0): ?>
                <span style="background: #f39c12; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold;"><?= $notif_kembali ?></span>
            <?php endif; ?>
        </a>

        <a href="kelola_anggota.php" class="<?= $page == 'kelola_anggota.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Kelola Anggota
        </a>
    </nav>

    <a href="../logout.php" class="logout" onclick="return confirm('Yakin ingin keluar?')">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>