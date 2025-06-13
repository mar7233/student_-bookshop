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
            $stmt = $conn->prepare("INSERT INTO kullanicilar (kullanici_id, kullanici_ad, parola, yetki_turu, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $_POST['kullanici_id'], $_POST['kullanici_ad'], $_POST['parola'], $_POST['yetki_turu'], $_POST['email']);
            $stmt->execute();
            $basari = "Kullanıcı başarıyla eklendi!";
        } elseif (isset($_POST['sil']) && isset($_POST['kullanici_id_sil'])) {
            $stmt = $conn->prepare("DELETE FROM kullanicilar WHERE kullanici_id = ?");
            $stmt->bind_param("s", $_POST['kullanici_id_sil']);
            $stmt->execute();
            $basari = "Kullanıcı başarıyla silindi!";
        } elseif (isset($_POST['guncelle']) && isset($_POST['kullanici_id_guncelle'])) {
            $stmt = $conn->prepare("UPDATE kullanicilar SET kullanici_ad = ?, parola = ?, yetki_turu = ?, email = ? WHERE kullanici_id = ?");
            $stmt->bind_param("sssss", $_POST['kullanici_ad'], $_POST['parola'], $_POST['yetki_turu'], $_POST['email'], $_POST['kullanici_id_guncelle']);
            $stmt->execute();
            $basari = "Kullanıcı başarıyla güncellendi!";
        }
    }

    $result = $conn->query("SELECT * FROM kullanicilar");
    $kullanicilar = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Kullanıcı Yönetimi</title>
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
        <h2>Kullanıcı Yönetimi</h2>
        <a href="ana_sayfa.php" class="btn">Ana Sayfaya Dön <i class="bi bi-house"></i></a>
        <a href="cikis.php" class="btn logout">Çıkış Yap <i class="bi bi-box-arrow-right"></i></a>

        <h3>Kullanıcı Ekle</h3>
        <form method="POST" class="form">
            <input type="hidden" name="islem" value="ekle">
            <div class="form-group">
                <label>Kullanıcı ID</label><input type="text" name="kullanici_id" required>
                <label>Kullanıcı Adı</label><input type="text" name="kullanici_ad" required>
                <label>Parola</label><input type="text" name="parola" required>
                <label>Yetki Türü</label><select name="yetki_turu" required>
                    <option value="Admin">Admin</option>
                    <option value="Gorevli">Görevli</option>
                </select>
                <label>E-posta</label><input type="text" name="email" required>
                <button type="submit" class="btn">Ekle</button>
            </div>
        </form>
        <?php if ($hata) echo "<p class='error'>$hata</p>"; ?>
        <?php if ($basari) echo "<p class='success'>$basari</p>"; ?>

        <h3>Kullanıcı Listesi</h3>
        <table>
            <thead><tr><th>Kullanıcı ID</th><th>Kullanıcı Adı</th><th>Parola</th><th>Yetki Türü</th><th>E-posta</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach ($kullanicilar as $kullanici): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kullanici['kullanici_id']); ?></td>
                        <td><?php echo htmlspecialchars($kullanici['kullanici_ad']); ?></td>
                        <td><?php echo htmlspecialchars($kullanici['parola']); ?></td>
                        <td><?php echo htmlspecialchars($kullanici['yetki_turu']); ?></td>
                        <td><?php echo htmlspecialchars($kullanici['email']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="kullanici_id_sil" value="<?php echo htmlspecialchars($kullanici['kullanici_id']); ?>">
                                <button type="submit" name="sil" class="btn" style="background-color: #800000; color: #fff;"><i class="bi bi-trash"></i> Sil</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="kullanici_id_guncelle" value="<?php echo htmlspecialchars($kullanici['kullanici_id']); ?>">
                                <input type="text" name="kullanici_ad" value="<?php echo htmlspecialchars($kullanici['kullanici_ad']); ?>" required>
                                <input type="text" name="parola" value="<?php echo htmlspecialchars($kullanici['parola']); ?>" required>
                                <select name="yetki_turu" required>
                                    <option value="Admin" <?php echo $kullanici['yetki_turu'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="Gorevli" <?php echo $kullanici['yetki_turu'] == 'Gorevli' ? 'selected' : ''; ?>>Görevli</option>
                                </select>
                                <input type="text" name="email" value="<?php echo htmlspecialchars($kullanici['email']); ?>" required>
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