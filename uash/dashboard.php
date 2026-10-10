<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}


$query_str = "SELECT games.*, MIN(items.price) as start_price 
              FROM games 
              LEFT JOIN items ON games.id = items.game_id 
              GROUP BY games.id 
              ORDER BY games.name ASC";
$result = mysqli_query($conn, $query_str);


$uid = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT profile_picture, fullname FROM users WHERE id = '$uid'");
$user_data = mysqli_fetch_assoc($user_query);
$_SESSION['profile_picture'] = $user_data['profile_picture'];
$_SESSION['fullname'] = $user_data['fullname'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pasar Game | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; color: white; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .card-glow:hover { border-color: #a855f7; box-shadow: 0 0 20px rgba(168, 85, 247, 0.15); }
    </style>
</head>

<body class="pb-20">

   <?php include 'navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-6 mt-12">
        <div class="mb-10">
            <h1 class="text-4xl font-black italic uppercase tracking-tighter">Pasar <span class="text-purple-500">Game</span></h1>
            <p class="text-slate-400">Cari item, skin, atau top up game favoritmu di sini.</p>
        </div>

        <section class="mb-12">
            <div class="relative w-full"> <input type="text" id="searchInput" placeholder="Ketik nama game favoritmu..."
                    class="w-full glass px-6 py-4 rounded-2xl outline-none focus:border-purple-500 transition text-white">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </section>

        <div id="gameContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="game-card glass p-5 rounded-[2.5rem] hover:scale-105 transition-all duration-300 group card-glow border border-white/5" 
                         data-name="<?= strtolower($row['name']) ?>">
                        
                        <div class="relative overflow-hidden rounded-[1.8rem] mb-5 aspect-video">
                            <img src="<?= $row['image_url'] ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                alt="<?= $row['name'] ?>">
                        </div>

                        <h3 class="game-title font-bold text-xl mb-1 px-1"><?= $row['name'] ?></h3>
                        <p class="text-slate-500 text-xs mb-6 px-1">Mulai dari 
                            <span class="text-green-400 font-bold text-sm">
                                Rp <?= number_format($row['start_price'] ?? 0, 0, ',', '.') ?>
                            </span>
                        </p>

                        <a href="detail-game.php?id=<?= $row['id'] ?>"
                            class="block text-center bg-white/5 group-hover:bg-purple-600 py-4 rounded-2xl font-black text-sm transition-all duration-300">
                            BELI SEKARANG
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div id="noGameFound" class="col-span-full py-20 text-center glass rounded-[2.5rem]">
                    <p class="text-slate-500 italic">Belum ada game tersedia.</p>
                </div>
            <?php endif; ?>
        </div>

        <div id="emptySearchMessage" class="hidden col-span-full py-20 text-center glass rounded-[2.5rem]">
            <p class="text-slate-500 italic">Maaf, game tidak ditemukan.</p>
        </div>
    </main>

    <script>
        const searchInput = document.getElementById('searchInput');
        const gameCards = document.querySelectorAll('.game-card');
        const emptyMessage = document.getElementById('emptySearchMessage');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let hasVisibleCards = false;

            gameCards.forEach(card => {
                const gameName = card.getAttribute('data-name');
                if (gameName.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    hasVisibleCards = true;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (!hasVisibleCards) {
                emptyMessage.classList.remove('hidden');
            } else {
                emptyMessage.classList.add('hidden');
            }
        });
    </script>

</body>
</html>