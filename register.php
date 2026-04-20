<?php
include 'config/koneksi.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek apakah username sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah dipakai!'); window.location='register.php';</script>";
    } else {
        // Masukkan ke database
        $query = mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, password, role, status) VALUES ('$nama', '$username', '$password', 'user', 'aktif')");
        if ($query) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Perpus Modern</title>
</head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f4f7f6;">
    <div style="background:white; padding:40px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:350px;">
        <h2 style="text-align:center;">Daftar Akun</h2>
        <form method="POST" style="margin-top:20px;">
            <div style="margin-bottom:15px;">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required style="width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ddd;">
            </div>
            <div style="margin-bottom:15px;">
                <label>Username</label>
                <input type="text" name="username" required style="width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ddd;">
            </div>
            <div style="margin-bottom:20px;">
                <label>Password</label>
                <input type="password" name="password" required style="width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ddd;">
            </div>
            <button type="submit" name="register" style="width:100%; padding:12px; border:none; border-radius:8px; background:#2ecc71; color:white; font-weight:bold; cursor:pointer;">DAFTAR</button>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:13px;">
            Sudah punya akun? <a href="login.php" style="color:#3498db; text-decoration:none;">Login di sini</a>
        </p>
    </div>
</body>
</html>