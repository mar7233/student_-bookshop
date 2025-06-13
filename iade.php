<?php
include 'db_connect.php';
$baglanti = dbBaglan();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'iade') {
    $sorgu = $baglanti->prepare("UPDATE kiralamalar SET iade_tarih = NOW() WHERE kiralama_id = ?");
    $sorgu->bind_param("s", $_POST['kiralamaId']);
    $sorgu->execute();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İade Yönetimi</title>
    <link rel="stylesheet" href="stil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Öğrenci Kütüphanesi</div>
        <ul class="nav-links">
            <li><a href="ana_sayfa.php"><i class="fas fa-home"></i> Ana Sayfa</a></li>
            <li><a href="cikis.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2><i class="fas fa-undo"></i> İade Yönetimi</h2>
        <button onclick="iadeYapFormuGoster()" class="btn">İade Yap</button>
        <div id="iadeFormu" class="form-container" style="display:none;">
            <input type="text" id="iadeKiralamaId" placeholder="Kiralama ID">
            <button onclick="iadeYap()" class="btn">İade Et</button>
            <button onclick="this.parentElement.style.display='none'" class="btn cancel">İptal</button>
        </div>
        <div id="iadeListesi"></div>
    </div>
    <script src="script.js"></script>
</body>
</html>

<?php dbKapat($baglanti); ?>