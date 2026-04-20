<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}

// --- LOGIKA SIMPAN (TAMBAH & EDIT USER) ---
if(isset($_POST['simpan_user'])) {
    $id       = mysqli_real_escape_string($conn, $_POST['id']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $status   = mysqli_real_escape_string($conn, $_POST['status']);
    
    if($id == "") {
        // --- SUDAH DIUBAH: Tanpa Hash (Plain Text) ---
        mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, password, role, status) 
                             VALUES ('$nama', '$username', '123', '$role', 'aktif')");
    } else {
        // Edit User
        mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', username='$username', role='$role', status='$status' WHERE id='$id'");
    }
    header("Location: kelola_anggota.php"); exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Anggota - PerpusAdmin</title>
    <style>
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 25px; width: 400px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        /* Perbaikan Warna Badge */
        .badge-aktif { background: #2ecc71; color: white; }
        .badge-nonaktif { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1><i class="fas fa-users"></i> Manajemen Anggota</h1>
            <button class="btn btn-add" onclick="openModal()"><i class="fas fa-user-plus"></i> Tambah Anggota</button>
        </div>

        <div class="card">
            <form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">
                <input type="text" name="search" placeholder="Cari nama atau username..." value="<?= $search ?>" style="flex:1; padding:10px; border-radius:8px; border:1px solid #ddd;">
                <button type="submit" class="btn btn-add">Cari</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM users WHERE nama_lengkap LIKE '%$search%' ORDER BY role ASC");
                    while($u = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><strong><?= $u['nama_lengkap'] ?></strong></td>
                        <td><?= $u['username'] ?></td>
                        <td><span style="background:#eee; padding:3px 8px; border-radius:4px; font-size:11px;"><?= strtoupper($u['role']) ?></span></td>
                        <td>
                            <span class="badge <?= ($u['status'] ?? 'aktif') == 'aktif' ? 'badge-aktif' : 'badge-nonaktif' ?>">
                                <?= $u['status'] ?? 'aktif' ?>
                            </span>
                        </td>
                        <td style="text-align:center">
                            <button class="btn" style="background: #3498db; color: white;" onclick='editUser(<?= json_encode($u) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if($u['id'] != ($_SESSION['user_id'] ?? '')): ?>
                                <a href="hapus_user.php?id=<?= $u['id'] ?>" class="btn btn-reject" onclick="return confirm('Hapus user ini?')"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Tambah Anggota</h2>
            <form action="" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id" id="f_id">
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="f_nama" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="f_username" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="f_role">
                        <option value="user">User / Siswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group" id="statusGroup">
                    <label>Status Akun</label>
                    <select name="status" id="f_status">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif (Disable)</option>
                    </select>
                </div>
                <p id="passNote" style="font-size: 11px; color: #e67e22; margin-bottom: 15px;">*Password default user baru: <strong>123</strong></p>

                <div style="text-align: right;">
                    <button type="button" onclick="closeModal()" class="btn" style="background:#eee;">Batal</button>
                    <button type="submit" name="simpan_user" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('userModal');

        function openModal() {
            document.getElementById('modalTitle').innerText = "Tambah Anggota";
            document.getElementById('f_id').value = "";
            document.getElementById('f_nama').value = "";
            document.getElementById('f_username').value = "";
            document.getElementById('f_role').value = "user";
            document.getElementById('f_status').value = "aktif";
            document.getElementById('statusGroup').style.display = "none";
            document.getElementById('passNote').style.display = "block";
            modal.style.display = "block";
        }

        function editUser(data) {
            document.getElementById('modalTitle').innerText = "Edit Anggota";
            document.getElementById('f_id').value = data.id;
            document.getElementById('f_nama').value = data.nama_lengkap;
            document.getElementById('f_username').value = data.username;
            document.getElementById('f_role').value = data.role;
            document.getElementById('f_status').value = data.status;
            document.getElementById('statusGroup').style.display = "block";
            document.getElementById('passNote').style.display = "none";
            modal.style.display = "block";
        }

        function closeModal() { modal.style.display = "none"; }
        window.onclick = function(e) { if(e.target == modal) closeModal(); }
    </script>
</body>
</html>