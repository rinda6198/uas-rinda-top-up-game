<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}




$query_games = mysqli_query($conn, "SELECT * FROM games ORDER BY name ASC LIMIT 12");

$features = [
    [
        "icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_0_8px_rgba(168,85,247,0.8)]"><path d="m6 2 3 9c.2.6.5 1 1 1h4c.5 0 .8-.4 1-1l3-9"/><path d="M3 14h18"/><path d="m21 14-1.5 6c-.1.6-.6 1-1.2 1H5.7c-.6 0-1.1-.4-1.2-1L3 14"/></svg>',
        "title" => "Proses Instan",
        "desc" => "Top up diproses otomatis dalam hitungan detik tanpa antre."
    ],
    [
        "icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_0_8px_rgba(34,211,238,0.8)]"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        "title" => "100% Aman",
        "desc" => "Sistem enkripsi tingkat tinggi menjamin keamanan transaksi Anda."
    ],
    [
        "icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#facc15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_0_8px_rgba(250,204,21,0.8)]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        "title" => "Rating Tinggi",
        "desc" => "Telah melayani banyak gamer di seluruh Indonesia."
    ],
];
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <title>GameStore | Pusat Top Up Game Tercepat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: #020617;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .neon-border:hover {
            border-color: #a855f7;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);
        }

        .hero-gradient {
            background: radial-gradient(circle at 50% -20%, #3b0764, transparent);
        }

        .glow-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                filter: brightness(1);
            }

            50% {
                filter: brightness(1.5);
            }
        }
    </style>
</head>

<body class="text-slate-200 antialiased">

    <header class="fixed top-0 w-full z-50 glass">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2 font-extrabold text-2xl tracking-tighter">
                <span class="text-purple-500">GAME</span>STORE
            </div>
            <nav class="hidden md:flex gap-8 text-sm font-medium">
                <a href="index.php" class="hover:text-purple-400 transition">Beranda</a>
                <a href="#games" class="hover:text-purple-400 transition">Daftar Game</a>
            </nav>
            <div class="flex gap-4 items-center">
                <?php if (isset($_SESSION['login'])): ?>
                    <a href="<?= $_SESSION['role'] == 'admin' ? 'admin-dashboard.php' : 'dashboard.php' ?>"
                        class="text-sm font-semibold hover:text-purple-400 transition">Dashboard
                        (<?= $_SESSION['name'] ?>)</a>
                    <a href="logout.php" class="text-sm font-bold text-red-400 hover:text-red-300">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="text-sm font-semibold hover:text-purple-400 transition">Login</a>
                    <a href="register.php"
                        class="px-5 py-2.5 bg-purple-600 rounded-xl text-sm font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-900/20 text-white">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden"
         style="background-image: url('uploads/items/default.jpg'); 
                background-size: cover; 
                background-position: center; 
                background-repeat: no-repeat;">

    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-slate-950/40 to-[#020617]"></div>
    
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 pointer-events-none"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-7xl font-extrabold mb-8 tracking-tight leading-tight text-white">
            Top Up Game Favorit <br>
            <span class="bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
                Tanpa Ribet, Hitungan Detik
            </span>
        </h1>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#games"
               class="px-10 py-4 bg-purple-600 rounded-2xl font-bold text-lg hover:scale-105 transition-all shadow-xl shadow-purple-600/20 text-white">
                Mulai Top Up Sekarang
            </a>
        </div>
    </div>
</section>



    <section class="max-w-7xl mx-auto px-6 py-24 border-y border-white/5">
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($features as $f): ?>
                <div
                    class="glass p-10 rounded-3xl group hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                    <div
                        class="absolute -top-10 -left-10 w-32 h-32 bg-purple-600/10 blur-[50px] group-hover:bg-purple-600/20 transition-all">
                    </div>
                    <div
                        class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mb-6 border border-white/10 group-hover:border-purple-500/50 transition-all glow-pulse">
                        <?= $f['icon'] ?>
                    </div>
                    <h3 class="font-bold text-xl mb-3 group-hover:text-purple-400 transition-colors"><?= $f['title'] ?></h3>
                    <p class="text-slate-400 leading-relaxed"><?= $f['desc'] ?></p>
                </div>
            <?php endforeach ?>
        </div>
    </section>

    <section id="games" class="max-w-7xl mx-auto px-6 py-24">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-extrabold mb-2 text-white">Game Populer</h2>
                <p class="text-slate-400">Pilih game favoritmu dan mulai bertransaksi</p>
            </div>
            <a href="#" class="text-purple-400 font-bold hover:underline">Lihat Semua</a>
        </div>


        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <?php while ($g = mysqli_fetch_assoc($query_games)): ?>
                <a href="detail-game.php?id=<?= $g['id'] ?>" class="block group">
                    <div
                        class="glass rounded-2xl overflow-hidden hover:scale-105 transition-all duration-300 cursor-pointer neon-border">
                        <div class="relative overflow-hidden">
                            <img src="<?= $g['image_url'] ?>"
                                class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500"
                                alt="<?= $g['name'] ?>">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4">
                                <span
                                    class="text-xs font-bold px-3 py-1 bg-purple-600 rounded-lg text-white shadow-lg shadow-purple-600/50">Top
                                    Up</span>
                            </div>
                        </div>
                        <div class="p-4 text-center font-bold text-sm tracking-wide text-white group-hover:text-purple-400">
                            <?= $g['name'] ?>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

    <footer class="py-12 border-t border-white/5 bg-black/20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="font-bold text-xl uppercase tracking-widest text-slate-300">
                <span class="text-purple-500">GAME</span>STORE
            </div>

            <div class="flex gap-8 text-sm text-slate-400 font-medium">
                <a href="syarat-ketentuan.php" class="hover:text-purple-400 transition">Syarat & Ketentuan</a>
                <a href="kebijakan-privasi.php" class="hover:text-purple-400 transition">Kebijakan Privasi</a>
                <a href="hubungi-kami.php" class="hover:text-purple-400 transition">Hubungi Kami</a>
            </div>

            <div class="text-sm text-slate-500">
                © 2025 GameStore. Created by Rinda Wulandari.
            </div>
        </div>
    </footer>

</body>

</html>