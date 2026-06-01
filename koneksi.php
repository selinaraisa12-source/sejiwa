<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sejiwa";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set timezone MySQL ke WIB
mysqli_query($conn, "SET time_zone = '+07:00'");
?>
