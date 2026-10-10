<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 1;


$query_game = mysqli_query($conn, "SELECT * FROM games WHERE id = '$id'");
$game = mysqli_fetch_assoc($query_game);

if (!$game) {
    die("Game tidak ditemukan!");
}

$query_items = mysqli_query($conn, "SELECT * FROM items WHERE game_id = '$id' ORDER BY item_group DESC, price ASC");
$grouped_items = [];
while ($row = mysqli_fetch_assoc($query_items)) {
    $group = $row['item_group'] ?: 'Umum';
    $grouped_items[$group][] = $row;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $user_id_login = $_SESSION['user_id'];
    $game_id = $_POST['game_id'];
    $item_id = $_POST['item_id'];
    $account_data = mysqli_real_escape_string($conn, $_POST['account_id']);

    
    $image_name = "";
    if (isset($_FILES['image_proof']) && $_FILES['image_proof']['error'] == 0) {
        $target_dir = "uploads/orders/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);
        $image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['image_proof']['name']);
        move_uploaded_file($_FILES['image_proof']['tmp_name'], $target_dir . $image_name);
    }

    $res_item = mysqli_query($conn, "SELECT * FROM items WHERE id = '$item_id'");
    $item_info = mysqli_fetch_assoc($res_item);

    if ($item_info) {
        $item_name = $item_info['item_name'];
        $price = $item_info['price'];

        $insert = "INSERT INTO cart (user_id, game_id, item_name, price, account_data, image_proof) 
                   VALUES ('$user_id_login', '$game_id', '$item_name', '$price', '$account_data', '$image_name')";

        if (mysqli_query($conn, $insert)) {
            echo "<script>alert('Berhasil ditambah ke keranjang!'); window.location='keranjang.php';</script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Top Up <?= $game['name'] ?> | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: #020617;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: white;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backface-visibility: hidden;
            transform: translateZ(0);
        }

        input:checked+label {
            border: 2px solid #a855f7 !important;
            background: rgba(168, 85, 247, 0.15) !important;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .item-img {
            backface-visibility: hidden;
            transform: translateZ(0);
            will-change: transform;
        }

        /* Hilangkan scrollbar tapi tetap bisa scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased pb-20">

    <?php include 'navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-8 mt-10">
        <div class="md:col-span-1">
            <div class="glass p-6 rounded-[2.5rem] sticky top-24">
                <img src="<?= $game['image_url'] ?>" class="w-full h-72 object-cover rounded-[2rem] mb-6 shadow-2xl">
                <h1 class="text-3xl font-black italic uppercase mb-2"><?= $game['name'] ?></h1>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed">Top up aman, murah, dan terpercaya. Proses kilat
                    hanya dalam hitungan detik.</p>
                <div class="space-y-3">
                    <div
                        class="flex items-center gap-4 bg-white/[0.03] border border-white/[0.08] pl-2 pr-5 py-2 rounded-full w-fit group">
                        <div
                            class="w-8 h-8 rounded-full bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                            <div class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <span
                                class="text-[10px] font-black tracking-widest text-purple-400 uppercase leading-none">System
                                Active</span>
                            <span class="text-[8px] font-medium text-slate-500 uppercase tracking-tight mt-1">Verified
                                Gateway</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <form action="" method="POST" enctype="multipart/form-data" id="orderForm">
                <input type="hidden" name="game_id" value="<?= $game['id'] ?>">

                <div class="glass p-8 rounded-[2.5rem] mb-6">
                    <div class="flex gap-4 items-center mb-6">
                        <span
                            class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center font-black italic">1</span>
                        <h2 class="text-xl font-black uppercase italic tracking-tight">Id Game</h2>
                    </div>
                    <div class="space-y-6">
                        <input type="text" name="account_id" placeholder="<?= $game['input_label'] ?>" required
                            class="bg-white/5 border border-white/10 p-5 rounded-2xl focus:outline-none focus:border-purple-500 transition w-full text-white font-bold placeholder:text-slate-600">
                    </div>
                </div>

                <div class="glass p-8 rounded-[2.5rem] mb-6">
                    <div class="flex gap-4 items-center mb-8">
                        <span
                            class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center font-black italic">2</span>
                        <h2 class="text-xl font-black uppercase italic tracking-tight">Pilih Kategori</h2>
                    </div>

                    <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar">
                        <?php $count = 0;
                        foreach ($grouped_items as $group_name => $items_list): ?>
                            <button type="button" onclick="showTab('tab-<?= $count ?>', this)"
                                class="tab-btn px-8 py-3 rounded-2xl font-black text-xs uppercase transition-all whitespace-nowrap <?= $count === 0 ? 'bg-purple-600 text-white' : 'bg-white/5 text-slate-400 border border-white/5 hover:bg-white/10' ?>">
                                <?= $group_name ?>
                            </button>
                            <?php $count++; endforeach; ?>
                    </div>

                    <div class="mt-6">
                        <?php $count = 0;
                        foreach ($grouped_items as $group_name => $items_list): ?>
                            <div id="tab-<?= $count ?>" class="tab-content <?= $count === 0 ? 'active' : '' ?>">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    <?php foreach ($items_list as $item): ?>
                                        <div class="relative">
                                            <input type="radio" name="item_id" value="<?= $item['id'] ?>"
                                                id="item-<?= $item['id'] ?>" class="hidden peer" required>
                                            <label for="item-<?= $item['id'] ?>"
                                                class="flex items-center gap-4 glass p-4 rounded-3xl border border-white/5 cursor-pointer hover:bg-white/10 transition-all h-full group peer-checked:border-purple-500">

                                                <div
                                                    class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white/10 rounded-xl overflow-hidden">
                                                    <img src="uploads/items/<?= $item['image_url'] ?>"
                                                        class="item-img w-10 h-10 object-contain transition-transform duration-300 group-hover:scale-110"
                                                        onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512/5260/5260498.png';">
                                                </div>

                                                <div class="flex-grow">
                                                    <div
                                                        class="text-sm font-black text-white group-hover:text-purple-400 transition leading-tight">
                                                        <?= $item['item_name'] ?>
                                                    </div>
                                                    <div class="text-xs font-bold text-slate-500 mt-1 italic">
                                                        Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php $count++; endforeach; ?>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-6 bg-purple-600 hover:bg-purple-700 text-white rounded-[2rem] font-black text-xl transition-all shadow-2xl shadow-purple-600/30 active:scale-95 uppercase italic">
                    Tambah Ke Keranjang
                </button>
            </form>
        </div>
    </main>

    <script>
        function showTab(tabId, btn) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(tab => tab.classList.remove('active'));

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(b => {
                b.classList.remove('bg-purple-600', 'text-white');
                b.classList.add('bg-white/5', 'text-slate-400');
            });

            const target = document.getElementById(tabId);
            if (target) target.classList.add('active');

            btn.classList.add('bg-purple-600', 'text-white');
            btn.classList.remove('bg-white/5', 'text-slate-400');
        }
    </script>
</body>

</html>