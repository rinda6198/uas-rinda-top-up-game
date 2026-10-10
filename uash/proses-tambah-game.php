<?php
include 'config.php';

if (isset($_POST['submit_game'])) {
    $name = mysqli_real_escape_string($conn, $_POST['game_name']);
    $label = mysqli_real_escape_string($conn, $_POST['game_label']);
    
    $target_dir = "uploads/games/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $file_extension = pathinfo($_FILES["game_img"]["name"], PATHINFO_EXTENSION);
    $filename = "game_" . time() . "." . $file_extension;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["game_img"]["tmp_name"], $target_file)) {
        mysqli_query($conn, "INSERT INTO games (name, image_url, input_label) VALUES ('$name', '$target_file', '$label')");
        header("Location: admin-games.php?success=1");
    } else {
        echo "Gagal upload gambar.";
    }
}
?>