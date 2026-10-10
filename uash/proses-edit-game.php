<?php
include 'config.php';

if (isset($_POST['update_game'])) {
    $id = $_POST['game_id'];
    $name = mysqli_real_escape_string($conn, $_POST['game_name']);
    $label = mysqli_real_escape_string($conn, $_POST['game_label']);

    $query_old = mysqli_query($conn, "SELECT image_url FROM games WHERE id = '$id'");
    $old_data = mysqli_fetch_assoc($query_old);
    $image_path = $old_data['image_url'];

    if ($_FILES['game_img']['name'] != "") {
        $target_dir = "uploads/games/";
        $file_extension = pathinfo($_FILES["game_img"]["name"], PATHINFO_EXTENSION);
        $filename = "game_" . time() . "." . $file_extension;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["game_img"]["tmp_name"], $target_file)) {
            if (file_exists($image_path)) { unlink($image_path); }
            $image_path = $target_file;
        }
    }

    $update = mysqli_query($conn, "UPDATE games SET name = '$name', image_url = '$image_path', input_label = '$label' WHERE id = '$id'");
    
    header("Location: admin-games.php?updated=1");
}
?>