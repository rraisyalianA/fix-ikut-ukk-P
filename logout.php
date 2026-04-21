<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout - Perpus Modern</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body class="auth-container">
    <div class="auth-card">
        <h2 style="color: #55afba; margin-bottom: 15px;">Berhasil Keluar</h2>
        <p style="color: #7f8c8d;">Terima kasih sudah berkunjung.<br>Sedang mengalihkan ke halaman login...</p>
        
        <div style="margin-top: 20px;">
            <div class="loader" style="border: 4px solid #f3f3f3; border-top: 4px solid #55afba; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: auto;"></div>
        </div>
    </div>

    <style>
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    <script>
        // Pindah ke login otomatis setelah 2.5 detik
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 2500);
    </script>
</body>
</html>