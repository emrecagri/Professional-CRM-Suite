-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 22 Oca 2026, 00:35:08
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `danisan-yonetim-sistemi`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `adresler`
--

CREATE TABLE `adresler` (
  `adres_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `olusturulma_tarihi` datetime DEFAULT NULL,
  `olusturan_ip_adresi` varchar(100) DEFAULT NULL,
  `degistirme_tarihi` datetime DEFAULT NULL,
  `degistiren_ip_adresi` varchar(100) DEFAULT NULL,
  `adres` varchar(300) DEFAULT NULL,
  `adres_il` varchar(100) DEFAULT NULL,
  `adres_ilce` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ayarlar`
--

CREATE TABLE `ayarlar` (
  `id` int(11) NOT NULL,
  `site_logo` varchar(250) NOT NULL,
  `site_baslik` varchar(250) NOT NULL,
  `site_aciklama` varchar(400) NOT NULL,
  `site_anahtar_kelimeler` varchar(300) NOT NULL,
  `site_sahip` varchar(300) NOT NULL,
  `yonetim_tema` int(2) NOT NULL,
  `site_arkaplan_renk_kodu` varchar(7) DEFAULT NULL,
  `kullanici_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `danisanlar`
--

CREATE TABLE `danisanlar` (
  `danisan_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `tc_kimlik_no` varchar(11) DEFAULT NULL,
  `ad` varchar(50) DEFAULT NULL,
  `soyad` varchar(50) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `eposta` varchar(100) DEFAULT NULL,
  `adres` varchar(300) DEFAULT NULL,
  `adres_il` varchar(100) DEFAULT NULL,
  `adres_ilce` varchar(100) DEFAULT NULL,
  `danisan_not` text DEFAULT NULL,
  `kayit_tarihi` datetime NOT NULL DEFAULT curtime(),
  `adres_mahalle` varchar(255) DEFAULT NULL,
  `dosya_listesi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dosya_listesi`)),
  `musteri_profil_fotografi` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `danisanlar`
--

INSERT INTO `danisanlar` (`danisan_id`, `kullanici_id`, `tc_kimlik_no`, `ad`, `soyad`, `telefon`, `eposta`, `adres`, `adres_il`, `adres_ilce`, `danisan_not`, `kayit_tarihi`, `adres_mahalle`, `dosya_listesi`, `musteri_profil_fotografi`) VALUES
(100015, 100012, '00000000000', 'Ece', 'Yılmaz', '00000000000', 'eceyilmaz@mail.local', 'Örnek Mah. Örnek Cad. Örnek Apt. No:1/2', 'İSTANBUL', 'BEYKOZ', '&lt;p&gt;Örnek not&lt;/p&gt;', '2026-01-22 01:34:33', 'MERKEZ MAHALLESİ', NULL, NULL),
(100018, 100012, '', '', '', '', '', '', '', '', '', '2026-01-22 02:16:12', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `kullanici_id` int(11) NOT NULL,
  `diyetisyen_resim` varchar(250) DEFAULT NULL,
  `diyetisyen_isim` varchar(250) DEFAULT NULL,
  `diyetisyen_mail` varchar(250) DEFAULT NULL,
  `diyetisyen_sifre` varchar(250) DEFAULT NULL,
  `diyetisyen_tel` varchar(20) DEFAULT NULL,
  `diyetisyen_soyisim` varchar(50) DEFAULT NULL,
  `diyetisyen_yetki` int(10) NOT NULL,
  `diyetisyen_hesap_bitis` date DEFAULT NULL,
  `diyetisyen_hesap_baslangic` date DEFAULT NULL,
  `diyetisyen_isletme_adi` varchar(200) DEFAULT NULL,
  `diyetisyen_hesap_paketi` varchar(200) DEFAULT NULL,
  `diyetisyen_isletme_adresi` varchar(200) DEFAULT NULL,
  `diyetisyen_telefon_numarasi` varchar(25) DEFAULT NULL,
  `diyetisyen_unvani` varchar(200) DEFAULT NULL,
  `diyetisyen_vergi_numarasi` varchar(25) DEFAULT NULL,
  `diyetisyen_isletme_sicil_numarasi` varchar(20) DEFAULT NULL,
  `son_oturum_giris_ip_adres` varchar(300) DEFAULT NULL,
  `session_diyetisyen_mail` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`kullanici_id`, `diyetisyen_resim`, `diyetisyen_isim`, `diyetisyen_mail`, `diyetisyen_sifre`, `diyetisyen_tel`, `diyetisyen_soyisim`, `diyetisyen_yetki`, `diyetisyen_hesap_bitis`, `diyetisyen_hesap_baslangic`, `diyetisyen_isletme_adi`, `diyetisyen_hesap_paketi`, `diyetisyen_isletme_adresi`, `diyetisyen_telefon_numarasi`, `diyetisyen_unvani`, `diyetisyen_vergi_numarasi`, `diyetisyen_isletme_sicil_numarasi`, `son_oturum_giris_ip_adres`, `session_diyetisyen_mail`) VALUES
(100012, 'dosyalar/kullanici_profil_fotografi/100012.jpg', 'Ebru', 'ebru@testmail.local', '$2y$10$G/PEL4FES/zG5XmL4AmdGOUwXUYgJEyE5JJbo6xKtxGp4MxIz8FQi', '05123456789', 'Kaya', 1, '2026-01-01', '2024-06-01', 'Ebru Kaya', '6 Aylık Paket', 'Demo Mah. Demo Cad. 32/2 Demo / DEMO', NULL, NULL, NULL, '000000-0', '::1', '54bdd590fc1eb0c292f255ca16f0a133');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `logkayitlari`
--

CREATE TABLE `logkayitlari` (
  `log_id` int(11) NOT NULL,
  `dis_adres` varchar(45) NOT NULL,
  `ip` varchar(18) NOT NULL,
  `ic_adres` varchar(100) NOT NULL,
  `giris_tarihi` datetime NOT NULL DEFAULT curtime(),
  `pcadi` varchar(200) DEFAULT NULL,
  `tarayici` varchar(200) DEFAULT NULL,
  `tarayicidili` varchar(200) DEFAULT NULL,
  `gercekip` varchar(200) DEFAULT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `islem_yada_ziyaret` int(10) DEFAULT NULL,
  `olay_bilgisi` varchar(250) DEFAULT NULL,
  `hata_yada_basarili` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mesajlar`
--

CREATE TABLE `mesajlar` (
  `mesaj_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `danisan_id` int(11) DEFAULT NULL,
  `olusturulma_tarihi` datetime DEFAULT NULL,
  `olusturan_ip_adresi` varchar(100) DEFAULT NULL,
  `olusturan_kullanici_id` varchar(100) DEFAULT NULL,
  `degistirme_tarihi` datetime DEFAULT NULL,
  `degistiren_ip_adresi` varchar(100) DEFAULT NULL,
  `goruntuleme_durumu` tinyint(1) DEFAULT NULL,
  `konu` varchar(100) DEFAULT NULL,
  `mesaj` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muhasebe`
--

CREATE TABLE `muhasebe` (
  `muhasebe_islem_id` int(11) NOT NULL,
  `danisan_id` int(11) DEFAULT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `odeme_durumu` varchar(50) DEFAULT NULL,
  `ödeme_turu` varchar(100) DEFAULT NULL,
  `islem_tarihi` date DEFAULT NULL,
  `tutar` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `muhasebe`
--

INSERT INTO `muhasebe` (`muhasebe_islem_id`, `danisan_id`, `kullanici_id`, `odeme_durumu`, `ödeme_turu`, `islem_tarihi`, `tutar`) VALUES
(100845, 100000, 100012, 'Ödendi', 'Nakit', '2025-03-01', 500.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notlar`
--

CREATE TABLE `notlar` (
  `not_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `danisan_id` int(11) DEFAULT NULL,
  `olusturulma_tarihi` datetime DEFAULT NULL,
  `olusturan_ip_adresi` varchar(100) DEFAULT NULL,
  `degistirme_tarihi` datetime DEFAULT NULL,
  `degistiren_ip_adresi` varchar(100) DEFAULT NULL,
  `not` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notlar`
--

INSERT INTO `notlar` (`not_id`, `kullanici_id`, `danisan_id`, `olusturulma_tarihi`, `olusturan_ip_adresi`, `degistirme_tarihi`, `degistiren_ip_adresi`, `not`) VALUES
(100000, NULL, NULL, NULL, NULL, NULL, NULL, 'test');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `programlar`
--

CREATE TABLE `programlar` (
  `program_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `olusturulma_tarihi` datetime DEFAULT NULL,
  `olusturan_ip_adresi` varchar(100) DEFAULT NULL,
  `degistirme_tarihi` datetime DEFAULT NULL,
  `degistiren_ip_adresi` varchar(100) DEFAULT NULL,
  `program` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevular`
--

CREATE TABLE `randevular` (
  `randevu_id` int(11) NOT NULL,
  `kullanici_id` int(11) DEFAULT NULL,
  `danisan_id` int(11) DEFAULT NULL,
  `olusturulma_tarihi` datetime DEFAULT NULL,
  `olusturan_ip_adresi` varchar(100) DEFAULT NULL,
  `degistirme_tarihi` datetime DEFAULT NULL,
  `degistiren_ip_adresi` varchar(100) DEFAULT NULL,
  `randevu_tarihi` datetime DEFAULT NULL,
  `randevu_durumu` varchar(100) DEFAULT NULL,
  `randevu_notu` text DEFAULT NULL,
  `randevu_suresi` int(11) DEFAULT NULL,
  `randevu_ucreti` decimal(10,2) NOT NULL DEFAULT 0.00,
  `kullanici_bildirim_suresi` int(11) NOT NULL DEFAULT 0 COMMENT 'Kullaniciya kac dakika once bildirim gonderilecegi',
  `kullanici_bildirim_kanallari` text DEFAULT NULL COMMENT 'Kullaniciya bildirim gonderilecek kanallar (JSON formatinda)',
  `musteri_bildirim_suresi` int(11) NOT NULL DEFAULT 0 COMMENT 'Musteriye kaç dakika önce bildirim gönderileceği',
  `musteri_bildirim_kanallari` text DEFAULT NULL COMMENT 'Musteriye bildirim gönderilecek kanallar (JSON formatında)',
  `kullanici_bildirim_durum` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Bekliyor, 1=Gönderildi, 2=Hata Aldı',
  `musteri_bildirim_durum` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Bekliyor, 1=Gönderildi, 2=Hata Aldı'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `randevular`
--

INSERT INTO `randevular` (`randevu_id`, `kullanici_id`, `danisan_id`, `olusturulma_tarihi`, `olusturan_ip_adresi`, `degistirme_tarihi`, `degistiren_ip_adresi`, `randevu_tarihi`, `randevu_durumu`, `randevu_notu`, `randevu_suresi`, `randevu_ucreti`, `kullanici_bildirim_suresi`, `kullanici_bildirim_kanallari`, `musteri_bildirim_suresi`, `musteri_bildirim_kanallari`, `kullanici_bildirim_durum`, `musteri_bildirim_durum`) VALUES
(100018, 100012, 100015, '2026-01-22 01:35:31', NULL, NULL, NULL, '2026-01-26 01:34:00', 'Onaylandı', '<p>Örnek not</p>', 30, 1.00, 30, '[\"sms\",\"mail\"]', 30, '[\"sms\",\"mail\"]', 0, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yapilacaklar`
--

CREATE TABLE `yapilacaklar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kullanici_id` bigint(20) UNSIGNED DEFAULT NULL,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `bitis_tarihi` datetime DEFAULT NULL,
  `oncelik` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `durum` enum('beklemede','tamamlandi') NOT NULL DEFAULT 'beklemede',
  `siralama` int(11) NOT NULL DEFAULT 0,
  `olusturan_ip` varchar(45) DEFAULT NULL,
  `guncelleyen_ip` varchar(45) DEFAULT NULL,
  `silen_ip` varchar(45) DEFAULT NULL,
  `olusturulma_tarihi` datetime NOT NULL DEFAULT current_timestamp(),
  `guncellenme_tarihi` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tamamlanma_tarihi` datetime DEFAULT NULL,
  `silinme_tarihi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `adresler`
--
ALTER TABLE `adresler`
  ADD PRIMARY KEY (`adres_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`);

--
-- Tablo için indeksler `ayarlar`
--
ALTER TABLE `ayarlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`);

--
-- Tablo için indeksler `danisanlar`
--
ALTER TABLE `danisanlar`
  ADD PRIMARY KEY (`danisan_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`kullanici_id`);

--
-- Tablo için indeksler `logkayitlari`
--
ALTER TABLE `logkayitlari`
  ADD PRIMARY KEY (`log_id`);

--
-- Tablo için indeksler `mesajlar`
--
ALTER TABLE `mesajlar`
  ADD PRIMARY KEY (`mesaj_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`),
  ADD KEY `danisan_id` (`danisan_id`);

--
-- Tablo için indeksler `muhasebe`
--
ALTER TABLE `muhasebe`
  ADD PRIMARY KEY (`muhasebe_islem_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`),
  ADD KEY `danisan_id` (`danisan_id`);

--
-- Tablo için indeksler `notlar`
--
ALTER TABLE `notlar`
  ADD PRIMARY KEY (`not_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`),
  ADD KEY `danisan_id` (`danisan_id`);

--
-- Tablo için indeksler `programlar`
--
ALTER TABLE `programlar`
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`);

--
-- Tablo için indeksler `randevular`
--
ALTER TABLE `randevular`
  ADD PRIMARY KEY (`randevu_id`),
  ADD KEY `diyetisyen_id` (`kullanici_id`),
  ADD KEY `danisan_id` (`danisan_id`);

--
-- Tablo için indeksler `yapilacaklar`
--
ALTER TABLE `yapilacaklar`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `adresler`
--
ALTER TABLE `adresler`
  MODIFY `adres_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100000;

--
-- Tablo için AUTO_INCREMENT değeri `danisanlar`
--
ALTER TABLE `danisanlar`
  MODIFY `danisan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100019;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `kullanici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204019;

--
-- Tablo için AUTO_INCREMENT değeri `logkayitlari`
--
ALTER TABLE `logkayitlari`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `mesajlar`
--
ALTER TABLE `mesajlar`
  MODIFY `mesaj_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100000;

--
-- Tablo için AUTO_INCREMENT değeri `muhasebe`
--
ALTER TABLE `muhasebe`
  MODIFY `muhasebe_islem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100846;

--
-- Tablo için AUTO_INCREMENT değeri `notlar`
--
ALTER TABLE `notlar`
  MODIFY `not_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100001;

--
-- Tablo için AUTO_INCREMENT değeri `programlar`
--
ALTER TABLE `programlar`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100000;

--
-- Tablo için AUTO_INCREMENT değeri `randevular`
--
ALTER TABLE `randevular`
  MODIFY `randevu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100026;

--
-- Tablo için AUTO_INCREMENT değeri `yapilacaklar`
--
ALTER TABLE `yapilacaklar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
