<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
  header("Location: awal.php");
  exit();
}

$id_user = $_SESSION['id_user'];

$sql = "SELECT nama, email, foto_profil FROM tb_users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : ['nama'=>'','email'=>'','foto_profil'=>''];
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil - SEJIWA</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&family=Raleway:wght@600&display=swap" rel="stylesheet">
</head>
<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f0e1ff, #e6ccff);
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  transition: background 1s ease-in-out;
}

.content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  animation: fadeInUp 0.8s ease-in-out;
}

.profile-card {
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(14px);
  border-radius: 25px;
  padding: 40px;
  width: 95%;
  max-width: 600px;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-shadow: 0 10px 30px rgba(255, 95, 162, 0.25);
  position: relative;
  transition: all 0.3s ease-in-out;
}

.profile-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 40px rgba(255, 95, 162, 0.3);
}

.edit-icon {
  position: absolute;
  top: 20px;
  right: 20px;
  background: linear-gradient(135deg, #ff5fa2, #ff9fc9);
  border: none;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  cursor: pointer;
  font-size: 18px;
  transition: transform 0.3s, background 0.3s;
  box-shadow: 0 5px 15px rgba(255, 95, 162, 0.3);
  z-index: 10;
}

.edit-icon:hover {
  transform: scale(1.1) rotate(10deg);
  background: linear-gradient(135deg, #e14b90, #ff7ebf);
}

.icon-label {
  position: absolute;
  top: 75px;
  right: 20px;
  font-size: 12px;
  color: #a65fa5;
  font-weight: 500;
  text-align: center;
  background: rgba(255, 255, 255, 0.6);
  padding: 4px 8px;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

h2 {
  margin-bottom: 25px;
  font-family: 'Raleway', sans-serif;
  color: #ff5fa2;
  text-align: center;
  letter-spacing: 1px;
}

.profile-info {
  display: flex;
  align-items: center;
  width: 100%;
  gap: 25px;
  flex-wrap: wrap;
  justify-content: center;
}

.profile-img-container {
  position: relative;
  width: 120px;
  height: 120px;
}

.profile-img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid #ff5fa2;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
}

.profile-img:hover {
  transform: scale(1.1);
  box-shadow: 0 0 20px rgba(255, 95, 162, 0.6);
}

.info-fields {
  flex: 1;
  width: 100%;
}

.field-label {
  font-size: 14px;
  font-weight: 500;
  color: #a65fa5;
  margin-top: 10px;
}

.info-fields input {
  width: 100%;
  padding: 12px;
  margin: 6px 0 18px;
  border: none;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.85);
  font-size: 14px;
  transition: box-shadow 0.3s, border 0.3s;
}

.info-fields input:focus {
  outline: none;
  border: 2px solid #ff5fa2;
  box-shadow: 0 2px 8px rgba(255, 95, 162, 0.4);
}

.password-info {
  display: none;
  font-size: 12px;
  color: #a65fa5;
  margin-top: -10px;
  margin-bottom: 10px;
  text-align: left;
}

.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #a65fa5;
}

.back-btn {
  position: fixed;
  top: 20px;
  left: 20px;
  background: #ff5fa2;
  border: none;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  font-size: 18px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.25);
  display: flex;
  justify-content: center;
  align-items: center;
  transition: transform 0.2s, background 0.3s;
}

.back-btn:hover {
  background: #e14b90;
  transform: scale(1.1);
}

.password-container {
  position: relative;
}

/* Tombol ganti background */
.bg-switcher {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  gap: 12px;
  z-index: 10;
}

.bg-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  box-shadow: 0 3px 10px rgba(0,0,0,0.2);
  transition: transform 0.3s ease;
}

.bg-btn:hover {
  transform: scale(1.2);
}
.bg-purple { background: linear-gradient(135deg, #f0e1ff, #e6ccff); }
.bg-pink { background: linear-gradient(135deg, #ffe0f0, #ffd6e8); }
.bg-blue { background: linear-gradient(135deg, #d6e6ff, #c8d8ff); }

@keyframes fadeInUp {
  from {opacity: 0; transform: translateY(20px);}
  to {opacity: 1; transform: translateY(0);}
}
</style>
<body>

  <button class="back-btn" onclick="window.location.href='dashboard.php'"><i class="fas fa-arrow-left"></i></button>

 <div class="content">
    <div class="profile-card">
      <button class="edit-icon" id="editSaveBtn"><i class="fas fa-pen"></i></button>
      <div class="icon-label" id="iconLabel">Edit</div>
      <h2>PROFIL</h2>
      <div class="profile-info">
        <div class="profile-img-container">
          <?php
            $fotoPath = (!empty($data['foto_profil']) && file_exists("foto_user/".$data['foto_profil'])) 
              ? "foto_user/".$data['foto_profil'] 
              : "foto_user/default_profile.png";
          ?>
          <img src="<?php echo $fotoPath; ?>" class="profile-img" id="profilePic" alt="Foto Profil">
          <input type="file" id="uploadPic" accept="image/*" style="display:none;">
          <div id="changePhotoText" style="display:none; text-align:center; color:#a65fa5; font-size:13px; margin-top:6px; font-weight:500;">
            Ubah Foto Profil
          </div>
        </div>
        <div class="info-fields">
          <div class="field-label">Nama</div>
          <input type="text" id="name" value="<?php echo htmlspecialchars($data['nama']); ?>" disabled>

          <div class="field-label">Email</div>
          <input type="email" id="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled>

          <div class="field-label" id="labelNewPass" style="display:none;">Ubah Kata Sandi</div>
          <div class="password-container" id="divNewPass" style="display:none;">
            <input type="password" id="new_password" placeholder="Masukkan kata sandi baru" disabled>
            <i class="fas fa-eye toggle-password"></i>
          </div>

          <div class="field-label" id="labelConfirmPass" style="display:none;">Konfirmasi Kata Sandi</div>
          <div class="password-container" id="divConfirmPass" style="display:none;">
            <input type="password" id="confirm_password" placeholder="Konfirmasi kata sandi" disabled>
            <i class="fas fa-eye toggle-password"></i>
          </div>

          <div class="password-info" id="passwordInfo">Kata sandi harus 8–20 karakter, mengandung minimal 1 huruf kapital, dan 1 angka.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tombol ubah warna background -->
  <div class="bg-switcher">
    <button class="bg-btn bg-purple" data-bg="purple" title="Ungu"></button>
    <button class="bg-btn bg-pink" data-bg="pink" title="Pink"></button>
    <button class="bg-btn bg-blue" data-bg="blue" title="Biru"></button>
  </div>

<script>
const editSaveBtn = document.getElementById('editSaveBtn');
const iconLabel = document.getElementById('iconLabel');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const newPass = document.getElementById('new_password');
const confirmPass = document.getElementById('confirm_password');
const profilePic = document.getElementById('profilePic');
const uploadPic = document.getElementById('uploadPic');
const passwordInfo = document.getElementById('passwordInfo');
const labelNewPass = document.getElementById('labelNewPass');
const labelConfirmPass = document.getElementById('labelConfirmPass');
const divNewPass = document.getElementById('divNewPass');
const divConfirmPass = document.getElementById('divConfirmPass');
const changePhotoText = document.getElementById('changePhotoText');
let isEditing = false;

// toggle password visibility
document.querySelectorAll('.toggle-password').forEach(icon => {
  icon.addEventListener('click', () => {
    const input = icon.previousElementSibling;
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
  });
});

// edit/save profile
editSaveBtn.addEventListener('click', () => {
  isEditing = !isEditing;
  nameInput.disabled = !isEditing;
  emailInput.disabled = !isEditing;
  newPass.disabled = !isEditing;
  confirmPass.disabled = !isEditing;

  labelNewPass.style.display = isEditing ? 'block' : 'none';
  labelConfirmPass.style.display = isEditing ? 'block' : 'none';
  divNewPass.style.display = isEditing ? 'block' : 'none';
  divConfirmPass.style.display = isEditing ? 'block' : 'none';
  passwordInfo.style.display = isEditing ? 'block' : 'none';
  changePhotoText.style.display = isEditing ? 'block' : 'none';

  editSaveBtn.innerHTML = isEditing ? '<i class="fas fa-save"></i>' : '<i class="fas fa-pen"></i>';
  iconLabel.textContent = isEditing ? 'Simpan' : 'Edit';

  if (!isEditing) {
    const formData = new FormData();
    formData.append('nama', nameInput.value);
    formData.append('email', emailInput.value);
    if (newPass.value) formData.append('password', newPass.value);
    if (uploadPic.files[0]) formData.append('foto_profil', uploadPic.files[0]);

    fetch('update_profile.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Profil berhasil diperbarui!');
          location.reload();
        } else alert('Error: ' + data.message);
      })
      .catch(err => alert('Error: ' + err));
  }
});

// upload preview
profilePic.addEventListener('click', () => { if (isEditing) uploadPic.click(); });
uploadPic.addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => profilePic.src = e.target.result;
    reader.readAsDataURL(file);
  }
});

// ubah background warna
const bgButtons = document.querySelectorAll('.bg-btn');
bgButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    document.body.style.transition = 'background 1s ease-in-out';
    if (btn.dataset.bg === 'purple') document.body.style.background = 'linear-gradient(135deg, #f0e1ff, #e6ccff)';
    if (btn.dataset.bg === 'pink') document.body.style.background = 'linear-gradient(135deg, #ffe0f0, #ffd6e8)';
    if (btn.dataset.bg === 'blue') document.body.style.background = 'linear-gradient(135deg, #d6e6ff, #c8d8ff)';
  });
});
</script>
</body>
</html>
