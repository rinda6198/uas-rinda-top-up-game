<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['update_status'])) {
    $id_order = mysqli_real_escape_string($conn, $_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $admin_note = "";

    if (!empty($_FILES['bukti_proses']['name'])) {
        $target_dir = "uploads/bukti_proses/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES["bukti_proses"]["name"], PATHINFO_EXTENSION);
        $file_name = "DONE_" . time() . "_" . $id_order . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["bukti_proses"]["tmp_name"], $target_file)) {
            $admin_note = ", admin_note = '$target_file'";
        }
    }

    $sql = "UPDATE orders SET status = '$status' $admin_note WHERE id = '$id_order'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>window.location='admin-dashboard.php?msg=updated';</script>";
        exit;
    }
}

$query = "SELECT orders.*, users.fullname FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Management Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; font-family: 'Plus Jakarta Sans', sans-serif; color: white; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 100; align-items: center; justify-content: center; padding: 20px; transition: all 0.3s; }
        .modal.active { display: flex; animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #020617; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }

        .glow-purple { box-shadow: 0 0 20px rgba(168, 85, 247, 0.15); }
        select option { background: #0f172a; color: white; }
    </style>
</head>
<body class="flex min-h-screen">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-10">
        <header class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-10">
            <div>
                <h1 class="text-4xl font-black tracking-tighter italic uppercase">Pesanan <span class="text-purple-500">Masuk</span></h1>
                <p class="text-slate-400 text-sm mt-1">Verifikasi pembayaran dan selesaikan transaksi secara real-time.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                <div class="relative flex-1 md:flex-none">
                    <input type="text" id="globalSearch" placeholder="Cari ID, Nama, Akun..." 
                        class="w-full md:w-64 bg-white/5 border border-white/10 px-5 py-3 rounded-2xl text-sm outline-none focus:border-purple-500 transition-all focus:ring-4 ring-purple-500/10">
                </div>
                
                <select id="statusFilter" class="bg-white/5 border border-white/10 px-4 py-3 rounded-2xl text-sm outline-none cursor-pointer focus:border-purple-500 transition">
                    <option value="all">Semua Status</option>
                    <option value="pending">🟡 Pending</option>
                    <option value="success">🟢 Success</option>
                    <option value="canceled">🔴 Canceled</option>
                </select>

                <select id="paymentFilter" class="bg-white/5 border border-white/10 px-4 py-3 rounded-2xl text-sm outline-none cursor-pointer focus:border-purple-500 transition">
                    <option value="all">Semua Bayar</option>
                    <option value="dana">DANA</option>
                    <option value="bca">BCA</option>
                    <option value="qris">QRIS</option>
                    <option value="gopay">GOPAY</option>
                </select>

                <button onclick="resetFilters()" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl transition group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:rotate-180 transition-transform duration-500"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                </button>
            </div>
        </header>

        <div class="glass rounded-[2.5rem] overflow-hidden glow-purple">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-white/5 text-slate-400 text-[11px] uppercase tracking-[0.25em]">
                        <tr>
                            <th class="p-6">Detail Transaksi</th>
                            <th class="p-6">Metode & Bukti</th>
                            <th class="p-6">Item & Tujuan</th>
                            <th class="p-6 text-center">Status</th>
                            <th class="p-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5" id="orderTableBody">
                        <?php while ($o = mysqli_fetch_assoc($result)): ?>
                            <tr class="order-row hover:bg-white/[0.03] transition-all" 
                                data-status="<?= strtolower($o['status'] ?: 'pending') ?>"
                                data-payment="<?= strtolower($o['payment_method'] ?: 'n/a') ?>"
                                data-search="<?= strtolower($o['trx_id'].' '.$o['fullname'].' '.$o['game_name'].' '.$o['account_data']) ?>">
                                
                                <td class="p-6">
                                    <div class="font-mono text-purple-400 text-xs mb-1 font-bold">#<?= $o['trx_id'] ?></div>
                                    <div class="font-bold text-white text-base leading-tight"><?= $o['fullname'] ?? 'Guest User' ?></div>
                                    <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
                                    </div>
                                </td>

                                <td class="p-6">
                                    <div class="inline-flex flex-col">
                                        <span class="text-xs font-black text-slate-200 uppercase tracking-tighter"><?= $o['payment_method'] ?? 'N/A' ?></span>
                                        <?php if (!empty($o['payment_proof'])): ?>
                                            <button onclick="viewImage('<?= $o['payment_proof'] ?>', 'Bukti Bayar: <?= $o['fullname'] ?>')" 
                                                class="mt-2 text-[10px] bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1.5 rounded-xl hover:bg-blue-500 hover:text-white transition font-bold">
                                                CEK PEMBAYARAN
                                            </button>
                                        <?php else: ?>
                                            <span class="text-[10px] text-slate-600 italic mt-1">Belum Upload</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td class="p-6">
                                    <div class="font-bold text-sm text-white"><?= $o['game_name'] ?></div>
                                    <div class="text-xs text-purple-400 mb-2"><?= $o['item_name'] ?></div>
                                    <div class="bg-slate-800/50 px-3 py-1.5 rounded-lg border border-white/5 inline-flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                                        <span class="font-mono text-xs text-slate-300"><?= $o['account_data'] ?></span>
                                    </div>
                                </td>

                                <td class="p-6 text-center">
                                    <?php 
                                        $s = strtolower($o['status'] ?: 'pending');
                                        $theme = ($s == 'success') ? 'green' : (($s == 'canceled') ? 'red' : 'yellow');
                                    ?>
                                    <span class="px-4 py-2 rounded-full text-[10px] font-black border bg-<?= $theme ?>-500/10 text-<?= $theme ?>-400 border-<?= $theme ?>-500/20 inline-block">
                                        <?= strtoupper($s) ?>
                                    </span>
                                </td>

                                <td class="p-6">
                                    <div class="flex flex-col items-end gap-2">
                                        <?php if ($s == 'pending'): ?>
                                            <div class="flex gap-2">
                                                <button onclick="openUploadModal('<?= $o['id'] ?>', '<?= $o['trx_id'] ?>')" 
                                                    title="Selesaikan"
                                                    class="p-3 bg-green-500/10 hover:bg-green-600 text-green-500 hover:text-white rounded-2xl transition border border-green-500/20 shadow-lg shadow-green-500/5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                                </button>

                                                <form action="" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                                    <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                                    <input type="hidden" name="status" value="Canceled">
                                                    <button type="submit" name="update_status" 
                                                        class="p-3 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-2xl transition border border-red-500/20 shadow-lg shadow-red-500/5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($o['admin_note'])): ?>
                                            <button onclick="viewImage('<?= $o['admin_note'] ?>', 'Bukti Pengiriman Admin')" 
                                                class="text-[10px] font-black text-slate-500 hover:text-purple-400 transition-all flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                LIHAT BUKTI PROSES
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div id="noData" class="hidden p-20 text-center flex flex-col items-center">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center text-slate-500 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <h3 class="text-xl font-bold">Data tidak ditemukan</h3>
                <p class="text-slate-500 text-sm">Coba ubah kata kunci atau filter kamu.</p>
            </div>
        </div>
    </main>

    <div id="uploadModal" class="modal">
        <div class="glass p-8 rounded-[2.5rem] w-full max-w-md border border-white/10 shadow-2xl relative">
            <button onclick="closeModal('uploadModal')" class="absolute top-6 right-6 text-slate-500 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <h3 class="text-2xl font-black mb-1 text-white uppercase italic">Selesaikan</h3>
            <p id="modalTrxId" class="text-purple-400 font-mono text-sm mb-6 font-bold"></p>
            
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="id" id="targetOrderId">
                <input type="hidden" name="status" value="Success">
                
                <div class="space-y-3">
                    <label class="block text-[11px] text-slate-400 uppercase font-black tracking-widest">Unggah Screenshot Sukses</label>
                    <div class="group relative">
                        <input type="file" name="bukti_proses" required id="fileInput"
                            class="w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer bg-white/5 rounded-2xl border border-white/10 p-2">
                    </div>
                </div>

                <button type="submit" name="update_status" class="w-full py-4 bg-purple-600 hover:bg-purple-700 rounded-2xl font-black text-sm transition-all shadow-xl shadow-purple-600/20 active:scale-95">
                    KONFIRMASI SELESAI
                </button>
            </form>
        </div>
    </div>

    <div id="imageModal" class="modal" onclick="closeModal('imageModal')">
        <div class="max-w-4xl w-full p-4 flex flex-col items-center">
            <div class="glass p-2 rounded-3xl relative">
                <h3 id="imageTitle" class="text-white font-black mb-4 uppercase tracking-widest text-xs text-center pt-2">Preview</h3>
                <img id="viewImg" src="" class="max-h-[80vh] w-auto rounded-2xl shadow-2xl border border-white/10">
                <p class="text-slate-500 text-[10px] mt-4 italic text-center pb-2">Klik di mana saja untuk menutup</p>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('globalSearch');
        const statusFilter = document.getElementById('statusFilter');
        const paymentFilter = document.getElementById('paymentFilter');
        const rows = document.querySelectorAll('.order-row');
        const noData = document.getElementById('noData');

        function doFilter() {
            const q = searchInput.value.toLowerCase();
            const s = statusFilter.value.toLowerCase();
            const p = paymentFilter.value.toLowerCase();
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                const rowStatus = row.getAttribute('data-status');
                const rowPayment = row.getAttribute('data-payment');

                const matchText = searchData.includes(q);
                const matchStatus = (s === 'all' || rowStatus === s);
                const matchPayment = (p === 'all' || rowPayment.includes(p));

                if (matchText && matchStatus && matchPayment) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            noData.classList.toggle('hidden', visibleCount > 0);
        }

        searchInput.addEventListener('input', doFilter);
        statusFilter.addEventListener('change', doFilter);
        paymentFilter.addEventListener('change', doFilter);

        function resetFilters() {
            searchInput.value = '';
            statusFilter.value = 'all';
            paymentFilter.value = 'all';
            doFilter();
        }

        function openUploadModal(id, trx) {
            document.getElementById('targetOrderId').value = id;
            document.getElementById('modalTrxId').innerText = "TRANSAKSI #" + trx;
            document.getElementById('uploadModal').classList.add('active');
        }

        function viewImage(src, title) {
            if(!src || src === '') return;
            document.getElementById('imageTitle').innerText = title;
            document.getElementById('viewImg').src = src;
            document.getElementById('imageModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        
        window.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') {
                closeModal('uploadModal');
                closeModal('imageModal');
            }
        });
    </script>
</body>
</html>