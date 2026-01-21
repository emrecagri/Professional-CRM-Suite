<?php

//hataları gösterek 2 satırlık kod parçası
error_reporting(E_ALL);
ini_set('display_errors', 1);

@ob_start();
@session_start();
include 'baglan.php';
include '../fonksiyonlar.php'; // 'guvenlik', 'sifreleme' (artık kullanılmayacak), 'tum_bosluk_sil' ve 'yetkikontrol' burada varsayılıyor
include 'logtutucu.php';

// Veritabanı bağlantısı $db 'baglan.php' dosyasından gelmeli.
// Hata modunu ayarlamak en iyi uygulamadır
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//Site ayarlarını veritabanından çekme işlemi
try {
	$ayarsor=$db->prepare("SELECT * FROM ayarlar");
	$ayarsor->execute();
	$ayarcek=$ayarsor->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	// Ayarlar çekilemezse kritik hata.
	die("Sistem ayarları yüklenemedi: " . $e->getMessage());
}



if (isset($_GET['api_key'])) {
	// API KEY GÜVENLİK UYARISI: API anahtarını GET ile göndermek güvensizdir. 
	// HTTP Header (Authorization: Bearer <key>) olarak göndermeniz şiddetle tavsiye edilir.
	if ($_GET['api_key']==$api_key) { // $api_key 'baglan.php' içinde tanımlı varsayılıyor
		$api=true;
	} else {
		echo json_encode(['durum' => 'no', 'mesaj' => "API Bilgileriniz Hatalıdır"]);
		$api=false;
		exit;
	}
} else {
	$api=false;
}


/********************************************************************************/
/* YARDIMCI FONKSİYONLAR */
/********************************************************************************/

/**
 * API'dan gelen 'ORDER BY' sütun adlarını güvenli bir şekilde doğrular.
 * SQL Injection saldırılarını önler.
 * @param string $column_name - Kullanıcıdan gelen sütun adı.
 * @param array $allowed_columns - İzin verilen sütun adlarının bir dizisi.
 * @return string - Güvenli sütun adı veya varsayılan (örn: 'id').
 */
function validateSortColumn($column_name, $allowed_columns) {
    if (in_array(strtolower($column_name), $allowed_columns)) {
        return $column_name; // Sütun adı beyaz listede var, güvenli.
    }
    return $allowed_columns[0]; // Varsayılan güvenli sütun (genellikle ilk sütun, örn: id)
}


/**
 * Dosya adını güvenli ve URL dostu hale getirir (slugify).
 * @param string $text
 * @return string
 */
function slugify($text) {
	$text = str_replace(
		['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'],
		['i', 'g', 'u', 's', 'o', 'c', 'I', 'G', 'U', 'S', 'O', 'C'],
		$text
	);
	$text = strtolower(trim($text));
	$text = preg_replace('/[^a-z0-9\-\.]/', '-', $text);
	$text = preg_replace('/-+/', '-', $text);
	return $text;
}

/********************************************************************************/
/* OTURUM AÇMA İŞLEMİ (GÜVENLİ HASH GEÇİŞİ İLE GÜNCELLENDİ) */
/********************************************************************************/
if (isset($_POST['oturumac'])) {

	if (isset($_POST['diyetisyen_mail']) && isset($_POST['diyetisyen_sifre'])) {
		
		$diyetisyen_mail = guvenlik($_POST['diyetisyen_mail']);
		$girilen_sifre = $_POST['diyetisyen_sifre']; // MD5 yapmadan ham şifreyi al
		
		try {
			$diyetisyensorgu=$db->prepare("SELECT * FROM kullanicilar WHERE diyetisyen_mail=:diyetisyen_mail");
			$diyetisyensorgu->execute(['diyetisyen_mail'=> $diyetisyen_mail]);
			$diyetisyenveri=$diyetisyensorgu->fetch(PDO::FETCH_ASSOC);

			$giris_basarili = false;
			$kullanici_bulundu = $diyetisyenveri ? true : false;
			
			if ($kullanici_bulundu) {
				$kayitli_sifre_hash = $diyetisyenveri['diyetisyen_sifre'];

				// 1. YENİ ve GÜVENLİ yöntemle kontrol et
				if (password_verify($girilen_sifre, $kayitli_sifre_hash)) {
					$giris_basarili = true;
				} 
				// 2. ESKİ md5 yöntemiyle kontrol et (Fallback ve Kademeli Geçiş)
				else if (md5($girilen_sifre) === $kayitli_sifre_hash) {
					$giris_basarili = true;
					
					// KADEMELİ GEÇİŞ: Kullanıcı eski şifreyle girdi. Şifresini GÜNCELLE.
					try {
						$yeni_guvenli_hash = password_hash($girilen_sifre, PASSWORD_BCRYPT);
						$rehash_stmt = $db->prepare("UPDATE kullanicilar SET diyetisyen_sifre = :yeni_sifre WHERE kullanici_id = :id");
						$rehash_stmt->execute([
							'yeni_sifre' => $yeni_guvenli_hash,
							'id' => $diyetisyenveri['kullanici_id']
						]);
					} catch (PDOException $e) {
						// Yeniden hashleme başarısız olursa logla, ama girişi engelleme.
						logtutucu(1, "Kullanıcı (ID: " . $diyetisyenveri['kullanici_id'] . ") şifre yeniden hashleme hatası: " . $e->getMessage(), 1);
					}
				}
			}

			if ($giris_basarili) {
				// Oturum değişkenlerini ayarla
				// 'sifreleme' fonksiyonu güvenlik için iyi bir pratik değil, ancak mevcut yapıyı bozmamak için bırakıldı.
				$_SESSION['session_diyetisyen_mail'] = sifreleme($diyetisyen_mail); 
				$_SESSION['kullanici_id'] = $diyetisyenveri['kullanici_id'];
				
				// DUZENLENDI: Detaylı loglama için kullanıcı adını session'a al
				$_SESSION['kullanici_isim'] = $diyetisyenveri['diyetisyen_isim'] . " " . $diyetisyenveri['diyetisyen_soyisim'];

				// IP Adresi Tespiti
				if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
					$son_oturum_giris_ip_adres=$_SERVER['HTTP_CLIENT_IP'];
				} else {
					$son_oturum_giris_ip_adres=$_SERVER['REMOTE_ADDR'];
				}

				// IP Adresi ve Session ID'sini güncelle
				$ipkaydet=$db->prepare("UPDATE kullanicilar SET
					son_oturum_giris_ip_adres=:son_oturum_giris_ip_adres, 
					session_diyetisyen_mail=:session_diyetisyen_mail 
					WHERE diyetisyen_mail=:diyetisyen_mail
				");

				$ipkaydet->execute(array(
					'son_oturum_giris_ip_adres' => $son_oturum_giris_ip_adres, 
					'session_diyetisyen_mail' => sifreleme($diyetisyen_mail),
					'diyetisyen_mail' => $diyetisyen_mail
				));

				if ($api) {
					echo json_encode([
						'durum' => 'ok',
						'bilgiler' => $diyetisyenveri
					]);
				} else {
					// DUZENLENDI: Detaylı loglama
					$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Bilinmeyen Kullanıcı') . " (ID: " . $_SESSION['kullanici_id'] . ") oturum açtı.";
					logtutucu(1, $log_mesaj, 0);
					header("location:../index.php");
					exit;
				}

			} else {
				// Giriş başarısız
				if ($api) {
					echo json_encode([
						'durum' => 'no',
						'mesaj' => 'Giriş Bilgileriniz Hatalı'
					]);
				} else {
					// DUZENLENDI: Detaylı loglama
					$log_mesaj = "Oturum açılamadı. Hatalı giriş denemesi: " . $diyetisyen_mail;
					logtutucu(1, $log_mesaj, 1);
					header("location:../giris.php?durum=hata2548");
					exit;
				}
			}

		} catch (PDOException $e) {
			$log_mesaj = "Oturum açma sırasında veritabanı hatası: " . $e->getMessage();
			logtutucu(1, $log_mesaj, 1);
			if ($api) {
				echo json_encode(['durum' => 'no', 'mesaj' => 'Sistem hatası.', 'hata' => $e->getMessage()]);
			} else {
				header("location:../giris.php?durum=sistem_hatasi");
			}
			exit;
		}

	} else {
		echo json_encode([
			'durum' => 'no',
			'mesaj' => 'Mail veya Şifre Parametreleri Boş'
		]);
		exit;
	}
}


/*******************************************************************************/
/* GENEL AYARLARI KAYDET (GÜVENLİ DOSYA YÜKLEME İLE GÜNCELLENDİ) */
/*******************************************************************************/
if (isset($_POST['genelayarkaydet'])) {
	if (yetkikontrol()!="yetkili") {
		header("location:../index.php");
		exit;
	}

	try {
		// 1. Metin bilgilerini güncelle
		$genelayarkaydet=$db->prepare("UPDATE ayarlar SET
			site_baslik=:baslik,
			site_aciklama=:aciklama,
			site_sahibi=:sahip,
			mail_onayi=:mail_onayi,
			duyuru_onayi=:duyuru_onayi 
			WHERE id=1
		");

		$ekleme=$genelayarkaydet->execute(array(
			'baslik' => guvenlik($_POST['site_baslik']),
			'aciklama' => guvenlik($_POST['site_aciklama']),
			'sahip' => guvenlik($_POST['site_sahibi']),
			'mail_onayi' => guvenlik($_POST['mail_onayi']),
			'duyuru_onayi' => guvenlik($_POST['duyuru_onayi'])
		));

		
		// 2. YENİ LOGO YÜKLENDİYSE İŞLEM YAP (GÜVENLİ BLOK)
		if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {

			$file_input = $_FILES['site_logo'];
			$max_size = 5 * 1024 * 1024; // 5 MB
			$allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'gif'];
			$upload_directory = '../dosyalar/'; // Ana dizin

			$file_tmp_name = $file_input['tmp_name'];
			$file_size = $file_input['size'];
			$file_name = $file_input['name'];
			$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

			$hata_mesaji = "";

			if ($file_size > $max_size) {
				$hata_mesaji = "Dosya boyutu 5 MB'den büyük olamaz.";
			} elseif (!in_array($file_extension, $allowed_extensions)) {
				$hata_mesaji = "Sadece " . implode(', ', $allowed_extensions) . " dosya türleri kabul edilmektedir.";
			} else {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime_type = finfo_file($finfo, $file_tmp_name);
				finfo_close($finfo);
				
				// MIME Türü Kontrolü (SVG ve GIF için genişletildi)
				$valid_mime = false;
				if (($file_extension == 'jpg' || $file_extension == 'jpeg') && $mime_type == 'image/jpeg') $valid_mime = true;
				if ($file_extension == 'png' && $mime_type == 'image/png') $valid_mime = true;
				if ($file_extension == 'gif' && $mime_type == 'image/gif') $valid_mime = true;
				if ($file_extension == 'svg' && $mime_type == 'image/svg+xml') $valid_mime = true;

				if (!$valid_mime) {
					$hata_mesaji = "Dosya türü (MIME) doğrulanmadı: " . $mime_type;
				}
			}

			if (empty($hata_mesaji)) {
				
				// A. ESKİ LOGOYU SİL
				$eski_logo_sorgu = $db->prepare("SELECT site_logo FROM ayarlar WHERE id = 1");
				$eski_logo_sorgu->execute();
				$eski_kayit = $eski_logo_sorgu->fetch(PDO::FETCH_ASSOC);
				if ($eski_kayit && !empty($eski_kayit['site_logo'])) {
					$eski_dosya_sunucu_yolu = '../' . $eski_kayit['site_logo']; // DB'de 'dosyalar/logo.png' olarak tutulmalı
					if (file_exists($eski_dosya_sunucu_yolu)) {
						@unlink($eski_dosya_sunucu_yolu);
					}
				}
				
				// B. YENİ LOGOYU YÜKLE
				// Benzersiz isim: logo-RASTGELESAYI.uzanti
				$benzersizsayi = rand(100000, 999999);
				$new_file_name = 'logo-' . $benzersizsayi . '.' . $file_extension;
				$final_upload_path = $upload_directory . $new_file_name;
				$db_path = 'dosyalar/' . $new_file_name; // DB'ye 'dosyalar/' ile kaydedilmeli

				if (move_uploaded_file($file_tmp_name, $final_upload_path)) {
					// Veritabanını güncelle
					$foto_guncelle = $db->prepare("UPDATE ayarlar SET site_logo = :site_logo WHERE id = 1");
					$foto_guncelle->execute(['site_logo' => $db_path]);
				} else {
					// Dosya taşıma hatası
					$log_mesaj = "Site logosu sunucuya taşınamadı. İzinleri kontrol edin.";
					logtutucu(1, $log_mesaj, 1);
				}
			} else {
				// Yükleme hatası (boyut, tür vb.)
				$log_mesaj = "Site logosu yüklemesi başarısız: $hata_mesaji";
				logtutucu(1, $log_mesaj, 1);
				// Hata olsa bile metin ayarlarının kaydedilmesi için işleme devam et
			}
		} // <-- Dosya yükleme bloğu sonu


		// 3. Yönlendir
		if ($ekleme) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") genel ayarları güncelledi.";
			logtutucu(1, $log_mesaj, 0);
			header("location:../ayarlar?durum=ok");
		} else {
			header("location:../ayarlar?durum=no");
		}

	} catch (PDOException $e) {
		$log_mesaj = "Genel ayarlar güncellenirken hata: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		header("location:../ayarlar?durum=no");
	}
	exit;
}

/*******************************************************************************/
/* YENİ DANIŞAN EKLEME  */
/*******************************************************************************/
if (isset($_POST['yeni_danisan'])) {
	if (yetkikontrol() != "yetkili" && !$api) {
		header("location:../index.php");
		exit;
	}
	
	// YENI LOGLAMA: İsim bilgisini al
	$danisan_ad_soyad = guvenlik($_POST['ad']) . " " . guvenlik($_POST['soyad']);

	try {
		// 1. Danışanı Fotoğraf Bilgisi OLMADAN Ekle (PARAMETRELİ GÜVENLİ SORGU)
		$yenidanisan = $db->prepare("INSERT INTO danisanlar SET
			ad=:ad,
			soyad=:soyad,
			tc_kimlik_no=:tc_kimlik_no,
			telefon=:telefon,
			eposta=:eposta,
			adres=:adres,
			adres_mahalle=:adres_mahalle,
			adres_ilce=:adres_ilce,
			adres_il=:adres_il,
			danisan_not=:danisan_not,
			kullanici_id=:kullanici_id
		");

		$ekleme = $yenidanisan->execute(array(
			'ad' => guvenlik($_POST['ad']),
			'soyad' => guvenlik($_POST['soyad']),
			'tc_kimlik_no' => guvenlik($_POST['tc_kimlik_no']),
			'telefon' => guvenlik($_POST['telefon']),
			'eposta' => guvenlik($_POST['eposta']),
			'adres' => guvenlik($_POST['adres']),
			'adres_mahalle' => guvenlik($_POST['adres_mahalle']),
			'adres_ilce' => guvenlik($_POST['adres_ilce']),
			'adres_il' => guvenlik($_POST['adres_il']),
			'danisan_not' => guvenlik($_POST['danisan_not']),
			'kullanici_id' => $_SESSION['kullanici_id']
		));

		// 2. Danışan Ekleme Başarılıysa Fotoğrafı İşle
		if ($ekleme) {
			$son_eklenen_id = $db->lastInsertId();

			// --- FOTOĞRAF YÜKLEME BLOĞU (GÜVENLİ) ---
			if (isset($_FILES['musteri_profil_foto']) && $_FILES['musteri_profil_foto']['error'] == 0) {

				$file_input = $_FILES['musteri_profil_foto'];
				$max_size = 5 * 1024 * 1024; // 5 MB
				$allowed_extensions = ['jpg', 'jpeg', 'png'];
				$upload_directory = '../dosyalar/musteri_profil_fotograflari/';

				$file_tmp_name = $file_input['tmp_name'];
				$file_size = $file_input['size'];
				$file_name = $file_input['name'];
				$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
				$hata_mesaji = "";

				if ($file_size > $max_size) {
					$hata_mesaji = "Dosya boyutu 5 MB'den büyük olamaz.";
				} elseif (!in_array($file_extension, $allowed_extensions)) {
					$hata_mesaji = "Sadece JPG, JPEG veya PNG dosya türleri kabul edilmektedir.";
				} else {
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime_type = finfo_file($finfo, $file_tmp_name);
					finfo_close($finfo);

					if (($file_extension == 'jpg' || $file_extension == 'jpeg') && $mime_type != 'image/jpeg') {
						$hata_mesaji = "Dosya türü (MIME) JPG/JPEG olarak doğrulanmadı.";
					} elseif ($file_extension == 'png' && $mime_type != 'image/png') {
						$hata_mesaji = "Dosya türü (MIME) PNG olarak doğrulanmadı.";
					}
				}

				if (empty($hata_mesaji)) {
					// Yeni dosya adı: danisan_id.uzanti (örn: 125.jpg)
					$new_file_name = $son_eklenen_id . '.' . $file_extension;
					$final_upload_path = $upload_directory . $new_file_name;
					$db_path = 'dosyalar/musteri_profil_fotograflari/' . $new_file_name;

					if (!is_dir($upload_directory)) {
						mkdir($upload_directory, 0755, true);
					}

					if (move_uploaded_file($file_tmp_name, $final_upload_path)) {
						$foto_guncelle = $db->prepare("UPDATE danisanlar SET
							musteri_profil_fotografi = :musteri_profil_foto
							WHERE danisan_id = :danisan_id
						");
						$foto_guncelle->execute([
							'musteri_profil_foto' => $db_path,
							'danisan_id' => $son_eklenen_id
						]);
					} else {
						// YENI LOGLAMA: İsim kullan
						$log_mesaj = "$danisan_ad_soyad (ID: $son_eklenen_id) fotoğrafı sunucuya taşınamadı.";
						logtutucu(1, $log_mesaj, 1);
					}
				} else {
					// YENI LOGLAMA: İsim kullan
					$log_mesaj = "$danisan_ad_soyad (ID: $son_eklenen_id) fotoğraf yüklemesi başarısız: $hata_mesaji";
					logtutucu(1, $log_mesaj, 1);
				}
			}
			// --- FOTOĞRAF YÜKLEME BLOĞU SONU ---

			// Başarılı yanıt
			if ($api) {
				echo json_encode(['durum' => 'ok', 'id' => $son_eklenen_id]);
			} else {
				// YENI LOGLAMA: İsim kullan
				$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") yeni danışan kaydı oluşturdu: $danisan_ad_soyad (ID: $son_eklenen_id).";
				logtutucu(1, $log_mesaj, 0);
				header("location:../danisanlar.php?durum=905362");
				exit;
			}
		} 
		// Ekleme Başarısız Oldu (else $ekleme)
		else {
			// Bu senaryo normalde try-catch bloğuna düşer, ancak PDO::ERRMODE_SILENT kullanılırsa buraya düşebilir.
			$log_mesaj = "Yeni danışan kaydı ($danisan_ad_soyad) oluşturulamadı (SQL Hatası).";
			logtutucu(1, $log_mesaj, 1);
			if ($api) {
				echo json_encode(['durum' => 'no', 'mesaj' => 'İşlem Başarısız', 'hata' => implode(",", $yenidanisan->errorInfo())]);
			} else {
				header("location:../danisanlar.php?durum=750372");
				exit;
			}
		}

	} catch (PDOException $e) {
		$log_mesaj = "Yeni danışan ($danisan_ad_soyad) eklenirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if ($api) {
			echo json_encode(['durum' => 'no', 'mesaj' => 'Sistem hatası.', 'hata' => $e->getMessage()]);
		} else {
			header("location:../danisanlar.php?durum=sistem_hatasi");
		}
		exit;
	}
}


/********************************************************************************/
/* DANIŞAN DÜZENLEME */
/********************************************************************************/
if (isset($_POST['danisan_duzenle'])) {
	if (yetkikontrol() != "yetkili" && !$api) {
		header("location:../index.php");
		exit;
	}

	// --- YENİ LOGLAMA DEĞİŞKENLERİ ---
	$danisan_id = $_POST['danisan_id'];
	$danisan_ad_soyad = guvenlik($_POST['ad']) . " " . guvenlik($_POST['soyad']);
	$guncelle_basarili = false; // Sorgunun çalışıp çalışmadığını kontrol eder
	$metin_guncellendi = false; // Veritabanında satırın değişip değişmediğini kontrol eder
	$fotograf_guncellendi = false; // Fotoğrafın yüklenip yüklenmediğini kontrol eder
	$fotograf_hatasi = "";
	// --- ---

	try {
		// 1. TEMEL BİLGİLERİ GÜNCELLE
		$projeguncelle = $db->prepare("UPDATE danisanlar SET
			tc_kimlik_no=:tc_kimlik_no,
			ad=:ad,
			soyad=:soyad,
			eposta=:eposta,
			telefon=:telefon,
			adres=:adres,
			adres_mahalle=:adres_mahalle,
			adres_ilce=:adres_ilce,
			adres_il=:adres_il,
			danisan_not=:danisan_not
			WHERE danisan_id=:danisan_id"); 
			
			
			
			
-


		$guncelle = $projeguncelle->execute(array(
			'tc_kimlik_no' => guvenlik($_POST['tc_kimlik_no']),
			'ad' => guvenlik($_POST['ad']),
			'soyad' => guvenlik($_POST['soyad']),
			'eposta' => guvenlik($_POST['eposta']),
			'telefon' => guvenlik($_POST['telefon']),
			'adres' => guvenlik($_POST['adres']),
			'adres_mahalle' => guvenlik($_POST['adres_mahalle']),
			'adres_ilce' => guvenlik($_POST['adres_ilce']),
			'adres_il' => guvenlik($_POST['adres_il']),
			'danisan_not' => guvenlik($_POST['danisan_not']),
			'danisan_id' => $danisan_id 
			
			
			


		));
		
		$guncelle_basarili = $guncelle; // Sorgu çalıştı mı?
		if ($guncelle && $projeguncelle->rowCount() > 0) {
			$metin_guncellendi = true; // Satır etkilendi mi?
		}

		// 2. YENİ PROFİL FOTOĞRAFI YÜKLENDİYSE İŞLEM YAP
		if (isset($_FILES['musteri_profil_foto']) && $_FILES['musteri_profil_foto']['error'] == 0) {

			$file_input = $_FILES['musteri_profil_foto'];
			$max_size = 5 * 1024 * 1024; // 5 MB
			$allowed_extensions = ['jpg', 'jpeg', 'png'];
			$upload_directory = '../dosyalar/musteri_profil_fotograflari/';
			
			$file_tmp_name = $file_input['tmp_name'];
			$file_size = $file_input['size'];
			$file_name = $file_input['name'];
			$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$hata_mesaji = "";

			if ($file_size > $max_size) {
				$hata_mesaji = "Dosya boyutu 5 MB'den büyük olamaz.";
			} elseif (!in_array($file_extension, $allowed_extensions)) {
				$hata_mesaji = "Sadece JPG, JPEG veya PNG dosya türleri kabul edilmektedir.";
			} else {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime_type = finfo_file($finfo, $file_tmp_name);
				finfo_close($finfo);

				if (($file_extension == 'jpg' || $file_extension == 'jpeg') && $mime_type != 'image/jpeg') {
					$hata_mesaji = "Dosya türü (MIME) JPG/JPEG olarak doğrulanmadı.";
				} elseif ($file_extension == 'png' && $mime_type != 'image/png') {
					$hata_mesaji = "Dosya türü (MIME) PNG olarak doğrulanmadı.";
				}
			}

			if (empty($hata_mesaji)) {
				// --- A. ESKİ FOTOĞRAFI BUL VE SİL ---
				$eski_foto_sorgu = $db->prepare("SELECT musteri_profil_fotografi FROM danisanlar WHERE danisan_id = :id");
				$eski_foto_sorgu->execute(['id' => $danisan_id]);
				$eski_foto_kaydi = $eski_foto_sorgu->fetch(PDO::FETCH_ASSOC);

				if ($eski_foto_kaydi && !empty($eski_foto_kaydi['musteri_profil_fotografi'])) {
					$eski_dosya_sunucu_yolu = '../' . $eski_foto_kaydi['musteri_profil_fotografi'];
					if (file_exists($eski_dosya_sunucu_yolu)) {
						@unlink($eski_dosya_sunucu_yolu); // Sunucudan sil
					}
				}
				
				// --- B. YENİ FOTOĞRAFI YÜKLE ---
				$new_file_name = $danisan_id . '.' . $file_extension;
				$final_upload_path = $upload_directory . $new_file_name;
				$db_path = 'dosyalar/musteri_profil_fotograflari/' . $new_file_name;

				if (!is_dir($upload_directory)) {
					mkdir($upload_directory, 0755, true);
				}

				if (move_uploaded_file($file_tmp_name, $final_upload_path)) {
					$foto_guncelle = $db->prepare("UPDATE danisanlar SET
						musteri_profil_fotografi = :musteri_profil_foto
						WHERE danisan_id = :danisan_id
					");
					$foto_guncelle->execute([
						'musteri_profil_foto' => $db_path,
						'danisan_id' => $danisan_id
					]);
					$fotograf_guncellendi = true; // YENİ LOGLAMA
				} else {
					$fotograf_hatasi = "Fotoğraf sunucuya taşınamadı."; // YENİ LOGLAMA
				}
			} else {
				$fotograf_hatasi = $hata_mesaji; // YENİ LOGLAMA
			}
			
			// Fotoğraf hatası oluştuysa bunu ayrıca logla
			if (!empty($fotograf_hatasi)) {
				$log_mesaj = "$danisan_ad_soyad (ID: $danisan_id) fotoğraf güncellemesi başarısız: $fotograf_hatasi";
				logtutucu(1, $log_mesaj, 1);
			}
		} // <-- Fotoğraf yükleme bloğu sonu


		// 3. SONUCU KULLANICIYA BİLDİR VE DETAYLI LOGLA
		if ($guncelle_basarili) { // Metin güncelleme sorgusu başarılıysa
			
			// --- YENİ DİNAMİK LOGLAMA MANTIĞI ---
			$log_parcalari = [];
			if ($metin_guncellendi) {
				$log_parcalari[] = "temel bilgileri";
			}
			if ($fotograf_guncellendi) {
				$log_parcalari[] = "profil fotoğrafı";
			}
			
			$log_eylem = "";
			if (count($log_parcalari) > 0) {
				// Örn: "temel bilgileri ve profil fotoğrafı güncellendi."
				$log_eylem = implode(" ve ", $log_parcalari) . " güncellendi.";
			} else if (empty($fotograf_hatasi)) {
				// Hiçbir şey değişmedi (fotoğraf yüklenmedi VE metin bilgisi değişmedi)
				$log_eylem = "kaydı güncellendi (değişiklik yok).";
			} else {
				// Metin bilgisi değişmedi AMA fotoğraf yüklemesi başarısız oldu
				$log_eylem = "kaydı güncellenemedi (fotoğraf hatası).";
			}

			// Log mesajını oluştur
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . "), $danisan_ad_soyad (ID: $danisan_id) danışanın $log_eylem";
			logtutucu(1, $log_mesaj, 0);
			// --- YENİ LOGLAMA SONU ---

			if ($api) {
				echo json_encode(['durum' => 'ok']);
			} else {
				header("location:../danisanlar.php?durum=703714");
				exit;
			}
		} else {
			// Metin güncelleme sorgusu $guncelle_basarili == false ise (bu try-catch'e düşer)
			throw new Exception("Temel bilgi güncelleme sorgusu başarısız: " . implode(",", $projeguncelle->errorInfo()));
		}

	} catch (PDOException $e) {
		$log_mesaj = "$danisan_ad_soyad (ID: $danisan_id) danışan kaydı güncellenirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if ($api) {
			echo json_encode(['durum' => 'no', 'mesaj' => 'Sistem hatası.', 'hata' => $e->getMessage()]);
		} else {
			header("location:../danisanlar.php?durum=sistem_hatasi");
		}
		exit;
	}
}

/********************************************************************************/
/* DANIŞAN SİLME İŞLEMİ */
/********************************************************************************/
if (isset($_GET['danisansil']) && $_GET['danisansil']=="ok") {

    // 1. Yetki Kontrolü
    if (yetkikontrol()!="yetkili" AND !$api) {
        header("location:../index.php");
        exit;
    }

    // 2. ID'yi Al
    $danisan_id = $_GET['danisan_id'];

    try {
        // (Opsiyonel) Profil Fotoğrafını Sunucudan Silme İşlemi
        /* $resim_sorgu = $db->prepare("SELECT musteri_profil_fotografi FROM danisanlar WHERE danisan_id=:id");
        $resim_sorgu->execute(['id' => $danisan_id]);
        $resim_cek = $resim_sorgu->fetch(PDO::FETCH_ASSOC);
        if(!empty($resim_cek['musteri_profil_fotografi']) && file_exists("../".$resim_cek['musteri_profil_fotografi'])){
            unlink("../".$resim_cek['musteri_profil_fotografi']);
        }
        */

        // 3. Veritabanından Silme Sorgusu
        $sil = $db->prepare("DELETE FROM danisanlar WHERE danisan_id=:danisan_id");
        $kontrol = $sil->execute(array(
            'danisan_id' => $danisan_id
        ));

        // 4. Sonuç ve Yönlendirme
        if ($kontrol) {
            
            // Log Kaydı
            $log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") " . $danisan_id . " nolu danışan kaydını sildi.";
            logtutucu(1, $log_mesaj, 0);

            // Başarılıysa Listeye Dön (Danışanlar Sayfası)
            header("Location:../danisanlar.php?durum=ok");
            exit;

        } else {
            throw new Exception("Silme işlemi başarısız.");
        }

    } catch (Exception $e) {
        
        $log_mesaj = "Danışan silinirken hata: " . $e->getMessage();
        logtutucu(1, $log_mesaj, 1);

        header("Location:../danisanlar.php?durum=no");
        exit;
    }
}


/*******************************************************************************/
/* MÜŞTERİ DOSYA İŞLEMLERİ (YENİ DETAYLI LOGLAMA İLE GÜNCELLENDİ) */
/*******************************************************************************/
if (isset($_POST['musteri_dosya_islemleri'])) {
	
	if (yetkikontrol() != "yetkili" && !$api) {
		header("location:../index.php");
		exit;
	}

	$musteri_id = isset($_POST['danisan_id']) ? (int)$_POST['danisan_id'] : 0; 
	
	if ($musteri_id <= 0) {
		$error_msg = "Geçersiz Müşteri ID'si alındı.";
		logtutucu(1, $error_msg, 1);
		if ($api) { echo json_encode(['durum' => 'no', 'mesaj' => $error_msg]); }
		else { header("location:../danisan-kaydi-duzenle.php?id=$musteri_id&durum=hata"); }
		exit;
	}

	$uploaded_files = $_FILES['yuklenecek_musteri_dosyalar'];
	$success_files_data = [];
	$upload_errors = [];

	$allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
	$max_file_size = 10485760; // 10MB
	$target_dir = "../dosyalar/musteri_dosyalari/" . $musteri_id . "/";
	
	// --- YENİ LOGLAMA: Danışan adını al ---
	$danisan_ad_soyad = "Bilinmeyen Danışan";
	try {
		$stmt_get_name = $db->prepare("SELECT ad, soyad FROM danisanlar WHERE danisan_id = ?");
		$stmt_get_name->execute([$musteri_id]);
		if ($danisan_veri = $stmt_get_name->fetch(PDO::FETCH_ASSOC)) {
			$danisan_ad_soyad = $danisan_veri['ad'] . " " . $danisan_veri['soyad'];
		}
	} catch (PDOException $e) {
		// İsim alınamazsa logla ama devam et
		logtutucu(1, "Dosya yüklerken danışan adı alınamadı (ID: $musteri_id): " . $e->getMessage(), 1);
	}
	// --- ---

	try {
		if (!is_dir($target_dir)) {
			if (!mkdir($target_dir, 0777, true)) {
				throw new Exception("Dizin oluşturulamadı. İzinleri kontrol edin.");
			}
		}

		// 3. Çoklu Dosya Yükleme Döngüsü
		foreach ($uploaded_files['name'] as $key => $name) {
			
			if (empty($name) || $uploaded_files['error'][$key] == UPLOAD_ERR_NO_FILE) {
				continue;
			}
			
			if ($uploaded_files['error'][$key] != UPLOAD_ERR_OK) {
				$upload_errors[] = "Yükleme hatası (" . $uploaded_files['error'][$key] . "): " . $name;
				continue;
			}
			
			$tmp_name = $uploaded_files['tmp_name'][$key];
			$file_size = $uploaded_files['size'][$key];
			$file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

			if ($file_size > $max_file_size) {
				$upload_errors[] = "Dosya boyutu büyük (Max 10MB): " . $name;
				continue;
			}
			if (!in_array($file_extension, $allowed_extensions)) {
				$upload_errors[] = "Geçersiz dosya türü (" . strtoupper($file_extension) . "): " . $name;
				continue;
			}
			
			// GÜVENLİK: MIME Türü kontrolü de eklenmeli (yukarıdaki tekli yüklemelerdeki gibi)
			// Ancak bu bloğun hızını etkileyebilir, şimdilik es geçildi.

			$original_file_name = $name;
			$safe_file_name_base = slugify(pathinfo($name, PATHINFO_FILENAME));
			$unique_file_name = time() . "_" . $key . "_" . $safe_file_name_base . "." . $file_extension;
			$target_file = $target_dir . $unique_file_name;

			if (move_uploaded_file($tmp_name, $target_file)) {
				$success_files_data[] = [
					'yol' => str_replace('../', '', $target_file), // DB yolu
					'ad' => $original_file_name,
					'benzersiz_ad' => $unique_file_name,
					'boyut' => $file_size
				];
			} else {
				$upload_errors[] = "Dosya taşıma hatası: " . $name;
			}
		}
		
		$guncelle = false;

		// 4. Veritabanı Kaydı (Sadece başarılı yükleme varsa)
		if (!empty($success_files_data)) {
			
			// A) Mevcut Dosyaları Çek (Kilitleme ile daha güvenli olabilir - FOR UPDATE)
			$stmt_get = $db->prepare("SELECT dosya_listesi FROM danisanlar WHERE danisan_id = ?");
			$stmt_get->execute([$musteri_id]);
			$current_data = $stmt_get->fetch(PDO::FETCH_ASSOC)['dosya_listesi'];
			
			$existing_files = json_decode($current_data ?? '[]', true);
			$new_files_list = array_merge($existing_files, $success_files_data);
			$dosya_listesi_json = json_encode($new_files_list, JSON_UNESCAPED_UNICODE);

			// D) Veritabanını Güncelle
			$stmt_update = $db->prepare("UPDATE danisanlar SET dosya_listesi = ? WHERE danisan_id = ?");
			$guncelle = $stmt_update->execute([$dosya_listesi_json, $musteri_id]);
			
		} else {
			// Hiç dosya yüklenmediyse (veya hepsi hata verdiyse)
			$guncelle = true; 
		}

		// 5. Sonuç Kontrolü ve Yönlendirme
		if ($guncelle) {
			// YENİ LOGLAMA: İsim kullan
			$log_message = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . "), $danisan_ad_soyad (ID: $musteri_id) için " . count($success_files_data) . " yeni dosya yükledi.";
			
			if (!empty($upload_errors)) {
				$log_message .= " (" . count($upload_errors) . " dosyada hata: " . implode(" | ", $upload_errors) . ")";
			}
			
			logtutucu(1, $log_message, empty($upload_errors) ? 0 : 1);
			
			if ($api) {
				echo json_encode(['durum' => 'ok', 'yuklenen' => count($success_files_data), 'hatalar' => $upload_errors]);
			} else {
				$durum_kodu = empty($upload_errors) ? 'ok' : 'kismi_hata';
				header("location:../danisan-kaydi-duzenle.php?id=$musteri_id&durum=$durum_kodu#danisandosyalari");
				exit;
			}
		} 
		// $guncelle false ise (veritabanı hatası)
		else { 
			throw new Exception("Veritabanı güncelleme hatası: " . implode(",", $stmt_update->errorInfo()));
		}

	} catch (Exception $e) {
		$error_details = $e->getMessage() . " | " . implode(" | ", $upload_errors);
		// YENİ LOGLAMA: İsim kullan
		$log_mesaj = "$danisan_ad_soyad (ID: $musteri_id) müşteri dosyaları yüklenirken ciddi hata: " . $error_details;
		logtutucu(1, $log_mesaj, 1);
		
		if ($api) {
			echo json_encode(['durum' => 'no', 'mesaj' => 'İşlem Başarısız', 'hata' => $error_details]);
		} else {
			header("location:../danisan-kaydi-duzenle.php?id=$musteri_id&durum=sistem_hatasi#danisandosyalari");
			exit;
		}
	}
}
/********************************************************************************/
/* YENİ RANDEVU EKLEME */
/********************************************************************************/
if (isset($_POST['yeni_randevu'])) {
    if (yetkikontrol()!="yetkili" AND !$api) {
        header("location:../index.php");
        exit;
    }

    date_default_timezone_set('Europe/Istanbul'); 

    try {
        // Bildirim ayarları
        $kullanici_bildirim_kanallari_json = isset($_POST['kullanici_radvevu_bildirimi_kanallar']) 
            ? json_encode($_POST['kullanici_radvevu_bildirimi_kanallar']) 
            : json_encode([]);
        $kullanici_bildirim_suresi = $_POST['kullanici_radvevu_bildirimi_suresi'] ?? 0;
        
        $musteri_bildirim_kanallari_json = isset($_POST['musteri_radvevu_bildirimi_kanallar']) 
            ? json_encode($_POST['musteri_radvevu_bildirimi_kanallar']) 
            : json_encode([]);
        $musteri_bildirim_suresi = $_POST['musteriye_randevu_hatirlatma_bildirim_suresi'] ?? 0;


        $randevu_ucreti = !empty($_POST['randevu_ucreti']) ? $_POST['randevu_ucreti'] : 0;

   
        $yenirandevu=$db->prepare("INSERT INTO randevular SET
            randevu_tarihi=:randevu_tarihi,
            randevu_suresi=:randevu_suresi,
            olusturulma_tarihi=:olusturulma_tarihi,
            randevu_durumu=:randevu_durumu,
            danisan_id=:danisan_id,
            randevu_ucreti=:randevu_ucreti,
            randevu_notu=:randevu_notu,
            kullanici_id=:kullanici_id,
            kullanici_bildirim_suresi=:kullanici_bildirim_suresi,
            kullanici_bildirim_kanallari=:kullanici_bildirim_kanallari,
            musteri_bildirim_suresi=:musteri_bildirim_suresi,
            musteri_bildirim_kanallari=:musteri_bildirim_kanallari
        ");

        $ekleme=$yenirandevu->execute(array(
            'randevu_tarihi' => guvenlik($_POST['randevu_tarihi']),
            'randevu_suresi' => guvenlik($_POST['randevu_suresi']),
            'olusturulma_tarihi' => date('Y-m-d H:i:s'),
            'randevu_durumu' => guvenlik($_POST['randevu_durumu']),
            'danisan_id' => guvenlik($_POST['danisan_id']),
            'randevu_ucreti' => $randevu_ucreti,
            'randevu_notu' => guvenlik($_POST['randevu_notu']),
            'kullanici_id' => $_SESSION['kullanici_id'],
            'kullanici_bildirim_suresi' => $kullanici_bildirim_suresi,
            'kullanici_bildirim_kanallari' => $kullanici_bildirim_kanallari_json,
            'musteri_bildirim_suresi' => $musteri_bildirim_suresi,
            'musteri_bildirim_kanallari' => $musteri_bildirim_kanallari_json
        ));
        
        $son_eklenen_randevu_id = $db->lastInsertId();

        // 2. ADIM: Dosya Yükleme (Eski 'proje' kısımları güncellendi)
        // HTML formundaki input adını 'randevu_dosya' olarak değiştirmelisiniz.
        if (isset($_FILES['randevu_dosya']) && $_FILES['randevu_dosya']['error']=="0") {
            
            $yuklemeklasoru = '../dosyalar';
            @$gecici_isim = $_FILES['randevu_dosya']["tmp_name"];
            @$dosya_ismi = $_FILES['randevu_dosya']["name"];
            $benzersizsayi1=rand(100000,999999);
            $isim=tum_bosluk_sil($benzersizsayi1.$dosya_ismi);
            
            if (@move_uploaded_file($gecici_isim, "$yuklemeklasoru/$isim")) {
                
                $dosyayukleme=$db->prepare("UPDATE randevular SET
                    dosya_yolu=:dosya_yolu WHERE randevu_id=:randevu_id ");

                $yukleme=$dosyayukleme->execute(array(
                    'dosya_yolu' => $isim,
                    'randevu_id' => $son_eklenen_randevu_id 
                ));
            }
        }

        if ($ekleme) {
            if ($api) {
                echo json_encode(['durum' => 'ok', 'id' => $son_eklenen_randevu_id]);
            } else {
                $log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") yeni randevu kaydı oluşturdu (Randevu ID: $son_eklenen_randevu_id).";
                logtutucu(1, $log_mesaj, 0);
                header("location:../randevular.php?durum=108723");
                exit;
            }
        } else {
            $log_mesaj = "Yeni randevu kaydı oluşturulurken hata alındı.";
            logtutucu(1, $log_mesaj, 1);
            if ($api) {
                echo json_encode(['durum' => 'no','mesaj' => 'İşlem Başarısız', 'hata' => implode(",", $yenirandevu->errorInfo())]);
            } else {
                header("location:../randevular.php?durum=830233");
                exit;
            }
        }

    } catch (PDOException $e) {
        $log_mesaj = "Yeni randevu eklenirken veritabanı hatası: " . $e->getMessage();
        logtutucu(1, $log_mesaj, 1);
        if ($api) {
            echo json_encode(['durum' => 'no', 'mesaj' => 'Sistem hatası.', 'hata' => $e->getMessage()]);
        } else {
            header("location:../randevular.php?durum=sistem_hatasi");
        }
        exit;
    }
}


/********************************************************************************/
/* GÖREV İŞLEMLERİ (YAPILACAKLAR) */
/********************************************************************************/

/********* GÖREV EKLE *********/
if (isset($_POST['gorev_ekle'])) {
	if (function_exists('yetkikontrol') && yetkikontrol()!="yetkili" && empty($api)) {
		header("location:../index.php"); exit;
	}

	try {
		$ekle = $db->prepare("INSERT INTO yapilacaklar
			SET kullanici_id=:kullanici_id,
				baslik=:baslik,
				aciklama=:aciklama,
				bitis_tarihi=:bitis_tarihi,
				oncelik=:oncelik,
				durum='beklemede',
				olusturan_ip=:ip
		");
		$sonuc = $ekle->execute([
			':kullanici_id' => $_POST['kullanici_id'] ?? $_SESSION['kullanici_id'], // Oturumdaki ID'yi de kullanabilir
			':baslik'		=> guvenlik($_POST['baslik']),
			':aciklama'		=> trim(guvenlik($_POST['aciklama'] ?? '')) ?: null,
			':bitis_tarihi'	=> guvenlik($_POST['bitis_tarihi']) ?: null,
			':oncelik'		=> (int)($_POST['oncelik'] ?? 0),
			':ip'			=> $_SERVER['REMOTE_ADDR'] ?? null
		]);

		if ($sonuc) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") yeni görev ekledi: " . guvenlik($_POST['baslik']);
			logtutucu(1, $log_mesaj, 0);
			if (!empty($api)) echo json_encode(['durum'=>'ok']); else header("location:../?durum=ok");
		} else {
			throw new Exception("Görev ekleme sorgusu başarısız: " . implode(",", $ekle->errorInfo()));
		}
	} catch (PDOException $e) {
		$log_mesaj = "Görev eklerken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if (!empty($api)) echo json_encode(['durum'=>'no','hata'=>$e->getMessage()]); else header("location:../?durum=no");
	}
	exit;
}

/********* GÖREV DURUM DEĞİŞTİR (tamamlandı/beklemede) *********/
if (isset($_POST['gorev_durum_degistir'])) {
	if (function_exists('yetkikontrol') && yetkikontrol()!="yetkili" && empty($api)) {
		header("location:../index.php"); exit;
	}

	try {
		$gorev_id	= (int)$_POST['gorev_id'];
		$yeni_durum	= ($_POST['yeni_durum']==='tamamlandi') ? 'tamamlandi' : 'beklemede';

		$sql = "UPDATE yapilacaklar SET
					durum=:durum,
					guncelleyen_ip=:ip,
					tamamlanma_tarihi = ".($yeni_durum==='tamamlandi' ? "NOW()" : "NULL")."
				WHERE id=:id";
		
		$guncelle = $db->prepare($sql);
		$sonuc = $guncelle->execute([
			':durum' => $yeni_durum,
			':ip'	=> $_SERVER['REMOTE_ADDR'] ?? null,
			':id'	=> $gorev_id
		]);

		if ($sonuc) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") $gorev_id nolu görevin durumunu '$yeni_durum' olarak güncelledi.";
			logtutucu(1, $log_mesaj, 0);
			if (!empty($api)) echo json_encode(['durum'=>'ok']); else header("location:../?durum=ok");
		} else {
			throw new Exception("Görev durum güncelleme sorgusu başarısız: " . implode(",", $guncelle->errorInfo()));
		}
	} catch (PDOException $e) {
		$log_mesaj = "Görev durumu değiştirilirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if (!empty($api)) echo json_encode(['durum'=>'no','hata'=>$e->getMessage()]); else header("location:../?durum=no");
	}
	exit;
}

/********* GÖREV SİL (soft delete) *********/
if (isset($_POST['gorev_sil'])) {
	if (function_exists('yetkikontrol') && yetkikontrol()!="yetkili" && empty($api)) {
		header("location:../index.php"); exit;
	}

	try {
		$gorev_id = (int)$_POST['gorev_id'];
		$sil = $db->prepare("UPDATE yapilacaklar SET silinme_tarihi=NOW(), silen_ip=:ip WHERE id=:id");
		$sonuc = $sil->execute([':ip'=>$_SERVER['REMOTE_ADDR'] ?? null, ':id'=>$gorev_id]);

		if ($sil) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") $gorev_id nolu görevi sildi (soft delete).";
			logtutucu(1, $log_mesaj, 0);
			if (!empty($api)) echo json_encode(['durum'=>'ok']); else header("location:../?durum=ok");
		} else {
			throw new Exception("Görev silme sorgusu başarısız: " . implode(",", $sil->errorInfo()));
		}
	} catch (PDOException $e) {
		$log_mesaj = "Görev silinirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if (!empty($api)) echo json_encode(['durum'=>'no','hata'=>$e->getMessage()]); else header("location:../?durum=no");
	}
	exit;
}

/********* GÖREV GÜNCELLE *********/
if (isset($_POST['gorev_guncelle'])) {
	if (function_exists('yetkikontrol') && yetkikontrol()!="yetkili" && empty($api)) {
		header("location:../index.php"); exit;
	}

	try {
		$gorev_id = (int)$_POST['gorev_id'];

		$guncelle = $db->prepare("UPDATE yapilacaklar SET
			baslik=:baslik,
			aciklama=:aciklama,
			bitis_tarihi=:bitis_tarihi,
			oncelik=:oncelik,
			guncelleyen_ip=:ip
			WHERE id=:id
		");
		$sonuc = $guncelle->execute([
			':baslik'		=> guvenlik($_POST['baslik']),
			':aciklama'		=> trim(guvenlik($_POST['aciklama'] ?? '')) ?: null,
			':bitis_tarihi'	=> guvenlik($_POST['bitis_tarihi']) ?: null,
			':oncelik'		=> (int)($_POST['oncelik'] ?? 0),
			':ip'			=> $_SERVER['REMOTE_ADDR'] ?? null,
			':id'			=> $gorev_id
		]);

		if ($sonuc) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") $gorev_id nolu görevi güncelledi.";
			logtutucu(1, $log_mesaj, 0);
			if (!empty($api)) echo json_encode(['durum'=>'ok']); else header("location:../?durum=ok");
		} else {
			throw new Exception("Görev güncelleme sorgusu başarısız: " . implode(",", $guncelle->errorInfo()));
		}
	} catch (PDOException $e) {
		$log_mesaj = "Görev güncellenirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if (!empty($api)) echo json_encode(['durum'=>'no','hata'=>$e->getMessage()]); else header("location:../?durum=no");
	}
	exit;
}


/********************************************************************************/
/* RANDEVU GÜNCELLEME İŞLEMİ */
/********************************************************************************/
if (isset($_POST['randevu_duzenle'])) {

    // 1. Yetki Kontrolü
    if (yetkikontrol()!="yetkili" AND !$api) {
        header("location:../index.php");
        exit;
    }
    
    // 2. ID Güvenliği
    $randevu_id = (int)$_POST['randevu_id'];
    
    try {
        // 3. Veritabanı Güncelleme Sorgusu
        $randevuguncelle = $db->prepare("UPDATE randevular SET
            randevu_tarihi  = :randevu_tarihi,
            randevu_suresi  = :randevu_suresi,
            randevu_durumu  = :randevu_durumu,
            randevu_ucreti  = :randevu_ucreti,
            randevu_notu    = :randevu_notu
            WHERE randevu_id = :randevu_id");

        $guncelle = $randevuguncelle->execute(array(
            'randevu_tarihi' => guvenlik($_POST['randevu_tarihi']),
            'randevu_suresi' => guvenlik($_POST['randevu_suresi']),
            'randevu_durumu' => guvenlik($_POST['randevu_durumu']),
            'randevu_ucreti' => guvenlik($_POST['randevu_ucreti']),
            'randevu_notu'   => $_POST['randevu_notu'], // Summernote HTML içerdiği için guvenlik() fonksiyonu içeriği bozuyorsa bunu direkt $_POST alabilirsiniz.
            'randevu_id'     => $randevu_id
        ));

        // 4. Sonuç ve Yönlendirme
        if ($guncelle) {
            
            // Log Kaydı
            $log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") " . $randevu_id . " nolu randevu kaydını güncelledi.";
            logtutucu(1, $log_mesaj, 0);

            if ($api) {
                echo json_encode(['durum' => 'ok']);
            } else {
                // İşlem başarılıysa tekrar aynı sayfaya dön (kullanıcı değişikliği görsün)
                header("Location:../randevu-kaydi-duzenle.php?randevu_id=$randevu_id&durum=ok");
                // Eğer listeye dönmek isterseniz üstteki satırı silip şunu açın:
                // header("Location:../randevular.php?durum=ok");
            }
            exit;

        } else {
            // Sorgu çalıştı ama etkilenen satır yok veya hata oluştu
            throw new Exception("Randevu güncelleme sorgusu başarısız: " . implode(",", $randevuguncelle->errorInfo()));
        }

    } catch (PDOException $e) {
        
        // Hata Loglama
        $log_mesaj = "Randevu güncellenirken veritabanı hatası: " . $e->getMessage();
        logtutucu(1, $log_mesaj, 1);

        if ($api) {
            echo json_encode(['durum' => 'no','mesaj' => 'İşlem Başarısız', 'hata' => $e->getMessage()]);
        } else {
            header("Location:../randevu-kaydi-duzenle.php?randevu_id=$randevu_id&durum=no");
        }
        exit;
    }
}

/********************************************************************************/
/* RANDEVU SİLME İŞLEMİ */
/********************************************************************************/
if (isset($_GET['randevusil']) && $_GET['randevusil']=="ok") {


    if (yetkikontrol()!="yetkili" AND !$api) {
        header("location:../index.php");
        exit;
    }


    $randevu_id = $_GET['randevu_id'];

    try {

        $sil = $db->prepare("DELETE FROM randevular WHERE randevu_id=:randevu_id");
        $kontrol = $sil->execute(array(
            'randevu_id' => $randevu_id
        ));

        if ($kontrol) {
            
            $log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") " . $randevu_id . " nolu randevu kaydını sildi.";
            logtutucu(1, $log_mesaj, 0);

            header("Location:../randevular.php?durum=ok");
            exit;

        } else {
            throw new Exception("Silme işlemi başarısız.");
        }

    } catch (Exception $e) {
        
        $log_mesaj = "Randevu silinirken hata: " . $e->getMessage();
        logtutucu(1, $log_mesaj, 1);

        header("Location:../randevular.php?durum=no");
        exit;
    }
}

/********************************************************************************/
/* PROFİL VE ŞİFRE GÜNCELLEME (BLOKLARA AYRILDI VE GÜVENLİ HALE GETİRİLDİ) */
/********************************************************************************/

//=========================================================
// 1. PROFİL BİLGİLERİNİ GÜNCELLEME BLOĞU
//=========================================================
if (isset($_POST['profilguncelle'])) {

	if (yetkikontrol() != "yetkili" && !$api) {
		header("location:../index.php");
		exit;
	}

	if ($api && isset($_GET['kullanici_id'])) {
		$_SESSION['kullanici_id'] = guvenlik($_GET['kullanici_id']);
	}
	
	$kullanici_id = $_SESSION['kullanici_id'];
	$guncelle_basarili = false;
	
	try {
		// 1. Metin Bilgilerini Güncelle
		$profilguncelle = $db->prepare("UPDATE kullanicilar SET
			diyetisyen_isim = :isim,
			diyetisyen_soyisim = :soyisim,
			diyetisyen_mail = :mail,
			diyetisyen_tel = :telefon
			WHERE kullanici_id = :kullanici_id");
		$ekleme = $profilguncelle->execute(array(
			'isim' => guvenlik($_POST['diyetisyen_isim']),
			'soyisim' => guvenlik($_POST['diyetisyen_soyisim']),
			'mail' => guvenlik($_POST['diyetisyen_mail']),
			'telefon' => guvenlik($_POST['diyetisyen_tel']),
			'kullanici_id' => $kullanici_id
		));

		$guncelle_basarili = $ekleme;

		// Sessiondaki ismi de güncelle
		if ($ekleme) {
			$_SESSION['kullanici_isim'] = guvenlik($_POST['diyetisyen_isim']) . " " . guvenlik($_POST['diyetisyen_soyisim']);
		}

		// 2. Yeni Fotoğraf Yüklendiyse İşlem Yap
		if (isset($_FILES['diyetisyen_resim']) && $_FILES['diyetisyen_resim']['error'] == 0) {

			$file_input = $_FILES['diyetisyen_resim'];
			$max_size = 5 * 1024 * 1024; // 5 MB
			$allowed_extensions = ['jpg', 'jpeg', 'png'];
			$upload_directory = '../dosyalar/kullanici_profil_fotografi/'; 

			$file_tmp_name = $file_input['tmp_name'];
			$file_size = $file_input['size'];
			$file_name = $file_input['name'];
			$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$hata_mesaji = "";

			if ($file_size > $max_size) {
				$hata_mesaji = "Dosya boyutu 5 MB'den büyük olamaz.";
			} elseif (!in_array($file_extension, $allowed_extensions)) {
				$hata_mesaji = "Sadece JPG, JPEG veya PNG dosya türleri kabul edilmektedir.";
			} else {
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime_type = finfo_file($finfo, $file_tmp_name);
				finfo_close($finfo);
				if (($file_extension == 'jpg' || $file_extension == 'jpeg') && $mime_type != 'image/jpeg') {
					$hata_mesaji = "Dosya türü (MIME) JPG/JPEG olarak doğrulanmadı.";
				} elseif ($file_extension == 'png' && $mime_type != 'image/png') {
					$hata_mesaji = "Dosya türü (MIME) PNG olarak doğrulanmadı.";
				}
			}
			
			if (empty($hata_mesaji)) {
				// A. ESKİ FOTOĞRAFI BUL VE SİL
				$eski_foto_sorgu = $db->prepare("SELECT diyetisyen_resim FROM kullanicilar WHERE kullanici_id = :id");
				$eski_foto_sorgu->execute(['id' => $kullanici_id]);
				$eski_kayit = $eski_foto_sorgu->fetch(PDO::FETCH_ASSOC);
				
				if ($eski_kayit && !empty($eski_kayit['diyetisyen_resim'])) {
					$eski_dosya_sunucu_yolu = '../' . $eski_kayit['diyetisyen_resim'];
					if (file_exists($eski_dosya_sunucu_yolu)) {
						@unlink($eski_dosya_sunucu_yolu);
					}
				}

				// B. YENİ FOTOĞRAFI YÜKLE
				$new_file_name = $kullanici_id . '.' . $file_extension;
				$db_path = 'dosyalar/kullanici_profil_fotografi/' . $new_file_name;
				$final_upload_path = $upload_directory . $new_file_name;

				if (!is_dir($upload_directory)) {
					mkdir($upload_directory, 0755, true);
				}

				if (move_uploaded_file($file_tmp_name, $final_upload_path)) {
					$foto_guncelle = $db->prepare("UPDATE kullanicilar SET
						diyetisyen_resim = :diyetisyen_resim 
						WHERE kullanici_id = :kullanici_id");
					$foto_guncelle->execute([
						'diyetisyen_resim' => $db_path,
						'kullanici_id' => $kullanici_id
					]);
				} else {
					logtutucu(1, "Profil fotoğrafı sunucuya taşınamadı.", 1);
				}
			} else {
				logtutucu(1, "Fotoğraf yükleme hatası: " . $hata_mesaji, 1);
			}
		} // <-- Fotoğraf IF bloğu sonu

		// 3. Yönlendir
		if ($guncelle_basarili) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $kullanici_id . ") profil bilgilerini güncelledi.";
			logtutucu(1, $log_mesaj, 0);
			if ($api) {
				echo json_encode(['durum' => 'ok']);
			} else {
				header("location:../profil.php?durum=726241");
				exit;
			}
		} else {
			throw new Exception("Profil güncelleme sorgusu başarısız: " . implode(",", $profilguncelle->errorInfo()));
		}

	} catch (PDOException $e) {
		$log_mesaj = "Profil güncellenirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if ($api) {
			echo json_encode(['durum' => 'no', 'mesaj' => 'İşlem Başarısız', 'hata' => $e->getMessage()]);
		} else {
			header("location:../profil.php?durum=290603");
			exit;
		}
	}
} 

//=========================================================
// 2. ŞİFRE GÜNCELLEME BLOĞU (GÜVENLİ HASH İLE GÜNCELLENDİ)
//=========================================================
if (isset($_POST['sifreguncelle'])) {

	if (yetkikontrol() != "yetkili" && !$api) {
		header("location:../index.php");
		exit;
	}
	
	if ($api && isset($_GET['kullanici_id'])) {
		$_SESSION['kullanici_id'] = guvenlik($_GET['kullanici_id']);
	}
	
	$kullanici_id = $_SESSION['kullanici_id'];
	$sifre_1 = $_POST['diyetisyen_sifre'];
	$sifre_2 = $_POST['diyetisyen_sifre_tekrar']; 

	// Kontroller
	if (empty($sifre_1) || strlen($sifre_1) < 6) { // Şifre minimum 6 karakter olmalı
		$log_mesaj = "Şifre alanı boş bırakıldı veya çok kısa (ID: $kullanici_id).";
		logtutucu(1, $log_mesaj, 1);
		if ($api) { echo json_encode(['durum' => 'no', 'mesaj' => 'Şifre en az 6 karakter olmalıdır.']); exit; }
		header("location:../profil.php?durum=sifre_bos");
		exit;
	}
	
	if ($sifre_1 != $sifre_2) {
		$log_mesaj = "Şifreler uyuşmuyor (ID: $kullanici_id).";
		logtutucu(1, $log_mesaj, 1);
		if ($api) { echo json_encode(['durum' => 'no', 'mesaj' => 'Şifreler uyuşmuyor.']); exit; }
		header("location:../profil.php?durum=sifre_uyusmuyor");
		exit;
	}

	try {
		// ŞİFREYİ GÜVENLİ HASH'E ÇEVİR
		$yeni_guvenli_hash = password_hash($sifre_1, PASSWORD_BCRYPT);
		
		$profilguncelle = $db->prepare("UPDATE kullanicilar SET
			diyetisyen_sifre=:diyetisyen_sifre
			WHERE kullanici_id=:kullanici_id");
		$ekleme = $profilguncelle->execute(array(
			'diyetisyen_sifre' => $yeni_guvenli_hash, // GÜVENLİ HASH
			'kullanici_id' => $kullanici_id
		));

		if ($ekleme) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $kullanici_id . ") şifresini güncelledi.";
			logtutucu(1, $log_mesaj, 0);
			if ($api) {
				echo json_encode(['durum' => 'ok']);
			} else {
				header("location:../profil.php?durum=sifre_ok");
				exit;
			}
		} else {
			throw new Exception("Şifre güncelleme sorgusu başarısız: " . implode(",", $profilguncelle->errorInfo()));
		}

	} catch (PDOException $e) {
		$log_mesaj = "Şifre güncellenirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if ($api) {
			echo json_encode(['durum' => 'no', 'mesaj' => 'İşlem Başarısız', 'hata' => $e->getMessage()]);
		} else {
			header("location:../profil.php?durum=sifre_no");
			exit;
		}
	}
}

/********************************************************************************/
/* SİLME İŞLEMLERİ (GÜVENLİ) */
/********************************************************************************/
if (isset($_POST['siparissilme'])) {

	if (yetkikontrol()!="yetkili" AND !$api) {
		header("location:../index.php");
		exit;
	}
	
	$siparis_id = (int)$_POST['sip_id'];

	try {
		// Not: İlişkili dosyaları da sunucudan silmek iyi bir pratiktir.
		// (Bu kodda eklenmedi)
		// Gelişmiş Loglama: Silmeden önce bilgiyi al
		$stmt_get = $db->prepare("SELECT sip_baslik FROM siparis WHERE sip_id = ?");
		$stmt_get->execute([$siparis_id]);
		$siparis_baslik = $stmt_get->fetch(PDO::FETCH_ASSOC)['sip_baslik'] ?? "Bilinmeyen Sipariş";

		$sil=$db->prepare("DELETE from siparis where sip_id=:id");
		$kontrol=$sil->execute(array(
			'id' => $siparis_id
		));

		if ($kontrol) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") '$siparis_baslik' (ID: $siparis_id) nolu siparişi sildi.";
			logtutucu(1, $log_mesaj, 0);
			if ($api) {
				echo json_encode(['durum' => 'ok']);
			} else {
				header("location:../siparisler?durum=ok");
			}
			exit;
		} else {
			throw new Exception("Sipariş silme sorgusu başarısız: " . implode(",", $sil->errorInfo()));
		}

	} catch (PDOException $e) {
		$log_mesaj = "Sipariş (ID: $siparis_id) silinirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if ($api) {
			echo json_encode(['durum' => 'no','mesaj' => 'İşlem Başarısız','hata' => $e->getMessage()]);
		} else {
			header("location:../siparisler?durum=no");
		}
		exit;
	}
}


/********************************************************************************/
if (isset($_POST['projesilme'])) {
	if (yetkikontrol()!="yetkili" AND !$api) {
		header("location:../index.php");
		exit;
	}
	
	$proje_id = (int)$_POST['proje_id'];
	
	try {
		// Gelişmiş Loglama: Silmeden önce bilgiyi al
		$stmt_get = $db->prepare("SELECT proje_baslik FROM proje WHERE proje_id = ?"); // Sütun adını varsayıyorum
		$stmt_get->execute([$proje_id]);
		$proje_baslik = $stmt_get->fetch(PDO::FETCH_ASSOC)['proje_baslik'] ?? "Bilinmeyen Proje";

		$sil=$db->prepare("DELETE from proje where proje_id=:id");
		$kontrol=$sil->execute(array(
			'id' => $proje_id
		));

		if ($kontrol) {
			$log_mesaj = ($_SESSION['kullanici_isim'] ?? 'Yetkili') . " (ID: " . $_SESSION['kullanici_id'] . ") '$proje_baslik' (ID: $proje_id) nolu projeyi sildi.";
			logtutucu(1, $log_mesaj, 0);
			if ($api) {
				echo json_encode(['durum' => 'ok']);
			} else {
				header("location:../projeler?durum=ok");
			}
			exit;
		} else {
			throw new Exception("Proje silme sorgusu başarısız: " . implode(",", $sil->errorInfo()));
		}
	
	} catch (PDOException $e) {
		$log_mesaj = "Proje (ID: $proje_id) silinirken veritabanı hatası: " . $e->getMessage();
		logtutucu(1, $log_mesaj, 1);
		if ($api) {
			echo json_encode(['durum' => 'no','mesaj' => 'İşlem Başarısız','hata' => $e->getMessage()]);
		} else {
			header("location:../projeler?durum=no");
		}
		exit;
	}
}


/********************************************************************************/
/* API VERİ GETİRME (SQL INJECTION DÜZELTİLDİ) */
/********************************************************************************/
if (isset($_POST['projeleri_getir'])) {
	if(!$api){
		echo json_encode(['durum' => 'no','mesaj'=>'API Bilgileriniz Eksik']);
		exit;
	} 
	
	try {
		// GÜVENLİK: ORDER BY için beyaz liste (Whitelist)
		// 'proje' tablosundaki sütun adlarını buraya ekleyin
		$proje_allowed_columns = ['proje_id', 'proje_baslik', 'proje_teslim_tarihi', 'proje_durum']; // Tahmini
		$order = "";
		if (isset($_POST['sirala'])) {
			$safe_column = validateSortColumn(guvenlik($_POST['sirala']), $proje_allowed_columns);
			$order="ORDER BY " . $safe_column; // GÜVENLİ
		}

		// GÜVENLİK: LIMIT için (int) dönüşümü
		$limit = "";
		if (isset($_POST['limit'])) {
			$limit="LIMIT " . (int)$_POST['limit']; // GÜVENLİ
		}

		$x=$db->prepare("SELECT * FROM proje $order $limit");
		$x->execute();
		$sonuc=$x->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode(['durum' => 'ok', 'projeler' => $sonuc],JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
	
	} catch (PDOException $e) {
		echo json_encode(['durum' => 'no','mesaj' => 'Sistem hatası','hata' => $e->getMessage()]);
	}
	exit;
}


if (isset($_POST['siparisleri_getir'])) {
	if(!$api){
		echo json_encode(['durum' => 'no','mesaj'=>'API Bilgileriniz Eksik']);
		exit;
	} 
	
	try {
		// GÜVENLİK: ORDER BY için beyaz liste (Whitelist)
		$siparis_allowed_columns = ['sip_id', 'musteri_isim', 'sip_baslik', 'sip_teslim_tarihi', 'sip_aciliyet', 'sip_durum', 'sip_ucret', 'sip_baslama_tarih'];
		$order = "";
		if (isset($_POST['sirala'])) {
			$safe_column = validateSortColumn(guvenlik($_POST['sirala']), $siparis_allowed_columns);
			$order="ORDER BY " . $safe_column; // GÜVENLİ
		}

		// GÜVENLİK: LIMIT için (int) dönüşümü
		$limit = "";
		if (isset($_POST['limit'])) {
			$limit="LIMIT " . (int)$_POST['limit']; // GÜVENLİ
		}

		$x=$db->prepare("SELECT * FROM siparis $order $limit");
		$x->execute();
		$sonuc=$x->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode(['durum' => 'ok', 'siparisler' => $sonuc],JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
	
	} catch (PDOException $e) {
		echo json_encode(['durum' => 'no','mesaj' => 'Sistem hatası','hata' => $e->getMessage()]);
	}
	exit;
}

?>