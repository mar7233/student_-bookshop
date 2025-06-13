<?php
include 'db_connect.php';
$baglanti = dbBaglan();

// مثال بسيط لجلب التقارير
$sorgu = $baglanti->prepare("SELECT * FROM kiralamalar");
$sorgu->execute();
$raporlar = $sorgu->get_result()->fetch_all(MYSQLI_ASSOC);
$sorgu->close();
dbKapat($baglanti);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlar</title>
    <link rel="stylesheet" href="stil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container {
            margin: 40px auto 25px;
            background: rgba(32,32,36,0.97);
            max-width: 700px;
            border-radius: 22px;
            box-shadow: 0 8px 32px #0007;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .container h2 {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            justify-content: center;
            margin-bottom: 1.6rem;
            color: #ffe082;
        }
        #raporListesi ul {
            list-style: none;
            padding: 0;
        }
        #raporListesi li {
            background: linear-gradient(90deg, #35343c 0%, #23243a 100%);
            color: #ffe082;
            margin-bottom: 0.75em;
            padding: 1em 1.2em;
            border-radius: 12px;
            font-size: 1.08em;
            box-shadow: 0 2px 8px #0002;
            text-align: left;
            transition: background 0.17s, color 0.16s;
        }
        #raporListesi li:hover {
            background: linear-gradient(90deg, #ffe082 0%, #23243a 100%);
            color: #23243a;
        }
        .navbar {
            background: #18181a;
            padding: 0.7rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px #0006;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .navbar .logo {
            color: #fff;
            font-size: 1.7rem;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.6em;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
            margin: 0;
        }
        .nav-links li a {
            color: #f0f0f0;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1.1rem;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.4em;
            font-size: 1rem;
        }
        .nav-links li a:hover, .nav-links li a.active {
            background: #333;
            color: #ffe082;
        }
        @media (max-width: 700px) {
            .container { padding: 1rem; }
            .nav-links { gap: 0.3rem; flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-book-open"></i> Öğrenci Kütüphanesi</div>
        <ul class="nav-links">
            <li><a href="ana_sayfa.php"><i class="fas fa-home"></i> Ana Sayfa</a></li>
            <li><a href="cikis.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2><i class="fas fa-chart-bar"></i> Raporlar</h2>
        <div id="raporListesi">
            <?php if (count($raporlar) > 0): ?>
            <ul>
                <?php foreach ($raporlar as $rapor) { ?>
                    <li>
                        <b>Kiralama ID:</b> <?php echo htmlspecialchars($rapor['kiralama_id']); ?>
                        <br>
                        <b>Öğrenci ID:</b> <?php echo htmlspecialchars($rapor['ogrenci_id']); ?>
                        <br>
                        <b>Kitap ID:</b> <?php echo htmlspecialchars($rapor['kitap_id']); ?>
                        <br>
                        <b>Kullanıcı ID:</b> <?php echo htmlspecialchars($rapor['kullanici_id']); ?>
                        <br>
                        <b>Kiralama Tarihi:</b> <?php echo htmlspecialchars($rapor['kiralama_tarih']); ?>
                        <br>
                        <b>İade Tarihi:</b> <?php echo htmlspecialchars($rapor['iade_tarih']); ?>
                        <br>
                        <b>Gecikti mi?:</b> <?php echo htmlspecialchars($rapor['gecikti_mi']); ?>
                    </li>
                <?php } ?>
            </ul>
            <?php else: ?>
                <p style="color:#ffe082;">Kayıtlı rapor bulunamadı.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>