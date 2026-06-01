<?php
session_start();
include 'koneksi.php';

// Proteksi: Hanya admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== '1') {
    header("Location: dashboard.php");  // Redirect ke dashboard biasa jika bukan admin
    exit();
}

// Handle delete hasil tes
if (isset($_GET['delete'])) {
    $id_hasil = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM tb_hasil WHERE id_hasil = ?");
    $stmt->bind_param("i", $id_hasil);
    if ($stmt->execute()) {
        $success = "Hasil tes berhasil dihapus.";
    } else {
        $error = "Gagal menghapus hasil tes.";
    }
}

// Ambil semua hasil tes dengan info user
$query = "SELECT h.id_hasil, h.skor, h.kategori, h.tanggal, u.nama AS user_nama, u.email AS user_email 
          FROM tb_hasil h 
          JOIN tb_users u ON h.user_email = u.email  -- Ganti 'user_email' jika pakai id_user, misalnya h.id_user = u.id_user
          ORDER BY h.tanggal DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SEJIWA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">  <!-- Sesuaikan path jika CSS di folder lain -->
    <style>
        /* CSS sederhana untuk tabel - bisa tambah dari style.css kamu */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .action-btn { padding: 8px 12px; background: #9b5de5; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .delete-btn { background: #ff4d4d; }
    </style>
</head>
<body>
    <div class="container">  <!-- Reuse class dari CSS kamu jika ada -->
        <h2>Admin Dashboard: Kelola Hasil Tes</h2>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <table>
            <tr>
                <th>Nama User</th>
                <th>Email</th>
                <th>Skor</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['user_nama']) ?></td>
                <td><?= htmlspecialchars($row['user_email']) ?></td>
                <td><?= $row['skor'] ?></td>
                <td><?= $row['kategori'] ?></td>
                <td><?= $row['tanggal'] ?></td>
                <td>
                    <!-- View detail: Sementara pakai alert JS -->
                    <button class="action-btn" onclick="alert('Detail Tes:\nNama: <?= $row['user_nama'] ?>\nSkor: <?= $row['skor'] ?>\nKategori: <?= $row['kategori'] ?>\nTanggal: <?= $row['tanggal'] ?>')">View</button>
                    <!-- Delete dengan konfirmasi JS -->
                    <a href="?delete=<?= $row['id_hasil'] ?>" class="action-btn delete-btn" onclick="return confirm('Yakin hapus hasil tes ini? Ini permanen!')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>