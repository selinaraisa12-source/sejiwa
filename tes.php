<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user']) || !isset($_SESSION['user_email'])) {
    header("Location: awal.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$namaPengguna = $_SESSION['nama'] ?? 'Pengguna';
$email_user = $_SESSION['user_email'];

$questions = [
    'q1'=>'Apakah dalam sebulan belakangan ini Anda sering menderita sakit kepala?',
    'q2'=>'Apakah dalam sebulan belakangan ini nafsu makan Anda turun?',
    'q3'=>'Apakah dalam sebulan belakangan ini Anda sulit tidur?',
    'q4'=>'Apakah dalam sebulan belakangan ini Anda sering ketakutan?',
    'q5'=>'Apakah dalam sebulan belakangan ini Anda merasa gugup tegang atau khawatir?',
    'q6'=>'Apakah dalam sebulan belakangan ini tangan Anda sering gemetar?',
    'q7'=>'Apakah dalam sebulan belakangan ini pencernaan Anda terganggu?',
    'q8'=>'Apakah dalam sebulan belakangan ini Anda sulit berpikir jernih?',
    'q9'=>'Apakah dalam sebulan belakangan ini Anda merasa tidak bahagia?',
    'q10'=>'Apakah dalam sebulan belakangan ini Anda menjadi sering menangis?',
    'q11'=>'Apakah dalam sebulan belakangan ini Anda kurang menikmati kegiatan sehari-hari?',
    'q12'=>'Apakah dalam sebulan belakangan ini Anda sulit mengambil keputusan?',
    'q13'=>'Apakah dalam sebulan belakangan ini pekerjaan rutin Anda terganggu?',
    'q14'=>'Apakah dalam sebulan belakangan ini Anda tidak mampu melakukan hal-hal yang bermanfaat?',
    'q15'=>'Apakah dalam sebulan belakangan ini Anda kehilangan minat terhadap berbagai hal?',
    'q16'=>'Apakah dalam sebulan belakangan ini Anda merasa diri Anda tidak berharga?',
    'q17'=>'Apakah dalam sebulan belakangan ini dalam benak Anda ada pikiran untuk mengakhiri hidup?',
    'q18'=>'Apakah dalam sebulan belakangan ini Anda merasa lelah berkepanjangan?',
    'q19'=>'Apakah dalam sebulan belakangan ini lambung Anda terasa tidak nyaman?',
    'q20'=>'Apakah dalam sebulan belakangan ini Anda gampang capek?'
];

$score = null;
$lastResult = null;
$canTakeTest = true;
$nextAvailableDate = null;

// Cek tes terakhir
$stmt = $conn->prepare("SELECT skor, kategori, tanggal FROM tb_hasil WHERE user_email=? ORDER BY tanggal DESC LIMIT 1");
$stmt->bind_param("s",$email_user);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows>0){
    $lastResultDB = $result->fetch_assoc();
    $lastDate = new DateTime($lastResultDB['tanggal']);
    $now = new DateTime();
    $diff = $now->diff($lastDate)->days;
    if($diff < 30){
        $canTakeTest = false;
        $nextAvailableDate = $lastDate->modify('+30 days')->format('d-m-Y');
        $lastResult = $lastResultDB;
    }
}
$stmt->close();

// Proses submit
if($_SERVER['REQUEST_METHOD']==='POST' && $canTakeTest){
    $score = 0;
    foreach($questions as $key => $q){
        if(isset($_POST[$key]) && $_POST[$key]==='ya') $score++;
    }
    if($score <=7) $kategori = 'Normal';
    elseif($score <=14) $kategori = 'Sedang';
    else $kategori = 'Tinggi';

    $tanggal = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO tb_hasil (user_email, skor, kategori, tanggal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss",$email_user,$score,$kategori,$tanggal);
    if($stmt->execute()){
        // Ambil kembali data terakhir agar langsung tampil
        $lastResult = ['skor'=>$score,'kategori'=>$kategori,'tanggal'=>$tanggal];
        $canTakeTest = false;
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan hasil tes.');</script>";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SRQ-20 - Sejiwa</title>
<style>
body {
    margin:0; font-family: 'Raleway', sans-serif; overflow-x:hidden; min-height:100vh;
    display:flex; justify-content:center; align-items:center;
    background: linear-gradient(135deg,#ffd6e7,#c6d4ff); background-size: 400% 400%;
    animation: gradientBG 15s ease infinite; position:relative;
}
@keyframes gradientBG{0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
.particle { position:absolute; border-radius:50%; background:rgba(255,255,255,0.3); animation: floatParticle linear infinite;}
@keyframes floatParticle {0%{transform: translateY(0);}100%{transform: translateY(-120vh);}}
.container {background:white; padding:30px; border-radius:25px; max-width:600px; width:90%; box-shadow:0 10px 25px rgba(0,0,0,0.12); position:relative; overflow:hidden; z-index:1;}
h2 { text-align:center; margin-bottom:20px;}
.question { display:none; opacity:0; transition: opacity 0.5s ease;}
.question.active { display:block; opacity:1;}
.options { display:flex; justify-content:space-around; margin-top:10px; gap:30px;}
.options label { min-width:100px; background:#f5f5f5; padding:12px 18px; border-radius:8px; cursor:pointer; text-align:center;}
.actions { display:flex; justify-content:space-between; margin-top:30px; gap:20px;}
button.nextBtn, button.backBtn { flex:1; padding:12px 0; font-weight:600; font-size:1rem; border-radius:50px; cursor:pointer; border:none; transition:transform 0.2s, box-shadow 0.2s;}
button.nextBtn { background: linear-gradient(90deg,#ff6f91,#9b5de5); color:white;}
button.backBtn { background:white; border:2px solid #9b5de5; color:#9b5de5;}
button.nextBtn:hover, button.backBtn:hover { transform: scale(1.05); box-shadow:0 8px 20px rgba(155,93,229,0.5);}
.back-btn { position:absolute; top:20px; left:20px; width:45px; height:45px; background:linear-gradient(135deg,#ff6f91,#9b5de5); border-radius:50%; display:flex; justify-content:center; align-items:center; text-decoration:none; color:white; font-size:1.2rem; transition:0.3s; z-index:2;}
.back-btn:hover { transform:scale(1.1); box-shadow:0 8px 20px rgba(0,0,0,0.3);}
.circle-score {margin:20px auto; background:#fff; border:5px solid; border-radius:50%; width:130px; height:130px; display:flex; flex-direction:column; justify-content:center; align-items:center; font-size:36px; font-weight:bold; box-shadow:0 10px 25px rgba(0,0,0,0.15); animation: pulse 1.5s infinite;}
@keyframes pulse {0%,100%{transform:scale(1);}50%{transform:scale(1.15);}}
.result {position: relative; text-align:center; font-size:1.2rem; font-weight:bold; opacity:0; transition:opacity 1s ease; background: rgba(255,255,255,0.85); padding: 30px; border-radius:25px; box-shadow:0 15px 35px rgba(0,0,0,0.15); backdrop-filter: blur(6px);}
.result-buttons {display:flex; justify-content:center; gap:15px; margin-top:25px;}
.button-link {display:inline-block; padding:14px 24px; background: linear-gradient(90deg,#9b5de5,#ff6f91); color:white; text-decoration:none; border-radius:15px; font-weight:bold; transition: transform 0.2s, box-shadow 0.2s;}
.button-link:hover { transform: scale(1.05); box-shadow:0 10px 25px rgba(155,93,229,0.5);}
</style>
</head>
<body>

<?php for($p=0;$p<50;$p++): ?>
<div class="particle" style="width:<?=rand(6,20)?>px; height:<?=rand(6,20)?>px; left:<?=rand(0,100)?>%; bottom:-20px; animation-duration:<?=rand(15,35)?>s; animation-delay:<?=rand(0,10)?>s;"></div>
<?php endfor; ?>

<div class="container">

<?php if($canTakeTest): ?>
<a href="dashboard.php" class="back-btn">←</a>
<form method="post" id="srqForm">
<h2>SRQ-20 – Skrining Kesehatan Mental</h2>
<?php $i=0; foreach($questions as $key=>$question): $i++; ?>
<div class="question <?= $i===1?'active':'' ?>">
<p><strong><?= $i ?>.</strong> <?= htmlspecialchars($question) ?></p>
<div class="options">
<label><input type="radio" name="<?= $key ?>" value="ya"> Ya</label>
<label><input type="radio" name="<?= $key ?>" value="tidak"> Tidak</label>
</div>
<div class="actions">
<?php if($i>1): ?><button type="button" class="backBtn" onclick="prevStep()">Sebelumnya</button><?php endif; ?>
<?php if($i<count($questions)): ?><button type="button" class="nextBtn" onclick="nextStep()">Selanjutnya</button><?php else: ?><button type="submit" class="nextBtn">Lihat Hasil</button><?php endif; ?>
</div>
</div>
<?php endforeach; ?>
</form>
<?php endif; ?>

<?php if(!$canTakeTest && $lastResult): 
$scoreColor='#4caf50'; $emoji='😊';
if($lastResult['kategori']=='Sedang') { $scoreColor='#ffb300'; $emoji='😐'; }
if($lastResult['kategori']=='Tinggi') { $scoreColor='#f44336'; $emoji='😟'; }
?>
<div class="result show" id="resultBox">
<h2>Hasil Tes Anda</h2>
<div class="circle-score" style="border-color:<?= $scoreColor ?>; color:<?= $scoreColor ?>;">
    <?= $emoji ?><br><?= $lastResult['skor'] ?>
</div>
<p style="color:<?= $scoreColor ?>; font-size:1.4rem; font-weight:600; margin-top:15px;">
Tingkat stres Anda: <strong><?= $lastResult['kategori'] ?></strong>
</p>
<p><small>Tanggal tes: <?= $lastResult['tanggal'] ?></small></p>
<?php if(isset($nextAvailableDate)): ?>
<p style="color:red; font-weight:bold;">Anda hanya bisa mengisi tes lagi setelah tanggal <?= $nextAvailableDate ?></p>
<?php endif; ?>
<div class="result-buttons">
    <a href="dashboard.php" class="button-link">Kembali ke Dashboard</a>
    <a href="musik.php" class="button-link">Musik Relaksasi</a>
</div>
<?php if($lastResult['kategori']=='Tinggi'): ?>
<canvas id="confettiCanvas" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></canvas>
<?php endif; ?>
</div>
<?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 0;
    const questionsEl = document.querySelectorAll('.question');

    function showStep(index) {
        questionsEl.forEach((q,i) => q.classList.toggle('active', i===index));
    }

    showStep(currentStep);

    window.nextStep = function() {
        const current = questionsEl[currentStep];
        const radios = current.querySelectorAll('input[type=radio]');
        let answered = false;
        radios.forEach(r => { if(r.checked) answered = true; });
        if(!answered){ alert('Silakan pilih jawaban terlebih dahulu.'); return; }

        // Auto next jika ada step berikut
        if(currentStep < questionsEl.length - 1){
            currentStep++;
            showStep(currentStep);
        } else {
            document.getElementById('srqForm').submit();
        }
    }

    window.prevStep = function() {
        if(currentStep > 0){
            currentStep--;
            showStep(currentStep);
        }
    }

    // Auto next saat radio dipilih
    questionsEl.forEach(q => {
        const radios = q.querySelectorAll('input[type=radio]');
        radios.forEach(r => {
            r.addEventListener('change', function(){
                setTimeout(nextStep, 200); // delay 200ms untuk animasi
            });
        });
    });
});
</script>

</body>
</html>
