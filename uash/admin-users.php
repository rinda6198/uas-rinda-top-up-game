<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin' || $_SESSION['is_superadmin'] != 1) {
    echo "<script>alert('Akses Ditolak! Hanya Super Admin yang boleh masuk.'); window.location='admin-dashboard.php';</script>";
    exit;
}


if (isset($_POST['add_admin'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Menggunakan enkripsi
    $role = 'admin';
    $status = 'active';

 
    $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Gagal: Email sudah digunakan!'); window.location='admin-users.php';</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (fullname, email, password, role, status, is_superadmin) 
                                       VALUES ('$fullname', '$email', '$password', '$role', '$status', 0)");
        if ($insert) {
            header("Location: admin-users.php?msg=success");
            exit;
        } else {
            echo "<script>alert('Gagal menambah data!');</script>";
        }
    }
}


if (isset($_GET['toggle_status'])) {
    $target_id = mysqli_real_escape_string($conn, $_GET['toggle_status']);
    $check_target = mysqli_query($conn, "SELECT is_superadmin FROM users WHERE id = '$target_id'");
    $target_data = mysqli_fetch_assoc($check_target);

    if ($target_data['is_superadmin'] == 0 && $target_id != $_SESSION['user_id']) {
        $current_status = $_GET['current'];
        $new_status = ($current_status == 'active') ? 'blocked' : 'active';
        mysqli_query($conn, "UPDATE users SET status = '$new_status' WHERE id = '$target_id'");
        header("Location: admin-users.php?msg=updated");
        exit;
    } else {
        echo "<script>alert('Gagal: Super Admin tidak dapat diblokir!'); window.location='admin-users.php';</script>";
        exit;
    }
}


if (isset($_GET['toggle_super']) && isset($_GET['verify_code'])) {
    $target_id = mysqli_real_escape_string($conn, $_GET['toggle_super']);
    $current_super = $_GET['current_super'];
    $input_code = $_GET['verify_code'];
    $secret_code = "12345"; 

    if ($input_code === $secret_code) {
        $new_super = ($current_super == 1) ? 0 : 1;
        if ($target_id != $_SESSION['user_id']) {
            mysqli_query($conn, "UPDATE users SET is_superadmin = '$new_super' WHERE id = '$target_id'");
            header("Location: admin-users.php?msg=role_updated");
            exit;
        }
    } else {
        echo "<script>alert('Kode Verifikasi Salah!'); window.location='admin-users.php';</script>";
        exit;
    }
}


if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $check_del = mysqli_query($conn, "SELECT is_superadmin FROM users WHERE id = '$delete_id'");
    $del_data = mysqli_fetch_assoc($check_del);

    if ($del_data['is_superadmin'] == 0 && $delete_id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = '$delete_id'");
        header("Location: admin-users.php?msg=deleted");
        exit;
    } else {
        echo "<script>alert('Gagal: Super Admin tidak dapat dihapus!'); window.location='admin-users.php';</script>";
        exit;
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, fullname ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User | Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #0f172a; font-family: 'Plus Jakarta Sans', sans-serif; color: white; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        select option { background-color: #1e293b; color: white; }
    </style>
</head>
<body class="flex">

   <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-3xl font-extrabold">Kelola <span class="text-purple-500">User</span></h1>
                <p class="text-slate-400 text-sm mt-1">Gunakan filter untuk mempersempit pencarian.</p>
            </div>

            <button onclick="document.getElementById('modalAdmin').classList.remove('hidden')"
                class="bg-purple-600 px-6 py-3 rounded-2xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-600/20">
                + Tambah Admin
            </button>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-[200px]">
                <input type="text" id="searchInput" placeholder="Cari nama atau email..." 
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 outline-none focus:border-purple-500 transition text-sm text-white">
            </div>
            
            <div class="w-48">
                <select id="roleFilter" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 outline-none focus:border-purple-500 transition text-sm text-white cursor-pointer">
                    <option value="all">Semua Jenis User</option>
                    <option value="admin">ADMIN</option>
                    <option value="user">USER</option>
                </select>
            </div>

            <div class="w-48">
                <select id="statusFilter" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3 outline-none focus:border-purple-500 transition text-sm text-white cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="active">ACTIVE</option>
                    <option value="blocked">BLOCKED</option>
                </select>
            </div>
        </div>

        <div class="glass rounded-[2rem] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="p-6">Nama / Email</th>
                        <th class="p-6">Role / Hak Akses</th>
                        <th class="p-6">Status</th>
                        <th class="p-6 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="divide-y divide-white/5">
                    <?php while ($u = mysqli_fetch_assoc($users)): ?>
                        <tr class="user-row hover:bg-white/[0.02] transition-colors" 
                            data-role="<?= strtolower($u['role']) ?>" 
                            data-status="<?= strtolower($u['status'] ?: 'active') ?>">
                            <td class="p-6">
                                <div class="font-bold text-white search-name"><?= $u['fullname'] ?></div>
                                <div class="text-[10px] text-slate-500 font-mono search-email"><?= $u['email'] ?></div>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black role-badge <?= $u['role'] == 'admin' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20' ?>">
                                        <?= strtoupper($u['role']) ?>
                                    </span>
                                    <?php if($u['is_superadmin'] == 1): ?>
                                        <span class="bg-yellow-500/10 text-yellow-500 text-[9px] px-2 py-1 rounded-md font-bold border border-yellow-500/20">SUPER</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-6">
                                <span class="text-[11px] font-bold status-text <?= ($u['status'] ?: 'active') == 'active' ? 'text-green-500' : 'text-red-500' ?>">
                                    ● <?= ucfirst($u['status'] ?: 'active') ?>
                                </span>
                            </td>
                            <td class="p-6 text-center">
                                <div class="flex justify-center gap-2">
                                    <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <button onclick="verifySuperAdmin(<?= $u['id'] ?>, <?= $u['is_superadmin'] ?>)" 
                                               class="p-2 rounded-xl transition-all <?= $u['is_superadmin'] == 1 ? 'bg-yellow-500/20 text-yellow-500' : 'bg-white/5 text-slate-500 hover:text-yellow-500' ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="<?= $u['is_superadmin'] == 1 ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($u['is_superadmin'] == 0): ?>
                                            <a href="?toggle_status=<?= $u['id'] ?>&current=<?= $u['status'] ?: 'active' ?>"
                                                class="px-3 py-2 rounded-xl text-[10px] font-black transition-all <?= ($u['status'] ?: 'active') == 'active' ? 'bg-red-500/10 text-red-500 hover:bg-red-500' : 'bg-green-500/10 text-green-500 hover:bg-green-500' ?> hover:text-white">
                                                <?= ($u['status'] ?: 'active') == 'active' ? 'BLOKIR' : 'AKTIFKAN' ?>
                                            </a>
                                            <a href="?delete_id=<?= $u['id'] ?>" class="p-2 bg-white/5 hover:bg-red-600 text-slate-400 hover:text-white rounded-xl transition-all" onclick="return confirm('Hapus user?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[9px] text-slate-600 font-bold uppercase tracking-tighter">Protected</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-500 text-[9px] font-bold italic uppercase">My Account</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
    function applyFilters() {
        let searchValue = document.getElementById('searchInput').value.toLowerCase();
        let roleValue = document.getElementById('roleFilter').value.toLowerCase();
        let statusValue = document.getElementById('statusFilter').value.toLowerCase();
        let rows = document.querySelectorAll('.user-row');

        rows.forEach(row => {
            let name = row.querySelector('.search-name').textContent.toLowerCase();
            let email = row.querySelector('.search-email').textContent.toLowerCase();
            let rowRole = row.getAttribute('data-role');
            let rowStatus = row.getAttribute('data-status');

            let matchSearch = name.includes(searchValue) || email.includes(searchValue);
            let matchRole = (roleValue === 'all') || (rowRole === roleValue);
            let matchStatus = (statusValue === 'all') || (rowStatus === statusValue);

            row.style.display = (matchSearch && matchRole && matchStatus) ? "" : "none";
        });
    }

    document.getElementById('searchInput').addEventListener('keyup', applyFilters);
    document.getElementById('roleFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);

    function verifySuperAdmin(id, current) {
        let code = prompt("Masukkan Kode Keamanan untuk mengubah status Super Admin:");
        if (code != null && code != "") {
            window.location.href = `admin-users.php?toggle_super=${id}&current_super=${current}&verify_code=${code}`;
        }
    }
    </script>
    
    <div id="modalAdmin" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-6">
        <div class="glass p-8 rounded-[2.5rem] w-full max-w-md shadow-2xl text-white">
             <h2 class="text-2xl font-bold mb-6">Tambah <span class="text-purple-500">Admin</span></h2>
             <form action="" method="POST" class="space-y-4">
                <input type="text" name="fullname" placeholder="Nama Lengkap" required class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl outline-none focus:border-purple-500 transition text-sm">
                <input type="email" name="email" placeholder="Email" required class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl outline-none focus:border-purple-500 transition text-sm">
                <input type="password" name="password" placeholder="Password" required class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl outline-none focus:border-purple-500 transition text-sm">
                <div class="flex gap-3 pt-6">
                    <button type="button" onclick="document.getElementById('modalAdmin').classList.add('hidden')" class="flex-1 p-4 bg-white/5 rounded-2xl font-bold text-sm">Batal</button>
                    <button type="submit" name="add_admin" class="flex-1 p-4 bg-purple-600 rounded-2xl font-bold text-sm">Simpan</button>
                </div>
             </form>
        </div>
    </div>
</body>
</html>