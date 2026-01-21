<?php 

function logtutucu($islem_yada_ziyaret, $olay_bilgisi, $hata_yada_basarili) {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include 'baglan.php';

    // Diyetisyen ID'sini oturum bilgisinden alma
    $kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : 0;

    try {
        // IP adresini alıyoruz
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // İstek yapılan sayfanın adresini alıyoruz
        $ic_adres = $_SERVER['REQUEST_URI'];

        // Referans yapılan sayfanın domain adresini alıyoruz
        if (isset($_SERVER['HTTP_REFERER'])) {
            preg_match('@^(?:http://)?([^/]+)@i', $_SERVER['HTTP_REFERER'], $matches);
            $dis_adres = $matches[1];
        } else {
            $dis_adres = 'direct';
        }

        // Pc adı alıyoruz
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $pcadi = gethostbyaddr($_SERVER['HTTP_CLIENT_IP']);
        } else {
            $pcadi = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        }

        $tarayici = @$_SERVER['HTTP_USER_AGENT'];
        $tarayicidili = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];

        // Veritabanına yeni ziyaretçi kaydı ekliyoruz
        $sql = "INSERT INTO logkayitlari (olay_bilgisi, islem_yada_ziyaret, hata_yada_basarili, kullanici_id, dis_adres, ip, ic_adres, giris_tarihi, pcadi, tarayici, tarayicidili, gercekip) VALUES (:olay_bilgisi, :islem_yada_ziyaret, :hata_yada_basarili, :kullanici_id, :dis_adres, :ip, :ic_adres, NOW(), :pcadi, :tarayici, :tarayicidili, :gercekip)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(
            ':olay_bilgisi' => $olay_bilgisi,
            ':islem_yada_ziyaret' => $islem_yada_ziyaret, //0 ise görüntülenme işlem 1 ise işlem
            ':hata_yada_basarili' => $hata_yada_basarili, //0 ise başarılı işlem 1 ise hatalı işlem
            ':kullanici_id' => $kullanici_id,
            ':dis_adres' => $dis_adres,
            ':ip' => $ip,
            ':ic_adres' => $ic_adres,
            ':pcadi' => $pcadi,
            ':tarayici' => $tarayici,
            ':tarayicidili' => $tarayicidili,
            ':gercekip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null // HTTP_X_FORWARDED_FOR belirtilmemişse null atanacak
        ));
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }

}

// Başka sayfada fonksiyonu kullanarak loglama işlemi örneği
// include 'islemler/logtutucu.php';
// logtutucu(1, "Profil bilgileri güncellendi.", 0);
// yada
// $degiskenli_log_mesaji = $_POST['danisan_id'] . " nolu danışan kaydı güncellendi.";
// logtutucu(1, $degiskenli_log_mesaji, 0);


?>
