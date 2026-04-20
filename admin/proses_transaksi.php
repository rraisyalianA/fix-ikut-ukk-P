<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
$aksi = $_GET['aksi'];

// Ambil info buku & stok
$res = mysqli_query($conn, "SELECT t.book_id, b.stok FROM transaksi t JOIN buku b ON t.book_id = b.id WHERE t.id = '$id'");
$data = mysqli_fetch_assoc($res);
$book_id = $data['book_id'];

if ($aksi == 'approve_pinjam') {
    if ($data['stok'] > 0) {
        $deadline = date('Y-m-d', strtotime('+7 days'));
        mysqli_query($conn, "UPDATE transaksi SET status='approved', tgl_pinjam=CURDATE(), tgl_kembali_seharusnya='$deadline' WHERE id='$id'");
        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id='$book_id'");
        header("Location: approval_pinjam.php?msg=success");
    } else {
        header("Location: approval_pinjam.php?msg=stok_habis");
    }
} elseif ($aksi == 'reject_pinjam') {
    mysqli_query($conn, "UPDATE transaksi SET status='rejected' WHERE id='$id'");
    header("Location: approval_pinjam.php?msg=rejected");
} elseif ($aksi == 'approve_kembali') {
    mysqli_query($conn, "UPDATE transaksi SET status='returned', tgl_kembali_aktual=CURDATE() WHERE id='$id'");
    mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id='$book_id'");
    header("Location: approval_kembali.php?msg=success");
}
?>       