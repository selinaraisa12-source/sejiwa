<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
ob_start(); // pastikan buffer aktif

include 'koneksi.php';

// Nonaktifkan error ke browser
error_reporting(0);
ini_set('display_errors', 0);

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi login tidak ditemukan']);
    exit;
}

$id_user = $_SESSION['id_user'];
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$foto_profil = null;

// Validasi dasar
if (empty($nama) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Nama dan email wajib diisi']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid']);
    exit;
}

if (!empty($password) && !preg_match('/^(?=.*[A-Z])(?=.*\d).{8,20}$/', $password)) {
    echo json_encode(['status' => 'error', 'message' => 'Kata sandi harus 8–20 karakter, mengandung huruf besar dan angka']);
    exit;
}

// Sanitasi nama
$nama = preg_replace("/[^a-zA-Z0-9\s\p{L}]/u", "", $nama);

// Folder upload
$upload_dir = __DIR__ . "/foto_user/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Upload foto
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Format foto tidak valid (hanya JPG, PNG, GIF)']);
        exit;
    }

    if ($_FILES['foto_profil']['size'] > 2 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'Ukuran foto maksimal 2MB']);
        exit;
    }

    $new_filename = 'user_' . $id_user . '_' . time() . '.' . $ext;
    $target_path = $upload_dir . $new_filename;

    // Hapus foto lama
    $get_old = $conn->prepare("SELECT foto_profil FROM tb_users WHERE id_user=?");
    $get_old->bind_param("i", $id_user);
    $get_old->execute();
    $result_old = $get_old->get_result()->fetch_assoc();
    if (!empty($result_old['foto_profil'])) {
        $old_path = $upload_dir . $result_old['foto_profil'];
        if (file_exists($old_path)) unlink($old_path);
    }
    $get_old->close();

    if (!move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_path)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah foto']);
        exit;
    }

    $foto_profil = $new_filename;
}

try {
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        if ($foto_profil) {
            $sql = "UPDATE tb_users SET nama=?, email=?, password=?, foto_profil=? WHERE id_user=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $nama, $email, $hashed, $foto_profil, $id_user);
        } else {
            $sql = "UPDATE tb_users SET nama=?, email=?, password=? WHERE id_user=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nama, $email, $hashed, $id_user);
        }
    } else {
        if ($foto_profil) {
            $sql = "UPDATE tb_users SET nama=?, email=?, foto_profil=? WHERE id_user=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nama, $email, $foto_profil, $id_user);
        } else {
            $sql = "UPDATE tb_users SET nama=?, email=? WHERE id_user=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nama, $email, $id_user);
        }
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diperbarui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}

$conn->close();
exit;
?>
