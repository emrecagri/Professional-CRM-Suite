<?php

include 'islemler/logtutucu.php';
logtutucu(0, "Giriş sayfası görüntülendi.", 0);

session_start();
// Oturum zaten açıksa index.php'ye yönlendir
if (isset($_SESSION['diyetisyen_mail']) && $_SESSION['diyetisyen_mail'] == true) {
    header("location: index.php");
    exit;
}
?>


<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Danışan Takip Sistemi | Giriş Yap</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="login-page" style="min-height: 495.6px;">
<div class="login-box">
  
  <div class="login-logo">
    <a href="index.php"><b>Danışan Yönetim </b>Sistemi</a>
  </div><!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Danışan Yönetim Sistemine Hoş Geldiniz</p>

      <form action="islemler/islem.php" method="post">
        <div class="input-group mb-3">
          <input required name="diyetisyen_mail" type="email" class="form-control" placeholder="E-Posta">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input required name="diyetisyen_sifre" type="password" class="form-control" placeholder="Şifre">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">Beni Hatırla</label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button name="oturumac" type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
          </div>
          <!-- /.col -->
        </div>
      </form>


      <p class="mb-1">
        <a href="sifremi-unuttum.php">Şifremi unuttum</a>
      </p>
      
    </div>
    <!-- /.login-card-body -->
  </div>
<div class="card-footer text-center">
    <a href="https://loruv.com"><img src="dist/img/saydam_siyah_logo.webp" height="85px"></img></a>
  </div></div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- sweetalert2 başlangıç -->
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<?php
if (isset($_GET['durum']) && $_GET['durum'] == "hata2548") {
    echo "
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Oturum Açılamadı',
        text: 'Lütfen Bilgilerinizi Kontrol Ediniz',
        showConfirmButton: true,
        confirmButtonText: 'Tamam'
        })
    </script>
    ";
}
if (isset($_GET['durum']) && $_GET['durum'] == "501874") {
    echo "
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Giriş İzniniz Yok',
        text: 'Sayfayı Görüntüleyebilmek için Önce Oturum Açmalısınız',
        showConfirmButton: true,
        confirmButtonText: 'Tamam'
        })
    </script>
    ";
}
if (isset($_GET['durum']) && $_GET['durum'] == "874523") {
    echo "
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Bir Şeyler Ters Gitti',
        text: 'Şüpheli Bir Hareket Oldu Lütfen Güvenlik için Tekrar Oturum Açın',
        showConfirmButton: true,
        confirmButtonText: 'Tamam'
        })
    </script>
    ";
}
?>
<!-- sweetalert2 bitiş -->



</body></html>