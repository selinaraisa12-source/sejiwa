<?php
// artikel.php
session_start();
require_once 'koneksi.php';

// opsional: wajib login
if (!isset($_SESSION['id_user'])) {
    header("Location: intro.php");
    exit();
}

// ambil id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(404);
    echo "Artikel tidak ditemukan.";
    exit();
}

// query artikel
$stmt = $conn->prepare("SELECT judul, penulis, isi, created_at FROM tb_artikel WHERE id_artikel = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$artikel = $res->fetch_assoc();
$stmt->close();

if (!$artikel) {
    http_response_code(404);
    echo "Artikel tidak ditemukan.";
    exit();
}

$judul   = $artikel['judul']   ?: 'Tanpa Judul';
$penulis = $artikel['penulis'] ?: 'Admin';
$tanggal = $artikel['created_at']
    ? date('d M Y', strtotime($artikel['created_at']))
    : '';
$isi     = $artikel['isi'] ?? ''; // sudah dalam HTML dari CKEditor (diasumsikan dari admin)
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($judul) ?> - SEJIWA</title>

<!-- sync dark mode -->
<script>
  if (localStorage.getItem("darkMode")==="true") {
    document.documentElement.classList.add("dark-mode");
  }
</script>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="assets/favicon.ico">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --brand:#7c3aed; --brand2:#ec4899;
  --ink:#111827; --muted:#6b7280;
  --card:#ffffff; --r:20px;
  --shadow:0 14px 34px rgba(15,23,42,.14);
}
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#f6f3ff,#eef9ff);
  color:var(--ink);
  min-height:100vh;
}
.wrapper{
  max-width:1100px;        /* dari 900 → 1100 agar lebih lebar */
  width:95%;               /* biar tetap adaptif di layar kecil */
  margin:30px auto 60px;
  padding:0 24px;
}
.back{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:8px 12px;
  border-radius:999px;
  background:#ffffff;
  color:#4b5563;
  text-decoration:none;
  box-shadow:0 6px 18px rgba(148,163,253,.25);
  font-size:.9rem;
}
.card{
  margin-top:18px;
  background:var(--card);
  border-radius:var(--r);
  padding:36px 40px;        /* tambah padding biar lebih lega */
  box-shadow:var(--shadow);
}
.title{
  font-size:1.5rem;
  font-weight:800;
  margin-bottom:6px;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  -webkit-background-clip:text;
  color:transparent;
}
.meta{
  font-size:.85rem;
  color:var(--muted);
  display:flex;
  gap:14px;
  margin-bottom:16px;
  flex-wrap:wrap;
}
.meta i{margin-right:4px}
.content{
  font-size:.98rem;
  line-height:1.8;
  color:#111827;
}
.content img{
  max-width:100%;
  height:auto;
  display:block;
  margin:14px auto;
  border-radius:14px;
}
.content p{margin-bottom:10px}
.content h2,.content h3,.content h4{
  margin:18px 0 8px;
  font-weight:700;
}
.badge{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:5px 10px;
  border-radius:999px;
  background:rgba(124,58,237,.06);
  color:#6d28d9;
  font-size:.78rem;
}

/* DARK MODE */
body.dark-mode,
html.dark-mode body{
  background:radial-gradient(1200px 600px at 20% -10%, #1f1f29 0%, #12121a 35%, #0f1117 100%) fixed;
  color:#e5e7eb;
}
body.dark-mode .back{
  background:#111827;
  color:#e5e7eb;
  box-shadow:0 8px 22px rgba(0,0,0,.6);
}
body.dark-mode .card{
  background:#0b0d14;
  box-shadow:0 18px 40px rgba(0,0,0,.75);
}
body.dark-mode .title{
  color:#fff;
  background:linear-gradient(90deg,#c4b5fd,#f472b6);
  -webkit-background-clip:text;
}
body.dark-mode .meta{color:#9ca3af}
body.dark-mode .content{color:#e5e7eb}
body.dark-mode .badge{
  background:rgba(129,140,248,.16);
  color:#bfdbfe;
}
</style>
</head>
<body>

<div class="wrapper">
  <a href="artikel_list.php" class="back">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke semua artikel
  </a>

  <div class="card">
    <div class="badge">
      <i class="fa-solid fa-newspaper"></i> Artikel SEJIWA
    </div>

    <h1 class="title"><?= htmlspecialchars($judul) ?></h1>

    <div class="meta">
      <?php if ($tanggal): ?>
        <span><i class="fa-regular fa-calendar"></i><?= htmlspecialchars($tanggal) ?></span>
      <?php endif; ?>
      <span><i class="fa-regular fa-user"></i><?= htmlspecialchars($penulis) ?></span>
    </div>

    <div class="content">
      <?= $isi /* isi dari admin, mengandung HTML CKEditor */ ?>
    </div>
  </div>
</div>

<script>
// sinkronkan class dark-mode dengan localStorage
(function(){
  const root = document.documentElement;
  const body = document.body;
  if (localStorage.getItem("darkMode")==="true") {
    root.classList.add("dark-mode");
    body.classList.add("dark-mode");
  }
})();
</script>

</body>
</html>
