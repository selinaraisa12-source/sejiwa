<?php
session_start();

// Jika user sudah login, arahkan langsung ke dashboard
if (isset($_SESSION['id_user'])) {
  header("Location: dashboard.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sejiwa — Ruang Aman Kesehatan Mental</title>
  <meta name="description" content="Sejiwa: tes emosi, artikel tepercaya, dan musik relaksasi untuk menenangkan pikiran." />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
/* ===================== THEME & BASE ===================== */
  :root{
    --brand:#7c3aed; --brand2:#ec4899; --brand3:#06b6d4;
    --bg:#ffffff; --text:#0f172a; --muted:#475569; --surface:#f8fafc; --border:#e2e8f0;
    --ring:rgba(124,58,237,.35);
    --radius:14px; --shadow-sm:0 6px 18px rgba(16,24,40,.08);
    --shadow:0 16px 40px rgba(16,24,40,.12); --shadow-lg:0 28px 80px rgba(16,24,40,.18);
    --container:1180px;
    --header-h:75px;
  }

  *{box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{margin:0;font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;color:var(--text);background:var(--bg);line-height:1.65}
  img{max-width:100%;display:block}
  a{color:var(--brand);text-decoration:none}
  a:focus-visible,button:focus-visible{outline:3px solid var(--ring);outline-offset:2px;border-radius:10px}
  .container{width:min(100% - 2rem, var(--container));margin-inline:auto}
  section[id]{scroll-margin-top: calc(var(--header-h) + 12px)}
  section{padding:72px 0}
  html,body{overflow-x:hidden}

/* Aurora global lembut */
body::before{
  content:""; position:fixed; inset:-20% -10%; z-index:-2;
  background:
    radial-gradient(40% 30% at 20% 10%, rgba(124,58,237,.16), transparent 60%),
    radial-gradient(40% 30% at 80% 0%,  rgba(236,72,153,.14), transparent 60%),
    radial-gradient(35% 28% at 50% 100%, rgba(6,182,212,.12), transparent 60%),
    #fff;
  filter: blur(42px) saturate(1);
  animation: aurora 16s ease-in-out infinite alternate;
}
@keyframes aurora{ 0%{transform:translate3d(0,0,0) scale(1)} 100%{transform:translate3d(0,-4%,0) scale(1.03)} }

/* ===================== HEADER ===================== */
header{
  position:sticky; top:0; z-index:10; height:var(--header-h);
  display:flex; align-items:center; background:rgba(255,255,255,.85);
  backdrop-filter:blur(10px); border-bottom:1px solid var(--border);
}
.nav{display:flex; align-items:center; justify-content:space-between; width:100%; gap:1rem; padding-inline:clamp(28px,6vw,72px)}
.brand{display:flex; align-items:center; gap:.7rem; text-decoration:none}
.brand__name{
  font-weight:800;
  font-size:clamp(1.35rem, 2.2vw, 1.7rem);
  letter-spacing:.02em; line-height:1.1;
  background:linear-gradient(90deg,#111 0%,var(--brand) 55%,var(--brand2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
}
.brand {
  display: flex;
  align-items: center;
  gap: 10px; /* jarak antara logo dan teks */
  text-decoration: none;
}

.logo {
  width: 42px; /* ukuran pas buat header */
  height: 42px;
  object-fit: contain;
  border-radius: 10px; /* opsional, biar lembut */
}

.brand__name {
  font-size: 1.4rem;
  font-weight: 700;
  color: #ffffff; /* sesuaikan dengan warna teks header kamu */
  font-family: 'Raleway', sans-serif;
}

/* Nav links */
.nav__links{display:flex; align-items:center; gap:clamp(1.2rem,3vw,2.4rem)}
.nav__links .nav-link{
  color:var(--muted); font-weight:600; padding:.7rem 1.1rem; border-radius:14px; transition:.2s; position:relative;
}
.nav__links .nav-link:hover{color:var(--text); background:#f5f5ff}
.nav__links .nav-link.active{color:var(--brand)}
.nav__links .nav-link.active::after{
  content:""; position:absolute; left:12px; right:12px; bottom:6px; height:2px; border-radius:2px;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
}

/* CTA hanya “Masuk” */
.nav__cta .btn{background:#fff; border:1px solid color-mix(in srgb,var(--brand) 40%,#fff 60%); color:var(--brand); border-radius:999px; padding:.7rem 1rem}
.nav-toggle{display:none;border:1px solid var(--border);background:#fff;border-radius:10px;padding:.55rem}
.mobile-menu{display:none}
@media (max-width:900px){
  .nav__links,.nav__cta{display:none}
  .nav-toggle{display:inline-flex}
  .mobile-menu{display:none;border-top:1px solid var(--border);padding:.75rem 0}
  .mobile-menu.open{display:block}
  .mobile-menu a{display:block;padding:.65rem 0;color:var(--muted)}
  .mobile-menu a:hover{color:var(--text)}
}

/* ===================== SECTION STYLES ===================== */
.section-colorful{ position:relative; isolation:isolate; }
.section-colorful::before,
.section-colorful::after{
  content:""; position:absolute; z-index:-1; filter: blur(36px);
  width:min(1100px,95%); height:620px; pointer-events:none; opacity:.6;
}
.theme-lilac{   --sec-1:#7c3aed; --sec-2:#ec4899; --sec-3:#06b6d4; --sec-bg:#f6f3ff; }
.theme-blossom{ --sec-1:#ec4899; --sec-2:#f472b6; --sec-3:#93c5fd; --sec-bg:#fff1f7; }
.theme-sky{     --sec-1:#06b6d4; --sec-2:#93c5fd; --sec-3:#7c3aed; --sec-bg:#f0f9ff; }

.section-colorful{
  background:
    radial-gradient(1200px 600px at 10% -10%, color-mix(in oklab, var(--sec-1) 10%, transparent), transparent 60%),
    radial-gradient(1200px 600px at 90% -10%, color-mix(in oklab, var(--sec-2) 10%, transparent), transparent 60%),
    var(--sec-bg,#fff);
}

.section-head{text-align:center;margin:0 0 2.2rem}
.section-eyebrow{font-size:.95rem;letter-spacing:.1em;color:var(--brand);font-weight:700;text-transform:uppercase}
.section-title{font-size:clamp(2rem,3vw,2.6rem);font-weight:700;line-height:1.2;
  background:linear-gradient(90deg,var(--sec-1,#7c3aed),var(--sec-2,#ec4899));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-top:.3rem}
.section-desc{font-size:1.05rem;color:#475569;max-width:760px;margin:.75rem auto 0}

/* ===================== HERO ===================== */
.hero{padding:clamp(72px,8vw,120px) 0 40px; position:relative}
.hero__inner{display:grid; grid-template-columns:1.1fr .9fr; gap:2.5rem; align-items:center; position:relative; z-index:3}
.hero h1{font-size:clamp(2rem,3.6vw,3.25rem); line-height:1.15; margin:0 0 .75rem}
.hero p{color:var(--muted)}
.hero h1, .hero p{ text-shadow:0 1px 3px rgba(255,255,255,.6) }

/* MATIKAN layer konika yang nutupin teks */
.hero-rainbow::after{ content:none !important; }

/* Orbs animasi lembut – DI BELAKANG konten */
.hero__fx{ position:absolute; inset:0; z-index:0; overflow:hidden }
.hero__fx::before,
.hero__fx::after{
  content:""; position:absolute; width:520px; height:520px; border-radius:50%;
  filter:blur(52px); opacity:.52; animation:heroFloat 18s ease-in-out infinite;
}
.hero__fx::before{
  left:-120px; top:-80px;
  background:
    radial-gradient(circle at 30% 30%, rgba(124,58,237,.35), transparent 60%),
    radial-gradient(circle at 75% 70%, rgba(236,72,153,.30), transparent 60%);
}
.hero__fx::after{
  right:-140px; bottom:-160px; animation-duration:22s;
  background:
    radial-gradient(circle at 60% 40%, rgba(6,182,212,.32), transparent 60%),
    radial-gradient(circle at 40% 60%, rgba(167,139,250,.28), transparent 60%);
}
@keyframes heroFloat{ 0%{transform:translate3d(0,0,0) scale(1)} 50%{transform:translate3d(0,-10px,0) scale(1.02)} 100%{transform:translate3d(0,0,0) scale(1)} }

/* ===== Buttons (rapi seperti sebelumnya) ===== */
.btn{
  display:inline-flex; align-items:center; gap:.5rem;
  padding:.9rem 1.1rem; border-radius:999px;
  border:1px solid transparent; font-weight:600;
  transition:.2s; text-decoration:none;
}
.btn:hover{ transform:translateY(-1px) }
.btn:active{ transform:translateY(0) scale(.99) }

/* Primary: gradient ungu→pink + bayangan lembut */
.btn-primary{
  background: linear-gradient(90deg, var(--brand), var(--brand2));
  color:#fff; box-shadow: var(--shadow);
  border-color: transparent;
}
.btn-primary:hover{ box-shadow: var(--shadow-lg) }

/* Outline: putih, border ungu, teks ungu */
.btn-outline{
  background:#fff; color:var(--brand);
  border:2px solid var(--brand);
}
.btn-outline:hover{
  background: color-mix(in srgb, var(--brand) 8%, #fff 92%);
}

/* Fokus: glow lembut, BUKAN kotak ungu tipis */
a:focus-visible, button:focus-visible{
  outline: none;
  box-shadow: 0 0 0 4px var(--ring);
  border-radius: 999px; /* pill */
}

/* Ilustrasi */
.hero__visual{ background:transparent !important; box-shadow:none !important; padding:0 !important }
.hero__img{ display:block; background:transparent; border-radius:0; filter:drop-shadow(0 24px 40px rgba(16,24,40,.18)); animation:bob 6s ease-in-out infinite }
@keyframes bob{ 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
@media (max-width:900px){ .hero__inner{grid-template-columns:1fr} .hero__visual{display:none} }

/* ===================== STEPS ===================== */
.steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}
.step{position:relative;background:#fff;border:1px solid var(--border);border-radius:18px;padding:1.25rem;box-shadow:var(--shadow-sm);transition:.2s}
.step:hover{transform:translateY(-2px);box-shadow:var(--shadow)}
.step-no{position:absolute;top:-14px;left:-14px;background:#fff;border:4px solid var(--brand2);color:var(--brand2);width:42px;height:42px;display:grid;place-items:center;border-radius:50%;font-weight:700}
@media (max-width:900px){.steps-grid{grid-template-columns:1fr}}

/* ===================== FEATURES (3 kolom) ===================== */
.features .grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; align-items:stretch }
.features .feature{
  background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.5rem;
  box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:1rem; transition:transform .25s, box-shadow .25s;
}
.features .feature:hover{ transform:translateY(-4px); box-shadow:var(--shadow) }
.features .icon{
  width:52px; height:52px; border-radius:14px; display:grid; place-items:center;
  background:linear-gradient(135deg,var(--sec-1,#7c3aed),var(--sec-2,#ec4899)); color:#fff;
}
/* Offline SVG icon look */
.features .icon svg{
  width:26px; height:26px;
  stroke:#fff; stroke-width:2; fill:none;
  stroke-linecap:round; stroke-linejoin:round;
}
@media (max-width:900px){ .features .grid{grid-template-columns:repeat(2,1fr)} }
@media (max-width:600px){ .features .grid{grid-template-columns:1fr} }
.features .section-head{ margin-bottom:1.8rem }

/* ===================== TEAM / FOOTER / MISC ===================== */
.team-marquee{position:relative;overflow:hidden;--gap:16px;--visible:5}
.team-rail{display:flex;gap:var(--gap);will-change:transform}
.team-ctrl{position:absolute;top:50%;transform:translateY(-50%);border:1px solid var(--border);background:#fff;color:#111;
  width:40px;height:40px;border-radius:10px;cursor:pointer;box-shadow:var(--shadow-sm);font-size:22px;line-height:38px;display:grid;place-items:center;z-index:2;opacity:.95;transition:.2s}
.team-ctrl.prev{left:-4px} .team-ctrl.next{right:-4px}
.team-ctrl:hover{transform:translateY(-50%) scale(1.05)}
.member3d{flex:0 0 calc((100% - (var(--gap) * (var(--visible) - 1))) / var(--visible));cursor:pointer;transform-style:preserve-3d}
.card3d{background:#0f172a;border:1px solid #1f2937;border-radius:18px;box-shadow:var(--shadow);overflow:hidden;transition:transform .25s,box-shadow .25s}
.member3d:hover .card3d{transform:translateZ(16px) rotateX(2deg) rotateY(-2deg);box-shadow:var(--shadow-lg)}
.photo-wrap{position:relative;aspect-ratio:3/3.8;overflow:hidden;isolation:isolate}
.member3d .photo-wrap img{width:100%;height:100%;max-height:260px;object-fit:cover;object-position:50% 10%;filter:saturate(.8) contrast(1.03) hue-rotate(-10deg)}
.member-meta{padding:.7rem .9rem;text-align:center;background:linear-gradient(180deg,rgba(124,58,237,.14),rgba(124,58,237,0));color:#e5e7eb}
.member-role{color:#cbd5e1;font-size:.9rem}

footer{background:#0b1220;color:#cbd5e1}
.footer{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:2rem;padding:56px 0}
.footer h4{color:#fff;margin:0 0 .75rem}
.footer a{color:#cbd5e1}
.footer a:hover{color:#fff}
.sub{border-top:1px solid rgba(255,255,255,.08);padding:16px 0;display:flex;justify-content:space-between;gap:.75rem;font-size:.95rem}
@media (max-width:900px){.footer{grid-template-columns:1fr}.sub{flex-direction:column;align-items:flex-start}}

/* Reveal on scroll */
.reveal{opacity:0;transform:translateY(14px);transition:.6s cubic-bezier(.2,.7,.2,1)}
.reveal.is-inview{opacity:1;transform:none}

/* reduce motion */
@media (prefers-reduced-motion: reduce){
  *{animation:none!important;transition:none!important;scroll-behavior:auto}
}
/* === Modal Quotes === */
.modal-backdrop{
  position:fixed; inset:0; background:rgba(0,0,0,.45);
  display:none; align-items:center; justify-content:center;
  padding:1rem; z-index:50; animation:fadeIn .25s ease;
}
.modal{
  max-width:560px; width:100%;
  background:#fff; border:1px solid var(--border);
  border-radius:18px; box-shadow:var(--shadow); padding:1.1rem;
}
.modal .close{
  float:right; border:1px solid var(--border); background:#fff;
  border-radius:10px; padding:.4rem .6rem; cursor:pointer;
}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

  </style>
</head>
<body>

<header>
  <div class="container nav" role="navigation" aria-label="Navigasi utama">
<a class="brand" href="index.php">
  <img src="./logo.png" alt="Logo Sejiwa" class="logo">
  <span class="brand__name">Sejiwa</span>
</a>

    <nav class="nav__links" aria-label="Tautan utama">
      <a href="#home"  class="nav-link active">Beranda</a>
      <a href="#fitur" class="nav-link">Fitur</a>
      <a href="#team"  class="nav-link">Tim</a>
      <a href="#faq"   class="nav-link">FAQ</a>
    </nav>
    <div class="nav__cta"><a class="btn" href="awal.php">Masuk</a></div>
    <button class="nav-toggle" aria-label="Buka menu" aria-expanded="false">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="#111827" stroke-width="2" stroke-linecap="round"/></svg>
    </button>
  </div>
  <div class="container mobile-menu" id="mobileMenu" role="navigation" aria-label="Menu seluler">
    <a href="#home"  class="nav-link">Beranda</a>
    <a href="#fitur" class="nav-link">Fitur</a>
    <a href="#team"  class="nav-link">Tim</a>
    <a href="#faq"   class="nav-link">FAQ</a>
    <div style="display:flex; gap:.5rem; margin-top:.5rem">
      <a class="btn" href="awal.php" style="flex:1; justify-content:center">Masuk</a>
    </div>
  </div>
</header>

<!-- Accent bar dinamis -->
<div id="headerAccent" style="position:fixed;left:0;right:0;top:0;height:3px;z-index:12;background:linear-gradient(90deg,#7c3aed,#ec4899)"></div>

<main id="main">
  <!-- HERO -->
  <section class="hero hero-rainbow section-colorful theme-lilac" id="home">
    <div class="hero__fx" aria-hidden="true"></div>
    <div class="container hero__inner">
      <div class="reveal">
        <div aria-hidden="true" style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.6rem">
          <span class="chip">Aman & Rahasia - </span><span class="chip"> Bahasa Indonesia - </span><span class="chip">Gratis untuk mulai</span>
        </div>
        <h1>Kenali Dirimu, Rawat Jiwamu.</h1>
        <p>Sejiwa adalah ruang aman untuk memahami emosi, belajar dari artikel tepercaya, dan menenangkan pikiran lewat musik relaksasi.</p>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem">
          <a class="btn btn-primary" href="awal.php">Mulai Sekarang</a>
          <a class="btn btn-outline" href="buku_panduan.pdf">Pelajari Lebih Lanjut</a>
        </div>
      </div>
      <div class="hero__visual reveal" aria-hidden="true">
        <img src="./illustration.png" alt="Ilustrasi 3D relaksasi" class="hero__img" loading="eager" fetchpriority="high" decoding="async">
      </div>
    </div>
  </section>

  <!-- STEPS -->
  <section class="steps section-colorful theme-blossom" aria-label="Langkah awal">
    <div class="container">
      <div class="section-head">
        <p class="section-eyebrow">Mulai dalam 3 langkah</p>
        <h2 class="section-title">Bangun kebiasaan yang menenangkan</h2>
      </div>
      <div class="steps-grid">
        <article class="step glass reveal"><span class="step-no">1</span><h3>Lengkapi Profil</h3><p class="muted">Agar rekomendasi dan perjalananmu lebih personal & relevan.</p></article>
        <article class="step glass reveal"><span class="step-no">2</span><h3>Screening Emosi</h3><p class="muted">Jawab pertanyaan ringan untuk memahami kondisi emosimu.</p></article>
        <article class="step glass reveal"><span class="step-no">3</span><h3>Dengarkan Musik Relaksasi</h3><p class="muted">Putar playlist tenang - bantu atur napas, fokus, dan turunkan ketegangan.</p></article>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="features section-colorful theme-mint" id="fitur">
    <div class="container">
      <div class="section-head">
        <p class="section-eyebrow">Fitur</p>
        <h2 class="section-title">Semua alat untuk merawat diri, dalam satu aplikasi</h2>
        <p class="section-desc">Sederhana, aman, dan efektif bagi pemula maupun yang sudah berpengalaman.</p>
      </div>
      <div class="grid">
        <!-- Tes Kesehatan Mental -->
        <article class="feature">
          <div class="icon">
            <!-- brain (inline SVG) -->
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M9 8a3 3 0 1 1 3-3"/>
              <path d="M15 8a3 3 0 1 0-3-3"/>
              <path d="M6 12a3 3 0 1 1 0-6"/>
              <path d="M18 12a3 3 0 1 0 0-6"/>
              <path d="M6 12v3a4 4 0 0 0 4 4h1"/>
              <path d="M18 12v3a4 4 0 0 1-4 4h-1"/>
            </svg>
          </div>
          <div>
            <h3>Tes Kesehatan Mental</h3>
            <p>Kuisioner valid yang mudah dipahami untuk mengenali kondisi emosi Anda.</p>
          </div>
        </article>

        <!-- Artikel Tepercaya -->
        <article class="feature">
          <div class="icon">
            <!-- book-open (inline SVG) -->
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 19c-2-1-4-1.5-7-1.5V6.5C8 6.5 10 7 12 8"/>
              <path d="M12 19c2-1 4-1.5 7-1.5V6.5C16 6.5 14 7 12 8"/>
              <path d="M12 8v11"/>
            </svg>
          </div>
          <div>
            <h3>Artikel Tepercaya</h3>
            <p>Kumpulan panduan praktis yang dikurasi dengan bahasa yang ramah.</p>
          </div>
        </article>

        <!-- Musik Relaksasi -->
        <article class="feature">
          <div class="icon">
            <!-- music-2 (inline SVG) -->
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M9 18V5l10-2v13"/>
              <circle cx="7" cy="18" r="3"/>
              <circle cx="17" cy="16" r="3"/>
            </svg>
          </div>
          <div>
            <h3>Musik Relaksasi</h3>
            <p>Playlist suara alam & instrumen lembut untuk menenangkan pikiran.</p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- TEAM -->
  <section class="team section-colorful theme-lilac" id="team" aria-label="Tim Sejiwa">
    <!-- Modal Quotes -->
<div class="modal-backdrop" id="quoteModal" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="modal">
    <button class="close" id="closeQuote" aria-label="Tutup">X</button>
    <h4 id="quoteName">Nama</h4>
    <p class="muted" id="quoteRole">NIM</p>
    <p style="margin-top:.8rem" id="quoteText">"Quote di sini."</p>
  </div>
</div>

    <div class="container">
      <div class="section-head">
        <p class="section-eyebrow">Tim</p>
        <h2 class="section-title">Kenalan dengan Tim Kami</h2>
        <p class="section-desc">Deretan wajah yang merawat Sejiwa - klik untuk membaca quotes mereka.</p>
      </div>

      <div class="team-marquee reveal" id="teamMarquee">
        <button class="team-ctrl prev" id="teamPrev" aria-label="Geser ke kiri"><</button>
        <button class="team-ctrl next" id="teamNext" aria-label="Geser ke kanan">></button>
        <div class="team-rail" id="teamRail">
          <?php
          $members = [
            ["file"=>"zaky.png","name"=>"Alfari Zaky Firdaus","role"=>"P20637124002","quote"=>"Bahagia bukan soal sempurna, tapi soal menerima."],
            ["file"=>"david.png","name"=>"David Dharmawan","role"=>"P20637124006","quote"=>"Biarkan yang hitam menjadi hitam, tak perlu memaksanya untuk menjadi putih, karena tak semua yang kelam adalah salah. Putih belum tentu suci, dan hitam pun belum tentu bersalah."],
            ["file"=>"fauzi.png","name"=>"Fauzi Athalah","role"=>"P20637124015","quote"=>"Setiap dari kita adalah pemenang."],
            ["file"=>"haya.png","name"=>"Haya Rumaisha Azra Faridi","role"=>"P20637124020","quote"=>"Ingat, cinta terbaik yang bisa kamu berikan pada dunia adalah dirimu yang utuh, bahagia, dan tidak kehilangan jati diri."],
            ["file"=>"hilda.png","name"=>"Hilda Herlina","role"=>"P20637124022","quote"=>"Orang yang kuat bukan berarti tak pernah jatuh, melainkan yang selalu menemukan alasan untuk bangkit kembali."],
            ["file"=>"meri.png","name"=>"Meri Agustina","role"=>"P20637124026","quote"=>"Jatuh tujuh kali, bangun delapan kali. Yang penting bukan bagaimana kau jatuh, tapi bagaimana kau bangkit kembali."],
            ["file"=>"hasan.png","name"=>"Muhammad Hasan Maulana","role"=>"P20637124027","quote"=>"Berhenti sebentar bukan berarti menyerah, tapi memberi ruang agar jiwa bisa bernapas."],
            ["file"=>"rahma.png","name"=>"Rahmayulia Nurfazriyah","role"=>"P20637124034","quote"=>"Nanti kita rayakan perjalanan hebat yang diiringi air mata ini. Jadi, jangan nyerah dulu, ya. Terimakasih sudah membawa dirimu bertahan sampai sejauh ini."],
            ["file"=>"selin.png","name"=>"Selina Raisa Pujianti","role"=>"P20637124035","quote"=>"Kadang, keberanian terbesar bukan tentang melawan dunia, tapi tetap bernapas saat segalanya terasa sesak. Kamu hebat karena masih bertahan."],
            ["file"=>"shofi.png","name"=>"Shofiyah Nurul Jannah","role"=>"P20637124036","quote"=>"Kamu tidak harus selalu on fire, bahkan server pun butuh downtime buat maintenance."]
          ];
          foreach ($members as $m): ?>
            <figure class="member3d" data-name="<?=htmlspecialchars($m['name'])?>" data-role="<?=htmlspecialchars($m['role'])?>" data-quote="<?=htmlspecialchars($m['quote'])?>">
              <div class="card3d">
                <div class="photo-wrap">
                  <img src="foto/<?=htmlspecialchars($m['file'])?>" alt="<?=htmlspecialchars($m['name'])?>" loading="lazy" decoding="async">
                  <span class="gloss" aria-hidden="true"></span>
                </div>
                <figcaption class="member-meta">
                  <strong><?=htmlspecialchars($m['name'])?></strong>
                  <div class="member-role"><?=htmlspecialchars($m['role'])?></div>
                </figcaption>
              </div>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section-colorful theme-sand" id="faq" aria-label="Pertanyaan yang sering ditanyakan">
    <div class="container">
      <div class="section-head">
        <p class="section-eyebrow">FAQ</p>
        <h2 class="section-title">Pertanyaan yang sering ditanyakan</h2>
        <p class="section-desc">Masih ragu? Berikut jawaban singkatnya.</p>
      </div>
      <div class="faq-list" style="max-width:900px;margin:0 auto;display:grid;gap:14px">
        <div class="faq-card" style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:16px 18px;box-shadow:var(--shadow-sm)">
          <strong>Apakah data saya aman?</strong>
          <p class="muted">Kami menerapkan praktik keamanan aplikasi. Data jurnal bersifat privat dan tidak dibagikan tanpa persetujuan Anda.</p>
        </div>
        <div class="faq-card" style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:16px 18px;box-shadow:var(--shadow-sm)">
          <strong>Apakah Sejiwa gratis?</strong>
          <p class="muted">Semua fitur yang tersedia saat ini dapat digunakan secara gratis.</p>
        </div>
        <div class="faq-card" style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:16px 18px;box-shadow:var(--shadow-sm)">
          <strong>Apakah ini pengganti profesional?</strong>
          <p class="muted">Tidak. Sejiwa bukan pengganti psikolog/psikiater. Jika mengalami krisis, segera hubungi layanan darurat atau tenaga profesional.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta section-colorful theme-sky" aria-label="Ajak bergabung">
    <div class="container">
      <div class="box glass reveal" style="display:grid;grid-template-columns:1.5fr .8fr;gap:1rem;align-items:center">
        <div>
          <h2 style="margin:.25rem 0 .25rem">Mulai langkah kecil menuju ketenangan batin.</h2>
          <p class="muted" style="margin:0">Daftar gratis, tanpa kartu. Butuh waktu kurang dari 1 menit.</p>
        </div>
        <div style="justify-self:end;display:flex;gap:.75rem;flex-wrap:wrap">
          <a class="btn btn-primary" href="register.php">Daftar Gratis</a>
          <a class="btn btn-outline" href="Tentang.php">Pelajari Sejiwa</a>
        </div>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="container footer">
    <div>
      <div class="brand" style="color:#fff">
        <span class="brand__name" style="-webkit-text-fill-color:#fff;background:none">Sejiwa</span>
      </div>
      <p class="muted" style="margin:.75rem 0 0; color:#94a3b8">© <?=date('Y');?> Sejiwa. Semua hak dilindungi.</p>
    </div>

    <div>
      <h4>Produk</h4>
      <nav>
        <a href="#fitur">Fitur</a><br/>
        <a href="Artikel.php">Artikel</a><br/>
        <a href="dashboard.php">Dashboard</a>
      </nav>
    </div>

    <div>
      <h4>Perusahaan</h4>
      <nav>
        <a href="#home">Beranda</a><br/>
        <a href="#team">Tim</a><br/>
        <a href="#faq">FAQ</a>
      </nav>
    </div>
  </div>

  <div class="container sub">
    <span>Made by Poltekkes Kemenkes Tasikmalaya</span>
    <span>Bahasa: Indonesia</span>
  </div>
</footer>

<script>
  // Mobile menu
  const toggleBtn = document.querySelector('.nav-toggle');
  const mobileMenu = document.getElementById('mobileMenu');
  toggleBtn?.addEventListener('click', () => {
    const open = mobileMenu.classList.toggle('open');
    toggleBtn.setAttribute('aria-expanded', String(open));
  });
  mobileMenu?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    mobileMenu.classList.remove('open'); toggleBtn?.setAttribute('aria-expanded', 'false');
  }));

  // Nav highlight + immediate active state on click
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach(l => l.addEventListener('click', () => {
    navLinks.forEach(a => a.classList.remove('active'));
    l.classList.add('active');
  }));
  window.addEventListener("scroll", () => {
    let current = "";
    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 76;
      if (scrollY >= sectionTop) current = section.getAttribute("id");
    });
    navLinks.forEach((link) => link.classList.toggle("active", link.getAttribute("href") === "#" + current));
  });

  // Team rail auto-play + modal
  (function(){
    const rail   = document.getElementById('teamRail');
    const prevBt = document.getElementById('teamPrev');
    const nextBt = document.getElementById('teamNext');
    if(!rail) return;

    rail.innerHTML += rail.innerHTML; // loop mulus
    let offset = 0, speed = 0.6, paused = false;

    const halfWidth = () => rail.scrollWidth / 2;
    const cardStep = () => {
      const first = rail.querySelector('.member3d');
      const gap   = parseFloat(getComputedStyle(rail).gap || '16');
      return first ? first.getBoundingClientRect().width + gap : 220;
    };

    function tick(){
      if(!paused){ offset -= speed; }
      if(offset <= -halfWidth()){ offset += halfWidth(); }
      rail.style.transform = `translateX(${offset}px)`;
      requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);

    rail.addEventListener('mouseenter', ()=> paused = true);
    rail.addEventListener('mouseleave', ()=> paused = false);

    const nudge = (dir)=>{ // +1 kanan, -1 kiri
      paused = true; offset += dir * cardStep();
      if(offset > 0){ offset -= halfWidth(); }
      if(offset <= -halfWidth()){ offset += halfWidth(); }
      rail.style.transform = `translateX(${offset}px)`;
      setTimeout(()=> paused = false, 700);
    };
    prevBt?.addEventListener('click', ()=> nudge(+1));
    nextBt?.addEventListener('click', ()=> nudge(-1));

    // Modal quotes
    const modal  = document.getElementById('quoteModal');
    const closeB = document.getElementById('closeQuote');
    const qName  = document.getElementById('quoteName');
    const qRole  = document.getElementById('quoteRole');
    const qText  = document.getElementById('quoteText');

    rail.addEventListener('click', (e)=>{
      const fig = e.target.closest('.member3d');
      if(!fig) return;
      qName.textContent = fig.dataset.name || '';
      qRole.textContent = fig.dataset.role || '';
      qText.textContent = '“' + (fig.dataset.quote || '') + '”';
      modal.style.display = 'flex';
      modal.setAttribute('aria-hidden','false');
    });
    function closeModal(){ modal.style.display='none'; modal.setAttribute('aria-hidden','true'); }
    closeB?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e)=>{ if(e.target===modal) closeModal(); });
    window.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeModal(); });
  })();

  // Reveal on scroll
  (function(){
    const els=document.querySelectorAll('.reveal');
    if(!('IntersectionObserver'in window)||!els.length){ els.forEach(e=>e.classList.add('is-inview')); return; }
    const io=new IntersectionObserver((entries)=>{
      entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('is-inview'); io.unobserve(e.target);} });
    },{threshold:.16});
    els.forEach(el=>io.observe(el));
  })();

  // Header accent mengikuti section aktif
  (function(){
    const bar=document.getElementById('headerAccent');
    const secs=[...document.querySelectorAll('section.section-colorful[id]')];
    if(!bar||!secs.length) return;
    const io=new IntersectionObserver((entries)=>{
      let top=null, y=Infinity;
      entries.forEach(en=>{
        if(en.isIntersecting){
          const r=en.target.getBoundingClientRect();
          if(r.top<y){ y=r.top; top=en; }
        }
      });
      const sec=top?.target; if(!sec) return;
      const cs=getComputedStyle(sec);
      const c1=cs.getPropertyValue('--sec-1').trim()||'#7c3aed';
      const c2=cs.getPropertyValue('--sec-2').trim()||'#ec4899';
      bar.style.background=`linear-gradient(90deg, ${c1}, ${c2})`;
    },{threshold:[0.35,0.6]});
    secs.forEach(s=>io.observe(s));
  })();
</script>
</body>
</html>
