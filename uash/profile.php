<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

$success = "";
$error = "";

if (isset($_POST['update_profile'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $photo_name = $user['profile_picture']; 

    if ($_FILES['photo']['name'] != "") {
        $target_dir = "uploads/profile/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $new_filename = "user_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_name = $new_filename;
            }
        } else {
            $error = "Format file tidak didukung (Gunakan JPG, PNG, atau WEBP).";
        }
    }

    if (empty($error)) {
        $update = mysqli_query($conn, "UPDATE users SET fullname = '$fullname', email = '$email', profile_picture = '$photo_name' WHERE id = '$user_id'");

        if ($update) {
            $_SESSION['fullname'] = $fullname; 
            $success = "Profil berhasil diperbarui!";
            $query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
            $user = mysqli_fetch_assoc($query);
        } else {
            $error = "Gagal memperbarui database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Profil | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #020617; color: white; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="p-6">
    <div class="max-w-2xl mx-auto mt-10">
<a href="<?= $_SESSION['role'] == 'admin' ? 'admin-dashboard.php' : 'dashboard.php' ?>" 
   class="text-purple-400 mb-6 inline-block font-bold hover:translate-x-[-5px] transition-transform">
   ← Kembali ke dashboard
</a>        
        <div class="glass p-8 rounded-[2.5rem]">
            <h1 class="text-3xl font-black mb-6 uppercase italic">Kelola <span class="text-purple-500">Profil</span></h1>

            <?php if($success): ?>
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-xl text-sm"><?= $success ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="flex items-center gap-6 mb-8">
                    <img src="uploads/profile/<?= $user['profile_picture'] ?: 'default.png' ?>" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-purple-500/30">
                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Ganti Foto Profil</label>
                        <input type="file" name="photo" class="text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="fullname" value="<?= $user['fullname'] ?>" required
                           class="w-full glass px-5 py-3 rounded-xl outline-none focus:border-purple-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Alamat Email</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>" required
                           class="w-full glass px-5 py-3 rounded-xl outline-none focus:border-purple-500 transition text-slate-400">
                </div>

                <button type="submit" name="update_profile" 
                        class="w-full bg-purple-600 py-4 rounded-xl font-black hover:bg-purple-700 transition shadow-lg shadow-purple-600/20">
                    SIMPAN PERUBAHAN
                </button>
            </form>
        </div>
    </div>
</body>
</html>