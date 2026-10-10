<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id_login = $_SESSION['user_id'];
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

$payment_proof_path = "";
if (!empty($_FILES['payment_proof']['name'])) {
    $target_dir = "uploads/bukti_bayar/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_extension = pathinfo($_FILES["payment_proof"]["name"], PATHINFO_EXTENSION);
    $file_name = "USER_" . time() . "_" . $user_id_login . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
        $payment_proof_path = $target_file;
    }
}

$query_cart = mysqli_query($conn, "SELECT cart.*, games.name as game_name 
                                    FROM cart 
                                    JOIN games ON cart.game_id = games.id 
                                    WHERE cart.user_id = '$user_id_login'");

if (mysqli_num_rows($query_cart) > 0) {
    while ($item = mysqli_fetch_assoc($query_cart)) {
        $trx_id = "GS-" . strtoupper(uniqid());
        $game_name = $item['game_name'];
        $nominal = $item['item_name'];
        $account_player = $item['account_data'];

        $insert_order = "INSERT INTO orders (trx_id, game_name, user_id, item_name, account_data, status, payment_method, payment_proof) 
                         VALUES ('$trx_id', '$game_name', '$user_id_login', '$nominal', '$account_player', 'Pending', '$payment_method', '$payment_proof_path')";
        
        mysqli_query($conn, $insert_order);
    }

    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id_login'");
    echo "<script>alert('Checkout Berhasil! Pesanan sedang diproses admin.'); window.location='riwayat.php';</script>";
} else {
    header("Location: dashboard.php");
}
?>