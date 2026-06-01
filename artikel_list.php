<?php
session_start();
include 'koneksi.php';

/* Opsional: boleh tanpa login */
// if (!isset($_SESSION['id_user'])) { header("Location: intro.php"); exit(); }

$per_page = 8;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

/* Hitung total artikel */
$total = 0;
$res = $conn->query("SELECT COUNT(*) AS n FROM tb_artikel");
if ($res) {
    $row = $res->fetch_assoc();
    $total = (int)$row['n'];
    $res->close();
}
$pages = max(1, (int)ceil($total / $per_page));

/* Ambil data halaman ini */
$items = [];
$query = "
  SELECT id_artikel, judul, penulis, isi, created_at
  FROM tb_artikel
  ORDER BY created_at DESC
  LIMIT $per_page OFFSET $offset
";
$result = $conn->query($query);

if ($result) {
    while ($a = $result->fetch_assoc()) {
        // cover dari img pertama di isi (kalau ada)
        $cover = 'assets/cover-placeholder.jpg';
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $a['isi'] ?? '', $m)) {
            $cover = $m[1];
        }

        // excerpt text
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($a['isi'] ?? '')));
        $excerpt = mb_substr($plain, 0, 180, 'UTF-8');
        if (mb_strlen($plain, 'UTF-8') > 180) {
            $excerpt .= '…';
        }

        // estimasi menit baca
        $readmin = max(1, (int)ceil(str_word_count($plain) / 200));

        $items[] = [
            'id'       => (int)$a['id_artikel'],
            'judul'    => $a['judul'] ?: 'Tanpa Judul',
            'penulis'  => $a['penulis'] ?: 'Admin',
            'tanggal'  => $a['created_at'],
            'cover'    => $cover,
            'excerpt'  => $excerpt,
            'readmin'  => $readmin,
        ];
    }
    $result->close();
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Artikel — SEJIWA</title>

<!-- Sinkronisasi dark mode (anti flicker) -->
<script>
  if (localStorage.getItem("darkMode") === "true") {
    document.documentElement.classList.add("dark-mode");
  }
</script>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --brand:#7c3aed;
  --brand2:#ec4899;
  --ink:#111827;
  --muted:#6b7280;
  --card:#ffffff;
  --r:18px;
  --shadow:0 10px 26px rgba(15,23,42,.12);
}
*{box-sizing:border-box;}
body{
  margin:0;
  font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial;
  background:linear-gradient(135deg,#f6f3ff,#eef9ff);
  color:var(--ink);
  transition:background .3s,color .3s;
}
.container{
  max-width:1100px;
  margin:0 auto;
  padding:24px 16px 32px;
}
.head{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:10px;
  margin-bottom:18px;
}
.head h1{
  font-size:22px;
  font-weight:800;
  display:flex;
  align-items:center;
  gap:8px;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  -webkit-background-clip:text;
  background-clip:text;
  color:transparent;
}
.btn{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:9px 14px;
  border-radius:999px;
  text-decoration:none;
  font-weight:600;
  font-size:.9rem;
  transition:.25s;
}
.btn.back{
  background:#ffffff;
  border:1px solid #e5e7eb;
  color:#374151;
  box-shadow:0 6px 16px rgba(148,163,253,.25);
}
.btn.back:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 24px rgba(129,140,248,.3);
}

/* === LIST MODE: 1 baris memanjang per artikel === */
.list{
  display:flex;
  flex-direction:column;
  gap:14px;
}
.card{
  display:flex;
  align-items:stretch;
  gap:14px;
  background:var(--card);
  border-radius:var(--r);
  box-shadow:var(--shadow);
  padding:10px;
  transition:.22s ease;
}
.card:hover{
  transform:translateY(-3px);
  box-shadow:0 16px 34px rgba(15,23,42,.16);
}
.thumb{
  flex:0 0 190px;             /* lebar thumbnail */
  max-height:120px;
  border-radius:14px;
  overflow:hidden;
  background:#f1f5f9;
}
.thumb img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}
.body{
  flex:1;
  display:flex;
  flex-direction:column;
  justify-content:center;
  padding:4px 4px 4px 2px;
}
.title{
  font-weight:800;
  margin-bottom:4px;
  font-size:1rem;
  color:#111827;
}
.meta{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  font-size:.78rem;
  color:var(--muted);
  margin-bottom:4px;
}
.meta i{margin-right:4px;}
.excerpt{
  font-size:.9rem;
  color:#4b5563;
  line-height:1.5;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
}
.more{
  margin-left:auto;
  align-self:flex-end;
}
.more a{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 12px;
  font-size:.8rem;
  font-weight:700;
  text-decoration:none;
  border-radius:999px;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  color:#fff;
  box-shadow:0 6px 14px rgba(129,140,248,.4);
}
.more a i{font-size:.75rem}

/* Paging */
.paging{
  display:flex;
  gap:8px;
  justify-content:center;
  margin:20px 0 4px;
  flex-wrap:wrap;
}
.paging a,
.paging span{
  padding:7px 11px;
  border-radius:10px;
  border:1px solid #e5e7eb;
  background:#fff;
  color:#374151;
  text-decoration:none;
  font-size:.82rem;
}
.paging .active{
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  color:#fff;
  border-color:transparent;
}

/* Mobile responsif: tumpuk vertikal */
@media (max-width:640px){
  .card{
    flex-direction:column;
    padding:10px;
  }
  .thumb{
    flex:0 0 auto;
    width:100%;
    max-height:180px;
  }
  .more{
    margin:8px 0 2px auto;
  }
}

/* ===== DARK MODE (sinkron dengan .dark-mode) ===== */
body.dark-mode{
  background:radial-gradient(1200px 600px at 20% -10%, #1f1f29 0%, #12121a 35%, #0f1117 100%) fixed;
  color:#e5e7eb;
}
body.dark-mode .head h1{
  background:linear-gradient(90deg,#c4b5fd,#f472b6);
  -webkit-background-clip:text;
  background-clip:text;
  color:transparent;
}
body.dark-mode .btn.back{
  background:#020817;
  color:#e5e7eb;
  border-color:#111827;
  box-shadow:0 8px 22px rgba(0,0,0,.7);
}
body.dark-mode .card{
  background:#020817;
  box-shadow:0 14px 34px rgba(0,0,0,.9);
}
body.dark-mode .title{ color:#f9fafb; }
body.dark-mode .meta{ color:#9ca3af; }
body.dark-mode .excerpt{ color:#e5e7eb; }
body.dark-mode .more a{
  background:linear-gradient(90deg,#8b5cf6,#ec4899);
  box-shadow:0 10px 24px rgba(0,0,0,.85);
}
body.dark-mode .paging a,
body.dark-mode .paging span{
  background:#020817;
  border-color:#111827;
  color:#e5e7eb;
}
body.dark-mode .paging .active{
  background:linear-gradient(90deg,#8b5cf6,#ec4899);
  border:none;
}
</style>
</head>
<body>
<div class="container">
  <div class="head">
    <h1><i class="fa-solid fa-newspaper"></i> Semua Artikel</h1>
    <a class="btn back" href="dashboard.php"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>

  <?php if (empty($items)): ?>
    <div class="card" style="padding:16px; justify-content:center;">
      Belum ada artikel.
    </div>
  <?php else: ?>
    <div class="list">
      <?php foreach($items as $it): ?>
        <article class="card">
          <div class="thumb">
            <img src="<?= htmlspecialchars($it['cover']) ?>" alt="<?= htmlspecialchars($it['judul']) ?>">
          </div>
          <div class="body">
            <div class="title"><?= htmlspecialchars($it['judul']) ?></div>
            <div class="meta">
              <span><i class="fa-regular fa-calendar"></i><?= htmlspecialchars(date('d M Y', strtotime($it['tanggal']))) ?></span>
              <span><i class="fa-regular fa-user"></i><?= htmlspecialchars($it['penulis']) ?></span>
              <span><i class="fa-regular fa-clock"></i><?= (int)$it['readmin'] ?> menit</span>
            </div>
            <div class="excerpt"><?= htmlspecialchars($it['excerpt']) ?></div>
          </div>
          <div class="more">
            <a href="artikel.php?id=<?= (int)$it['id'] ?>">
              Baca <i class="fa-solid fa-arrow-right"></i>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <div class="paging">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page-1 ?>">« Prev</a>
        <?php else: ?>
          <span>« Prev</span>
        <?php endif; ?>

        <?php for ($i=1; $i<=$pages; $i++): ?>
          <?php if ($i == $page): ?>
            <span class="active"><?= $i ?></span>
          <?php else: ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $pages): ?>
          <a href="?page=<?= $page+1 ?>">Next »</a>
        <?php else: ?>
          <span>Next »</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script>
// Sinkronkan dengan dark mode dashboard
document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem("darkMode") === "true") {
    document.documentElement.classList.add("dark-mode");
    document.body.classList.add("dark-mode");
  }
});
</script>
</body>
</html>
