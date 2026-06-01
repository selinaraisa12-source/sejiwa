<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>SEJIWA - Musik Relaksasi</title>

<!-- Font & Icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&display=swap" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #fff;
  background: linear-gradient(-45deg, #d0bfff, #ffb5e8, #b5ffea, #ffb5c5);
  background-size: 400% 400%;
  animation: gradientBG 20s ease infinite;
  transition: background 0.4s, color 0.4s;
}

@keyframes gradientBG {
  0% {background-position: 0% 50%;}
  50% {background-position: 100% 50%;}
  100% {background-position: 0% 50%;}
}

@keyframes fadeDown {from{opacity:0;transform:translateY(-50px);}to{opacity:1;transform:translateY(0);} }
@keyframes fadeLeft {from{opacity:0;transform:translateX(-50px);}to{opacity:1;transform:translateX(0);} }

/* Judul */
h2 {
  margin: 80px 0 40px;
  font-size: 54px;
  font-family: 'Playfair Display', serif;
  text-align: center;
  text-shadow: 2px 2px 6px rgba(0,0,0,0.25);
  animation: fadeDown 1s ease forwards;
  color: white;
}

/* Wrapper tombol musik */
.musik-wrapper {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  width: 100%;
  max-width: 900px;
  padding: 20px;
}

/* HP = 1 kolom */
@media (max-width: 768px) {
  .musik-wrapper {
    grid-template-columns: 1fr;
  }
}

/* Tombol musik */
.musik-btn {
  padding: 16px 24px;
  border: none;
  border-radius: 14px;
  background: linear-gradient(to right, #ff758c, #a566ff);
  color: white;
  font-size: 17px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  display: flex;
  align-items: center;
  gap: 10px;
  justify-content: flex-start;
  box-shadow: 0 6px 14px rgba(0,0,0,0.18);
  opacity: 0;
  transition: .3s;
}
.musik-btn:hover {transform: scale(1.05);}
.musik-btn i {font-size: 20px;}

/* Player Box */
.player-box {
  margin-top: 25px;
  width: 90%;
  max-width: 800px;
  background: rgba(165,102,255,.75);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  display: none;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
  animation: fadeDown 1s ease forwards;
}

audio {width: 100%;}

/* Tombol kembali */
.back-btn {
  position: fixed;
  top: 18px;
  left: 18px;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #a566ff, #d63384);
  border: none;
  color: white;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.25);
  transition: .3s;
}
.back-btn:hover {transform: scale(1.1);}

/* === DARK MODE (lebih hidup & elegan) === */
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

body.dark-mode h2 {
  color: #e0cfff;
  text-shadow: 0 0 10px rgba(165, 102, 255, 0.6);
}

/* Tombol musik */
body.dark-mode .musik-btn {
  background: linear-gradient(145deg, #3a2b52, #5a3e7a);
  color: #fff;
  box-shadow: 0 4px 14px rgba(165,102,255,0.3);
  border: 1px solid rgba(255,255,255,0.1);
  transition: 0.3s ease;
}

body.dark-mode .musik-btn:hover {
  background: linear-gradient(145deg, #5b3f7a, #7a5bb0);
  transform: scale(1.05);
  box-shadow: 0 0 15px rgba(221,160,255,0.4);
}

/* Player Box */
body.dark-mode .player-box {
  background: linear-gradient(145deg, #2d2145, #3b2b5a);
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 0 20px rgba(165,102,255,0.25);
  backdrop-filter: blur(12px);
}

/* Tombol kembali */
body.dark-mode .back-btn {
  background: linear-gradient(135deg, #7a44ff, #c77dff);
  box-shadow: 0 0 15px rgba(199,125,255,0.4);
}
body.dark-mode .back-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 0 25px rgba(199,125,255,0.6);
}
</style>
</head>

<body>

<h2>Musik Relaksasi</h2>

<?php $query = mysqli_query($conn, "SELECT * FROM tb_musik ORDER BY id_musik ASC"); ?>

<div class="musik-wrapper">
<?php $i=0; while ($musik=mysqli_fetch_assoc($query)): ?>
<button class="musik-btn"
  style="animation: fadeLeft 1s ease forwards; animation-delay: <?= 0.2 + $i*0.1 ?>s"
  onclick="playMusic('<?= $musik['file_musik'] ?>')">
  <i class="fa-solid fa-music"></i> <?= $musik['judul'] ?>
</button>
<?php $i++; endwhile; ?>
</div>

<div class="player-box" id="playerBox">
<audio id="audioPlayer" controls>
  <source src="" type="audio/mpeg">
  Browser Anda tidak mendukung audio.
</audio>
</div>

<button class="back-btn" onclick="window.history.back()">
  <i class="fas fa-arrow-left"></i>
</button>

<script>
function playMusic(file) {
  const box = document.getElementById("playerBox");
  const player = document.getElementById("audioPlayer");
  box.style.display = "block";
  player.src = "musik/" + file;
  player.play();
}
</script>

<script src="theme.js"></script>
</body>
</html>
