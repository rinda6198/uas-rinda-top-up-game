<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }


if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit;
}

$game_id = isset($_GET['game_id']) ? mysqli_real_escape_string($conn, $_GET['game_id']) : die("ID Game tidak ditemukan!");
$game_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM games WHERE id = '$game_id'"));


if (isset($_POST['add_item'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = $_POST['price'];
    $item_group = strtoupper(trim(mysqli_real_escape_string($conn, $_POST['item_group'])));
    $image_name = "";
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $target_dir = "uploads/items/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION);
        $image_name = "item_" . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['item_image']['tmp_name'], $target_dir . $image_name);
    }
    mysqli_query($conn, "INSERT INTO items (game_id, item_name, price, item_group, image_url) VALUES ('$game_id', '$item_name', '$price', '$item_group', '$image_name')");
    $_SESSION['msg'] = "Produk berhasil ditambahkan!";
    header("Location: admin-items.php?game_id=$game_id"); exit;
}


if (isset($_POST['quick_edit'])) {
    $id = $_POST['item_id'];
    $price = $_POST['price'];
    $group = strtoupper(trim(mysqli_real_escape_string($conn, $_POST['item_group'])));
    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
        $target_dir = "uploads/items/";
        $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_url FROM items WHERE id = '$id'"));
        if(!empty($old['image_url']) && file_exists($target_dir . $old['image_url'])) { @unlink($target_dir . $old['image_url']); }
        $ext = pathinfo($_FILES['update_image']['name'], PATHINFO_EXTENSION);
        $new_image = "item_upd_" . time() . "_" . uniqid() . "." . $ext;
        if(move_uploaded_file($_FILES['update_image']['tmp_name'], $target_dir . $new_image)) {
            mysqli_query($conn, "UPDATE items SET image_url = '$new_image' WHERE id = '$id'");
        }
    }
    mysqli_query($conn, "UPDATE items SET price = '$price', item_group = '$group' WHERE id = '$id'");
    $_SESSION['msg'] = "Data diperbarui!";
    header("Location: admin-items.php?game_id=$game_id"); exit;
}


if (isset($_POST['batch_update'])) {
    if (!empty($_POST['selected_items'])) {
        $new_group = strtoupper(trim(mysqli_real_escape_string($conn, $_POST['batch_group'])));
        $ids = implode(',', array_map('intval', $_POST['selected_items']));
        mysqli_query($conn, "UPDATE items SET item_group = '$new_group' WHERE id IN ($ids)");
        $_SESSION['msg'] = count($_POST['selected_items']) . " item diperbarui!";
    }
    header("Location: admin-items.php?game_id=$game_id"); exit;
}


if (isset($_GET['delete_item'])) {
    $id_item = $_GET['delete_item'];
    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_url FROM items WHERE id = '$id_item'"));
    if(!empty($old['image_url']) && file_exists("uploads/items/" . $old['image_url'])) @unlink("uploads/items/" . $old['image_url']);
    mysqli_query($conn, "DELETE FROM items WHERE id = '$id_item'");
    header("Location: admin-items.php?game_id=$game_id"); exit;
}


$list_groups = mysqli_query($conn, "SELECT DISTINCT TRIM(item_group) as grp FROM items WHERE game_id = '$game_id' AND item_group != '' ORDER BY grp ASC");
$all_groups = [];
while($g = mysqli_fetch_assoc($list_groups)) { $all_groups[] = $g['grp']; }
$query_items = mysqli_query($conn, "SELECT * FROM items WHERE game_id = '$game_id' ORDER BY item_group ASC, price ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - <?= $game_info['name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; font-family: 'Plus Jakarta Sans', sans-serif; color: white; }
        .glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; }
        
        
        .filter-input { 
            background: rgba(255, 255, 255, 0.07) !important; 
            color: #ffffff !important; 
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }
        .filter-input::placeholder { color: rgba(255, 255, 255, 0.4); }
        .filter-input option { background: #0f172a; color: white; }
    </style>
</head>
<body class="flex min-h-screen">
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 p-8">
        <?php if(isset($_SESSION['msg'])): ?>
            <div id="toast" class="fixed top-5 right-5 z-50 bg-purple-600 text-white px-6 py-3 rounded-xl shadow-2xl font-bold italic uppercase text-xs tracking-widest">
                <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
            <script>setTimeout(() => document.getElementById('toast').remove(), 3000);</script>
        <?php endif; ?>

        <header class="flex flex-col xl:flex-row justify-between items-center gap-6 mb-10">
            <h1 class="text-4xl font-black italic uppercase">Manage <span class="text-purple-500"><?= $game_info['name'] ?></span></h1>
            
            <div class="flex flex-wrap items-center justify-end gap-3">
                <input type="text" id="searchInput" placeholder="Cari item..." class="filter-input px-5 py-3 rounded-2xl text-sm outline-none focus:border-purple-500 w-44">
                
                <div class="flex items-center bg-white/5 rounded-2xl border border-white/10 overflow-hidden">
                    <input type="number" id="minPrice" placeholder="Min Rp" class="filter-input bg-transparent border-0 px-4 py-3 text-sm outline-none w-24">
                    <div class="text-white/20">-</div>
                    <input type="number" id="maxPrice" placeholder="Max Rp" class="filter-input bg-transparent border-0 px-4 py-3 text-sm outline-none w-24">
                </div>

                <select id="filterGroup" class="filter-input px-4 py-3 rounded-2xl text-sm outline-none">
                    <option value="all">Semua Grup</option>
                    <?php foreach($all_groups as $gr): ?>
                        <option value="<?= htmlspecialchars($gr) ?>"><?= htmlspecialchars($gr) ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="sortPrice" class="filter-input px-4 py-3 rounded-2xl text-sm outline-none">
                    <option value="default">Urutan Default</option>
                    <option value="low">Harga Termurah</option>
                    <option value="high">Harga Termahal</option>
                </select>

                <button onclick="resetFilters()" class="p-3 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                </button>
            </div>
        </header>

        <div class="grid lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <div class="glass p-6 rounded-[2rem] sticky top-8">
                    <h3 class="font-black uppercase text-purple-500 text-xs mb-6 tracking-tighter">Tambah Produk Baru</h3>
                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="relative bg-white/5 border-2 border-dashed border-white/10 rounded-2xl p-4 text-center">
                            <input type="file" name="item_image" id="imgInput" class="hidden" accept="image/*">
                            <label for="imgInput" class="cursor-pointer block">
                                <img id="imgPreview" class="hidden w-full h-32 object-contain mb-2 rounded-xl">
                                <div id="imgPlaceholder" class="text-[10px] font-bold text-slate-500 uppercase">Upload Thumbnail</div>
                            </label>
                        </div>
                        <input type="text" name="item_group" list="group_list" required placeholder="NAMA GRUP (CAPS)" class="w-full p-3 bg-white/5 border border-white/10 rounded-xl text-sm uppercase outline-none focus:border-purple-500 text-white">
                        <input type="text" name="item_name" required placeholder="Nama Produk" class="w-full p-3 bg-white/5 border border-white/10 rounded-xl text-sm outline-none focus:border-purple-500 text-white">
                        <input type="number" name="price" required placeholder="Harga Jual" class="w-full p-3 bg-white/5 border border-white/10 rounded-xl text-sm text-emerald-400 font-black outline-none">
                        <button type="submit" name="add_item" class="w-full py-4 bg-purple-600 rounded-xl font-black uppercase text-xs shadow-lg shadow-purple-900/40 hover:bg-purple-500 transition">Simpan Database</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-3">
                <form action="" method="POST" id="batchForm">
                    <div id="batchBar" class="hidden mb-4 p-3 bg-purple-600 rounded-2xl flex items-center justify-between">
                        <span class="text-xs font-black uppercase ml-2 text-white"><span id="selectedCount">0</span> Dipilih</span>
                        <div class="flex gap-2">
                            <input type="text" name="batch_group" list="group_list" placeholder="Pindah Grup..." class="bg-white/20 border-0 rounded-xl px-4 py-2 text-xs text-white placeholder-white/50 outline-none uppercase w-40">
                            <button type="submit" name="batch_update" class="bg-white text-purple-600 px-5 py-2 rounded-xl text-xs font-black uppercase">Update</button>
                        </div>
                    </div>
                </form>

                <div class="glass rounded-[2rem] overflow-hidden">
                    <table class="w-full text-left" id="itemTable">
                        <thead class="bg-white/5 text-[10px] uppercase font-black text-slate-400">
                            <tr>
                                <th class="p-5 w-12 text-center"><input type="checkbox" id="checkAll" class="w-4 h-4 rounded"></th>
                                <th class="p-5">Informasi Produk</th>
                                <th class="p-5">Quick Edit</th>
                                <th class="p-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="divide-y divide-white/5">
                            <?php while($item = mysqli_fetch_assoc($query_items)): $id = $item['id']; ?>
                                <tr class="item-row hover:bg-white/[0.02] transition" 
                                    data-group="<?= htmlspecialchars($item['item_group']) ?>" 
                                    data-price="<?= (int)$item['price'] ?>">
                                    
                                    <td class="p-5 text-center">
                                        <input type="checkbox" name="selected_items[]" value="<?= $id ?>" form="batchForm" class="item-checkbox w-4 h-4 rounded">
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-lg bg-slate-900 border border-white/10 overflow-hidden relative group">
                                                <input type="file" name="update_image" form="form_<?= $id ?>" id="upImg_<?= $id ?>" class="hidden" accept="image/*" onchange="previewUpdate(this, <?= $id ?>)">
                                                <label for="upImg_<?= $id ?>" class="cursor-pointer block w-full h-full">
                                                    <?php if(!empty($item['image_url'])): ?>
                                                        <img id="prev_<?= $id ?>" src="uploads/items/<?= $item['image_url'] ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div id="prev_<?= $id ?>" class="flex items-center justify-center h-full text-[8px] text-slate-600 font-black">N/A</div>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                                                <div class="text-[10px] text-purple-400 font-bold uppercase tracking-tight"><?= htmlspecialchars($item['item_group']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="price" form="form_<?= $id ?>" value="<?= (int)$item['price'] ?>" class="w-28 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-xs font-black text-emerald-400 outline-none">
                                            <input type="text" name="item_group" form="form_<?= $id ?>" value="<?= htmlspecialchars($item['item_group']) ?>" class="w-24 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-xs font-bold outline-none uppercase text-slate-300">
                                            <button type="submit" name="quick_edit" form="form_<?= $id ?>" class="p-2.5 bg-emerald-500/10 text-emerald-500 rounded-lg hover:bg-emerald-500 hover:text-white transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        <a href="?game_id=<?= $game_id ?>&delete_item=<?= $id ?>" onclick="return confirm('Hapus Item?')" class="p-2.5 bg-red-500/10 text-red-500 rounded-lg hover:bg-red-500 hover:text-white inline-block transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </a>
                                        <form id="form_<?= $id ?>" action="" method="POST" enctype="multipart/form-data" class="hidden">
                                            <input type="hidden" name="item_id" value="<?= $id ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
       
        function previewUpdate(input, id) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const prev = document.getElementById('prev_' + id);
                    if (prev.tagName === 'IMG') prev.src = e.target.result;
                    else {
                        const img = document.createElement('img');
                        img.id = 'prev_' + id; img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';
                        prev.parentNode.replaceChild(img, prev);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('imgInput').onchange = e => {
            const [file] = e.target.files;
            if (file) {
                document.getElementById('imgPreview').src = URL.createObjectURL(file);
                document.getElementById('imgPreview').classList.remove('hidden');
                document.getElementById('imgPlaceholder').classList.add('hidden');
            }
        }

     
        const sInp = document.getElementById('searchInput');
        const minP = document.getElementById('minPrice');
        const maxP = document.getElementById('maxPrice');
        const fGrp = document.getElementById('filterGroup');
        const sPrc = document.getElementById('sortPrice');
        const tBody = document.getElementById('tableBody');

        function filterAndSort() {
            let rows = Array.from(document.querySelectorAll('.item-row'));
            const q = sInp.value.toLowerCase();
            const minV = parseInt(minP.value) || 0;
            const maxV = parseInt(maxP.value) || Infinity;
            const g = fGrp.value;
            const sort = sPrc.value;

            rows.forEach(row => {
                const name = row.querySelector('.item-name').innerText.toLowerCase();
                const group = row.getAttribute('data-group');
                const price = parseInt(row.getAttribute('data-price'));

                const matchesSearch = name.includes(q);
                const matchesGroup = (g === 'all' || group === g);
                const matchesPrice = (price >= minV && price <= maxV);

                row.style.display = (matchesSearch && matchesGroup && matchesPrice) ? '' : 'none';
            });

            if (sort !== 'default') {
                rows.sort((a, b) => {
                    const priceA = parseInt(a.getAttribute('data-price'));
                    const priceB = parseInt(b.getAttribute('data-price'));
                    return sort === 'low' ? priceA - priceB : priceB - priceA;
                });
                rows.forEach(row => tBody.appendChild(row));
            }
        }

   
        [sInp, minP, maxP].forEach(el => el.addEventListener('input', filterAndSort));
        [fGrp, sPrc].forEach(el => el.addEventListener('change', filterAndSort));

        function resetFilters() {
            sInp.value = ''; minP.value = ''; maxP.value = ''; 
            fGrp.value = 'all'; sPrc.value = 'default';
            filterAndSort();
        }

       
        const cAll = document.getElementById('checkAll');
        const cItems = document.querySelectorAll('.item-checkbox');
        const bBar = document.getElementById('batchBar');
        const sCount = document.getElementById('selectedCount');

        function updateUI() {
            const selected = document.querySelectorAll('.item-checkbox:checked').length;
            sCount.innerText = selected;
            bBar.classList.toggle('hidden', selected === 0);
        }

        cAll.onclick = () => {
            cItems.forEach(c => { if(c.closest('tr').style.display !== 'none') c.checked = cAll.checked; });
            updateUI();
        }
        cItems.forEach(c => c.onchange = updateUI);

       
        document.addEventListener('input', e => {
            if(e.target.name === 'item_group' || e.target.name === 'batch_group') {
                e.target.value = e.target.value.toUpperCase();
            }
        });
    </script>

    <datalist id="group_list">
        <?php foreach($all_groups as $gr): ?><option value="<?= htmlspecialchars($gr) ?>"><?php endforeach; ?>
    </datalist>
</body>
</html>