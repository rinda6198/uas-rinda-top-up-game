<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }


if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


if (isset($_GET['delete'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['delete']);
    $get_img = mysqli_query($conn, "SELECT image_url FROM games WHERE id = '$id_hapus'");
    $img_data = mysqli_fetch_assoc($get_img);
    if ($img_data['image_url'] && file_exists($img_data['image_url'])) {
        unlink($img_data['image_url']);
    }
    mysqli_query($conn, "DELETE FROM items WHERE game_id = '$id_hapus'");
    mysqli_query($conn, "DELETE FROM games WHERE id = '$id_hapus'");
    header("Location: admin-games.php");
    exit;
}

$query_games = mysqli_query($conn, "SELECT * FROM games ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Game | Premium Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #020617; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: white;
            background-image: radial-gradient(circle at top right, rgba(139, 92, 246, 0.05), transparent), 
                              radial-gradient(circle at bottom left, rgba(139, 92, 246, 0.05), transparent);
        }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .modal { 
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); 
            z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(8px); 
        }
        .modal.active { display: flex; animation: fadeIn 0.3s ease-out; }
        
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        
        .game-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .game-card:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.05); border-color: rgba(168, 85, 247, 0.4); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        .game-card.hidden { display: none; }

        input:focus { box-shadow: 0 0 15px rgba(168, 85, 247, 0.1); }
        
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="flex min-h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-10">
        <header class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Katalog <span class="text-purple-500">Game</span></h1>
            <p class="text-slate-400 text-sm mt-1">Tambahkan dan atur daftar game yang tersedia untuk top up.</p>
        </header>

        <div class="grid lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4">
                <div class="glass p-8 rounded-[2.5rem] sticky top-10 border-t border-white/10">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-purple-500/20 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-purple-500" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        </div>
                        <h2 class="text-lg font-bold">Input Game Baru</h2>
                    </div>

                    <form action="proses-tambah-game.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                        <div class="group">
                            <label class="block text-[10px] text-slate-500 mb-2 uppercase font-black tracking-widest group-focus-within:text-purple-400 transition">Nama Game</label>
                            <input type="text" name="game_name" required placeholder="Mobile Legends" class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl outline-none focus:border-purple-500 text-sm transition-all placeholder:text-slate-600">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-2 uppercase font-black tracking-widest">Banner Image</label>
                            <div class="relative w-full">
                                <input type="file" name="game_img" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-full p-4 bg-white/5 border border-dashed border-white/20 rounded-2xl text-center">
                                    <span class="text-xs text-slate-400">Pilih file atau drop disini</span>
                                </div>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-[10px] text-slate-500 mb-2 uppercase font-black tracking-widest group-focus-within:text-purple-400 transition">Label Input User</label>
                            <input type="text" name="game_label" required placeholder="User ID (Zone ID)" class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl outline-none focus:border-purple-500 text-sm transition-all placeholder:text-slate-600">
                        </div>

                        <button type="submit" name="submit_game" class="w-full py-4 bg-purple-600 rounded-2xl font-black hover:bg-purple-700 transition-all uppercase text-xs tracking-[0.15em] shadow-xl shadow-purple-500/25 active:scale-[0.98]">Simpan Data</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                        <h2 class="font-bold text-slate-300 uppercase tracking-widest text-xs">Aktif Sekarang</h2>
                    </div>
                    
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <div class="relative flex-1 md:flex-none">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" id="searchInput" placeholder="Cari Game..." 
                                class="bg-white/5 border border-white/10 pl-11 pr-5 py-3 rounded-2xl focus:ring-2 ring-purple-500/20 focus:border-purple-500 outline-none text-sm w-full md:w-64 transition-all">
                        </div>
                        <button onclick="resetFilter()" class="p-3 bg-white/5 hover:bg-red-500/10 border border-white/10 rounded-2xl text-slate-400 hover:text-red-400 transition active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 xl:grid-cols-2 gap-5" id="gameGrid">
                    <?php while($g = mysqli_fetch_assoc($query_games)): ?>
                    <div class="game-card glass p-5 rounded-[2rem] border border-white/5 flex flex-col justify-between group" data-name="<?= strtolower($g['name']) ?>">
                        <div class="flex gap-5">
                            <div class="relative overflow-hidden rounded-2xl w-20 h-20 shrink-0 shadow-2xl border border-white/10">
                                <img src="<?= $g['image_url'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            </div>
                            <div class="overflow-hidden py-1">
                                <h3 class="font-bold text-white text-lg leading-tight truncate"><?= $g['name'] ?></h3>
                                <div class="inline-flex items-center gap-1.5 mt-2 bg-purple-500/10 px-2 py-1 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="text-purple-500"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <p class="text-[9px] text-purple-400 font-black uppercase tracking-tighter truncate"><?= $g['input_label'] ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-white/5 flex gap-2">
                            <a href="admin-items.php?game_id=<?= $g['id'] ?>" class="flex-1 text-center py-2.5 bg-white/5 hover:bg-white/10 rounded-xl text-[10px] font-black uppercase tracking-wider transition">Manage Items</a>
                            
                            <button onclick="openEditModal('<?= $g['id'] ?>', '<?= addslashes($g['name']) ?>', '<?= addslashes($g['input_label']) ?>')" class="p-2.5 bg-blue-500/10 hover:bg-blue-500 text-blue-500 hover:text-white rounded-xl transition shadow-lg shadow-blue-500/5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            
                            <a href="?delete=<?= $g['id'] ?>" onclick="return confirm('Hapus permanen game ini?')" class="p-2.5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-xl transition shadow-lg shadow-red-500/5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div id="noResults" class="hidden text-center py-32 bg-white/5 rounded-[3rem] border border-dashed border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto text-slate-700 mb-4" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <p class="text-slate-500 font-medium">Ops! Game yang kamu cari tidak ada.</p>
                </div>
            </div>
        </div>
    </main>

    <div id="editModal" class="modal">
        <div class="glass p-10 rounded-[3rem] w-full max-w-md border-t border-white/20 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-10 opacity-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            
            <h3 class="text-2xl font-black mb-8 relative">Edit <span class="text-purple-500 italic">Game</span></h3>
            
            <form action="proses-edit-game.php" method="POST" enctype="multipart/form-data" class="space-y-5 relative">
                <input type="hidden" name="game_id" id="edit_id">
                
                <div>
                    <label class="block text-[10px] text-slate-500 mb-2 uppercase font-black tracking-widest">Nama Game</label>
                    <input type="text" name="game_name" id="edit_name" required class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl focus:border-purple-500 outline-none transition text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] text-slate-500 mb-2 uppercase font-black tracking-widest">Ganti Banner</label>
                    <input type="file" name="game_img" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-white/10 file:text-white hover:file:bg-purple-600 transition">
                </div>
                
                <div>
                    <label class="block text-[10px] text-slate-500 mb-2 uppercase font-black tracking-widest">Label Input ID</label>
                    <input type="text" name="game_label" id="edit_label" required class="w-full p-4 bg-white/5 border border-white/10 rounded-2xl focus:border-purple-500 outline-none transition text-sm">
                </div>
                
                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="closeEditModal()" class="flex-1 py-4 bg-white/5 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-white/10 transition">Batal</button>
                    <button type="submit" name="update_game" class="flex-1 py-4 bg-purple-600 rounded-2xl font-black hover:bg-purple-700 transition shadow-xl shadow-purple-600/20 text-[10px] uppercase tracking-widest">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const gameCards = document.querySelectorAll('.game-card');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            let hasMatch = false;

            gameCards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(filter)) {
                    card.classList.remove('hidden');
                    hasMatch = true;
                } else {
                    card.classList.add('hidden');
                }
            });

            noResults.style.display = hasMatch ? 'none' : 'block';
        });

        function resetFilter() {
            searchInput.value = '';
            gameCards.forEach(card => card.classList.remove('hidden'));
            noResults.style.display = 'none';
            searchInput.focus();
        }

        const modal = document.getElementById('editModal');
        function openEditModal(id, name, label) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_label').value = label;
            modal.classList.add('active');
        }
        function closeEditModal() { modal.classList.remove('active'); }
        
      
        window.onclick = function(e) { if (e.target == modal) closeEditModal(); }
    </script>
</body>
</html>