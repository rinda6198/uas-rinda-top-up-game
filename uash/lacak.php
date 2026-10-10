<?php
include 'config.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$hasil = null;
$error = null;

if (isset($_POST['cari'])) {
    $id_input = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    
    $query = "SELECT * FROM orders WHERE trx_id = '$id_input'";
    $exec  = mysqli_query($conn, $query);
    
    if ($exec) {
        if (mysqli_num_rows($exec) > 0) {
            $hasil = mysqli_fetch_assoc($exec);
        } else {
            $error = "ID Transaksi '$id_input' tidak ditemukan.";
        }
    } else {
        $error = "Terjadi kesalahan database.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; font-family: 'Plus Jakarta Sans', sans-serif; color: white; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-6 py-10">
    <div class="max-w-xl w-full space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold mb-4 uppercase tracking-tighter">Lacak <span class="text-purple-500">Pesanan</span></h1>
            <p class="text-slate-400">Masukkan ID Transaksi untuk melihat status top up Anda.</p>
        </div>

        <div class="glass p-10 rounded-[2.5rem] relative overflow-hidden">
             <div class="absolute top-0 right-0 w-32 h-32 bg-purple-600/10 blur-[60px]"></div>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold mb-3 ml-1 text-slate-300">ID Transaksi</label>
                    <input type="text" name="id_transaksi" required placeholder="GS-6949940xxxxx" 
                           class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600 font-mono"
                           value="<?= isset($_POST['id_transaksi']) ? htmlspecialchars($_POST['id_transaksi']) : '' ?>">
                </div>
                <button type="submit" name="cari" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-purple-900/20 active:scale-95">
                    Cek Status Sekarang
                </button>
            </form>

            <?php if ($hasil): ?>
                <div class="mt-8 p-6 bg-white/5 rounded-2xl border border-purple-500/30">
                    <h3 class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-4">Detail Pesanan:</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-slate-400 text-sm">Game</span>
                            <span class="font-semibold text-sm"><?= $hasil['game_name']; ?></span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-2">
                            <span class="text-slate-400 text-sm">Item</span>
                            <span class="font-semibold text-sm"><?= $hasil['item_name']; ?></span>
                        </div>
                        <div class="flex justify-between pt-2">
                            <span class="text-slate-400 text-sm">Status</span>
                            <?php 
                                $statusColor = "text-yellow-400 bg-yellow-400/10";
                                if($hasil['status'] == 'Success') $statusColor = "text-green-400 bg-green-400/10";
                                if($hasil['status'] == 'Canceled') $statusColor = "text-red-400 bg-red-400/10";
                            ?>
                            <span class="px-3 py-1 <?= $statusColor ?> rounded-full text-xs font-bold uppercase">
                                <?= $hasil['status']; ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 text-sm">Waktu</span>
                            <span class="text-xs text-slate-300"><?= $hasil['created_at']; ?></span>
                        </div>
                    </div>
                </div>
            <?php elseif ($error): ?>
                <div class="mt-8 p-4 bg-red-500/10 border border-red-500/50 rounded-2xl text-red-500 text-center text-sm font-medium">
                    <?= $error; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center">
            <a href="dashboard.php" class="text-sm text-slate-500 hover:text-white transition">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>