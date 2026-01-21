<?php include 'header.php'; ?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Yeni Randevu</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Yeni Randevu</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">Randevu Ekle</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <!--
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
                -->
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">






            <form action="islemler/islem.php" method="POST" enctype="multipart/form-data" data-parsley-validate="">
                <div class="form-row">
                  <div class="form-group col-md-2">
                    <label for="randevu_tarihi">Randevu Tarihi</label>
                    <input type="datetime-local" class="form-control" id="randevu_tarihi" required name="randevu_tarihi">
                  </div>

                  <div class="form-group col-md-2">
                  <label for="randevu_suresi">Randevu Süresi</label>
                    <select class="form-control" id="randevu_suresi" name="randevu_suresi">
                      <option value="30">30 dakika</option>
                      <option value="45">45 dakika</option>
                      <option value="60">1 saat</option>
                      <option value="90">1 saat 30 dakika</option>
                    </select>
                  </div>

                  <div class="form-group col-md-2">
                    <label for="randevu_durumu">Randevu Durumu</label>
                    <select class="form-control" style="width: 100%;" id="randevu_durumu" name="randevu_durumu">
                      <option selected="selected">Onaylanmadı</option>
                      <option>Onaylandı</option>
                      <option>Yapıldı</option>
                      <option>İptal Edildi</option>
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="danisan_id">Danışan Seç</label>

                    <select class="form-control select2" style="width: 100%;" id="danisan_id" name="danisan_id" data-placeholder="Danışan Seçiniz">
                      <option value='0'>Danışan Seçiniz</option>
                      <?php
                        if (isset($_SESSION['kullanici_id'])) {
                          $kullanici_id = $_SESSION['kullanici_id'];

                          // Danışanları çek
                          $sql = "SELECT danisan_id, ad, soyad FROM danisanlar WHERE kullanici_id = :kullanici_id";
                          $stmt = $db->prepare($sql);
                          $stmt->bindParam(":kullanici_id", $kullanici_id, PDO::PARAM_INT);
                          $stmt->execute();
                          $danisanlar = $stmt->fetchAll(PDO::FETCH_ASSOC);

                          if (count($danisanlar) > 0) {
                            foreach ($danisanlar as $danisan) {
                              echo "<option value='" . htmlspecialchars($danisan['danisan_id'], ENT_QUOTES) . "'>" .
                                    htmlspecialchars($danisan['ad'], ENT_QUOTES) . " " .
                                    htmlspecialchars($danisan['soyad'], ENT_QUOTES) .
                                  "</option>";
                            }
                          } else {
                            echo "<option disabled>Hiç danışan yok.</option>";
                          }
                        }
                      ?>
                  </select>


                  </div>
                  
                  <div class="form-group col-md-2">
                    <label for="randevu_ucreti">Randevu Ücreti</label>
                    <div class="input-group mb-3">
                    <input type="number" class="form-control" id="randevu_ucreti" name="randevu_ucreti" placeholder="0,00" step="0.01" min="0">
                        <div class="input-group-append">
                          <span class="input-group-text">₺</span>
                        </div>
                    </div>
                 
                  
                  
                                  </div>
                  
                  
                  
                  
                  
                  


                    <div class="form-group col-md-6">
                        <div class="card card-outline card-info collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">Danışan Bilgilendirmesi</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <label>Kaç dakika önce bildirilsin?</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                    <select class="form-control" name="musteriye_randevu_hatirlatma_bildirim_suresi">
                                        <option value="0">Bildirim gönderme</option>
                                        <option value="15">15 dakika</option>
                                        <option value="30" selected>30 dakika (Varsayılan)</option>
                                        <option value="60">1 saat</option>
                                        <option value="120">2 saat</option>
                                        <option value="1440">1 gün</option>
                                    </select>
                                </div>

                                <label class="mt-2">Hangi kanallar kullanılsın?</label>
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="musteri_radvevu_bildirimi_sms" name="musteri_radvevu_bildirimi_kanallar[]" value="sms">
                                        <label class="form-check-label" for="musteri_radvevu_bildirimi_sms">SMS</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="musteri_radvevu_bildirimi_whatsapp" name="musteri_radvevu_bildirimi_kanallar[]" value="whatsapp" checked>
                                        <label class="form-check-label" for="musteri_radvevu_bildirimi_whatsapp">WhatsApp</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="musteri_radvevu_bildirimi_telegram" name="musteri_radvevu_bildirimi_kanallar[]" value="telegram">
                                        <label class="form-check-label" for="musteri_radvevu_bildirimi_telegram">Telegram</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="musteri_radvevu_bildirimi_mail" name="musteri_radvevu_bildirimi_kanallar[]" value="mail">
                                        <label class="form-check-label" for="musteri_radvevu_bildirimi_mail">E-posta</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <div class="card card-outline card-primary collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">Danışman Bilgilendirme</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <label>Kaç dakika önce bildirilsin?</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                                    </div>
                                    <select class="form-control" name="kullanici_radvevu_bildirimi_suresi">
                                        <option value="0">Bildirim gönderme</option>
                                        <option value="15">15 dakika</option>
                                        <option value="30" selected>30 dakika (Varsayılan)</option>
                                        <option value="60">1 saat</option>
                                        <option value="120">2 saat</option>
                                        <option value="1440">1 gün</option>
                                    </select>
                                </div>

                                <label class="mt-2">Hangi kanallar kullanılsın?</label>
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="kullanici_radvevu_bildirimi_sms" name="kullanici_radvevu_bildirimi_kanallar[]" value="sms">
                                        <label class="form-check-label" for="kullanici_radvevu_bildirimi_sms">SMS</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="kullanici_radvevu_bildirimi_whatsapp" name="kullanici_radvevu_bildirimi_kanallar[]" value="whatsapp" checked>
                                        <label class="form-check-label" for="kullanici_radvevu_bildirimi_whatsapp">WhatsApp</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="kullanici_radvevu_bildirimi_telegram" name="kullanici_radvevu_bildirimi_kanallar[]" value="telegram">
                                        <label class="form-check-label" for="kullanici_radvevu_bildirimi_telegram">Telegram</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="kullanici_mail" name="kullanici_radvevu_bildirimi_kanallar[]" value="mail">
                                        <label class="form-check-label" for="kullanici_radvevu_bildirimi_mail">E-posta</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                
                
                
                
                
                
                
                
                
                
                
                
                
                
                

                  <div class="form-group col-md-12">
                    <label for="randevu_notu">Varsa Not</label><textarea id="summernote" name="randevu_notu" style="display: none;"></textarea>
                  </div>

                </div>
                <button type="submit" name="yeni_randevu" class="btn btn-primary">Kaydet</button>
              </form>




            </div>
            <!-- /.card-body -->
          
      </div>







      
      </div><!-- /.container-fluid -->
    </section>






    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->





<?php include 'footer.php'; ?>