<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            
            if (isset($row['status']) && $row['status'] === 'blocked') {
                echo "<script>alert('Akun Anda telah diblokir. Silakan hubungi admin.'); window.location='login.php';</script>";
                exit;
            }

            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['fullname'];
            $_SESSION['is_superadmin'] = $row['is_superadmin'];

            if ($row['role'] == 'admin') {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }
    }
    echo "<script>alert('Email atau Password salah!');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | GameStore</title>
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

    <div class="max-w-md w-full relative z-10">
        

        <div class="glass p-8 md:p-10 rounded-[2.5rem] shadow-2xl border border-white/10">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold mb-2 text-white">Selamat <span class="text-purple-500">Datang!</span></h1>
                <p class="text-slate-400 text-sm">Masuk untuk melanjutkan transaksi Anda</p>
            </div>

            <form action="#" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 text-slate-300">Email</label>
                    <input type="email"name="email" required placeholder="nama@email.com" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2 ml-1 text-slate-300">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-purple-500 transition text-white placeholder:text-slate-600">
                </div>
                
                

                <button type="submit" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-purple-600/20 active:scale-95">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-10 text-center text-sm text-slate-400">
                Belum punya akun? 
                <a href="register.php" class="text-purple-400 font-bold hover:underline">Daftar Gratis</a>
                 <div class=" text-center text-sm text-slate-400">
                Kembali ke
                <a href="index.php" class="text-purple-400 font-bold hover:underline">Home</a>
                
            </div>
            </div>
            
        </div>
    </div>
</body>
</html>