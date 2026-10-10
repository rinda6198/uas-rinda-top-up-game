<?php
include 'config.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT cart.*, games.name as game_name, games.image_url 
          FROM cart 
          JOIN games ON cart.game_id = games.id 
          WHERE cart.user_id = '$user_id' 
          ORDER BY cart.id DESC";

$result = mysqli_query($conn, $query);
$total_bayar = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            background: #020617;
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="p-6 pb-20">

    <div class="max-w-4xl mx-auto">
        <header class="mb-10 flex justify-between items-center">
            <h1 class="text-4xl font-black italic uppercase tracking-tighter">
                Keranjang <span class="text-purple-500">Kamu</span>
            </h1>
            <a href="dashboard.php" class="text-slate-400 hover:text-white transition text-sm font-bold"> Kembali
                Belanja</a>
        </header>

        <div class="space-y-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $total_bayar += $row['price'];
                    ?>
                    <div
                        class="glass p-6 rounded-[2rem] flex flex-col md:flex-row items-center gap-6 border-l-4 border-purple-600">
                        <img src="<?= $row['image_url'] ?>" class="w-20 h-20 object-cover rounded-2xl shadow-lg">

                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-xl"><?= $row['game_name'] ?></h3>
                            <p class="text-slate-400 text-sm"><?= $row['item_name'] ?> • ID: <span
                                    class="text-white font-mono"><?= $row['account_data'] ?></span></p>
                        </div>

                        <div class="text-right">
                            <p class="text-purple-400 font-black text-xl mb-2">Rp
                                <?= number_format($row['price'], 0, ',', '.') ?></p>
                            <a href="hapus-item.php?id=<?= $row['id'] ?>"
                                class="text-xs bg-red-500/10 text-red-500 px-4 py-2 rounded-full font-bold hover:bg-red-500 hover:text-white transition">Hapus</a>
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="mt-12 glass p-8 rounded-[2.5rem] border-t-4 border-green-500">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">Total Pembayaran</p>
                            <h2 class="text-4xl font-black text-white">Rp <?= number_format($total_bayar, 0, ',', '.') ?>
                            </h2>
                        </div>
                        <div class="hidden md:block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                                stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="5" rx="2" />
                                <line x1="2" x2="22" y1="10" y2="10" />
                            </svg>
                        </div>
                    </div>

                    <form action="proses-checkout.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Metode
                                    Pembayaran</label>
                                <select name="payment_method" required
                                    class="w-full bg-white/5 border border-white/10 p-4 rounded-2xl outline-none focus:border-purple-500">
                                    <option value="QRIS" class="bg-slate-900">QRIS (Otomatis)</option>
                                    <option value="DANA" class="bg-slate-900">DANA - 08123456789</option>
                                    <option value="BCA" class="bg-slate-900">BCA - 123456789 a/n GameStore</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Upload Bukti
                                    Transfer</label>
                                <input type="file" name="payment_proof" required
                                    class="w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:bg-purple-600 file:text-white cursor-pointer bg-white/5 rounded-2xl border border-white/10">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-5 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-black text-xl transition-all shadow-xl shadow-purple-600/30">
                            KONFIRMASI & BAYAR SEKARANG
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="text-center py-20 glass rounded-[3rem]">
                    <div class="flex justify-center mb-6 opacity-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="8" cy="21" r="1" />
                            <circle cx="19" cy="21" r="1" />
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                        </svg>
                    </div>
                    <p class="text-slate-400 text-lg mb-8">Keranjang belanja kamu masih kosong nih...</p>
                    <a href="dashboard.php"
                        class="bg-purple-600 px-10 py-4 rounded-2xl font-bold hover:bg-purple-700 transition">Cari Game
                        Favorit</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>