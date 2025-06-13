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

    // --- تحديث الغرامات تلقائياً مع مراعاة الإعادة ---
    $now = date('Y-m-d H:i:s');
    $kiralama_cek = $conn->query("SELECT kiralama_id, iade_tarih, iade_edildi_mi, iade_edilis_tarihi FROM kiralamalar WHERE iade_tarih < '$now'");
    if ($kiralama_cek) {
        while ($kir = $kiralama_cek->fetch_assoc()) {
            $kiralama_id = $kir['kiralama_id'];
            $iade_tarih = $kir['iade_tarih'];
            $iade_edildi_mi = $kir['iade_edildi_mi'];
            $iade_edilis_tarihi = $kir['iade_edilis_tarihi'];
            $son_kontrol = ($iade_edildi_mi == 'evet' && !empty($iade_edilis_tarihi)) ? $iade_edilis_tarihi : $now;
            $gecikme_gun = (int) ((strtotime($son_kontrol) - strtotime($iade_tarih)) / 86400);
            if ($gecikme_gun > 0) {
                $ceza_ucreti = $gecikme_gun * 0.5;
                $stmt_gecikme = $conn->prepare("SELECT gecikme_id FROM gecikmeler WHERE kiralama_id = ?");
                $stmt_gecikme->bind_param("s", $kiralama_id);
                $stmt_gecikme->execute();
                $stmt_gecikme->store_result();
                if ($stmt_gecikme->num_rows > 0) {
                    $stmt_gecikme->close();
                    $stmt_update = $conn->prepare("UPDATE gecikmeler SET gecikme_suresi = ?, ceza_ucreti = ? WHERE kiralama_id = ?");
                    $stmt_update->bind_param("ids", $gecikme_gun, $ceza_ucreti, $kiralama_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                } else {
                    $stmt_gecikme->close();
                    $stmt_insert = $conn->prepare("INSERT INTO gecikmeler (kiralama_id, gecikme_suresi, ceza_ucreti) VALUES (?, ?, ?)");
                    $stmt_insert->bind_param("sid", $kiralama_id, $gecikme_gun, $ceza_ucreti);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
            }
        }
        $kiralama_cek->free();
    }
    // --- نهاية الغرامة التلقائية ---

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['sil']) && isset($_POST['gecikme_id_sil'])) {
            $stmt = $conn->prepare("DELETE FROM gecikmeler WHERE gecikme_id = ?");
            $stmt->bind_param("s", $_POST['gecikme_id_sil']);
            $stmt->execute();
            $basari = "Gecikme başarıyla silindi!";
        } elseif (isset($_POST['guncelle']) && isset($_POST['gecikme_id_guncelle'])) {
            $stmt = $conn->prepare("UPDATE gecikmeler SET ceza_ucreti = ? WHERE gecikme_id = ?");
            $stmt->bind_param("ds", $_POST['ceza_ucreti'], $_POST['gecikme_id_guncelle']);
            $stmt->execute();
            $basari = "Gecikme başarıyla güncellendi!";
        }
    }

    $result = $conn->query("CALL gecikmeler_hepsi()");
    $gecikmeler = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Gecikme Yönetimi</title>
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
        <h2>Gecikme Yönetimi</h2>
        <a href="ana_sayfa.php" class="btn">Ana Sayfaya Dön <i class="bi bi-house"></i></a>
        <a href="cikis.php" class="btn logout">Çıkış Yap <i class="bi bi-box-arrow-right"></i></a>

        <h3>Gecikme Listesi</h3>
        <table>
            <thead><tr><th>Gecikme ID</th><th>Kiralama ID</th><th>Gecikme Süresi</th><th>Ceza Ücreti</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach ($gecikmeler as $gecikme): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($gecikme['gecikme_id']); ?></td>
                        <td><?php echo htmlspecialchars($gecikme['kiralama_id']); ?></td>
                        <td><?php echo htmlspecialchars($gecikme['gecikme_suresi']); ?> gün</td>
                        <td><?php echo htmlspecialchars($gecikme['ceza_ucreti']); ?> TL</td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="gecikme_id_sil" value="<?php echo htmlspecialchars($gecikme['gecikme_id']); ?>">
                                <button type="submit" name="sil" class="btn" style="background-color: #800000; color: #fff;"><i class="bi bi-trash"></i> Sil</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="gecikme_id_guncelle" value="<?php echo htmlspecialchars($gecikme['gecikme_id']); ?>">
                                <input type="number" name="ceza_ucreti" value="<?php echo htmlspecialchars($gecikme['ceza_ucreti']); ?>" step="0.01" required>
                                <button type="submit" name="guncelle" class="btn" style="background-color: #4CAF50; color: #fff;"><i class="bi bi-pencil"></i> Güncelle</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($hata) echo "<p class='error'>$hata</p>"; ?>
        <?php if ($basari) echo "<p class='success'>$basari</p>"; ?>
    </div>
</body>
</html>