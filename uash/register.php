<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email sudah terdaftar!');</script>";
    } else {
        $query = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$password')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Pendaftaran Berhasil!'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar | GameStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: #020617; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .hero-gradient { background: radial-gradient(circle at 50% 50%, #3b0764, transparent); }
    </style>
</head>
<body class="text-slate-200 antialiased min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <div class="absolute inset-0 hero-gradient opacity-30"></div>

    <div class="max-w-lg w-full relative z-10 py-10">
        

        <div class="glass p-8 md:p-10 rounded-[2.5rem] shadow-2xl border border-white/10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold mb-2 text-white">Buat <span class="text-purple-500">Akun Baru</span></h1>
                <p class="text-slate-400 text-sm">Lengkapi data untuk mendapatkan promo menarik</p>
            </div>

            <form action="#" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 text-slate-300">Nama Lengkap</label>
                    <input input name="fullname"required type="text" placeholder="Masukkan nama lengkap" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 text-slate-300">Email</label>
                    <input type="email" name="email" required placeholder="nama@email.com" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold mb-2 ml-1 text-slate-300">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2 ml-1 text-slate-300">Konfirmasi Password</label>
                        <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-purple-600/20 active:scale-95">
                        Daftar Akun
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-slate-400">
                Sudah punya akun? 
                <a href="login.php" class="text-purple-400 font-bold hover:underline">Masuk di sini</a>
                <div> Kembali ke
                <a href="index.php" class="text-purple-400 font-bold hover:underline">Home</a>
            </div>
            </div>
            
        </div>
    </div>
</body>
</html>