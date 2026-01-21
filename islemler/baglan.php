<?php 
$host="localhost"; //Host adınızı girin varsayılan olarak Localhosttur eğer bilginiz yoksa bu şekilde bırakın
$veritabani_ismi="danisan-yonetim-sistemi"; //Veritabanı İsminiz
$kullanici_adi="root"; //Veritabanı kullanıcı adınız
$sifre=""; //Kullanıcı şifreniz şifre yoksa 123456789 yazan yeri silip boş bırakın

try {
	$db=new PDO("mysql:host=$host;dbname=$veritabani_ismi;charset=utf8",$kullanici_adi,$sifre);
	//echo "veritabanı bağlantısı başarılı";
}

catch (PDOExpception $e) {
	echo $e->getMessage();
}


$api_key="05a8acd63ecadfc55842804bc537f76e";




?>
