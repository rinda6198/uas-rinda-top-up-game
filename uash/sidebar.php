<?php
$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($admin_data) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $admin_query = mysqli_query($conn, "SELECT profile_picture, fullname FROM users WHERE id = '$uid'");
    $admin_data = mysqli_fetch_assoc($admin_query);
}
?>

<aside class="w-64 min-h-screen glass p-6 hidden md:block sticky top-0 h-screen">
    <div class="font-bold text-xl mb-10 text-purple-500 italic uppercase tracking-tighter">
        Game<span class="text-white">Store</span>
    </div>

    <div class="mb-10 group">
        <div class="glass p-5 rounded-[2rem] border border-white/5 hover:border-purple-500/50 transition-all duration-500">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <img src="uploads/profile/<?= $admin_data['profile_picture'] ?: 'default.png' ?>"
                    class="w-full h-full rounded-full object-cover border-4 border-purple-500/20 group-hover:border-purple-500/50 transition-all">
                <a href="profile.php" class="absolute inset-0 flex items-center justify-center bg-purple-600/60 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M12 20h9" /><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" /></svg>
                </a>
            </div>
            <div class="text-center">
                <h4 class="font-bold text-sm text-white truncate"><?= explode(' ', $admin_data['fullname'])[0] ?></h4>
                <p class="text-[9px] text-purple-400 font-black uppercase tracking-widest mt-1">Administrator</p>
            </div>
        </div>
    </div>

    <nav class="space-y-2">
        <a href="admin-dashboard.php" 
           class="block p-4 <?= $current_page == 'admin-dashboard.php' ? 'bg-purple-600 shadow-lg shadow-purple-600/20' : 'hover:bg-white/5' ?> rounded-2xl font-bold transition">
            Pesanan Masuk
        </a>

        <a href="admin-games.php" 
           class="block p-4 <?= ($current_page == 'admin-games.php' || $current_page == 'admin-items.php') ? 'bg-purple-600 shadow-lg shadow-purple-600/20' : 'hover:bg-white/5' ?> rounded-2xl font-bold transition">
            Kelola Game
        </a>

        <?php if (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1): ?>
            <a href="admin-users.php" 
               class="block p-4 <?= $current_page == 'admin-users.php' ? 'bg-purple-600 shadow-lg shadow-purple-600/20' : 'hover:bg-white/5' ?> rounded-2xl font-bold transition">
                Kelola User
            </a>
        <?php endif; ?>

        <div class="pt-10">
            <a href="logout.php" class="block p-4 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-2xl transition font-bold text-center">
                Keluar
            </a>
        </div>
    </nav>
</aside>