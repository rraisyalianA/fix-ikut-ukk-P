<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cari user berdasarkan username & password (TANPA HASH sesuai request)
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Cek Status Akun
        if ($data['status'] == 'nonaktif') {
            echo "<script>alert('Akun Anda dinonaktifkan Admin!'); window.location='login.php';</script>";
        } else {
            // Login Berhasil, Set Session
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nama']    = $data['nama_lengkap'];
            $_SESSION['role']    = $data['role'];

            if ($data['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: siswa/index.php");
            }
        }
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Perpus Modern</title>
    <link rel="stylesheet" href="assets/css/style.css"> </head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f4f7f6;">
    <div style="background:white; padding:40px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:350px;">
        <h2 style="text-align:center;">Selamat Datang!</h2>
        <p style="text-align:center; color:#777; font-size:14px;">Silakan login ke akun Anda</p>
        
        <form method="POST" style="margin-top:20px;">
            <div style="margin-bottom:15px;">
                <label>Username</label>
                <input type="text" name="username" required style="width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ddd;">
            </div>
            <div style="margin-bottom:20px;">
                <label>Password</label>
                <input type="password" name="password" required style="width:100%; padding:10px; margin-top:5px; border-radius:8px; border:1px solid #ddd;">
            </div>
            <button type="submit" name="login" style="width:100%; padding:12px; border:none; border-radius:8px; background:#3498db; color:white; font-weight:bold; cursor:pointer;">LOGIN</button>
        </form>
        
        <p style="text-align:center; margin-top:20px; font-size:13px;">
            Belum punya akun? <a href="register.php" style="color:#3498db; text-decoration:none;">Daftar Sekarang</a>
        </p>
    </div>
</body>
</html>