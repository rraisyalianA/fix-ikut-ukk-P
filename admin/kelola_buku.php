<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

// --- LOGIKA SIMPAN (TAMBAH & EDIT JADI SATU) ---
if(isset($_POST['simpan_buku'])) {
    $id      = mysqli_real_escape_string($conn, $_POST['id']);
    $judul   = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $stok    = mysqli_real_escape_string($conn, $_POST['stok']);
    $rak     = mysqli_real_escape_string($conn, $_POST['lokasi_rak']);

    // Logika Upload Cover
    $cover_sql = "";
    if($_FILES['cover']['name'] != "") {
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $file_name = time() . "." . $ext; 
        // Pastikan folder ../assets/img/cover/ sudah ada
        if(move_uploaded_file($_FILES['cover']['tmp_name'], "../assets/img/cover/" . $file_name)){
            $cover_sql = ", cover='$file_name'";
        }
    }

    if($id == "") {
        // TAMBAH BARU
        $img = (isset($file_name)) ? $file_name : "default.jpg";
        mysqli_query($conn, "INSERT INTO buku (judul, penulis, stok, lokasi_rak, cover) VALUES ('$judul', '$penulis', '$stok', '$rak', '$img')");
    } else {
        // EDIT DATA
        $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', stok='$stok', lokasi_rak='$rak' $cover_sql WHERE id='$id'";
        mysqli_query($conn, $sql);
    }
    header("Location: kelola_buku.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Buku - PerpusAdmin</title>
    <style>
        /* Styling Modal */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; padding: 25px; width: 450px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-flex { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        
        /* Gambar di Tabel */
        .img-cover { width: 50px; height: 70px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1><i class="fas fa-book"></i> Kelola Buku</h1>
            <button class="btn btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Tambah Buku</button>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Judul & Penulis</th>
                        <th>Stok</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC");
                    while($row = mysqli_fetch_assoc($query)):
                    ?>
                    <tr>
                        <td>
                            <img src="../assets/img/cover/<?= $row['cover'] ?>" class="img-cover" onerror="this.src='../assets/img/cover/default.jpg'">
                        </td>
                        <td>
                            <strong><?= $row['judul'] ?></strong><br>
                            <small style="color: #7f8c8d;"><?= $row['penulis'] ?> | Rak: <?= $row['lokasi_rak'] ?></small>
                        </td>
                        <td><?= $row['stok'] ?></td>
                        <td style="text-align:center">
                            <button class="btn" style="background: #3498db; color: white;" onclick='editBuku(<?= json_encode($row) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-reject" onclick="return confirm('Hapus buku?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="bukuModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 20px;">Tambah Buku</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="f_id">
                
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="judul" id="f_judul" required>
                </div>
                <div class="form-group">
                    <label>Penulis</label>
                    <input type="text" name="penulis" id="f_penulis" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" id="f_stok" required>
                </div>
                <div class="form-group">
                    <label>Lokasi Rak</label>
                    <input type="text" name="lokasi_rak" id="f_rak">
                </div>
                <div class="form-group">
                    <label>Ganti Cover (Opsional)</label>
                    <input type="file" name="cover" accept="image/*">
                </div>

                <div class="btn-flex">
                    <button type="button" onclick="closeModal()" class="btn" style="background: #eee;">Batal</button>
                    <button type="submit" name="simpan_buku" class="btn btn-add">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('bukuModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Tambah Buku Baru";
            document.getElementById('f_id').value = "";
            document.getElementById('f_judul').value = "";
            document.getElementById('f_penulis').value = "";
            document.getElementById('f_stok').value = "1";
            document.getElementById('f_rak').value = "";
            modal.style.display = "block";
        }

        function editBuku(data) {
            document.getElementById('modalTitle').innerText = "Edit Data Buku";
            document.getElementById('f_id').value = data.id;
            document.getElementById('f_judul').value = data.judul;
            document.getElementById('f_penulis').value = data.penulis;
            document.getElementById('f_stok').value = data.stok;
            document.getElementById('f_rak').value = data.lokasi_rak;
            modal.style.display = "block";
        }

        function closeModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) { closeModal(); }
        }
    </script>

</body>
</html>