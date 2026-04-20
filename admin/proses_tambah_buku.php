<?php
include '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $judul      = $_POST['judul'];
    $penulis    = $_POST['penulis'];
    $deskripsi  = $_POST['deskripsi'];
    $stok       = $_POST['stok'];
    $rak        = $_POST['lokasi_rak'];
    
    // Logika Upload Cover
    $cover_name = "default.jpg";
    if (!empty($_FILES['cover']['name'])) {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $cover_name = time() . "." . $ext;
        move_uploaded_file($_FILES['cover']['tmp_name'], "../assets/img/cover/" . $cover_name);
    }

    $sql = "INSERT INTO buku (judul, penulis, deskripsi, stok, lokasi_rak, cover) 
            VALUES ('$judul', '$penulis', '$deskripsi', '$stok', '$rak', '$cover_name')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: kelola_buku.php?status=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>