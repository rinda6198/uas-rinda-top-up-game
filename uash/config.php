<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gamestore_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
