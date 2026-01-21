<?php include 'header.php'; ?>


  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Randevular</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Randevular</li>
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
                <h3 class="card-title">Tüm randevuların listesi.</h3>
              </div>
               /.card-header -->
              <div class="card-body">
                <table id="randevulartablosu1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Randevu Numarası</th>
                    <th>Danışan Ad Soyad</th>
                    <th>Oluşturulma Tarihi</th>
                    <th>Randevu Tarihi</th>
                    <th>Randevu Durumu</th>
                    <th>Randevu Notu</th>
                    <th>İşlem</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $say=0;
                    $randevusorgu=$db->prepare("SELECT * FROM randevular ORDER BY kullanici_id DESC");
                    $randevusorgu->execute();
                    while ($randevuverileri=$randevusorgu->fetch(PDO::FETCH_ASSOC)) { $say++
                  ?>

                    <tr>
                      <td><?php echo $randevuverileri['randevu_id']; ?></td>
                      <td>
                        <?php 
                          // Sorguyu hazırlama
                          $stmt = $db->prepare("SELECT ad, soyad FROM danisanlar WHERE danisan_id = :danisan_id");
                          
                          // Sorguyu çalıştırma
                          $danisan_id = $randevuverileri['danisan_id'];
                          $stmt->bindParam(':danisan_id', $danisan_id);
                          $stmt->execute();
                          
                          // Sonuçları işleme
                          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          // Sonuçları yazdırma
                          foreach ($result as $row) {
                              //echo $row['ad'] . "<br>";
                              echo $row['ad'] . " " . $row['soyad'];
                          }
                          ?>
                      </td>
                      <td><?php echo $randevuverileri['olusturulma_tarihi']; ?></td>
                      <td><?php echo $randevuverileri['randevu_tarihi']; ?></td>
                      <td><?php echo $randevuverileri['randevu_durumu']; ?></td>
                      <td><?php echo $randevuverileri['randevu_notu']; ?></td>
                      <td>    
                        <div class="d-flex justify-content-left">
                          <form action="randevu-kaydi-duzenle.php" method="POST">
                            <input type="hidden" name="randevu_id" value="<?php echo $randevuverileri['randevu_id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm btn-icon-split">
                              <span class="icon text-white-60">
                                <i class="fas fa-edit"></i>
                              </span>
                            </button>
                          </form>

                          <!-- Modal -->
                          <div class="modal fade" id="<?php echo $randevuverileri['randevu_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $randevuverileri['randevu_id']; ?>Label" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="<?php echo $randevuverileri['randevu_id']; ?>Label"><?php echo $randevuverileri['ad']; ?></h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                <p>Randevu kaydını silmek istiyor musunuz?</p>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Vazgeç</button>
                                  <button type="button" class="btn btn-danger">Sil</button>
                                </div>
                              </div>
                            </div>
                          </div>


                        </div>
                      </td>
                    </tr>
                  

                  <?php
                  }
                  ?>

                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Randevu Numarası</th>
                      <th>Danışan Ad Soyad</th>
                      <th>Oluşturulma Tarihi</th>
                      <th>Randevu Tarihi</th>
                      <th>Randevu Durumu</th>
                      <th>Randevu Notu</th>
                      <th>İşlem</th>
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
  if (isset($_GET['durum']) && $_GET['durum'] == "830233") {
      echo "
      <script>
          Swal.fire({
          icon: 'error',
          title: 'Hata! Randevu Oluşturulamadı.',
          text: 'Hata Kodu: 830233',
          showConfirmButton: true,
          confirmButtonText: 'Tamam'
          })
      </script>
      ";
  }
  if (isset($_GET['durum']) && $_GET['durum'] == "108723") {
      echo "
      <script>
          Swal.fire({
          icon: 'success',
          title: 'İşlem Başarılı',
          text: 'Randevu Oluşturuldu.',
          showConfirmButton: true,
          confirmButtonText: 'Tamam'
          })
      </script>
      ";
  }
  ?>
  <!-- sweetalert2 bitiş -->


<?php include 'footer.php'; ?>