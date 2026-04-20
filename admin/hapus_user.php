<?php
session_start();
include '../config/koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../login.php"); exit; 
}

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Proteksi: Jangan biarkan admin hapus dirinya sendiri yang lagi login
    if($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    }
}

// Balik lagi ke halaman anggota
header("Location: kelola_anggota.php");
exit;