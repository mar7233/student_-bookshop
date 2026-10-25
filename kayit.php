<?php
session_start();
include 'db_connect.php';

$hata = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullaniciId = trim($_POST['kullaniciId']);
    $kullaniciAd = trim($_POST['kullaniciAd']);
    $parola = $_POST['parola'];
    $yetki = $_POST['yetkiTuru'];
    $email = trim($_POST['kullaniciEmail']);

    // Parola veritabanına hash'lenmiş olarak kaydedilir
    $parolaHash = password_hash($parola, PASSWORD_DEFAULT);

    $baglanti = dbBaglan();
    // Kayıtlı kullanıcı adı ya da e-posta var mı kontrolü
    $kontrol = $baglanti->prepare("SELECT kullanici_id FROM kullanicilar WHERE kullanici_ad = ? OR email = ?");
    $kontrol->bind_param("ss", $kullaniciAd, $email);
    $kontrol->execute();
    $kontrol->store_result();
    if ($kontrol->num_rows > 0) {
        $hata = "Bu kullanıcı adı veya e-posta zaten kayıtlı!";
    } else {
        $kontrol->close();
        $sorgu = $baglanti->prepare("INSERT INTO kullanicilar (kullanici_id, kullanici_ad, parola, yetki_turu, email) VALUES (?, ?, ?, ?, ?)");
        $sorgu->bind_param("sssss", $kullaniciId, $kullaniciAd, $parolaHash, $yetki, $email);
        $sorgu->execute();
        $sorgu->close();
        dbKapat($baglanti);
        header("Location: giris.php");
        exit();
    }
    $kontrol->close();
    dbKapat($baglanti);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Oluştur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #2d3250 0%, #37306b 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: #18181a;
            padding: 1.1rem 2.5rem 1rem 2.5rem;
            box-shadow: 0 2px 12px #0005;
            display: flex;
            align-items: center;
        }
        .navbar .logo {
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 0.6em;
        }
        .login-container {
            background: rgba(44, 48, 80, 0.92);
            border-radius: 18px;
            box-shadow: 0 8px 28px #0008;
            width: 390px;
            max-width: 96vw;
            margin: 64px auto 0;
            padding: 2.5rem 2rem 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-container h2 {
            color: #ffe082;
            margin-bottom: 1.6rem;
            font-size: 1.7rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.7em;
        }
        .login-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }
        .login-form input,
        .login-form select {
            padding: 0.9rem 1rem;
            border-radius: 9px;
            border: 1.5px solid #6e85b7;
            background: #23243a;
            color: #fff;
            font-size: 1.07rem;
            transition: border-color 0.18s, box-shadow 0.19s;
            outline: none;
            font-family: inherit;
        }
        .login-form input:focus,
        .login-form select:focus {
            border-color: #ffe082;
            box-shadow: 0 0 0 2px #ffe08244;
        }
        .login-form .btn {
            background: linear-gradient(90deg, #7c73e6 0%, #5c5470 100%);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 0.85rem 0;
            font-size: 1.15rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.16s, color 0.18s, transform 0.14s;
            box-shadow: 0 4px 16px #0005;
            margin-top: 0.5rem;
        }
        .login-form .btn:hover {
            background: linear-gradient(90deg, #ffe082 0%, #7c73e6 100%);
            color: #2d3250;
            transform: translateY(-2px) scale(1.035);
        }
        .login-form .link {
            display: block;
            margin-top: 0.5rem;
            color: #ffe082;
            text-align: right;
            text-decoration: none;
            font-size: 0.98rem;
            transition: color 0.17s;
        }
        .login-form .link:hover {
            color: #ffb300;
            text-decoration: underline;
        }
        .hata {
            color: #fff;
            background: #c0392b;
            padding: 0.8em 1.1em;
            border-radius: 7px;
            margin-bottom: 1.2rem;
            font-size: 1.04em;
            box-shadow: 0 2px 10px #0003;
            text-align: center;
        }
        @media (max-width: 600px) {
            .login-container {
                width: 98vw;
                padding: 1.4rem 0.7rem;
            }
            .navbar {
                padding: 1rem 1rem 0.9rem 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-book-open"></i> Öğrenci Kütüphanesi</div>
    </nav>
    <div class="login-container">
        <h2><i class="fas fa-user-plus"></i> Hesap Oluştur</h2>
        <?php if (!empty($hata)) echo "<p class='hata'>$hata</p>"; ?>
        <form method="POST" class="login-form" autocomplete="off">
            <input type="text" name="kullaniciId" placeholder="Kullanıcı ID" required>
            <input type="text" name="kullaniciAd" placeholder="Kullanıcı Adı" required>
            <input type="password" name="parola" placeholder="Parola" required>
            <select name="yetkiTuru" required>
                <option value="">Yetki Türü Seçiniz</option>
                <option value="Admin">Admin</option>
                <option value="Gorevli">Görevli</option>
            </select>
            <input type="email" name="kullaniciEmail" placeholder="E-posta" required>
            <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Hesap Oluştur</button>
            <a href="giris.php" class="link"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
        </form>
    </div>
</body>
</html>