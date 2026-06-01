<?php
session_start();

$userFile = __DIR__ . '/users.json';


// Pastikan file user ada
if (!file_exists($userFile)) {
    file_put_contents($userFile, json_encode([]));
}

$users = json_decode(file_get_contents($userFile), true) ?? [];

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Proses Registrasi
if (isset($_POST['register'])) {
    $newUser = trim($_POST['username']);
    $newPass = trim($_POST['password']);

    if ($newUser && $newPass) {
        if (isset($users[$newUser])) {
            $error = "Username sudah terdaftar!";
        } else {
            $users[$newUser] = password_hash($newPass, PASSWORD_DEFAULT);
            file_put_contents($userFile, json_encode($users));
            $success = "Pendaftaran berhasil! Silakan login.";
        }
    } else {
        $error = "Isi semua kolom!";
    }
}

// Proses Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        $_SESSION['user'] = ucfirst($username);
    } else {
        $error = "Username atau password salah!";
    }
}

// Jika belum login → tampil form login/daftar
if (!isset($_SESSION['user'])):
    $showRegister = isset($_GET['register']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $showRegister ? 'Daftar Akun' : 'Login' ?> SEJIWA</title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700&display=swap" rel="stylesheet"><style>
    :root {
        --pink: #ffd6e7;
        --purple: #e3c6ff;
    }
    body {
        font-family: 'Poppins', 'Segoe UI', Roboto, Arial, sans-serif;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .box {
        background: rgba(255,255,255,0.95);
        padding: 30px 25px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        text-align: center;
        width: 100%;
        max-width: 350px;
    }
    h1 {
        font-size: 2rem;
        margin-bottom: 5px;
        font-weight: bold;
        background: linear-gradient(90deg, #ff6f91, #9b5de5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .welcome {
        font-size: 1.4rem;
        font-weight: bold;
        color: #333;
        margin: 10px 0 5px;
    }
    .subtitle {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 20px;
    }
    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        font-family: 'Poppins', 'Segoe UI', Roboto, Arial, sans-serif;
    }
    button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(90deg, #ff6f91, #9b5de5);
        color: white;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s;
    }
    button:hover {
        transform: translateY(-2px);
    }
    .error { color: red; font-size: 0.9rem; margin-bottom: 10px; }
    .success { color: green; font-size: 0.9rem; margin-bottom: 10px; }
    a { display: block; margin-top: 15px; font-size: 0.9rem; color: #6b21a8; text-decoration: none; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="box">
    <h1>SEJIWA</h1>
    <div class="welcome"><?= $showRegister ? 'Daftar Akun' : 'Selamat Datang' ?></div>
    <div class="subtitle"><?= $showRegister ? 'Buat akun baru untuk memulai' : 'Silakan login untuk memulai tes kesehatan mental Anda' ?></div>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <?php if ($showRegister): ?>
            <button type="submit" name="register">Daftar</button>
            <a href="?">Sudah punya akun? Login</a>
        <?php else: ?>
            <button type="submit" name="login">Login</button>
            <a href="?register=1">Belum punya akun? Daftar</a>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
<?php
exit;
endif;
?>

<!-- Halaman Beranda -->
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SEJIWA - Beranda</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    :root {
        --pink: #ffd6e7;
        --purple: #e3c6ff;
        --text-dark: #2b2b2b;
    }
    body {
        margin: 0;
        font-family: 'Poppins', 'Segoe UI', Roboto, Arial, sans-serif;
        background: linear-gradient(135deg, var(--pink), var(--purple));
        color: var(--text-dark);
    }
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: white;
    }
    header h1 {
        font-size: 1.8rem;
        font-weight: bold;
        background: linear-gradient(90deg, #ff6f91, #9b5de5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
    }
    .user {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: bold;
        color: #008067;
    }
    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ccc;
    }
    main {
        text-align: center;
        padding: 40px 20px;
    }
    .menu {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-top: 40px;
    }
    .menu-item {
        background: white;
        border-radius: 20px;
        padding: 20px;
        width: 150px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.2s;
        text-decoration: none;
        color: var(--text-dark);
    }
    .menu-item:hover {
        transform: translateY(-5px);
    }
    .menu-item img {
        width: 60px;
        height: 60px;
        margin-bottom: 10px;
    }
    .menu-item p {
        margin: 0;
        font-weight: bold;
    }
    footer {
        margin-top: 50px;
        display: flex;
        justify-content: center;
        gap: 40px;
        padding-bottom: 20px;
    }
    .footer-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-weight: bold;
        text-decoration: none;
    }
    .footer-item img {
        width: 28px;
        height: 28px;
        filter: brightness(0) invert(1);
        margin-bottom: 5px;
    }
    .footer-item span {
        color: white;
    }
</style>
</head>
<body>

<header>
    <h1>SEJIWA</h1>
    <div class="user">
        <?= htmlspecialchars($_SESSION['user']); ?>
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profil" class="profile-pic">
    </div>
</header>

<main>
    <h2>Halo, <?= htmlspecialchars($_SESSION['user']); ?>!</h2>
    <div class="menu">
        <a href="mulai_tes.php" class="menu-item">
            <img src="https://cdn-icons-png.flaticon.com/512/1067/1067566.png" alt="Mulai Tes">
            <p>MULAI TES</p>
        </a>
        <a href="hasil_tes.php" class="menu-item">
            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828640.png" alt="Hasil Tes">
            <p>HASIL TES</p>
        </a>
        <a href="musik.php" class="menu-item">
            <img src="https://cdn-icons-png.flaticon.com/512/727/727245.png" alt="Musik">
            <p>MUSIK</p>
        </a>
    </div>
</main>

<footer>
    <a href="#" class="footer-item">
        <img src="https://cdn-icons-png.flaticon.com/512/1946/1946433.png" alt="Beranda">
        <span>BERANDA</span>
    </a>
    <a href="?logout=1" class="footer-item">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828490.png" alt="Logout">
        <span>LOGOUT</span>
    </a>
</footer>

</body>
</html>