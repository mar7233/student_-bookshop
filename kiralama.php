<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

$hata = "";
$basari = "";
$kiralamalar = [];

try {
    $conn = dbBaglan();

    // Kiralama ekle POST işlemi
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['islem']) && $_POST['islem'] === 'kiralama_ekle') {
        $kiralama_id = uniqid();
        $ogrenci_id = $_POST['ogrenci_id'];
        $kitap_id = $_POST['kitap_id'];
        $kullanici_id = $_SESSION['kullanici_id'];
        $kiralama_tarih = date('Y-m-d H:i:s');
        $iade_tarih = $_POST['iade_tarih'];
        $gecikti_mi = 'hayir';
        $gecikme_ucreti = 0.00;
        $iade_edildi_mi = 'hayir';
        $iade_edilis_tarihi = null;

        // Kitap stok kontrolü
        $stmt = $conn->prepare("SELECT adet FROM kitaplar WHERE kitap_id = ?");
        $stmt->bind_param("s", $kitap_id);
        $stmt->execute();
        $stmt->bind_result($adet);
        $stmt->fetch();
        $stmt->close();

        if ($adet > 0) {
            $stmt = $conn->prepare("INSERT INTO kiralamalar (kiralama_id, ogrenci_id, kitap_id, kullanici_id, kiralama_tarih, iade_tarih, gecikti_mi, gecikme_ucreti, iade_edildi_mi, iade_edilis_tarihi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssdds", $kiralama_id, $ogrenci_id, $kitap_id, $kullanici_id, $kiralama_tarih, $iade_tarih, $gecikti_mi, $gecikme_ucreti, $iade_edildi_mi, $iade_edilis_tarihi);
            $stmt->execute();
            $stmt->close();

            // Kitap adedini azalt
            $stmt = $conn->prepare("UPDATE kitaplar SET adet = adet - 1 WHERE kitap_id = ?");
            $stmt->bind_param("s", $kitap_id);
            $stmt->execute();
            $stmt->close();

            $basari = "Kiralama kaydı başarıyla eklendi.";
        } else {
            $hata = "Bu kitap stokta yok!";
        }
    }

    // Kiralama kayıtlarını çek
    $kiralamalar = [];
    $result = $conn->query("SELECT * FROM kiralamalar ORDER BY kiralama_tarih DESC");
    if ($result) {
        $kiralamalar = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
    }

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
    <title>Öğrenci Kitap Kiralama</title>
    <style>
        body { background: #23232b; color: #fff; font-family: Arial; }
        .container { max-width: 900px; margin: 40px auto; background: #202024; padding: 30px; border-radius: 8px; }
        h2, h3 { color: #ffd700; }
        label { color: #ffd700; margin-left: 8px;}
        .btn { background: #ffd700; color: #23232b; border: none; padding: 7px 18px; border-radius: 3px; cursor:pointer; margin: 3px;}
        .btn.logout { background: #800000; color: #fff;}
        .error { color: #e74c3c; padding: 7px 0; }
        .success { color: #2ecc71; padding: 7px 0; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse;}
        th, td { padding: 10px; border-bottom: 1px solid #444; }
        th { background: #2e2e38; color: #ffd700;}
    </style>
</head>
<body>
<div class="container">
    <h2>Öğrenci Kitap Kiralama</h2>
    <a href="ana_sayfa.php" class="btn">Ana Sayfa</a>
    <a href="cikis.php" class="btn logout">Çıkış Yap</a>
    <h3>Kitap Kirala</h3>
    <form method="POST">
        <input type="hidden" name="islem" value="kiralama_ekle">
        <label>Öğrenci ID:</label>
        <input type="text" name="ogrenci_id" required>
        <label>Kitap ID:</label>
        <input type="text" name="kitap_id" required>
        <label>İade Tarihi:</label>
        <input type="datetime-local" name="iade_tarih" required>
        <button type="submit" class="btn">Kirala</button>
    </form>
    <?php if($hata): ?><div class="error"><?= $hata ?></div><?php endif; ?>
    <?php if($basari): ?><div class="success"><?= $basari ?></div><?php endif; ?>

    <h3>Kiralama Listesi</h3>
    <table>
        <thead>
            <tr>
                <th>Kiralama ID</th>
                <th>Öğrenci ID</th>
                <th>Kitap ID</th>
                <th>Kullanıcı ID</th>
                <th>Kiralama Tarihi</th>
                <th>İade Tarihi</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($kiralamalar as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['kiralama_id']) ?></td>
                <td><?= htmlspecialchars($row['ogrenci_id']) ?></td>
                <td><?= htmlspecialchars($row['kitap_id']) ?></td>
                <td><?= htmlspecialchars($row['kullanici_id']) ?></td>
                <td><?= htmlspecialchars($row['kiralama_tarih']) ?></td>
                <td><?= htmlspecialchars($row['iade_tarih']) ?></td>
                <td>
                    <?php
                    if($row['iade_edildi_mi'] === 'evet') echo "<span style='color:#2ecc71;'>İade Edildi</span>";
                    elseif(strtotime($row['iade_tarih']) < time()) echo "<span style='color:#e74c3c;'>Gecikti</span>";
                    else echo "<span style='color:#ffd700;'>Aktif</span>";
                    ?>
                </td>
            </tr>
        <?php endforeach;
        if(count($kiralamalar) === 0): ?>
            <tr><td colspan="7">Kiralama kaydı bulunamadı.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>