<?php
$backLink = 'awal.php';
if (isset($_GET['from']) && $_GET['from'] === 'dashboard') {
    $backLink = 'dashboard.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tentang SEJIWA</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: 'Raleway', sans-serif;
  min-height: 100vh;
  padding: 15px;
  display: flex;
  justify-content: center;
  align-items: start;
  background: linear-gradient(135deg, #ffe4f0, #dcd0ff);
  overflow-x: hidden;
}

.container {
  width: 100%;
  max-width: 900px;
  background: rgba(255, 255, 255, 0.35);
  backdrop-filter: blur(18px);
  padding: 25px;
  border-radius: 25px;
  color: #333;
  animation: fadeInUp 0.7s ease forwards;
  box-shadow: 0 6px 28px rgba(0,0,0,0.15);
}

/* Button back */
.back-btn {
  position: fixed;
  top: 15px;
  left: 15px;
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #ff6f91, #9b5de5);
  border-radius: 50%;
  color: #fff;
  text-decoration: none;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.1rem;
  z-index: 99;
}

/* Title */
.section-title, .sub-title {
  text-align: center;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 1.7rem;
  margin-bottom: 20px;
  background: linear-gradient(90deg, #ff6f91, #9b5de5);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* TEAM GRID */
.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 18px;
  margin-bottom: 25px;
  justify-items: center;
}

.team-member {
  text-align: center;
  cursor: pointer;
  transition: .3s;
}

.team-member:hover {
  transform: scale(1.08);
}

.team-member img {
  width: 95px;
  height: 95px;
  object-fit: cover;
  border-radius: 50%;
  border: 3px solid #9b5de5;
}

.name { font-size: .9rem; font-weight: 600; margin-top: 6px; }
.nim { font-size: .8rem; color: #555; }

p { 
  font-size: .95rem;
  line-height: 1.7;
  text-align: justify;
  margin-bottom: 18px;
}

ul { padding-left: 18px; }
ul li { margin-bottom: 8px; font-size: .95rem; }

/* MODAL */
.modal {
  display: none;
  position: fixed;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.55);
  justify-content: center;
  align-items: flex-start;
  padding-top: 70px;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  padding: 22px;
  border-radius: 12px;
  width: 90%;
  max-width: 360px;
  text-align: center;
}

.modal-content img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 3px solid #9b5de5;
  margin-bottom: 10px;
}

.close-btn {
  position: absolute;
  top: 8px; right: 15px;
  font-size: 26px;
  cursor: pointer;
}

@media (max-width: 600px) {
  .container {
    padding: 18px;
  }
  
  .section-title, .sub-title {
    font-size: 1.4rem;
  }

  .team-member img {
    width: 80px;
    height: 80px;
  }
}
</style>
</head>

<body>

<div class="container">


<div class="team-grid">

<!-- **ISI FOTO TEAM TETAP SAMA — TIDAK DIUBAH** -->

<div class="container">
  <a href="<?= $backLink ?>" class="back-btn">←</a>

  <div class="section-title">SEJIWA TEAMS</div>

  <div class="team-grid">
    <div class="team-member" onclick="openModal('zaky.png', 'Alfari Zaky Firdaus', 'P20637124002')">
      <img src="foto/zaky.png" alt="">
      <div class="name">Alfari Zaky Firdaus</div>
      <div class="nim">P20637124002</div>
    </div>
    <div class="team-member" onclick="openModal('david.png', 'David Dharmawan', 'P20637124006')">
      <img src="foto/david.png" alt="">
      <div class="name">David Dharmawan</div>
      <div class="nim">P20637124006</div>
    </div>
    <div class="team-member" onclick="openModal('fauzi.png', 'Fauzi Athalah', 'P20637124015')">
      <img src="foto/fauzi.png" alt="">
      <div class="name">Fauzi Athalah</div>
      <div class="nim">P20637124015</div>
    </div>
    <div class="team-member" onclick="openModal('haya.png', 'Haya Rumaisha Azra Faridi', 'P20637124020')">
      <img src="foto/haya.png" alt="">
      <div class="name">Haya Rumaisha Azra Faridi</div>
      <div class="nim">P20637124020</div>
    </div>
    <div class="team-member" onclick="openModal('hilda.png', 'Hilda Herlina', 'P20637124022')">
      <img src="foto/hilda.png" alt="">
      <div class="name">Hilda Herlina</div>
      <div class="nim">P20637124022</div>
    </div>
    <div class="team-member" onclick="openModal('meri.png', 'Meri Agustina', 'P20637124026')">
      <img src="foto/meri.png" alt="">
      <div class="name">Meri Agustina</div>
      <div class="nim">P20637124026</div>
    </div>
    <div class="team-member" onclick="openModal('hasan.png', 'Muhammad Hasan Maulana', 'P20637124027')">
      <img src="foto/hasan.png" alt="">
      <div class="name">Muhammad Hasan Maulana</div>
      <div class="nim">P20637124027</div>
    </div>
    <div class="team-member" onclick="openModal('rahma.png', 'Rahmayulia Nurfazriyah', 'P20637124034')">
      <img src="foto/rahma.png" alt="">
      <div class="name">Rahmayulia Nurfazriyah</div>
      <div class="nim">P20637124034</div>
    </div>
    <div class="team-member" onclick="openModal('selin.png', 'Selina Raisa Pujianti', 'P20637124035')">
      <img src="foto/selin.png" alt="">
      <div class="name">Selina Raisa Pujianti</div>
      <div class="nim">P20637124035</div>
    </div>
    <div class="team-member" onclick="openModal('shofi.png', 'Shofiyah Nurul Jannah', 'P20637124036')">
      <img src="foto/shofi.png" alt="">
      <div class="name">Shofiyah Nurul Jannah</div>
      <div class="nim">P20637124036</div>
    </div>
  </div>

   <div class="sub-title">TENTANG SEJIWA</div>
  <p>Sejiwa adalah aplikasi skrining kesehatan mental digital yang dirancang untuk membantu pengguna mengenali kondisi emosional dan psikologis mereka secara mandiri. Di tengah kehidupan yang serba cepat dan penuh tekanan, Sejiwa hadir sebagai teman digital yang peduli, memberi ruang aman bagi siapa pun untuk memahami diri tanpa rasa takut atau stigma.</p>
  <p>Melalui beberapa pertanyaan berbasis metode psikologi ilmiah, Sejiwa membantu pengguna mengetahui apakah terdapat tanda-tanda stres, kecemasan, atau depresi, disertai saran praktis untuk menjaga keseimbangan mental. Dikembangkan oleh Kelompok 3, aplikasi ini menjadi bentuk nyata kepedulian terhadap pentingnya kesehatan mental masyarakat Indonesia.</p>

  <p><strong>🌿 Tujuan dan Filosofi</strong></p>
  <p>Filosofi utama Sejiwa berakar pada keyakinan sederhana namun mendalam:</p>
  <p>"Ketika pikiran tenang, hidup menjadi lebih berarti."</p>
  <p>Kami memahami bahwa kesehatan mental bukan sekadar tentang bebas dari stres atau depresi, tetapi tentang kemampuan untuk memahami diri, berdamai dengan masa lalu, dan berjalan dengan keyakinan menuju masa depan.</p>
  <p>Tujuan kami adalah untuk:</p>
  <ul>
    <li>Meningkatkan kesadaran masyarakat bahwa menjaga kesehatan mental sama pentingnya dengan menjaga kesehatan fisik.</li>
    <li>Memberikan akses mudah dan aman bagi siapa pun untuk melakukan deteksi dini terhadap kondisi psikologis tanpa rasa takut atau malu.</li>
    <li>Membantu individu memahami emosi dan pikiran mereka, agar mampu mengenali kapan mereka perlu beristirahat atau mencari dukungan profesional.</li>
    <li>Menumbuhkan budaya peduli diri (self-awareness dan self-compassion), di mana setiap orang diajak untuk menyayangi dirinya sendiri sebagaimana mereka menyayangi orang lain.</li>
  </ul>
  <p>Kami percaya bahwa setiap manusia memiliki kekuatan untuk pulih dan tumbuh. Melalui Sejiwa, kami ingin menyalakan cahaya kecil di tengah kegelapan batin, agar siapa pun yang merasa sendirian tahu bahwa mereka tidak benar-benar sendiri.</p>

  <p><strong>💡 Fitur Utama</strong></p>
  <ul>
    <li>Skrining Kesehatan Mental Mandiri:Lakukan pemeriksaan singkat berbasis metode psikologi yang terbukti ilmiah. Prosesnya sederhana, cepat, dan memberikan gambaran akurat tentang kondisi emosional saat ini.</li>
    <li>Hasil yang Informatif dan Mudah Dipahami: Hasil disampaikan dengan bahasa yang lembut dan jelas, membantu pengguna memahami apakah ada tanda-tanda stres, kecemasan, atau depresi, tanpa memberikan label yang menakutkan.</li>
    <li>Saran dan Edukasi Kesehatan Mental: Setelah tes, pengguna akan menerima rekomendasi praktis dan tips perawatan diri untuk menjaga kestabilan emosi dan keseimbangan mental.</li>
    <li>>Musik Relaksasi 🎵: Fitur ini menghadirkan pilihan musik relaksasi yang dapat membantu menenangkan pikiran, menurunkan stres, meningkatkan fokus, dan memperbaiki kualitas tidur. Setiap nada diciptakan untuk membawa ketenangan dan membantu pengguna kembali pada keheningan batinnya.</li>
    <li>Privasi dan Keamanan Data: Semua data pengguna disimpan secara terenkripsi dan rahasia. Sejiwa menjamin bahwa hanya pengguna yang dapat mengakses hasilnya — karena privasi adalah bentuk penghargaan terhadap kepercayaan.</li>
    <li>Akses Mudah dan Desain Ramah Pengguna: Dapat digunakan di mana saja dan kapan saja, Sejiwa dirancang dengan antarmuka yang sederhana dan bersahabat, sehingga siapa pun dapat merasakan manfaatnya tanpa kesulitan.</li>
  </ul>
<p><strong>💬 Penutup</strong></p>
  <p>Sejiwa bukan sekadar aplikasi, tetapi langkah kecil menuju kesadaran diri yang lebih baik. Kami percaya, menjaga kesehatan mental adalah bentuk tanggung jawab dan kasih terhadap diri sendiri. Dengan Sejiwa, setiap orang dapat memulai perjalanan untuk mengenali, memahami, dan merawat dirinya — karena setiap jiwa berhak merasa baik dan hidup dengan tenang.</p>
</div>

</div>

<!-- Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <img id="modal-photo" src="" alt="">
    <h3 id="modal-name"></h3>
    <div class="nim" id="modal-nim"></div>
    <p id="modal-quote" style="font-style: italic; margin-top: 15px; color: #555;"></p>
  </div>
</div>

<script>
  const quotes = {
    "Selina Raisa Pujianti": "Kadang, keberanian terbesar bukan tentang melawan dunia, tapi tetap bernapas saat segalanya terasa sesak. Kamu hebat karena masih bertahan.",
    "Shofiyah Nurul Jannah": "Kamu tidak harus selalu on fire, bahkan server pun butuh downtime buat maintenance.",
    "Hilda Herlina": "Orang yang kuat bukan berarti tak pernah jatuh, melainkan yang selalu menemukan alasan untuk bangkit kembali.",
    "Fauzi Athalah": "Setiap dari kita adalah pemenang.",
    "Meri Agustina": "Jatuh tujuh kali, bangun delapan kali. Yang penting bukan bagaimana kau jatuh, tapi bagaimana kau bangkit kembali.",
    "Rahmayulia Nurfazriyah": "Nanti kita rayakan perjalanan hebat yang diiringi air mata ini. Jadi, jangan nyerah dulu, ya. Terimakasih sudah membawa dirimu bertahan sampai sejauh ini.",
    "David Dharmawan": "Biarkan yang hitam menjadi hitam, tak perlu memaksanya untuk menjadi putih, karena tak semua yang kelam adalah salah. Putih belum tentu suci, dan hitam pun belum tentu bersalah.",
    "Alfari Zaky Firdaus": "Bahagia bukan soal sempurna, tapi soal menerima.",
    "Haya Rumaisha Azra Faridi": "Ingat, cinta terbaik yang bisa kamu berikan pada dunia adalah dirimu yang utuh, bahagia, dan tidak kehilangan jati diri.",
    "Muhammad Hasan Maulana": "Berhenti sebentar bukan berarti menyerah, tapi memberi ruang agar jiwa bisa bernapas."
  };

  function openModal(photo, name, nim) {
    document.getElementById('modal-photo').src = 'foto/' + photo;
    document.getElementById('modal-name').innerText = name;
    document.getElementById('modal-nim').innerText = nim;

    const quoteText = quotes[name] || "Anggota tim yang berdedikasi dalam proyek SEJIWA.";
    document.getElementById('modal-quote').innerText = quoteText;

    document.getElementById('modal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('modal').style.display = 'none';
  }

  window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if(event.target === modal) {
      modal.style.display = "none";
    }
  }
</script>

</body>
</html>
