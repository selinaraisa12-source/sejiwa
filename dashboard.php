<?php
// ===== SESSION & KONEKSI =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

/* ==== LOGIN (tetap) ==== */
if (!isset($_SESSION['id_user'])) {
    header("Location: intro.php");
    exit();
}

/* ==== AMBIL USER (tetap) ==== */
$id_user = $_SESSION['id_user'];
$sql = "SELECT nama, foto_profil FROM tb_users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

/* ==== FOTO PROFIL (tetap) ==== */
if (!empty($user['foto_profil']) && file_exists(__DIR__ . "/foto_user/" . $user['foto_profil'])) {
    $foto = "foto_user/" . $user['foto_profil'];
} else {
    $foto = "foto_user/default_profile.png";
}
$nama = $user['nama'] ?? 'Pengguna';

/* ==== ARTIKEL: SESUAIKAN DENGAN STRUKTUR YANG ADA ==== */
/* ambil max 10 artikel terbaru, cover dari <img> pertama di isi, ringkasan dari isi */
$articles = [];
$articles_err = '';

if ($conn instanceof mysqli) {
    $sqlA = "SELECT id_artikel, judul, penulis, isi, created_at
             FROM tb_artikel
             ORDER BY created_at DESC
             LIMIT 10";
    $resA = $conn->query($sqlA);

    if ($resA) {
        while ($row = $resA->fetch_assoc()) {
            $id    = (int)$row['id_artikel'];
            $judul = $row['judul'] ?: 'Tanpa Judul';
            $isi   = $row['isi'] ?? '';
            $tgl   = $row['created_at'] ?? '';

            // cari cover dari <img> pertama di isi
            $cover = 'assets/cover-placeholder.jpg';
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $isi, $m)) {
                $cover = $m[1];
            }

            // bikin excerpt dari isi (plain text)
            $plain   = trim(preg_replace('/\s+/', ' ', strip_tags($isi)));
            $excerpt = mb_substr($plain, 0, 140, 'UTF-8');
            if (mb_strlen($plain, 'UTF-8') > 140) {
                $excerpt .= '...';
            }

            // estimasi menit baca
            $readmin = max(1, (int)ceil(str_word_count($plain) / 200));

            $articles[] = [
                'id'      => $id,
                'judul'   => $judul,
                'cover'   => $cover,
                'excerpt' => $excerpt,
                'tanggal' => $tgl,
                'readmin' => $readmin,
            ];
        }
        $resA->close();
    } else {
        $articles_err = 'Belum ada artikel atau struktur tabel artikel tidak sesuai.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard - SEJIWA</title>

<!-- Anti flash putih saat reload dark mode -->
<script>
  if (localStorage.getItem("darkMode")==="true") {
    document.documentElement.classList.add("dark-mode");
    document.body?.classList?.add("dark-mode");
  }
</script>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="assets/favicon.ico">
<meta name="theme-color" content="#7c3aed">
<style>
/* ===== Reset & Palet ===== */
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#7c3aed; --brand2:#ec4899; --brand3:#06b6d4;
  --ink:#111827; --muted:#6b7280; --bg:#ffffff; --card:#ffffff;
  --r:22px; --gap:22px;
  --shadow-sm:0 6px 16px rgba(16,24,40,.06);
  --shadow-md:0 12px 28px rgba(16,24,40,.10);
}
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#f6f3ff 0%,#eef9ff 100%);
  color:var(--ink); display:flex; min-height:100vh; overflow-x:hidden;
}
body, .header, .sidebar, .card, .quote, .stat, .article {
  transition: background-color .3s ease, color .3s ease, box-shadow .3s ease;
}

/* ===== Dark mode ===== */
body.dark-mode{
  background: radial-gradient(1200px 600px at 20% -10%, #1f1f29 0%, #12121a 35%, #0f1117 100%) fixed;
  color:#e8e8ef;
}
body.dark-mode .stat{
  background: linear-gradient(90deg, #16174bff, #580c48ff);
  color:#fff;
  border-radius:var(--r);
  box-shadow:0 14px 34px rgba(0,0,0,.45);
  border:1px solid rgba(255,255,255,.06);
}
body.dark-mode .stat h2{ color:#ffffff; text-shadow:0 1px 2px rgba(0,0,0,.35); }
body.dark-mode .stat p{ color:#f3e8ff; opacity:.95; }
body.dark-mode .stat i,
body.dark-mode .stat .icon{
  color:#ffffff;
  filter: drop-shadow(0 1px 2px rgba(0,0,0,.35));
}
body.dark-mode .header{
  background:linear-gradient(90deg,#630AA2,#8b5cf6 50%,#580c48 100%);
}
body.dark-mode .sidebar{
  background:linear-gradient(180deg,#3b2a7a,#7a217b);
}
body.dark-mode .quote,
body.dark-mode .card,
body.dark-mode .article{
  background:#1f2230; color:#e8e8ef; box-shadow:none;
}
body.dark-mode .quote{ border-left-color:#c4b5fd }
body.dark-mode .muted,
body.dark-mode .article .excerpt,
body.dark-mode .article .meta{ color:#a5adba }
body.dark-mode .toggle-btn{ background:#1f2230; color:#c4b5fd }
body.dark-mode .darkmode-toggle{ background:#2a2f45; color:#fff }

/* ===== Sidebar ===== */
.sidebar{
  width:240px; min-width:240px; height:100vh; position:fixed; left:-260px; top:0; z-index:999;
  background:linear-gradient(180deg,var(--brand) 0%,var(--brand2) 100%);
  color:#fff; padding:22px;
  border-right:1px solid rgba(255,255,255,.15);
  transition:.3s;
}
.sidebar.show{ left:0 }
.sidebar h3{
  font-weight:800; text-align:center; margin-bottom:24px; letter-spacing:.3px;
}
.sidebar a{
  display:flex; align-items:center; gap:12px; color:#fff; text-decoration:none;
  padding:12px 10px; border-radius:12px; transition:.25s;
}
.sidebar a i{ width:22px; text-align:center }
.sidebar a:hover{
  background:rgba(255,255,255,.18); transform:translateX(6px);
}

/* Toggle sidebar */
.toggle-btn{
  position:fixed; top:18px; left:18px; z-index:1001; border:none;
  background:#fff; color:#7c3aed; padding:10px 12px; border-radius:12px;
  box-shadow:var(--shadow-md); cursor:pointer;
}

/* ===== Content & container ===== */
.content{
  width:100%; padding:30px 22px 80px; margin-left:0;
  transition:.3s; position:relative; z-index:1;
}
.sidebar.show ~ .content{ margin-left:240px }
.container{ max-width:1180px; margin:0 auto }

/* ===== Header ===== */
.header{
  position:relative; overflow:hidden;
  background:linear-gradient(90deg,var(--brand),var(--brand2) 60%,var(--brand3));
  color:#fff; border-radius:calc(var(--r) + 2px); padding:26px;
  display:flex; justify-content:space-between; align-items:center;
  box-shadow:var(--shadow-md);
}
.header h2{ font-size:1.55rem; font-weight:800; letter-spacing:.2px }
.actions{ display:flex; align-items:center; gap:16px }
.darkmode-toggle{
  background:rgba(255,255,255,.22); border:none; color:#fff;
  width:42px; height:42px; border-radius:12px;
  display:grid; place-items:center; font-size:18px;
  cursor:pointer; transition:.2s;
}
.darkmode-toggle:hover{ transform:scale(1.05) }
.profile{ display:flex; flex-direction:column; align-items:center; gap:6px }
.profile a{
  display:block; width:46px; height:46px; border-radius:50%; overflow:hidden;
  border:2px solid rgba(255,255,255,.9);
}
.profile img{ width:100%; height:100%; object-fit:cover }
.profile small{ color:#f5f3ff }

/* Blob dekor */
.header::before,.header::after{
  content:""; position:absolute; width:220px;height:220px;border-radius:50%;
  filter:blur(36px); opacity:.28; pointer-events:none;
}
.header::before{background:#a78bfa; right:-60px; top:-60px}
.header::after{background:#22d3ee; left:-80px; bottom:-80px}

/* ===== Quote ===== */
.quote{
  margin:20px 0 20px; padding:16px 18px; border-radius:16px; background:var(--card);
  border-left:6px solid #7c3aed; font-style:italic; color:#d9dcff; box-shadow:var(--shadow-sm);
}
body:not(.dark-mode) .quote{ color:#374151 }

/* ===== Utilities ===== */
.section-title{
  display:flex;align-items:center;gap:10px;font-weight:800;margin:26px 0 10px;
}
.section-title i{color:var(--brand)}

/* ===== Feature cards ===== */
.grid{ display:grid; gap:var(--gap) }
.grid-3{ grid-template-columns: repeat(3, minmax(0,1fr)) }
.card{
  background:var(--card); border-radius:var(--r); padding:22px; text-align:center;
  box-shadow:var(--shadow-sm); transition:.18s ease; cursor:pointer;
}
.card:hover{ transform: translateY(-4px); box-shadow:var(--shadow-md) }
.card i.feature-icon{
  font-size:40px; margin-bottom:10px; color:var(--brand);
  display:inline-block; transition:transform .2s;
}
.card:hover i.feature-icon{ transform:translateY(-5px) scale(1.03) }
.card h3{ font-size:1.08rem; margin-bottom:6px; font-weight:700 }
.card p{ color:var(--muted); font-size:.95rem }
/
/* ===== Artikel slider ===== */
.article-wrap{position:relative}
.scroller{
  display:grid;
  grid-auto-flow:column;
  grid-auto-columns:minmax(280px,1fr);
  gap:16px;
  overflow-x:auto;
  padding:6px 2px 8px;
  scroll-snap-type:x mandatory;
  scroll-behavior:smooth;
}
.scroller::-webkit-scrollbar{ height:10px }
.scroller::-webkit-scrollbar-thumb{ background:#d6d6f2; border-radius:999px }
body.dark-mode .scroller::-webkit-scrollbar-thumb{ background:#2f3651 }

.article{
  scroll-snap-align:start;
  background:var(--card);
  border-radius:18px;
  overflow:hidden;
  box-shadow:var(--shadow-sm);
  display:flex;
  flex-direction:column;
  height:100%;
  transition:transform .18s ease, box-shadow .18s ease;
}
.article:hover{ transform: translateY(-3px); box-shadow:var(--shadow-md) }

.article .thumb{
  width:100%;
  aspect-ratio:16/9;
  background:#f1f5f9;
  overflow:hidden;
}
.article .thumb img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
  transition:transform .5s ease;
}
.article:hover .thumb img{ transform:scale(1.06) }

.article .body{
  padding:14px 14px 8px;
  flex:1;
  display:flex;
  flex-direction:column;
}
.article .title{
  font-weight:800;
  margin-bottom:4px;
  line-height:1.35;
  /* jaga tinggi judul biar konsisten (2 baris-an) */
  min-height:3.2em;
}
.article .excerpt{
  color:#6b7280;
  font-size:.94rem;
  margin-top:2px;
  /* bikin rapi: maksimal 3 baris, sisanya dipotong */
  display:-webkit-box;
  -webkit-line-clamp:3;
  -webkit-box-orient:vertical;
  overflow:hidden;
}

.article .meta{
  display:flex;
  align-items:center;
  justify-content:space-between;
  font-size:.85rem;
  color:#6b7280;
  padding:0 14px 12px;
}
.article a.read{
  margin:0 14px 14px;
  display:inline-block;
  text-decoration:none;
  text-align:center;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  color:#fff;
  padding:10px 12px;
  border-radius:12px;
  font-weight:700;
  box-shadow:var(--shadow-sm);
}
/* tombol panah slider */
.sc-btn{
  position:absolute; top:50%; transform:translateY(-50%);
  width:42px;height:42px;border:none;border-radius:12px;
  background:linear-gradient(90deg,var(--brand),var(--brand2)); color:#fff;
  box-shadow:var(--shadow-md); cursor:pointer; display:grid;place-items:center;
}
.sc-left{left:-14px} .sc-right{right:-14px}

/* ===== Stats ===== */
.stats{
  display:grid; grid-template-columns: repeat(2, minmax(0,1fr));
  gap:var(--gap); margin-top:24px;
}
.stat{
  background:linear-gradient(90deg,#c8b6ff,#b8c0ff,#bbd0ff);
  border-radius:var(--r); padding:24px; text-align:center;
  color:#2d0a57; box-shadow:var(--shadow-sm);
}
.stat h2{ font-size:2rem; margin-bottom:4px }

/* ===== Logout overlay ===== */
.logout-overlay{
  position:fixed; inset:0; background:rgba(0,0,0,.9);
  z-index:2000; opacity:0; pointer-events:none; transition:.6s;
}
.logout-overlay.show{ opacity:1; pointer-events:auto }

/* ===== Responsive ===== */
@media (max-width:1024px){
  .grid-3{ grid-template-columns: repeat(2, minmax(0,1fr)) }
}
@media (max-width:720px){
  .sidebar.show ~ .content{ margin-left:0 }
  .grid-3{ grid-template-columns: 1fr }
  .stats{ grid-template-columns: 1fr }
  .toggle-btn{ top:14px; left:14px }
  .sc-left{left:-8px} .sc-right{right:-8px}
}

/* ===== SPACE LAYER ===== */
.space-sky{
  position:fixed; inset:0; z-index:0; pointer-events:none;
  opacity:0; transition:opacity .5s ease;
  background: radial-gradient(1200px 600px at 20% -10%, #141521 0%, #0f1117 40%, #0a0b10 100%);
}
html.dark-mode .space-sky,
body.dark-mode .space-sky{ opacity:1 }
#spaceCanvas{
  width:100%; height:100%; display:block;
  position:absolute; inset:0;
  filter: drop-shadow(0 0 2px rgba(255,255,255,.15));
}
.meteor{
  position:absolute; top:-10vh; left:-10vw;
  width:2px; height:2px; border-radius:999px; 
  background: linear-gradient(90deg, #a78bfa, #ec4899);
  box-shadow: 0 0 12px 4px rgba(167,139,250,.6);
  transform: translate3d(-100px,-100px,0) rotate(-20deg);
  opacity:0; will-change: transform, opacity;
}
.meteor.shoot{ animation: meteor-fall var(--dur,1.8s) cubic-bezier(.2,.8,.2,1) forwards }
@keyframes meteor-fall{
  0%  { opacity:0; transform: translate3d(var(--x,10vw), var(--y,-5vh), 0) rotate(-20deg) }
  5%  { opacity:1 }
  100%{ opacity:0; transform: translate3d(calc(var(--x,10vw) + 70vw), calc(var(--y,-5vh) + 50vh), 0) rotate(-20deg) }
}
</style>
</head>
<body>

<!-- SPACE LAYER -->
<div class="space-sky" aria-hidden="true">
  <canvas id="spaceCanvas"></canvas>
  <div class="meteor"></div>
</div>

<!-- Toggle Sidebar -->
<button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h3>SEJIWA</h3>
  <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="mulai_tes.php"><i class="fas fa-edit"></i> Mulai Tes</a>
  <a href="hasil_tes.php"><i class="fas fa-chart-bar"></i> Hasil Tes</a>
  <a href="musik.php"><i class="fas fa-music"></i> Musik Relaksasi</a>
  <a href="artikel_list.php"><i class="fa-solid fa-newspaper"></i> Semua Artikel</a>
  <a href="Tentang.php?from=dashboard"><i class="fas fa-info-circle"></i> Tentang Kami</a>
  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === '1'): ?>
    <a href="admin/index.php"><i class="fa-solid fa-user-shield"></i> Admin Dashboard</a>
  <?php endif; ?>
  <a href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Content -->
<div class="content">
  <div class="container">

    <div class="header">
      <h2>Halo, <?= htmlspecialchars($nama) ?></h2>
      <div class="actions">
        <button class="darkmode-toggle" id="darkModeBtn" aria-label="Toggle tema">
          <i class="fas fa-moon"></i>
        </button>
        <div class="profile">
          <a href="profil.php"><img src="<?= htmlspecialchars($foto) ?>" alt="Profil"></a>
          <small>Profil</small>
        </div>
      </div>
    </div>

    <div class="quote" id="quoteBox"></div>

    <div class="grid grid-3" style="margin-top:6px;">
      <div class="card" onclick="location.href='mulai_tes.php'">
        <i class="fa-solid fa-pen-to-square feature-icon"></i>
        <h3>Mulai Tes</h3>
        <p>Skrining cepat untuk mengenali kondisi emosionalmu.</p>
      </div>
      <div class="card" onclick="location.href='hasil_tes.php'">
        <i class="fa-solid fa-chart-column feature-icon"></i>
        <h3>Hasil Tes</h3>
        <p>Lihat hasil tes dan pelajari kondisi mentalmu.</p>
      </div>
      <div class="card" onclick="location.href='musik.php'">
        <i class="fa-solid fa-music feature-icon"></i>
        <h3>Musik Relaksasi</h3>
        <p>Dengarkan musik untuk menenangkan pikiranmu.</p>
      </div>
    </div>

    <h3 class="section-title"><i class="fa-solid fa-newspaper"></i> Artikel Terbaru</h3>

    <?php if (!empty($articles)): ?>
      <div class="article-wrap">
        <button class="sc-btn sc-left" aria-label="Artikel sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>

        <div class="scroller" id="articleScroller">
          <?php foreach ($articles as $a): ?>
            <article class="article">
              <div class="thumb">
                <img src="<?= htmlspecialchars($a['cover']) ?>" alt="<?= htmlspecialchars($a['judul']) ?>" loading="lazy">
              </div>
              <div class="body">
                <div class="title"><?= htmlspecialchars($a['judul']) ?></div>
                <div class="excerpt"><?= htmlspecialchars($a['excerpt']) ?></div>
              </div>
              <div class="meta">
                <span>
                  <i class="fa-regular fa-calendar"></i>
                  <?= $a['tanggal'] ? htmlspecialchars(date('d M Y', strtotime($a['tanggal']))) : '' ?>
                </span>
                <span>
                  <i class="fa-regular fa-clock"></i>
                  <?= (int)$a['readmin'] ?> menit
                </span>
              </div>
              <a class="read" href="artikel.php?id=<?= (int)$a['id'] ?>">Baca</a>
            </article>
          <?php endforeach; ?>
        </div>

        <button class="sc-btn sc-right" aria-label="Artikel selanjutnya"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
    <?php else: ?>
      <div class="card" style="cursor:default; text-align:left;">
        <h3 style="margin-bottom:6px;">Belum ada artikel</h3>
        <p class="muted">
          <?= htmlspecialchars($articles_err ?: 'Belum ada artikel. Tambahkan artikel dari halaman admin.') ?>
        </p>
      </div>
    <?php endif; ?>

    <div class="stats">
      <div class="stat">
        <h2>10</h2>
        <p>Anggota Tim</p>
      </div>
      <div class="stat">
        <h2>100%</h2>
        <p>Privasi Aman</p>
      </div>
    </div>

  </div>
</div>

<div class="logout-overlay" id="logoutOverlay"></div>

<script>
// Sidebar
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}

// Kutipan harian
const quotes = [
  "Setiap langkah kecil menuju kesadaran diri adalah langkah besar menuju kebahagiaan.",
  "Pikiran yang tenang adalah kekuatan terbesar dalam menghadapi badai.",
  "Merawat dirimu bukanlah kelemahan, tapi bentuk keberanian.",
  "Tidak apa-apa untuk istirahat. Kamu juga butuh dipedulikan.",
  "Jadilah ramah pada dirimu sendiri. Kamu layak mendapatkannya.",
  "Kesembuhan adalah perjalanan, bukan garis akhir.",
  "Satu hari buruk tidak berarti hidupmu buruk.",
  "Hari ini kamu cukup. Selalu cukup.",
  "Waktu istirahat juga produktif, jika itu yang kamu butuh."
];
document.getElementById('quoteBox').innerText =
  `"${quotes[new Date().getDate() % quotes.length]}"`;

// Logout efek
document.getElementById("logoutBtn").addEventListener("click",function(e){
  e.preventDefault();
  const overlay=document.getElementById("logoutOverlay");
  overlay.classList.add("show");
  setTimeout(()=>window.location.href="logout.php",1000);
});

// Panah artikel
(function(){
  const sc = document.getElementById('articleScroller');
  const L = document.querySelector('.sc-left');
  const R = document.querySelector('.sc-right');
  if (!sc || !L || !R) return;
  const step = () => Math.max(sc.clientWidth * 0.88, 320);
  L.addEventListener('click', ()=> sc.scrollBy({left: -step(), behavior:'smooth'}));
  R.addEventListener('click', ()=> sc.scrollBy({left:  step(), behavior:'smooth'}));
})();
</script>

<script src="theme.js"></script>

<script>
/* SPACE CANVAS + METEOR */
(function(){
  const canvas = document.getElementById('spaceCanvas');
  const meteor = document.querySelector('.meteor');
  if(!canvas || !meteor) return;
  const ctx = canvas.getContext('2d');
  let stars = [], rafId = null, scale = 1;

  function isDark(){
    return document.body.classList.contains('dark-mode') ||
           document.documentElement.classList.contains('dark-mode');
  }

  function resize(){
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const w = canvas.clientWidth, h = canvas.clientHeight;
    canvas.width = w * dpr; canvas.height = h * dpr;
    ctx.setTransform(dpr,0,0,dpr,0,0);
    scale = Math.max(w,h)/900;
    makeStars(w,h);
  }

  function makeStars(w,h){
    const count = Math.floor(140 * scale);
    stars = [];
    for(let i=0;i<count;i++){
      stars.push({
        x: Math.random()*w,
        y: Math.random()*h,
        z: Math.random()*0.6 + 0.4,
        r: Math.random()*1.4 + .4,
        a: Math.random()*0.6 + 0.25,
        t: Math.random()*Math.PI*2
      });
    }
  }

  function draw(ts){
    if(!isDark()){
      cancelAnimationFrame(rafId);
      rafId = null;
      ctx.clearRect(0,0,canvas.width,canvas.height);
      return;
    }
    const w = canvas.clientWidth, h = canvas.clientHeight;
    ctx.clearRect(0,0,w,h);

    const grad = ctx.createRadialGradient(w*0.75,h*0.15,0, w*0.75,h*0.15, w*0.6);
    grad.addColorStop(0,'rgba(124,58,237,.15)');
    grad.addColorStop(1,'rgba(124,58,237,0)');
    ctx.fillStyle = grad;
    ctx.fillRect(0,0,w,h);

    for(const s of stars){
      const tw = (Math.sin(s.t + ts*0.002*(1.5-s.z))+1)/2;
      ctx.globalAlpha = s.a*0.6 + tw*0.4;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r*(1.2-s.z), 0, Math.PI*2);
      ctx.fillStyle = '#ffffff';
      ctx.fill();

      s.x += (0.015*s.z);
      if(s.x > w+2) s.x = -2;
    }
    ctx.globalAlpha = 1;
    rafId = requestAnimationFrame(draw);
  }

  function start(){
    resize();
    if(!rafId) rafId = requestAnimationFrame(draw);
  }
  function stop(){
    if(rafId) cancelAnimationFrame(rafId);
    rafId = null;
    ctx.clearRect(0,0,canvas.width,canvas.height);
  }

  function spawnMeteor(){
    if(!isDark()) return;
    const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
    const startX = Math.random()*vw * 0.2;
    const startY = - (Math.random()*20 + 5);
    const dur   = (Math.random()*1.2 + 1.4).toFixed(2)+'s';
    meteor.style.setProperty('--x', startX + 'px');
    meteor.style.setProperty('--y', startY + 'vh');
    meteor.style.setProperty('--dur', dur);
    meteor.classList.remove('shoot');
    void meteor.offsetWidth;
    meteor.classList.add('shoot');
  }

  function meteorLoop(){
    const t = Math.random()*6000 + 6000;
    setTimeout(()=>{ spawnMeteor(); meteorLoop(); }, t);
  }

  window.addEventListener('resize', ()=> isDark() ? start() : stop());
  if (isDark()) start(); else stop();

  const dmBtn = document.getElementById('darkModeBtn');
  dmBtn?.addEventListener('click', ()=>{
    document.body.classList.toggle('dark-mode');
    document.documentElement.classList.toggle('dark-mode');
    localStorage.setItem("darkMode", document.body.classList.contains('dark-mode') ? "true" : "false");
    setTimeout(()=>{
      isDark() ? start() : stop();
      if(isDark()) spawnMeteor();
    }, 250);
  });

  meteorLoop();
})();
</script>

</body>
</html>
