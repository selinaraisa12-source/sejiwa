<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: awal.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$namaPengguna = $_SESSION['nama'] ?? 'Pengguna';
$user_email = $_SESSION['user_email'] ?? '';

$mode = $_SESSION['mode'] ?? 'light';
$bodyClass = ($mode === 'dark') ? 'dark-mode' : 'light-mode';

$sql = "SELECT tanggal FROM tb_hasil WHERE id_user_ref = ? ORDER BY tanggal DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$pesan = null;
$tes_terakhir = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (!empty($row['tanggal']) && $row['tanggal'] != "0000-00-00 00:00:00") {
        $tes_terakhir = $row['tanggal'];
        $last_test_date = new DateTime($tes_terakhir);
        $next_allowed = clone $last_test_date;
        $next_allowed->modify('+30 days');
        $now = new DateTime();
        if ($now < $next_allowed) {
            $pesan = "Hai <b>$namaPengguna</b>, kamu sudah melakukan tes dalam 1 bulan terakhir 💜";
        }
    }
}

$questions = [
    'Apakah dalam sebulan belakangan ini Anda sering menderita sakit kepala?',
    'Apakah nafsu makan Anda menurun?',
    'Apakah Anda sulit tidur?',
    'Apakah Anda sering ketakutan?',
    'Apakah Anda merasa gugup atau khawatir?',
    'Apakah tangan Anda sering gemetar?',
    'Apakah pencernaan Anda terganggu?',
    'Apakah Anda sulit berpikir jernih?',
    'Apakah Anda merasa tidak bahagia?',
    'Apakah Anda menjadi sering menangis?',
    'Apakah Anda kurang menikmati kegiatan sehari-hari?',
    'Apakah Anda sulit mengambil keputusan?',
    'Apakah pekerjaan rutin Anda terganggu?',
    'Apakah Anda tidak mampu melakukan hal-hal yang bermanfaat?',
    'Apakah Anda kehilangan minat terhadap berbagai hal?',
    'Apakah Anda merasa tidak berharga?',
    'Apakah Anda pernah berpikir untuk mengakhiri hidup?',
    'Apakah Anda merasa lelah berkepanjangan?',
    'Apakah lambung Anda terasa tidak nyaman?',
    'Apakah kamu gampang capek?'
];

$totalQuestions = count($questions);
if (!isset($_SESSION['tes_jawaban'])) $_SESSION['tes_jawaban'] = array_fill(0, $totalQuestions, '');
$current = isset($_POST['current']) ? intval($_POST['current']) : 0;

$show_score = false;
$score = 0;
$kategori = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$pesan) {
    $action = $_POST['action'] ?? 'next';
    $answer = $_POST['answer'] ?? '';

    if ($action === 'next') {
        if (!empty($answer)) $_SESSION['tes_jawaban'][$current] = $answer;

        if ($current + 1 < $totalQuestions) {
            $current++;
        } else {
            foreach ($_SESSION['tes_jawaban'] as $a) if ($a === 'ya') $score++;
            $kategori = ($score <= 7) ? 'Normal' : (($score <= 14) ? 'Sedang' : 'Tinggi');

            $tanggal = date('Y-m-d');
            $simpan = $conn->prepare("INSERT INTO tb_hasil (id_user_ref, skor, kategori, tanggal) VALUES (?, ?, ?, ?)");
            $simpan->bind_param("iiss", $id_user, $score, $kategori, $tanggal);
            $simpan->execute();
            unset($_SESSION['tes_jawaban']);

            $show_score = true;
        }
    } elseif ($action === 'back' && $current > 0) {
        $current--;
    }
}

$questionText = $questions[$current];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>SEJIWA - SRQ-20</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
<style>
body {margin:0; font-family:'Poppins',sans-serif; min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:flex-start; padding-top:60px; transition:0.4s;}
.card {width:90%; max-width:650px; border-radius:14px; padding:30px; text-align:left; box-shadow:0 6px 18px rgba(0,0,0,0.25); margin-bottom:30px; transition:0.4s;}
h2 {margin:40px 0; font-size:48px; font-family:'Playfair Display', serif; text-align:center; transition:0.4s;}
.options {display:flex; justify-content:center; gap:30px; margin-top:25px;}
.option-btn {display:inline-block; padding:16px 50px; border-radius:50px; border:2px solid #aaa; background:transparent; font-weight:600; font-size:16px; cursor:pointer; text-align:center; transition:0.3s;}
.option-btn:hover {border-color:#a566ff;}

 /* FIX PENTING — tombol kembali & selanjutnya jadi bisa diklik */
input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

input[type="radio"]:checked + .option-btn {transform:scale(1.05);}

.nav-buttons {display:flex; justify-content:space-between; margin-top:30px; gap:15px;}
.nav-btn {flex:1; padding:12px 0; border-radius:8px; border:none; font-weight:600; cursor:pointer; font-size:15px; transition:0.3s; text-decoration:none; text-align:center;}
.nav-btn:hover {transform:scale(1.05);}

.dashboard-btn {padding:10px 25px; border-radius:30px; text-decoration:none; font-weight:600; transition:0.3s; display:inline-block;}
.dashboard-btn:hover {transform:scale(1.05); text-decoration:none;}

body.light-mode {background: linear-gradient(-45deg,#d0bfff,#ffb5e8,#b5ffea,#ffb5c5); color:#222; background-size:400% 400%; animation: gradientBG 20s ease infinite;}
body.light-mode .card {background:#fff; color:#222;}
body.light-mode h2 {color:#fff; text-shadow:1px 1px 2px rgba(255,255,255,0.94),2px 2px 4px rgba(0,0,0,0.1);}
body.light-mode input[type="radio"]:checked + .option-btn {background: linear-gradient(to right,#ff758c,#a566ff); color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.2);}
body.light-mode .nav-btn, body.light-mode .dashboard-btn {background: linear-gradient(to right,#ff6f91,#9b5de5); color:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.15);}
body.light-mode .nav-btn:hover, body.light-mode .dashboard-btn:hover {background: linear-gradient(to right,#ff758c,#a347ff);}

body.dark-mode {background: linear-gradient(135deg, #1b0a2a, #2b1d4a, #141414); color:#f1f1f1; background-size:200% 200%; animation: darkGradient 20s ease infinite;}
body.dark-mode .card {background: linear-gradient(145deg, #2d2145, #3b2b5a); color:#f1f1f1; box-shadow:0 0 20px rgba(165,102,255,0.25);}
body.dark-mode h2 {color:#e0cfff; text-shadow:0 0 10px rgba(165,102,255,0.6);}
body.dark-mode input[type="radio"]:checked + .option-btn {background: linear-gradient(145deg,#5b3f7a,#7a5bb0); color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.3);}
body.dark-mode .nav-btn, body.dark-mode .dashboard-btn {background: linear-gradient(145deg,#5b3f7a,#7a5bb0); color:#fff; box-shadow:0 0 15px rgba(221,160,255,0.4);}
body.dark-mode .nav-btn:hover, body.dark-mode .dashboard-btn:hover {box-shadow:0 0 20px rgba(199,125,255,0.5);}

.question-wrapper { transition: opacity 0.4s ease, transform 0.4s ease;}
.fade-out {opacity: 0; transform: translateY(-15px);}
.fade-in {opacity: 1; transform: translateY(0);}

@keyframes gradientBG {0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
@keyframes darkGradient {0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
</style>
</head>
<body class="<?= $bodyClass ?>">

<h2>SRQ-20 – Skrining Kesehatan Mental</h2>

<div style="text-align:center; margin-bottom:30px;">
    <a href="dashboard.php" class="dashboard-btn"
       onclick="return confirm('Apakah kamu yakin ingin keluar? Jawaban saat ini tidak akan disimpan.');">
       Kembali ke Dashboard
    </a>
</div>

<div class="card">
<?php if ($pesan): ?>
<p><?= $pesan ?><br><br>
📅 <b>Tes terakhir:</b> <?= date('d F Y', strtotime($tes_terakhir)) ?><br>
🔒 <b>Kamu bisa tes lagi mulai:</b> <?= date('d F Y', strtotime('+30 days', strtotime($tes_terakhir))) ?>
</p>

<?php elseif($show_score): ?>
<div style="text-align:center; padding:20px;">
    <h2>SKOR ANDA</h2>
    <div style="width:150px; height:150px; line-height:150px; border-radius:50%; margin:20px auto;
        background:linear-gradient(135deg,#a566ff,#ff758c); color:#fff; font-size:36px; font-weight:700;">
        <?= $score ?> / <?= $totalQuestions ?>
    </div>
    <h3>Kategori Risiko: <?= $kategori ?></h3>
    <p>Terima kasih telah menyelesaikan tes! 💜</p>
    <p>Tes hanya bisa dilakukan <b>1 bulan sekali</b>.</p>
    <p>Tes saat ini: <b><?= date('d F Y') ?></b></p>

    <div style="margin-top:15px; display:flex; justify-content:center; gap:15px; flex-wrap:wrap;">
        <a href="hasil_tes.php" class="nav-btn" style="flex:1 1 200px; text-align:center; padding:12px 0;">
            Lihat Riwayat Hasil
        </a>
        <a href="musik.php" class="nav-btn" style="flex:1 1 200px; text-align:center; padding:12px 0;">
            Musik Relaksasi
        </a>
    </div>
</div>

<?php else: ?>
<form method="post" id="formTes">
    <input type="hidden" name="current" value="<?= $current ?>">
    <input type="hidden" name="action" value="next">

    <div class="question-wrapper fade-in" id="questionWrapper">
        <h3><?= ($current+1).'. '.htmlspecialchars($questionText) ?></h3>

        <div class="options">
            <input type="radio" id="ya" name="answer" value="ya" required>
            <label for="ya" class="option-btn">Ya</label>

            <input type="radio" id="tidak" name="answer" value="tidak">
            <label for="tidak" class="option-btn">Tidak</label>
        </div>

        <div class="nav-buttons">
        <?php if($current>0): ?>
            <button type="submit" name="submitBtn" value="back" class="nav-btn">Kembali</button>
        <?php else: ?>
            <div></div>
        <?php endif; ?>

            <button type="submit" name="submitBtn" value="next" class="nav-btn">
                <?= ($current+1==$totalQuestions)?'Lihat Hasil':'Selanjutnya' ?>
            </button>
        </div>
    </div>
</form>
<?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const wrapper = document.getElementById("questionWrapper");
    const form = document.getElementById("formTes");

    if (!form) return;

    // Tombol kembali
    document.querySelectorAll("button[name='submitBtn'][value='back']").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            form.querySelector('input[name="action"]').value = 'back';
            form.submit();
        });
    });

    // Tombol selanjutnya
    document.querySelectorAll("button[name='submitBtn'][value='next']").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            form.querySelector('input[name="action"]').value = 'next';

            wrapper.classList.remove("fade-in");
            wrapper.classList.add("fade-out");

            setTimeout(() => {
                form.submit();
            }, 350);
        });
    });

    // AUTO NEXT
    document.querySelectorAll('input[name="answer"]').forEach(radio => {
        radio.addEventListener("change", function () {

            wrapper.classList.remove("fade-in");
            wrapper.classList.add("fade-out");

            setTimeout(() => {
                form.querySelector('input[name="action"]').value = 'next';
                form.submit();
            }, 350);
        });
    });

});
</script>


<script src="theme.js"></script>
</body>
</html>
