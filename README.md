# 👥 Comprehensive Client Management & Tracking System (CRM)

![Language](https://img.shields.io/badge/Language-PHP%208-777BB4?style=flat&logo=php&logoColor=white)
![Database](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-GNU%20GPLv3-blue.svg)

[🇹🇷 Türkçe](#-türkçe) | [🇬🇧 English](#-english) 

---

## 🇹🇷 Türkçe

### 📌 Proje Hakkında

**Kapsamlı Danışan Yönetim Sistemi**, PHP ve MySQL mimarisi üzerine inşa edilmiş, danışmanlık hizmeti veren profesyonellerin tüm iş süreçlerini tek bir panelden yönetmelerini sağlayan uçtan uca bir CRM çözümüdür.

Bu proje, sadece veri saklayan bir sistem değil; randevu takibinden muhasebeye, güvenli mesajlaşmadan detaylı raporlamaya kadar iş akışını otomatize eden bir yönetim aracıdır.

### ✨ Öne Çıkan Özellikler

* **📊 Dinamik Dashboard:** Bekleyen işler (To-Do), yaklaşan randevular ve anlık istatistiklerin (aktif danışan, mesaj vb.) yer aldığı kart yapısı ve entegre takvim modülü.
![image](screenshots/tr/1.png)

* **🔔 Gelişmiş Bildirim Sistemi:** Randevu oluşturulurken danışan ve danışman için ayrı ayrı yapılandırılabilen çok kanallı bildirim desteği (**SMS, WhatsApp, Telegram, E-posta**) ile randevu kaçırma oranları minimize edilir.
![image](screenshots/tr/2.png)
![image](screenshots/tr/3.png)

* **📂 Danışan Veritabanı:** Gelişmiş filtreleme, detaylı adres seçimi (İl/İlçe/Mahalle entegrasyonu) ve verileri dışa aktarma (CSV, Excel, PDF) özellikleri.
![image](screenshots/tr/4.png)
![image](screenshots/tr/5.png)

* **💰 Muhasebe & Raporlama:** Gelir-gider dengesini ve randevu bazlı kazançları görselleştiren grafik destekli finansal takip modülü.
![image](screenshots/tr/6.png)

* **🔒 Güvenlik & Mesajlaşma:** Sistem içi güvenli mesajlaşma kutusu ve parola işlemlerinde karmaşıklık kurallarını (Regex) denetleyen dinamik validasyon yapısı.

![image](screenshots/tr/7.png)
![image](screenshots/tr/8.png)
![image](screenshots/tr/9.png)
![image](screenshots/tr/10.png)
![image](screenshots/tr/11.png)
![image](screenshots/tr/12.png)
![image](screenshots/tr/13.png)

### 🛠 Kullanılan Teknolojiler

* **Backend:** PHP 8, MySQL
* **Frontend:** HTML5, CSS3, JavaScript (AJAX)
* **Libraries:** Chart.js (Grafikler için), FPDF/PhpSpreadsheet (Raporlama için)

### 🚀 Kurulum (Local)

1.  Repoyu klonlayın:
    ```bash
    git clone [https://github.com/emrecagri/professional-crm-suite.git)
    ```
2.  `database.sql` dosyasını yerel veritabanınıza (phpMyAdmin vb.) içe aktarın.
3.  `config.php` dosyasındaki veritabanı bağlantı ayarlarını düzenleyin.
4.  Projeyi `localhost` üzerinde çalıştırın.

---

## 🇬🇧 English

### 📌 Project Overview

**Comprehensive Client Management System** is a full-featured CRM solution built on a robust PHP and MySQL architecture. It is designed for consultancy professionals to manage their entire workflow from a single, unified panel.

Beyond simple data storage, this system automates critical business processes ranging from appointment scheduling and accounting to secure internal messaging and detailed reporting.

### ✨ Key Features

* **📊 Dynamic Dashboard:** Features a "To-Do" list, upcoming appointments, and real-time statistics cards, along with an integrated calendar for daily/monthly planning.
![image](screenshots/en/1.png)

* **🔔 Advanced Notification System:** Supports multi-channel notifications (**SMS, WhatsApp, Telegram, Email**) configurable separately for both client and consultant to minimize no-show rates.
![image](screenshots/en/2.png)
![image](screenshots/en/3.png)

* **📂 Client Database:** Includes advanced filtering, detailed address integration, and export options (CSV, Excel, PDF) for client records.
![image](screenshots/en/4.png)
![image](screenshots/en/5.png)

* **💰 Accounting & Reporting:** A graph-supported module to track financial health, visualizing income-expense balances and appointment-based earnings.
![image](screenshots/en/6.png)

* **🔒 Security & Messaging:** Features a secure internal inbox and a dynamic validation structure enforcing strict password complexity rules.
![image](screenshots/en/7.png)
![image](screenshots/en/8.png)
![image](screenshots/en/9.png)
![image](screenshots/en/10.png)
![image](screenshots/en/11.png)
![image](screenshots/en/12.png)
![image](screenshots/en/13.png)

### 🛠 Tech Stack

* **Backend:** PHP 8, MySQL
* **Frontend:** HTML5, CSS3, JavaScript (AJAX)
* **Libraries:** Chart.js (for Analytics), FPDF/PhpSpreadsheet (for Reporting)

### 🚀 Installation (Local)

1.  Clone the repository:
    ```bash
    git clone [https://github.com/emrecagri/professional-crm-suite.git)
    ```
2.  Import the `database.sql` file into your local database (e.g., via phpMyAdmin).
3.  Update the database connection settings in `config.php`.
4.  Run the project on `localhost`.

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Emre Çağrı Başgül**

* Website: [emrecb.com](https://emrecb.com)
* GitHub: [@emrecb](https://github.com/emrecb)
