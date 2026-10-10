<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hubungi Kami | GameStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #020617; font-family: 'Plus Jakarta Sans', sans-serif; color: #cbd5e1; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="p-6 md:p-20 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full glass p-10 rounded-[2rem] text-center">
        <h1 class="text-3xl font-extrabold text-white mb-4">Butuh <span class="text-purple-500">Bantuan?</span></h1>
        <p class="text-slate-400 mb-8">Customer Service kami siap membantu Anda 24/7 terkait kendala top up.</p>
        
        <div class="space-y-4">
            <a href="https://wa.me/6288287151761" class="block w-full p-4 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-bold transition flex items-center justify-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81A2,2,0,0,1,.45,9L8.5,9a16,16,0,0,0,6,6l-.5-.5a2,2,0,0,1,.45-.5A13,13,0,0,0,.7,.5A2,2,0,0,1,.5,.5z"/></svg>
                WhatsApp (Admin)
            </a>
            
            <a href="mailto:support@gamestore.com" class="block w-full p-4 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-2xl font-bold transition">
                Email: support@gamestore.com
            </a>
        </div>
        
        <div class="mt-8">
            <a href="index.php" class="text-slate-500 hover:text-purple-400 transition text-sm">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>