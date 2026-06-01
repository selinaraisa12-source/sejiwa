<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'koneksi.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!$email || !$password) {
        $error = "Email dan kata sandi wajib diisi!";
    } else {
        $stmt = $conn->prepare("SELECT id_user, nama, password, role FROM tb_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['id_user']    = $user['id_user'];
                $_SESSION['nama']       = $user['nama'];
                $_SESSION['user_email'] = $email;
                $_SESSION['role']       = (string)$user['role'];

                if ($_SESSION['role'] === '1') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Kata sandi salah!";
            }
        } else {
            $error = "Email belum terdaftar!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Masuk — Sejiwa</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --brand:#7c3aed; --brand2:#ec4899; --cyan:#06b6d4;
  --ink:#0b1220; --muted:#64748b;
  --panel:#ffffffee; --stroke:rgba(2,6,23,.08);
  --radius:28px; --shadow:0 26px 70px rgba(17,12,46,.18);
  --ring:rgba(124,58,237,.22);
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{
  font-family:'Plus Jakarta Sans',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  color:var(--ink);
  display:flex; align-items:center; justify-content:center;
  min-height:100vh;
  background:
    radial-gradient(1100px 800px at -10% -10%, rgba(124,58,237,.14), transparent 60%),
    radial-gradient(1100px 900px at 110% 10%, rgba(6,182,212,.14), transparent 55%),
    linear-gradient(180deg,#ffffff,#fbf8ff 40%,#fff0f7 100%);
  overflow:hidden;
  transition:opacity 0.6s ease;
  opacity:0;
}

/* fade in */
body.loaded { opacity:1; }

/* Tombol kembali tetap di atas */
.back-btn{
  position:fixed; top:18px; left:18px; z-index:5;
  display:inline-flex; align-items:center; gap:8px;
  padding:10px 14px; border-radius:12px; font-weight:800;
  color:#7c3aed; background:#fff; text-decoration:none;
  border:1px solid var(--stroke); box-shadow:0 10px 24px rgba(17,12,46,.12);
  transition:.2s;
}
.back-btn:hover{ background:linear-gradient(90deg,var(--brand),var(--brand2)); color:#fff }

/* Background glow ringan */
.glows{position:fixed; inset:0; pointer-events:none; z-index:-1}
.glow{
  position:absolute; width:26vmax; height:26vmax; border-radius:50%;
  filter:blur(40px); opacity:.25;
  background:radial-gradient(circle at 30% 30%, var(--c, rgba(124,58,237,.9)) 0%, transparent 65%);
  animation: drift var(--dur,18s) ease-in-out infinite alternate;
}
.glow.g1{--c:rgba(124,58,237,.85); left:-6vmax; top:8vmax; --dur:22s}
.glow.g2{--c:rgba(236,72,153,.85); right:-8vmax; top:-6vmax; --dur:18s}
.glow.g3{--c:rgba(6,182,212,.85); left:18vmax; bottom:-10vmax; --dur:20s}
@keyframes drift{to{transform:translate3d(3vmax,-2vmax,0)}}

/* Konten utama */
.wrap{
  width:min(980px,96vw);
  display:flex; flex-direction:column; align-items:center; gap:18px;
  text-align:center;
}
.brand{display:flex; flex-direction:column; align-items:center; text-decoration:none}
.logo{width:120px; height:120px; object-fit:contain; filter:drop-shadow(0 10px 22px rgba(17,12,46,.18)); animation:float 6s ease-in-out infinite alternate}
@keyframes float{to{transform:translateY(-6px)}}
.brand__name{
  font-weight:800; font-size:2.2rem; line-height:1.1; letter-spacing:.02em;
  background:linear-gradient(90deg,#111 0%, var(--brand) 55%, var(--brand2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  margin-top:6px;
}

/* Card login */
.panel{
  width:min(720px,96vw);
  background:var(--panel); border:1px solid rgba(124,58,237,.12);
  border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;
  padding:32px 28px 30px;
}
.form-title{
  text-align:center; font-weight:900; font-size:1.7rem;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  margin-bottom:6px;
}
.form-sub{ text-align:center; color:var(--muted); font-size:1rem; margin-bottom:18px }

/* Form */
.form{ width:100%; max-width:560px; margin:0 auto }
.group{ position:relative; margin:14px 0 }
.input{
  width:100%; padding:16px 18px; border-radius:14px;
  border:1px solid rgba(2,6,23,.12); background:#eef2ff66;
  font:500 15px/1.25 'Plus Jakarta Sans',sans-serif; outline:none;
  transition:border .15s ease, box-shadow .15s ease, background .15s ease;
}
.input:hover{ background:#fff }
.input:focus{ border-color:var(--brand); box-shadow:0 0 0 6px var(--ring); background:#fff }
.eye{
  position:absolute; right:14px; top:50%; transform:translateY(-50%);
  color:#6b7280; cursor:pointer; font-size:18px;
}

/* Button */
.btn{
  width:100%; padding:16px; border:0; border-radius:14px;
  background:linear-gradient(90deg,var(--brand),var(--brand2));
  color:#fff; font-weight:900; letter-spacing:.3px; cursor:pointer;
  box-shadow:0 20px 56px rgba(124,58,237,.25); transition:transform .12s ease, box-shadow .12s ease;
}
.btn:hover{ transform:translateY(-1px); box-shadow:0 28px 66px rgba(124,58,237,.30) }
.btn:active{ transform:translateY(0) scale(.995) }

.switch{ text-align:center; color:var(--muted); margin-top:14px; font-size:1rem }
.switch a{ color:#7c3aed; font-weight:800; text-decoration:none }
.switch a:hover{text-decoration:underline}

.error{
  margin:12px auto 0; max-width:560px;
  padding:12px 14px; border-radius:12px; text-align:center;
  background:#fff1f2; color:#b91c1c; border:1px solid #fecaca; font-weight:700;
}

/* Responsive */
@media (max-width:600px){
  body{padding:24px 12px}
  .logo{width:96px;height:96px}
  .brand__name{font-size:1.9rem}
  .panel{padding:24px 18px}
  .input{padding:14px 16px}
  .btn{padding:14px}
  .glow{animation:none}
}
</style>
</head>
<body>

<a href="intro.php" class="back-btn">Kembali</a>

<div class="glows" aria-hidden="true">
  <span class="glow g1"></span>
  <span class="glow g2"></span>
  <span class="glow g3"></span>
</div>

<div class="wrap">
  <a class="brand" href="index.php">
    <img src="./logo.png" alt="Logo Sejiwa" class="logo">
    <span class="brand__name">Sejiwa</span>
  </a>

  <section class="panel">
    <h3 class="form-title">Masuk ke Akun</h3>
    <p class="form-sub">Gunakan email dan kata sandi yang terdaftar.</p>

    <?php if (!empty($error)) echo "<div class='error'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>

    <form method="post" class="form" autocomplete="on" novalidate>
      <div class="group">
        <input class="input" type="email" name="email" placeholder="Masukkan Email" required>
      </div>
      <div class="group">
        <input class="input" type="password" name="password" placeholder="Masukkan Kata Sandi" id="loginPassword" required>
        <span class="eye" onclick="togglePassword('loginPassword')" aria-label="Tampilkan/Sembunyikan kata sandi">
          <i class="fa-solid fa-eye" id="eyeIcon"></i>
        </span>
      </div>
      <button type="submit" name="login" class="btn">MASUK</button>
      <div class="switch">Belum punya akun? <a href="register.php">Daftar</a></div>
    </form>
  </section>
</div>

<script>
function togglePassword(id){
  const f=document.getElementById(id),i=document.getElementById('eyeIcon');
  if(f.type==='password'){ f.type='text'; i.classList.replace('fa-eye','fa-eye-slash'); }
  else { f.type='password'; i.classList.replace('fa-eye-slash','fa-eye'); }
}
document.addEventListener('DOMContentLoaded',()=>{document.body.classList.add('loaded');});
</script>
</body>
</html>
