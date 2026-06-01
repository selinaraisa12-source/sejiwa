<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['id_user'])) {
    header("Location: awal.php");
    exit();
}

// Ambil mode dari dashboard
$mode = $_SESSION['mode'] ?? 'light'; // default light
$bodyClass = ($mode === 'dark') ? 'dark-mode' : 'light-mode';

// Data user
$namaPengguna = $_SESSION['nama'] ?? 'Pengguna';

// Ambil hasil tes terbaru
$stmt = $conn->prepare("SELECT skor, kategori, tanggal FROM tb_hasil WHERE id_user_ref=? ORDER BY tanggal DESC LIMIT 10");
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$result = $stmt->get_result();
$riwayatTes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Tes - SEJIWA</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: background 0.4s, color 0.4s;
}

/* Container utama */
.container {
  margin-top: 90px;
  border-radius: 20px;
  padding: 35px;
  width: 90%;
  max-width: 950px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.25);
  animation: fadeDown 1s ease forwards;
  text-align: center;
  backdrop-filter: blur(14px);
}

@keyframes fadeDown {
  from {opacity:0;transform:translateY(-50px);}
  to {opacity:1;transform:translateY(0);}
}

/* Judul */
h2 {
  font-family: 'Playfair Display', serif;
  font-size: 48px;
  margin-bottom: 28px;
  text-shadow: 2px 2px 6px rgba(0,0,0,0.25);
}

/* Tabel hasil tes */
.table-wrapper { overflow-x: auto; border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
table { width: 100%; border-collapse: separate; border-spacing: 0; }
th, td { padding: 14px 16px; text-align: center; }
th { font-weight: 600; text-transform: uppercase; letter-spacing: .5px; font-size: 14px; }
tr { transition: 0.3s; }
tr:hover { transform: scale(1.01); }
tbody tr:not(:last-child) td { border-bottom: 1px solid rgba(0,0,0,0.1); }

/* Warna kategori (light mode default) */
.tr-normal { background: rgba(181,255,200,0.35); color: #003b1f; }
.tr-sedang { background: rgba(255,244,192,0.35); color: #4b3b00; }
.tr-tinggi { background: rgba(255,192,192,0.35); color: #3b0000; }

/* LIGHT MODE lebih berdimensi */
body.light-mode {
  background: linear-gradient(-45deg, #d0bfff, #ffb5e8, #b5ffea, #ffb5c5);
  color: #222;
  background-size: 400% 400%;
  animation: gradientBG 20s ease infinite;
}

body.light-mode .container {
  background: rgba(255,255,255,0.75); /* lebih tebal & lembut */
  backdrop-filter: blur(12px);
  border: 1px solid rgba(0,0,0,0.1);
  box-shadow: 0 6px 25px rgba(0,0,0,0.2);
}

body.light-mode h2 {
  color: #222;
  text-shadow: 1px 1px 6px rgba(0,0,0,0.2); /* efek dimensi */
}

body.light-mode table th {
  background: rgba(255,255,255,0.85);
  color: #222;
  text-shadow: 0 0 3px rgba(0,0,0,0.15);
}

body.light-mode td {
  color: #111;
  text-shadow: 0 0 2px rgba(0,0,0,0.1);
}

body.light-mode .tr-normal { background: rgba(181,255,200,0.5); color: #003b1f; }
body.light-mode .tr-sedang { background: rgba(255,244,192,0.5); color: #4b3b00; }
body.light-mode .tr-tinggi { background: rgba(255,192,192,0.5); color: #3b0000; }

body.light-mode tr:hover td { text-shadow: 0 0 4px rgba(0,0,0,0.25); }


/* === DARK MODE === */
body.dark-mode {
  background: linear-gradient(135deg, #1b0a2a, #2b1d4a, #141414);
  color: #f1f1f1;
  background-size: 200% 200%;
  animation: darkGradient 20s ease infinite;
}
@keyframes darkGradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

body.dark-mode .container {
  background: linear-gradient(145deg, #2d2145, #3b2b5a);
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 0 20px rgba(165,102,255,0.25);
}

body.dark-mode h2 { color: #e0cfff; text-shadow: 0 0 10px rgba(165,102,255,0.6); }
body.dark-mode table th { background: rgba(255,255,255,0.15); color: #fff; text-shadow: 0 0 8px rgba(165,102,255,0.6); }
body.dark-mode td { color: #fff; text-shadow: 0 0 4px rgba(165,102,255,0.4); }
body.dark-mode tr:hover td { text-shadow: 0 0 6px rgba(255,255,255,0.8); }

/* Warna kategori di dark mode */
body.dark-mode .tr-normal { background: rgba(0,255,100,0.25); color: #b8ffcd; box-shadow: inset 0 0 10px rgba(0,255,100,0.3); }
body.dark-mode .tr-sedang { background: rgba(255,215,0,0.25); color: #fff2a8; box-shadow: inset 0 0 10px rgba(255,215,0,0.3); }
body.dark-mode .tr-tinggi { background: rgba(255,80,80,0.25); color: #ffc4c4; box-shadow: inset 0 0 10px rgba(255,80,80,0.3); }

/* Tombol */
button {
  margin: 20px 10px;
  padding: 14px 30px;
  border: none;
  border-radius: 14px;
  font-weight: 600;
  cursor: pointer;
  background: linear-gradient(to right, #ff758c, #a566ff);
  color: white;
  box-shadow: 0 6px 14px rgba(0,0,0,0.18);
  transition: .3s;
}
button:hover { transform: scale(1.05); }

body.dark-mode button {
  background: linear-gradient(145deg, #5b3f7a, #7a5bb0);
  box-shadow: 0 0 15px rgba(221,160,255,0.4);
}
body.dark-mode button:hover {
  transform: scale(1.07);
  box-shadow: 0 0 20px rgba(199,125,255,0.5);
}
/* Pesan kosong (Belum ada hasil tes) */
body.light-mode .empty-msg {
    color: #222;
    font-size: 1.1rem;
}

body.dark-mode .empty-msg {
    color: #fff;
    font-size: 1.1rem;
}

</style>
</head>
<body class="<?= $bodyClass ?>">
<div class="container">
  <h2>Riwayat Hasil Tes Anda</h2>

  <?php if (empty($riwayatTes)): ?>
  <p class="empty-msg">Belum ada hasil tes.</p>
<?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Skor</th>
          <th>Kategori</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; foreach($riwayatTes as $tes): 
          $kelas = ($tes['kategori']=='Normal')?'tr-normal':(($tes['kategori']=='Sedang')?'tr-sedang':'tr-tinggi'); ?>
        <tr class="<?= $kelas ?>">
          <td><?= $no++ ?></td>
          <td><?= $tes['skor'] ?></td>
          <td><?= $tes['kategori'] ?></td>
          <td><?= date("d F Y", strtotime($tes['tanggal'])); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>


  <div>
    <button onclick="location.href='dashboard.php'">Kembali ke Dashboard</button>
    <button onclick="location.href='musik.php'">Musik Relaksasi</button>
  </div>
</div>
<script src="theme.js"></script>
</body>
</html>
