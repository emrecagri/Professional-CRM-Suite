<?php
/**
 * ============================================================================
 * BİLDİRİM OTOMASYONU 
 * ============================================================================
 */

// --- SİSTEM AYARLARI ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Istanbul');
set_time_limit(300);

try {
    require_once __DIR__ . '/plugins/phpmailer/Exception.php';
    require_once __DIR__ . '/plugins/phpmailer/PHPMailer.php';
    require_once __DIR__ . '/plugins/phpmailer/SMTP.php';
} catch (Exception $e) {
    // Bu dosyalar bulunamazsa betik çalışamaz
    die("KRİTİK HATA: PHPMailer kütüphane dosyaları '/plugins/phpmailer/' klasöründe bulunamadı. Lütfen dosya yolunu kontrol edin. Hata: " . $e->getMessage());
}

// Gerekli Sınıf (Class) yolları (Bunlar hala gerekli)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;



interface NotificationDriver
{
    public function send(array $alici, array $icerik): bool;
}

function log_mesaj($message, $level = 'INFO')
{
    $eol = (php_sapi_name() === 'cli') ? "\n" : "<br>";
    echo "[" . date('Y-m-d H:i:s') . "] [$level] - " . $message . $eol;
}


class MailDriver implements NotificationDriver
{
    private $smtpHost;
    private $smtpUser;
    private $smtpPass;
    private $smtpPort;
    private $smtpSecure;
    private $fromEmail;
    private $fromName;

    public function __construct($host, $user, $pass, $port, $secure, $fromEmail, $fromName)
    {
        $this->smtpHost = $host;
        $this->smtpUser = $user;
        $this->smtpPass = $pass;
        $this->smtpPort = (int)$port;
        $this->smtpSecure = $secure;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function send(array $alici, array $icerik): bool
    {
        $mail = new PHPMailer(true);
        try {
            $kime = $alici['adres'];
            $konu = $icerik['konu'] ?? 'Bildirim';
            $mesaj = $icerik['mesaj'];


            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUser;
            $mail->Password   = $this->smtpPass;
            $mail->SMTPSecure = $this->smtpSecure;
            $mail->Port       = $this->smtpPort;
            $mail->CharSet    = 'UTF-8';

            // Alıcılar ve Gönderici
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($kime); 

            // İçerik
            $mail->isHTML(true); 
            $mail->Subject = $konu;
            $mail->Body    = $mesaj;
            $mail->AltBody = strip_tags($mesaj);

            $mail->send();
            
            log_mesaj("[MailDriver] PHPMailer ile e-posta başarıyla gönderildi: $kime", 'SUCCESS');
            return true;

        } catch (PHPMailerException $e) {
            log_mesaj("[MailDriver] HATA: " . $e->getMessage(), 'ERROR');
            return false;
        } catch (Exception $e) {
             log_mesaj("[MailDriver] GENEL HATA: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}

class TelegramDriver implements NotificationDriver
{
    private $botToken;
    public function __construct($botToken) { $this->botToken = $botToken; }
    public function send(array $alici, array $icerik): bool
    {
        try {
            $chatId = $alici['adres'];
            $mesaj = $icerik['mesaj'];
            if (empty($this->botToken) || $this->botToken === 'BURAYA_TOKEN_GIRIN') {
                 throw new Exception("Telegram Bot Token ayarlanmamış.");
            }
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            $data = ['chat_id' => $chatId, 'text' => $mesaj, 'parse_mode' => 'HTML'];
            // ... (API'ye gönderme kodu - file_get_contents veya cURL) ...
            log_mesaj("[TelegramDriver] TEST: Telegram gönderildi: $chatId", 'SUCCESS');
            return true;
        } catch (Exception $e) {
            log_mesaj("[TelegramDriver] HATA: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}

class WhatsAppDriver implements NotificationDriver
{
    private $apiUrl;
    private $apiToken;
    public function __construct($apiUrl, $apiToken) { $this->apiUrl = $apiUrl; $this->apiToken = $apiToken; }
    public function send(array $alici, array $icerik): bool
    {
        try {
            $telefon = $alici['adres']; $mesaj = $icerik['mesaj'];
            if (empty($this->apiUrl)) { throw new Exception("WhatsApp API URL ayarlanmamış."); }
            // ... (API'ye gönderme kodu - cURL) ...
            log_mesaj("[WhatsAppDriver] TEST: WhatsApp gönderildi: $telefon", 'SUCCESS');
            return true;
        } catch (Exception $e) {
            log_mesaj("[WhatsAppDriver] HATA: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}


class NotificationService
{
    private $drivers = [];
    public function addDriver(string $kanal, NotificationDriver $driver)
    {
        $this->drivers[$kanal] = $driver;
        log_mesaj("[Service] '$kanal' kanalı için sürücü (driver) eklendi.", 'INFO');
    }
    public function dispatch(string $kanal, array $alici, array $icerik): bool
    {
        if (!isset($this->drivers[$kanal])) {
            log_mesaj("[Service] HATA: '$kanal' için tanımlı bir sürücü bulunamadı.", 'ERROR');
            return false;
        }
        if (empty($alici['adres'])) {
             log_mesaj("[Service] HATA: '$kanal' için alıcı adresi boş.", 'ERROR');
            return false;
        }
        log_mesaj("[Service] Görev '$kanal' sürücüsüne yönlendirildi. Alıcı: {$alici['adres']}");
        try {
            return $this->drivers[$kanal]->send($alici, $icerik);
        } catch (Exception $e) {
            log_mesaj("[Service] '$kanal' sürücüsünde kritik hata: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}


class CronProcessor
{
    private $db;
    private $service;
    public function __construct(PDO $db, NotificationService $service) { $this->db = $db; $this->service = $service; }
    public function run()
    {
        log_mesaj("================ CRON BAŞLADI ================", 'INFO');
        $this->processQueue('kullanici');
        $this->processQueue('musteri');
        log_mesaj("================ CRON TAMAMLANDI ================", 'INFO');
    }
    private function processQueue(string $target)
    {
        log_mesaj("$target kuyruğu işleniyor...");
        $sure_sutun = "{$target}_bildirim_suresi";
        $durum_sutun = "{$target}_bildirim_durum";
        $kanal_sutun = "{$target}_bildirim_kanallari";
        $sql = "
            SELECT * FROM randevular 
            WHERE $sure_sutun > 0 
              AND ($durum_sutun = 0 OR $durum_sutun = 2)
              AND randevu_tarihi <= DATE_ADD(NOW(), INTERVAL $sure_sutun MINUTE)
              AND randevu_tarihi > NOW()
            LIMIT 20;
        ";
        try {
            $stmt = $this->db->query($sql);
            $gorevler = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $gorev_sayisi = count($gorevler);
            if ($gorev_sayisi === 0) {
                log_mesaj("$target için gönderilecek yeni bildirim yok.");
                return;
            }
            log_mesaj("$gorev_sayisi adet $target bildirimi bulundu ve işlenecek.");
            foreach ($gorevler as $randevu) {
                $this->processSingleTask($randevu, $target, $durum_sutun, $kanal_sutun);
            }
        } catch (PDOException $e) {
            log_mesaj("KRİTİK ($target) Veritabanı Sorgu Hatası: " . $e->getMessage(), 'ERROR');
        }
    }
    private function processSingleTask($randevu, $target, $durum_sutun, $kanal_sutun)
    {
        $randevu_id = $randevu['id'];
        log_mesaj("Görev işleniyor: Randevu ID #$randevu_id ($target)");
        $this->db->beginTransaction();
        try {
            $kanallar = json_decode($randevu[$kanal_sutun], true);
            $icerik = $this->formatNotificationMessage($randevu, $target);
            if (!is_array($kanallar) || empty($kanallar)) {
                throw new Exception("Kanal listesi boş veya hatalı JSON.");
            }
            $tum_kanallar_basarili = true;
            foreach ($kanallar as $kanal) {
                $alici_adresi = $this->getRecipientAddress($randevu, $target, $kanal);
                if (empty($alici_adresi)) {
                    log_mesaj("Randevu ID #$randevu_id için '$kanal' alıcı bilgisi bulunamadı. Atlanıyor.", 'ERROR');
                    $tum_kanallar_basarili = false;
                    continue;
                }
                $alici = ['adres' => $alici_adresi];
                if (!$this->service->dispatch($kanal, $alici, $icerik)) {
                    $tum_kanallar_basarili = false;
                }
            }
            $yeniDurum = $tum_kanallar_basarili ? 1 : 2;
            $durumMesaji = $tum_kanallar_basarili ? "Başarıyla tamamlandı" : "Hata aldı (tekrar denenecek)";
            $updateSql = "UPDATE randevular SET $durum_sutun = ? WHERE id = ?";
            $this->db->prepare($updateSql)->execute([$yeniDurum, $randevu_id]);
            $this->db->commit();
            log_mesaj("Randevu ID #$randevu_id ($target) durumu: $durumMesaji.", ($yeniDurum == 1 ? 'SUCCESS' : 'ERROR'));
        } catch (Exception $e) {
            $this->db->rollBack();
            log_mesaj("KRİTİK HATA (Randevu ID #$randevu_id): " . $e->getMessage() . ". İşlem geri alındı.", 'ERROR');
            $updateSql = "UPDATE randevular SET $durum_sutun = 2 WHERE id = ?";
            $this->db->prepare($updateSql)->execute([$randevu_id]);
        }
    }
    private function getRecipientAddress($randevu, $target, $kanal)
    {
//bilgileri doldurun
        if ($target === 'kullanici') {
            if ($kanal === 'mail') return 'info@mail.local'; 
            if ($kanal === 'whatsapp') return '000000000000';
            if ($kanal === 'telegram') return '000000000';
        } else {
            if ($kanal === 'mail') return 'info@mail.local';
            if ($kanal === 'whatsapp') return '000000000000';
            if ($kanal === 'telegram') return '000000000';
        }
        return null;
    }
    private function formatNotificationMessage($randevu, $target)
    {
        
        $tarih = date('d.m.Y H:i', strtotime($randevu['randevu_tarihi']));
        $konu = "Randevu Hatırlatması";
        $mesaj = "Merhaba, $tarih tarihindeki randevunuzu hatırlatmak isteriz.";
        if ($target === 'kullanici') {
            $mesaj = "Hatırlatma: Müşteri ile $tarih tarihinde randevunuz bulunmaktadır.";
        }
        return ['konu' => $konu, 'mesaj' => $mesaj];
    }
}




log_mesaj("Betiğe giriş yapıldı. Sınıflar yüklendi.", 'INFO');

try {

    require_once(__DIR__ . '/islemler/baglan.php');

    if (!isset($db) || !$db instanceof PDO) {
        throw new Exception("Veritabanı bağlantı nesnesi ($db) 'baglan.php' dosyasından alınamadı.");
    }
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    log_mesaj("Veritabanı bağlantısı 'baglan.php' üzerinden başarıyla kuruldu.", 'INFO');


    
    // PHPMailer için SMTP Ayarları (KENDİ BİLGİLERİNİ GİR)
    define('SMTP_HOST', ''); // Örn: smtp.yandex.com.tr
    define('SMTP_USER', ''); // Mail adresi
    define('SMTP_PASS', ''); // Mail şifresi
    define('SMTP_PORT', 465); // 587 (TLS) veya 465 (SSL)
    define('SMTP_SECURE', 'ssl'); // 'tls' (587 portu için) veya 'ssl' (465 portu için)
    define('SMTP_FROM_EMAIL', ''); // Gönderen e-postası
    define('SMTP_FROM_NAME', ''); // Gönderen Adı

    // Diğer API Ayarları
    $telegramToken = 'BURAYA_TELEGRAM_BOT_TOKEN_GIRIN';
    $whatsappApiUrl = 'https://api.whatsapp-saglayiciniz.local/v1/messages';
    $whatsappApiToken = 'BURAYA_WHATSAPP_API_TOKEN_GIRIN';


    $notificationService = new NotificationService();

    $notificationService->addDriver('mail', new MailDriver(
        SMTP_HOST,
        SMTP_USER,
        SMTP_PASS,
        SMTP_PORT,
        SMTP_SECURE,
        SMTP_FROM_EMAIL,
        SMTP_FROM_NAME
    ));
    
    $notificationService->addDriver('telegram', new TelegramDriver($telegramToken));
    $notificationService->addDriver('whatsapp', new WhatsAppDriver($whatsappApiUrl, $whatsappApiToken));
    
    $processor = new CronProcessor($db, $notificationService);

    $processor->run();


} catch (PDOException $e) {
    log_mesaj("KRİTİK VERİTABANI HATASI: " . $e->getMessage(), 'ERROR');
} catch (Exception $e) {
    log_mesaj("KRİTİK GENEL HATA: " . $e->getMessage(), 'ERROR');
}

?>