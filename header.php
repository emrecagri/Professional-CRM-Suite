<?php
ob_start();
session_start(); 
include 'islemler/baglan.php';
include 'fonksiyonlar.php';
include 'islemler/logtutucu.php';
oturumkontrol();

$ayarsorgusu=$db->prepare("SELECT * FROM ayarlar");
$ayarsorgusu->execute();
$ayarveri=$ayarsorgusu->fetch(PDO::FETCH_ASSOC);

$diyetisyensorgusu=$db->prepare("SELECT * FROM kullanicilar where session_diyetisyen_mail=:mail");
$diyetisyensorgusu->execute(array(
	'mail' => $_SESSION['session_diyetisyen_mail']
));
//her oturum açışta sifremele fonksiyonu ile mail adresini taban alarak zamanı da katarak bir benzersiz kod oluşturup bu kodu sessiona atıyoruz ki her girişte benzersiz bir kod ile kullanıcı hesabı tanımlanıyor böylece güvenlik artıyor.
$say=$diyetisyensorgusu->rowcount();
$diyetisyenveri=$diyetisyensorgusu->fetch(PDO::FETCH_ASSOC);
if ($say==0) {
  logtutucu(1, "Oturum açılmadan sayfaya girildiği için giriş ekranına yönlendirildi.", 0);
	header("location:giris.php?durum=501874");
	exit;
};
/*Eğer IP Adresi Değiştiğinde Oturum Sonlandırılmasını İstemiyorsanız Aşağıdaki Satırları Silin*/

// IP adresini değişkene alıyoruz. (direkt $_SERVER['REMOTE_ADDR'] olarak aldığım zaman sunucunun ipsi dönüyor.)
if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
  $son_oturum_giris_ip_adres=$_SERVER['HTTP_CLIENT_IP'];
} else {
  $son_oturum_giris_ip_adres=$_SERVER['REMOTE_ADDR'];
}

if ($diyetisyenveri['son_oturum_giris_ip_adres']!=$son_oturum_giris_ip_adres) {
  logtutucu(1, "IP adresi değişikliği algılandığı için oturum kapatıldı.", 0);
	header("location:giris.php?durum=874523");
	session_destroy();
	exit;
}
/*Eğer IP Adresi Değiştiğinde Oturum Sonlandırılmasını İstemiyorsanız Yukarıdaki Satırları Silin*/


logtutucu(0, "Sayfa görüntülendi.", 0);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danışan Takip Sistemi | Yönetim Paneli</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  
  
  
<style>
        .dropdown-item {
            /* Bu kural, işlem geçmişi açılır menüsündeki uzun metinlerin alt satıra geçmesine izin verir. */
            white-space: normal;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed sidebar-mini-xs">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/saydam_siyah_logo.webp" alt="Danışan Takip Sistemi" >
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Ara..." aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Ali Bulut
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Size de iyi günler dilerim.</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 2 Saat Önce</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Kerem Demir
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Bu yeni liste mi?</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Saat Önce</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Ece Bahar
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">İşe yarıyor! Bu harika!!!</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 6 Saat Önce</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="mesajlar.php" class="dropdown-item dropdown-footer">Tüm Mesajları Gör</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">7</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">7 Bildirim</span>
          
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 yeni mesaj
            <span class="float-right text-muted text-sm">3 dakika</span>
          </a>

          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 3 yeni randevu talebi
            <span class="float-right text-muted text-sm">12 saat</span>
          </a>
          
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Tüm Bildirimleri Gör</a>
        </div>
      </li>
      <!-- Logs Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">İşlem Geçmişiniz</span>
          
          

          <?php 
          $say=0;
          $logkayitsorgusu = $db->prepare("SELECT * FROM logkayitlari WHERE kullanici_id = :kullanici_id AND islem_yada_ziyaret = 1 ORDER BY log_id DESC");
          $logkayitsorgusu->execute(array(':kullanici_id' => $_SESSION['kullanici_id']));
          while ($logkayitverileri = $logkayitsorgusu->fetch(PDO::FETCH_ASSOC)) {
              $say++;
          ?>

          <div class="dropdown-divider"></div>
          <a href="islem-gecmisi.php" class="dropdown-item">
            <?php echo $logkayitverileri['olay_bilgisi']; ?>
            <span class="float-right text-muted text-sm">
            <?php echo zamanFarki($logkayitverileri['giris_tarihi']); ?>
            </span>
          </a>

          <?php
          if ($say >= 5) break;
          }
          ?>


          
          <div class="dropdown-divider"></div>
          <a href="islem-gecmisi.php" class="dropdown-item dropdown-footer">Tüm Geçmişi Gör</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#cikisSorusuModal" role="button">
          <i class="fa-solid fa-power-off"></i>
        </a>
      </li>
      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>-->
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Çıkıs Sorusu Modal-->
  <div class="modal fade" id="cikisSorusuModal" tabindex="-1" role="dialog" aria-labelledby="cikisSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cikisSorusuModal">Oturum Kapatma</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Oturumu kapatmak istediğinize emin misiniz?</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
          <a class="btn btn-primary" href="islemler/cikis.php">Çıkış Yap</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="dist/img/icon_logo.png" alt="Danışan Takip Sistemi" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">LORUV</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="<?php echo (!empty($diyetisyenveri['diyetisyen_resim'])) ? $diyetisyenveri['diyetisyen_resim'] : 'dist/img/user2-160x160.png'; ?>" 
                 class="img-circle elevation-2" 
                 alt="Kullanıcı Profil Resmi"
                 style="width: 33.6px; height: 33.6px; object-fit: cover;">
        </div>
        <div class="info">
          <a href="profil.php" class="d-block">Sn. <?php echo $diyetisyenveri['diyetisyen_isim']." ".$diyetisyenveri['diyetisyen_soyisim']; ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Ara..." aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fa fa-line-chart"></i>
              <p>Genel Panel</p>
            </a>
          </li>


          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa-solid fa-clipboard-list"></i>
              <p>
              Randevularım
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="randevular.php" class="nav-link">
                  <i class="nav-icon fa-solid fa-list-ul"></i>
                  <p>Tüm Randevular</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="yeni-randevu.php" class="nav-link">
                  <i class="nav-icon fa-regular fa-calendar-plus"></i>
                  <p>Yeni Randevu</p>
                </a>
              </li>
            </ul>
          </li>







          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fa-solid fa-address-book"></i>
              <p>
                Danışanlar
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="danisanlar.php" class="nav-link">
                  <i class="nav-icon fa-solid fa-list-ul"></i>
                  <p>Tüm Danışanlar</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="yenidanisan.php" class="nav-link">
                  <i class="nav-icon fa-solid fa-user-plus"></i>
                  <p>Yeni Danışan</p>
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-item">
            <a href="mesajlar.php" class="nav-link">
              <i class="nav-icon fa-solid fa-comments"></i>
              <p>Mesajlar <span class="badge badge-info right">2</span></p>
            </a>
          </li>


          
          <li class="nav-item">
            <a href="muhasebe.php" class="nav-link">
              <i class="nav-icon fa-solid fa-cash-register"></i>
              <p>Muhasebe</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="profil.php" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>Ayarlar</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#cikisSorusuModal">
              <i class="nav-icon fa-regular fa-circle-xmark"></i>
              <p>Güvenli Çıkış</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>