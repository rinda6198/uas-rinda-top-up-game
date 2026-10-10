<?php

$game_name = $_POST['game_name'] ?? 'Tidak Diketahui';
$user_id = $_POST['user_id'] ?? '-';
$nominal = $_POST['nominal'] ?? '-';
$price = "Rp " . number_format(rand(10000, 500000), 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; font-family: 'Plus Jakarta Sans', sans-serif; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full glass rounded-[2.5rem] p-8 md:p-10 border-t-4 border-t-purple-500 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-600/10 blur-[50px]"></div>
        
        <h2 class="text-2xl font-extrabold mb-6 text-center italic uppercase tracking-tighter">Konfirmasi <span class="text-purple-500">Pesanan</span></h2>
        
        <div class="space-y-4 mb-8">
            <div class="flex justify-between border-b border-white/5 pb-3">
                <span class="text-slate-400 text-sm">Game</span>
                <span class="font-bold"><?= $game_name ?></span>
            </div>
            <div class="flex justify-between border-b border-white/5 pb-3">
                <span class="text-slate-400 text-sm">ID Akun</span>
                <span class="font-bold text-purple-400"><?= $user_id ?></span>
            </div>
            <div class="flex justify-between border-b border-white/5 pb-3">
                <span class="text-slate-400 text-sm">Item</span>
                <span class="font-bold"><?= $nominal ?></span>
            </div>
            <div class="flex justify-between pt-2">
                <span class="text-slate-400 text-sm">Total Bayar</span>
                <span class="font-extrabold text-xl text-green-400"><?= $price ?></span>
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="alert('Pesanan Diproses!')" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-bold transition shadow-lg shadow-purple-900/20">
                Bayar Sekarang
            </button>
            <a href="index.php" class="block w-full py-4 text-center text-slate-500 hover:text-white text-sm transition">
                Batalkan Pesanan
            </a>
        </div>
    </div>

</body>
</html>