<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; color: white; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .badge-pending { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.2); }
        .badge-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); }
        .badge-canceled { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    </style>
</head>
<body class="p-6 pb-20">

    <div class="max-w-7xl mx-auto">
        <header class="mb-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-black italic uppercase tracking-tighter">Riwayat <span class="text-purple-500">Transaksi</span></h1>
                <p class="text-slate-400 text-sm">Cek status dan detail pembayaran kamu.</p>
            </div>
            <a href="dashboard.php" class="text-slate-400 hover:text-white transition font-bold text-sm">Dashboard</a>
        </header>

        <div class="glass rounded-[2rem] overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-white/5 text-slate-400 text-[10px] uppercase tracking-widest">
                    <tr>
                        <th class="p-6">Transaksi</th>
                        <th class="p-6">Produk</th>
                        <th class="p-6">Tujuan / ID</th>
                        <th class="p-6 text-center">Metode</th>
                        <th class="p-6">Bukti Bayar</th>
                        <th class="p-6 text-center">Status</th>
                        <th class="p-6">Bukti Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="p-6">
                                <div class="font-mono text-purple-400 text-xs mb-1">#<?= $row['trx_id'] ?></div>
                                <div class="text-[10px] text-slate-500"><?= date('d M, H:i', strtotime($row['created_at'])) ?></div>
                            </td>
                            
                            <td class="p-6">
                                <div class="font-bold text-white text-sm"><?= $row['game_name'] ?></div>
                                <div class="text-xs text-slate-400"><?= $row['item_name'] ?></div>
                            </td>

                            <td class="p-6">
                                <div class="text-sm font-medium text-slate-300"><?= $row['account_data'] ?></div>
                            </td>

                            <td class="p-6 text-center">
                                <span class="bg-white/5 px-3 py-1 rounded text-[10px] font-bold text-slate-300 border border-white/10 italic">
                                    <?= !empty($row['payment_method']) ? strtoupper($row['payment_method']) : '-' ?>
                                </span>
                            </td>

                            <td class="p-6">
                                <?php if(!empty($row['payment_proof'])): ?>
                                    <a href="<?= $row['payment_proof'] ?>" target="_blank" class="text-[10px] font-bold text-blue-400 hover:text-blue-300 transition underline underline-offset-4 tracking-tighter">
                                        LIHAT PEMBAYARAN
                                    </a>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-600 italic">No Data</span>
                                <?php endif; ?>
                            </td>

                            <td class="p-6">
                                <div class="flex justify-center">
                                    <?php 
                                        $s = strtolower($row['status']);
                                        $cls = ($s == 'success') ? 'badge-success' : (($s == 'canceled') ? 'badge-canceled' : 'badge-pending');
                                    ?>
                                    <span class="<?= $cls ?> px-4 py-1 rounded-full text-[10px] font-black uppercase">
                                        <?= $row['status'] ?>
                                    </span>
                                </div>
                            </td>

                            <td class="p-6">
                                <?php if($row['status'] == 'Success' && !empty($row['admin_note'])): ?>
                                    <a href="<?= $row['admin_note'] ?>" target="_blank" class="text-[10px] font-bold text-green-400 hover:text-green-300 transition underline underline-offset-4 tracking-tighter">
                                        LIHAT SELESAI
                                    </a>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-600 italic">Menunggu...</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="p-20 text-center text-slate-500">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>