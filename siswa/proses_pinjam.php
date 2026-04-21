<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['pinjam'])) {
    // Sesuaikan dengan nama variabel di form (id_buku) dan kolom DB (book_id)
    $book_id = $_POST['id_buku']; 
    $user_id = $_SESSION['user_id'];
    $tgl_sekarang = date('Y-m-d');

    // 1. Cek apakah user lagi pinjam buku yang sama (Pakai kolom user_id & book_id)
    $cek = mysqli_query($conn, "SELECT * FROM transaksi WHERE user_id='$user_id' AND book_id='$book_id' AND status IN ('pending', 'approved')");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Kamu masih meminjam atau menunggu antrean untuk buku ini!'); window.location='index.php';</script>";
    } else {
        // 2. Masukkan ke tabel transaksi (Pakai kolom user_id, book_id, dan tgl_pengajuan)
        // Saya pakai tgl_pengajuan karena di screenshot DB kamu ada kolom itu
        $query = mysqli_query($conn, "INSERT INTO transaksi (user_id, book_id, tgl_pengajuan, tgl_pinjam, status) 
                                      VALUES ('$user_id', '$book_id', '$tgl_sekarang', '$tgl_sekarang', 'pending')");
        
        if ($query) {
            echo "<script>alert('Berhasil diajukan! Tunggu approval admin ya.'); window.location='riwayat.php';</script>";
        } else {
            // Debugging kalau misal gagal insert
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>