<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullaniciAd = $_POST['kullaniciAd'];
    $parola = $_POST['parola'];

    $baglanti = dbBaglan();
    $sorgu = $baglanti->prepare("SELECT kullanici_id, yetki_turu, parola FROM kullanicilar WHERE kullanici_ad = ?");
    $sorgu->bind_param("s", $kullaniciAd);
    $sorgu->execute();
    $sonuc = $sorgu->get_result();
    $kullanici = $sonuc->fetch_assoc();

    $girisBasarili = false;
    if ($kullanici) {
        if (password_verify($parola, $kullanici['parola'])) {
            $girisBasarili = true;
        } elseif (hash_equals((string)$kullanici['parola'], $parola)) {
            // Eski düz metin parola: doğrulandı, hash'lenmiş haliyle güncelle
            $girisBasarili = true;
            $yeniHash = password_hash($parola, PASSWORD_DEFAULT);
            $guncelle = $baglanti->prepare("UPDATE kullanicilar SET parola = ? WHERE kullanici_id = ?");
            $guncelle->bind_param("ss", $yeniHash, $kullanici['kullanici_id']);
            $guncelle->execute();
            $guncelle->close();
        }
    }

    if ($girisBasarili) {
        session_regenerate_id(true);
        $_SESSION['kullanici_id'] = $kullanici['kullanici_id'];
        $_SESSION['yetki_turu'] = $kullanici['yetki_turu'];
        header("Location: ana_sayfa.php");
        exit();
    } else {
        $hata = "Geçersiz kullanıcı adı veya parola!";
    }
    $sorgu->close();
    dbKapat($baglanti);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
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
            width: 380px;
            max-width: 92vw;
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
            gap: 1.2rem;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
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
        .login-form input[type="text"]:focus,
        .login-form input[type="password"]:focus {
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
                width: 96vw;
                padding: 1.5rem 0.9rem;
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
        <h2><i class="fas fa-sign-in-alt"></i> Giriş Yap</h2>
        <?php if (isset($hata)) echo "<p class='hata'>$hata</p>"; ?>
        <form method="POST" class="login-form" autocomplete="off">
            <input type="text" name="kullaniciAd" placeholder="Kullanıcı Adı" required autofocus>
            <input type="password" name="parola" placeholder="Parola" required>
            <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Giriş Yap</button>
            <a href="kayit.php" class="link"><i class="fas fa-user-plus"></i> Hesap Oluştur</a>
        </form>
    </div>
</body>
</html>