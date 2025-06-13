<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

$hata = "";
$basari = "";

try {
    $conn = dbBaglan();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['islem']) && $_POST['islem'] == 'ekle') {
            $stmt = $conn->prepare("INSERT INTO kitaplar (kitap_id, kitap_adi, yazar, kategori, adet) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssii", $_POST['kitap_id'], $_POST['kitap_adi'], $_POST['yazar'], $_POST['kategori'], $_POST['adet']);
            $stmt->execute();
            $basari = "Kitap başarıyla eklendi!";
        } elseif (isset($_POST['sil']) && isset($_POST['kitap_id_sil'])) {
            $stmt = $conn->prepare("DELETE FROM kitaplar WHERE kitap_id = ?");
            $stmt->bind_param("s", $_POST['kitap_id_sil']);
            $stmt->execute();
            $basari = "Kitap başarıyla silindi!";
        } elseif (isset($_POST['guncelle']) && isset($_POST['kitap_id_guncelle'])) {
            $stmt = $conn->prepare("UPDATE kitaplar SET kitap_adi = ?, yazar = ?, kategori = ?, adet = ? WHERE kitap_id = ?");
            $stmt->bind_param("sssii", $_POST['kitap_adi'], $_POST['yazar'], $_POST['kategori'], $_POST['adet'], $_POST['kitap_id_guncelle']);
            $stmt->execute();
            $basari = "Kitap başarıyla güncellendi!";
        }
    }

    $result = $conn->query("SELECT * FROM kitaplar");
    $kitaplar = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

} catch (Exception $e) {
    $hata = "Hata: " . $e->getMessage();
} finally {
    dbKapat($conn);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kitap Yönetimi</title>
    <link rel="stylesheet" href="stil.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .form-group { display: flex; gap: 10px; margin-bottom: 10px; }
        .btn { padding: 5px 10px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kitap Yönetimi</h2>
        <a href="ana_sayfa.php" class="btn">Ana Sayfaya Dön <i class="bi bi-house"></i></a>
        <a href="cikis.php" class="btn logout">Çıkış Yap <i class="bi bi-box-arrow-right"></i></a>

        <h3>Kitap Ekle</h3>
        <form method="POST" class="form">
            <input type="hidden" name="islem" value="ekle">
            <div class="form-group">
                <label>Kitap ID</label><input type="text" name="kitap_id" required>
                <label>Kitap Adı</label><input type="text" name="kitap_adi" required>
                <label>Yazar</label><input type="text" name="yazar" required>
                <label>Kategori</label><input type="text" name="kategori" required>
                <label>Adet</label><input type="number" name="adet" required>
                <button type="submit" class="btn">Ekle</button>
            </div>
        </form>
        <?php if ($hata) echo "<p class='error'>$hata</p>"; ?>
        <?php if ($basari) echo "<p class='success'>$basari</p>"; ?>

        <h3>Kitap Listesi</h3>
        <table>
            <thead><tr><th>Kitap ID</th><th>Kitap Adı</th><th>Yazar</th><th>Kategori</th><th>Adet</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach ($kitaplar as $kitap): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kitap['kitap_id']); ?></td>
                        <td><?php echo htmlspecialchars($kitap['kitap_adi']); ?></td>
                        <td><?php echo htmlspecialchars($kitap['yazar']); ?></td>
                        <td><?php echo htmlspecialchars($kitap['kategori']); ?></td>
                        <td><?php echo htmlspecialchars($kitap['adet']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="kitap_id_sil" value="<?php echo htmlspecialchars($kitap['kitap_id']); ?>">
                                <button type="submit" name="sil" class="btn" style="background-color: #800000; color: #fff;"><i class="bi bi-trash"></i> Sil</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="kitap_id_guncelle" value="<?php echo htmlspecialchars($kitap['kitap_id']); ?>">
                                <input type="text" name="kitap_adi" value="<?php echo htmlspecialchars($kitap['kitap_adi']); ?>" required>
                                <input type="text" name="yazar" value="<?php echo htmlspecialchars($kitap['yazar']); ?>" required>
                                <input type="text" name="kategori" value="<?php echo htmlspecialchars($kitap['kategori']); ?>" required>
                                <input type="number" name="adet" value="<?php echo htmlspecialchars($kitap['adet']); ?>" required>
                                <button type="submit" name="guncelle" class="btn" style="background-color: #4CAF50; color: #fff;"><i class="bi bi-pencil"></i> Güncelle</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>