<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cover FROM buku WHERE id='$id'"));

if ($data) {
    if ($data['cover'] != 'default.jpg' && file_exists('../assets/img/cover/'.$data['cover'])) {
        unlink('../assets/img/cover/'.$data['cover']);
    }
    mysqli_query($conn, "DELETE FROM buku WHERE id='$id'");
}

header("Location: kelola_buku.php");
?>