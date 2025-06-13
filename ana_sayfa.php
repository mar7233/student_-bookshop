<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <!-- Harici ikon kütüphanelerini kullanmak için -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    /* Ana Sayfa için stil */
    body {
        font-family: 'Segoe UI', 'Arial', sans-serif;
        background: linear-gradient(125deg, #23243a 20%, #35343c 100%);
        color: #fff;
        min-height: 100vh;
        margin: 0;
        padding: 0;
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
        padding: 0;
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
    .dashboard {
        display: flex;
        gap: 2rem;
        margin-top: 2rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    .dashboard-item {
        background: linear-gradient(135deg, #393969 60%, #23213c 100%);
        color: #ffe082;
        padding: 1.5rem 2.3rem;
        border-radius: 1.2rem;
        font-size: 1.15rem;
        font-weight: bold;
        text-decoration: none;
        box-shadow: 0 6px 18px #0004;
        transition: background 0.17s, transform 0.15s, color 0.12s;
        min-width: 180px;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        border: 1.5px solid #555;
        position: relative;
        overflow: hidden;
        justify-content: center;
    }
    .dashboard-item i {
        font-size: 2.2rem;
        margin-bottom: 0.2rem;
        z-index: 1;
    }
    .dashboard-item:hover {
        background: linear-gradient(135deg, #23213c 60%, #393969 100%);
        color: #fff;
        transform: scale(1.045);
        border-color: #ffe082;
    }
    .btn, .btn.shadow, .btn.logout {
        background: linear-gradient(90deg, #a61c1c 0%, #2c2222 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 11px 32px;
        font-size: 1.1rem;
        margin: 2px;
        cursor: pointer;
        box-shadow: 0 4px 16px #0006, 0 1.5px 0 #ffe08233 inset;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.4em;
        transition: background 0.17s, box-shadow 0.17s, color 0.19s;
        text-decoration: none;
    }
    .btn:hover, .btn.shadow:hover, .btn.logout:hover {
        background: linear-gradient(90deg, #ffe082 0%, #a61c1c 100%);
        color: #a61c1c;
    }
    @media (max-width: 700px) {
        .container { padding: 1rem; }
        .dashboard { flex-direction: column; gap: 1rem; }
        .nav-links { gap: 0.3rem; flex-wrap: wrap; }
    }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-book-open"></i> Öğrenci Kütüphanesi</div>
        <ul class="nav-links">
            <li><a href="ogrenciler.php"><i class="fas fa-users"></i> Öğrenciler</a></li>
            <li><a href="kitaplar.php"><i class="fas fa-book"></i> Kitaplar</a></li>
            <li><a href="kiralama.php"><i class="fas fa-exchange-alt"></i> Kiralamalar</a></li>
            <li><a href="gecikmeler.php"><i class="fas fa-exclamation-triangle"></i> Gecikmeler</a></li>
            <li><a href="kullanicilar.php"><i class="fas fa-user-shield"></i> Kullanıcılar</a></li>
            <li><a href="raporlar.php"><i class="fas fa-chart-bar"></i> Raporlar</a></li>
            <li><a href="cikis.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a></li>
        </ul>
    </nav>
    <div class="container">
        <h2><i class="fas fa-home"></i> Ana Sayfa</h2>
        <p style="margin-bottom:2.2rem;">Hoş geldiniz, <b><?php echo $_SESSION['kullanici_id']; ?></b>!</p>
        <div class="dashboard">
            <a href="ogrenciler.php" class="dashboard-item">
                <i class="fas fa-users"></i> Öğrenci Yönetimi
            </a>
            <a href="kitaplar.php" class="dashboard-item">
                <i class="fas fa-book"></i> Kitap Yönetimi
            </a>
            <a href="kiralama.php" class="dashboard-item">
                <i class="fas fa-exchange-alt"></i> Kiralama Yönetimi
            </a>
        </div>
        <div style="margin-top:2.5rem;text-align:center;">
            <a href="cikis.php" class="btn shadow logout" style="font-size:1.08rem;">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
        </div>
    </div>
</body>
</html>