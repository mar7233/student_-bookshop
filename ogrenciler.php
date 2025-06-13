<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

// Hata ve başarı mesajları için değişkenler
$hata = "";
$basari = "";

try {
    $conn = dbBaglan();

    // Form gönderildiğinde işlem kontrolü
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Öğrenci ekleme işlemi
        if (isset($_POST['islem']) && $_POST['islem'] == 'ekle') {
            $stmt = $conn->prepare("INSERT INTO ogrenciler (ogrenci_id, ad, soyad, bolum, telefon, email) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $_POST['ogrenci_id'], $_POST['ad'], $_POST['soyad'], $_POST['bolum'], $_POST['telefon'], $_POST['email']);
            $stmt->execute();
            $basari = "Öğrenci başarıyla eklendi!";
        }
        // Öğrenci silme işlemi
        elseif (isset($_POST['sil']) && isset($_POST['ogrenci_id_sil'])) {
            $stmt = $conn->prepare("DELETE FROM ogrenciler WHERE ogrenci_id = ?");
            $stmt->bind_param("s", $_POST['ogrenci_id_sil']);
            $stmt->execute();
            $basari = "Öğrenci başarıyla silindi!";
        }
        // Öğrenci güncelleme işlemi
        elseif (isset($_POST['guncelle']) && isset($_POST['ogrenci_id_guncelle'])) {
            $stmt = $conn->prepare("UPDATE ogrenciler SET ad = ?, soyad = ?, bolum = ?, telefon = ?, email = ? WHERE ogrenci_id = ?");
            $stmt->bind_param("ssssss", $_POST['ad'], $_POST['soyad'], $_POST['bolum'], $_POST['telefon'], $_POST['email'], $_POST['ogrenci_id_guncelle']);
            $stmt->execute();
            $basari = "Öğrenci başarıyla güncellendi!";
        }
    }

    // Öğrenci listesini çek
    $result = $conn->query("SELECT * FROM ogrenciler");
    $ogrenciler = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Öğrenci Yönetimi</title>
    <link rel="stylesheet" href="stil.css">
    <!-- Font Awesome ve Bootstrap Icons kütüphanesi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="container">
        <h2>Öğrenci Yönetimi</h2>
        <a href="ana_sayfa.php" class="btn shadow"><i class="bi bi-house"></i> Ana Sayfaya Dön</a>
        <a href="cikis.php" class="btn shadow logout"><i class="bi bi-box-arrow-right"></i> Çıkış Yap</a>

        <h3>Öğrenci Ekle</h3>
        <form method="POST" class="form">
            <input type="hidden" name="islem" value="ekle">
            <div class="form-group">
                <label>Öğrenci ID</label><input type="text" name="ogrenci_id" required>
                <label>Ad</label><input type="text" name="ad" required>
                <label>Soyad</label><input type="text" name="soyad" required>
                <label>Bölüm</label><input type="text" name="bolum" required>
                <label>Telefon</label><input type="text" name="telefon" required>
                <label>E-posta</label><input type="text" name="email" required>
                <button type="submit" class="btn shadow">Ekle</button>
            </div>
        </form>
        <?php if ($hata) echo "<p class='error'>$hata</p>"; ?>
        <?php if ($basari) echo "<p class='success'>$basari</p>"; ?>

        <h3>Öğrenci Listesi</h3>
        <div class="card-list">
        <?php foreach ($ogrenciler as $ogrenci): ?>
            <div class="card horizontal-card">
                <div class="card-header">
                    <i class="fas fa-user-graduate"></i>
                    <span><?php echo htmlspecialchars($ogrenci['ad'] . ' ' . $ogrenci['soyad']); ?></span>
                </div>
                <div class="card-body">
                    <div><b>Bölüm:</b> <?php echo htmlspecialchars($ogrenci['bolum']); ?></div>
                    <div><b>Telefon:</b> <?php echo htmlspecialchars($ogrenci['telefon']); ?></div>
                    <div><b>E-posta:</b> <?php echo htmlspecialchars($ogrenci['email']); ?></div>
                </div>
                <div class="card-actions">
                    <!-- Silme butonu -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="ogrenci_id_sil" value="<?php echo htmlspecialchars($ogrenci['ogrenci_id']); ?>">
                        <button type="submit" name="sil" class="btn shadow logout">
                            <i class="bi bi-trash"></i> Sil
                        </button>
                    </form>
                    <!-- Güncelleme butonu ve alanları -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="ogrenci_id_guncelle" value="<?php echo htmlspecialchars($ogrenci['ogrenci_id']); ?>">
                        <input type="text" name="ad" value="<?php echo htmlspecialchars($ogrenci['ad']); ?>" required style="width:70px;">
                        <input type="text" name="soyad" value="<?php echo htmlspecialchars($ogrenci['soyad']); ?>" required style="width:70px;">
                        <input type="text" name="bolum" value="<?php echo htmlspecialchars($ogrenci['bolum']); ?>" required style="width:90px;">
                        <input type="text" name="telefon" value="<?php echo htmlspecialchars($ogrenci['telefon']); ?>" required style="width:90px;">
                        <input type="text" name="email" value="<?php echo htmlspecialchars($ogrenci['email']); ?>" required style="width:110px;">
                        <button type="submit" name="guncelle" class="btn shadow">
                            <i class="bi bi-pencil"></i> Güncelle
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>