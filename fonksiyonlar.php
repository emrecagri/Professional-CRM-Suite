<?php

function aciliyet()
{
	return [
		0 => 'Acil',
		1 => 'Normal',
		2 => 'Acelesi Yok',
	];
}

function durum()
{
	return [
		0 => 'Yeni Başladı',
		1 => 'Devam Ediyor',
		2 => 'Bitti',
	];
}

function zamanFarki($tarih) {
    if (!strtotime($tarih)) {
        return 'Geçersiz tarih-saat formatı';
    }

	date_default_timezone_set('Europe/Istanbul'); // GMT+3 olarak ayarla
    $simdi = time();
    $tarih = strtotime($tarih);
    $fark = $simdi - $tarih;
    
    if ($fark < 60) {
        return 'şimdi';
    } elseif ($fark < 3600) {
        $dakika = round($fark / 60);
        return $dakika . ' dakika önce';
    } elseif ($fark < 86400) {
        $saat = round($fark / 3600);
        return $saat . ' saat önce';
    } elseif ($fark < 2592000) {
        $gun = round($fark / 86400);
        return $gun . ' gün önce';
    } elseif ($fark < 31536000) {
        $ay = round($fark / 2592000);
        return $ay . ' ay önce';
    } else {
        $yil = round($fark / 31536000);
        return $yil . ' yıl önce';
    }

	// kullanım örneği 1
	// $tarihFormati = '2024-04-09 16:05:44';
	// echo zamanFarki($tarihFormati);

	// kullanım örneği 2
	// mysql giris_tarihi sütununun formatı datatime olmalıdır.
	// echo zamanFarki($logkayitverileri['giris_tarihi']);


}

function turkce_temizle($metin) {
	$turkce=array("ş","Ş","ı","ü","Ü","ö","Ö","ç","Ç","ş","Ş","ı","ğ","Ğ","İ","ö","Ö","Ç","ç","ü","Ü");
	$duzgun=array("s","S","i","u","U","o","O","c","C","s","S","i","g","G","I","o","O","C","c","u","U");
	$metin=str_replace($turkce,$duzgun,$metin);
	$metin = preg_replace("@[^a-z0-9\-_şıüğçİŞĞÜÇ]+@i","-",$metin);
	$yeniisim = mb_strtolower($metin, 'utf8');
	return $yeniisim;
};


function tum_bosluk_sil($veri)
{
	return str_replace(" ", "", $veri); 
};

function yetkikontrol() {
	if (empty($_SESSION['session_diyetisyen_mail'])) {
		$diyetisyen_mail="x";
	} else {
		$diyetisyen_mail=$_SESSION['session_diyetisyen_mail'];
	}
	
	include 'islemler/baglan.php';
	$yetki=$db->prepare("SELECT diyetisyen_yetki FROM kullanicilar where session_diyetisyen_mail=:session_diyetisyen_mail");
	$yetki->execute(array(
		'session_diyetisyen_mail' => $diyetisyen_mail
	));
	$yetkicek=$yetki->fetch(PDO::FETCH_ASSOC);

	if ($yetkicek['diyetisyen_yetki']==1) {
		$sonuc="yetkili";
		return $sonuc;
	} else {
		$sonuc="yetkisiz";
		return $sonuc;
	}
};

function oturumkontrol() {
	//her oturum açışta sifremele fonksiyonu ile mail adresini taban alarak zamanı da katarak bir benzersiz kod oluşturup bu kodu sessiona atıyoruz ki her girişte benzersiz bir kod ile kullanıcı hesabı tanımlanıyor böylece güvenlik artıyor.
	include 'islemler/baglan.php';
	if (empty($_SESSION['session_diyetisyen_mail']) or empty($_SESSION['kullanici_id'])) {
		header("location:giris.php?durum=501874");
		exit;
	} else {

		$kullanici=$db->prepare("SELECT * FROM kullanicilar where session_diyetisyen_mail=:session_diyetisyen_mail");
		$kullanici->execute(array(
			'session_diyetisyen_mail' => $_SESSION['session_diyetisyen_mail']
		));

		$say=$kullanici->rowcount();
		$kullanicicek=$kullanici->fetch(PDO::FETCH_ASSOC);
		if ($say==0) {
			header("location:giris.php?durum=874523");
			exit;
		}
	}	
};

function guvenlik($gelen){
	$giden = addslashes($gelen); // Mümkünse kaldırın
    // Türkçe karakterleri &ccedil; yapmaktan kaçınmak için
    // sadece temel XSS koruması olan htmlspecialchars() kullanın
	$giden = htmlspecialchars($giden); 
    // htmlspecialchars zaten yapıldığı için strip_tags() sona alınabilir.
	$giden = strip_tags($giden);
    
    // htmlentities() artık YOK, böylece &ccedil; oluşmaz.
    // addslashes() ile ilgili endişeleriniz varsa onu da kaldırın.
	
	return $giden;
};

function sifreleme($diyetisyen_mail) {
	$gizlianahtar = '05a8acd63ecadfc55842804bc537f76e';
	return md5(sha1(md5($_SERVER['REMOTE_ADDR'] . $gizlianahtar . $diyetisyen_mail . "Loruv" . date('d.m.Y H:i:s') . $_SERVER['HTTP_USER_AGENT'])));
};

?>
