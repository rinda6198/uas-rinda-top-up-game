<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_item = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM cart WHERE id = '$id_item' AND user_id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        header("Location: keranjang.php");
        exit;
    } else {
        echo "Gagal menghapus item: " . mysqli_error($conn);
    }
} else {
    header("Location: keranjang.php");
    exit;
}
?>