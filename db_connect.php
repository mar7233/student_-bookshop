<?php
function dbBaglan() {
    $host = 'localhost';
    $kullanici = 'root';
    $parola = '';
    $veritabani = 'ogrenci_kitap';

    $baglanti = new mysqli($host, $kullanici, $parola, $veritabani);

    if ($baglanti->connect_error) {
        die("Bağlantı hatası: " . $baglanti->connect_error);
    }

    $baglanti->set_charset("utf8mb4");
    return $baglanti;
}

function dbKapat($baglanti) {
    $baglanti->close();
}
?>