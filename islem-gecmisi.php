<?php include 'header.php'; ?>


  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">İşlem Geçmişi</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">İşlem Geçmişi</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->

    

















    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <!-- <div class="card-header">
                <h3 class="card-title">Tüm danışanların listesi.</h3>
              </div>
               /.card-header -->
              <div class="card-body">
                <table id="islemgecmisitablosu1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Tarih</th>
                    <th>İşlem Bilgisi</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                  $say=0;
                  $logkayitsorgusu = $db->prepare("SELECT * FROM logkayitlari 
                        WHERE kullanici_id = :kullanici_id 
                          AND islem_yada_ziyaret = 1 
                        ORDER BY giris_tarihi ASC");
                  $logkayitsorgusu->execute(array(':kullanici_id' => $_SESSION['kullanici_id']));
                  while ($logkayitverileri = $logkayitsorgusu->fetch(PDO::FETCH_ASSOC)) {
                      $say++;
                  ?>

                    <tr>
                      <td><?php echo $logkayitverileri['giris_tarihi']; ?></td>
                      <td><?php echo $logkayitverileri['olay_bilgisi']; ?></td>
                    </tr>
                  

                  <?php
                  }
                  ?>

                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Tarih</th>
                      <th>İşlem Bilgisi</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
















  <!-- sweetalert2 başlangıç -->
  <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <?php
  if (isset($_GET['durum']) && $_GET['durum'] == "674062") {
      echo "
      <script>
          Swal.fire({
          icon: 'error',
          title: 'Hata! Danışan Bilgileri Güncellenemedi.',
          text: 'Hata Kodu: 674062',
          showConfirmButton: true,
          confirmButtonText: 'Tamam'
          })
      </script>
      ";
  }
  if (isset($_GET['durum']) && $_GET['durum'] == "703714") {
      echo "
      <script>
          Swal.fire({
          icon: 'success',
          title: 'İşlem Başarılı',
          text: 'Danışan Bilgileri Başarıyla Güncellendi.',
          showConfirmButton: true,
          confirmButtonText: 'Tamam'
          })
      </script>
      ";
  }
  if (isset($_GET['durum']) && $_GET['durum'] == "905362") {
    echo "
    <script>
        Swal.fire({
        icon: 'success',
        title: 'İşlem Başarılı',
        text: 'Yeni Danışan Başarıyla Kaydedildi.',
        showConfirmButton: true,
        confirmButtonText: 'Tamam'
        })
    </script>
    ";
  }
  if (isset($_GET['durum']) && $_GET['durum'] == "750372") {
    echo "
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Hata! Yeni Danışan Kaydedilemedi',
        text: 'Hata Kodu: 750372',
        showConfirmButton: true,
        confirmButtonText: 'Tamam'
        })
    </script>
    ";
  }
  ?>
  <!-- sweetalert2 bitiş -->


<?php include 'footer.php'; ?>