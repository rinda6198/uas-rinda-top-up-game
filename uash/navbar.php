<?php
if (!isset($user_data) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $user_query = mysqli_query($conn, "SELECT profile_picture, fullname FROM users WHERE id = '$uid'");
    $user_data = mysqli_fetch_assoc($user_query);
}
?>

<nav class="p-6 flex justify-between items-center glass sticky top-0 z-50">
    <a href="index.php" class="font-bold text-xl uppercase italic tracking-tighter">
        <span class="text-purple-500">Game</span>Store
    </a>

    <div class="flex items-center gap-6">
        <a href="lacak.php" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 hover:bg-purple-500/10 border border-white/5 hover:border-purple-500/30 transition-all text-xs font-bold tracking-wide">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-purple-500"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12c-1.1 0-2-.9-2-2V7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3c0 1.1-.9 2-2 2"/><path d="M13 17l2 2 4-4"/><path d="M2 12h10l1 8h1"/><path d="M20 12h2"/></svg>
            LACAK PESANAN
        </a>

        <a href="riwayat.php" class="text-sm font-semibold hover:text-purple-400 transition">Pesanan Saya</a>

        <a href="keranjang.php" class="relative p-2 hover:bg-white/5 rounded-full transition group">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:text-purple-500">
                <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
            <span class="absolute top-0 right-0 w-2 h-2 bg-purple-500 rounded-full border-2 border-[#020617]"></span>
        </a>

        <div class="flex items-center gap-3 border-l border-white/10 pl-6">
            <a href="profile.php" class="flex items-center gap-2 group">
                <img src="uploads/profile/<?= $user_data['profile_picture'] ?: 'default.png' ?>"
                     class="w-8 h-8 rounded-full object-cover border border-purple-500 group-hover:ring-4 ring-purple-500/20 transition-all">
                <span class="text-sm font-semibold text-purple-400 group-hover:text-white transition">
                    Halo, <?= explode(' ', $user_data['fullname'] ?? 'User')[0] ?>
                </span>
            </a>
            <a href="logout.php"
               class="bg-red-500/10 text-red-500 px-4 py-2 rounded-xl text-xs font-black hover:bg-red-500/20 transition">KELUAR</a>
        </div>
    </div>
</nav>