<?php
session_start();    // Mulai session
session_unset();    // Hapus semua variabel session
session_destroy();  // Hancurkan session

// Setelah session dihancurkan, redirect ke halaman intro.php
header("Location: intro.php");
exit();
?>
