<?php include 'header.php'; ?>

<?php
if (isset($_POST['danisan_id'])) {
	$danisansorgu=$db->prepare("SELECT * FROM danisanlar where danisan_id=:danisan_id");
	$danisansorgu->execute(array(
		'danisan_id' => $_POST['danisan_id']
	));
	$danisanveri=$danisansorgu->fetch(PDO::FETCH_ASSOC);

} else {
	header("Location: danisanlar.php");
} 
?>
  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Danışan Kayıt Düzenleme</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Danışan Kayıt Düzenleme</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->








    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="<?php echo (!empty($danisanveri['musteri_profil_fotografi'])) ? $danisanveri['musteri_profil_fotografi'] : 'dist/img/avatar2.png'; ?>"
                         alt="Kullanıcı Profil Resmi"
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>

                <h3 class="profile-username text-center">Sn. <?php echo $danisanveri['ad']." ".$danisanveri['soyad']; ?></h3>

                <p class="text-muted text-center">Danışan</p>

                <ul class="list-group list-group-unbordered mb-3">

                  <li class="list-group-item">
                    <b>Danışan No</b> <a class="float-right"><?php echo $danisanveri['danisan_id']; ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>Kayıt Tarihi</b> <a class="float-right"><?php echo $danisanveri['kayit_tarihi']; ?></a>
                  </li>

                  
                </ul>

                <a href="#" data-toggle="modal" data-target="#smsGonderilsinMiSorusuModal" class="btn btn-info btn-block"><b>SMS Gönder</b></a>
                <a href="#" data-toggle="modal" data-target="#mailGonderilsinMiSorusuModal" class="btn btn-info btn-block"><b>Mail Gönder</b></a>
                <a href="#" data-toggle="modal" data-target="#whatsappMesajiGonderilsinMiSorusuModal" class="btn btn-success btn-block"><b>WhatsApp Mesajı Gönder</b></a>
                <a href="#" data-toggle="modal" data-target="#danisanSilSorusuModal" class="btn btn-danger btn-block"><b>Danışan Kaydını Sil</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            
            
            
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">

                  <li class="nav-item"><a class="nav-link active" href="#danisanbilgileri" data-toggle="tab">Bilgileri</a></li>
                  <li class="nav-item"><a class="nav-link" href="#danisandosyalari" data-toggle="tab">Dosyaları</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  
                  
                  
                  

                  <div class="tab-pane active" id="danisanbilgileri">
                    <form class="form-horizontal" action="islemler/islem.php" method="POST" enctype="multipart/form-data" data-parsley-validate>
                      <div class="form-group row">
                        <label for="danisan_fotograf" class="col-sm-2 col-form-label">Danışan Fotoğrafı</label>
                        <div class="col-sm-10">
                          <input class="form-control" id="danisan_fotograf" name="musteri_profil_foto" type="file">
                          <small class="form-text text-muted">Sadece yeni bir fotoğraf yüklerseniz mevcut fotoğraf değişir. Boş bırakırsanız eski fotoğraf korunur.</small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="tc_kimlik_no" class="col-sm-2 col-form-label">TC Kimlik No</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="tc_kimlik_no" name="tc_kimlik_no" value="<?php echo $danisanveri['tc_kimlik_no']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="ad" class="col-sm-2 col-form-label">Ad</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="ad" name="ad" value="<?php echo $danisanveri['ad']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="soyad" class="col-sm-2 col-form-label">Soyad</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="soyad" name="soyad" value="<?php echo $danisanveri['soyad']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="eposta" class="col-sm-2 col-form-label">E-Posta</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="eposta" name="eposta" value="<?php echo $danisanveri['eposta']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="telefon" class="col-sm-2 col-form-label">Telefon</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="telefon" name="telefon" value="<?php echo $danisanveri['telefon']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="adres" class="col-sm-2 col-form-label">Adres</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="adres" name="adres" value="<?php echo $danisanveri['adres']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="adres_il" class="col-sm-2 col-form-label">İl</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="adres_il" name="adres_il" value="<?php echo $danisanveri['adres_il']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="adres_ilce" class="col-sm-2 col-form-label">İlçe</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="adres_ilce" name="adres_ilce" value="<?php echo $danisanveri['adres_ilce']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="adres_mahalle" class="col-sm-2 col-form-label">Mahalle</label> <div class="col-sm-10">
                          <input type="text" class="form-control" id="adres_mahalle" name="adres_mahalle" value="<?php echo $danisanveri['adres_mahalle']; ?>">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="danisan_not" class="col-sm-2 col-form-label">Not</label>
                        <div class="col-sm-10">
                          <textarea id="summernote" name="danisan_not" style="display: none;"><?php echo $danisanveri['danisan_not']; ?></textarea>
                        </div>
                      </div>
                    
                      <input type="hidden" name="danisan_id" value="<?php echo $danisanveri['danisan_id']; ?>">
                    
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" name="danisan_duzenle" class="btn btn-success">Kaydet</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                  
                  
                  


 <div class="tab-pane" id="danisandosyalari" role="tabpanel">
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="card-title mb-3 text-primary"><i class="fas fa-upload mr-2"></i>Yeni Dosya Yükle</h5>

        <form class="d-flex align-items-end gap-3 flex-wrap" action="islemler/islem.php" method="POST" enctype="multipart/form-data">
            
            <div class="flex-grow-1 mb-2 mb-md-0">
                <label for="formFileMultiple" class="form-label visually-hidden">Dosyaları Seç</label>
                <input class="form-control" type="file" id="formFileMultiple" name="yuklenecek_musteri_dosyalar[]" multiple required>
                <input type="hidden" id="danisan_id" name="danisan_id" value="<?php echo $danisanveri['danisan_id']; ?>">
            </div>

            <div class="ms-auto">
                <button type="submit" name="musteri_dosya_islemleri" class="btn btn-primary">
                    <i class="fas fa-cloud-upload-alt mr-1"></i>Yükle
                </button>
            </div>

        </form>
    </div>
    <?php
        $dosya_listesi = [];
        // JSON verisini güvenli bir şekilde alıp diziye dönüştürüyoruz
        if (isset($danisanveri['dosya_listesi']) && !empty($danisanveri['dosya_listesi'])) {
            $dosya_listesi = json_decode($danisanveri['dosya_listesi'], true);
            // Array olmaması durumunda boş dizi atanıyor
            if (!is_array($dosya_listesi)) {
                $dosya_listesi = [];
            }
        }
        
        // Diziyi ters çevirerek en son yükleneni en üste getiriyoruz
        // Not: Veritabanında ORDER BY kullanmak daha performanslı olabilir.
        $dosya_listesi = array_reverse($dosya_listesi);

        // 'formatBytes' fonksiyonunun tanımlı olduğundan emin olunmalı, yoksa hatalara neden olur.
        if (!function_exists('formatBytes')) {
            function formatBytes($bytes, $precision = 2) {
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                $bytes = max($bytes, 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);
                $bytes /= (1 << ($pow * 10));
                return round($bytes, $precision) . ' ' . $units[$pow];
            }
        }
    ?>

    <div class="card shadow-sm p-4">
        <h5 class="card-title mb-3 text-secondary"><i class="fas fa-list-alt mr-2"></i>Yüklenmiş Dosyalar</h5>
        
        <?php if (!empty($dosya_listesi)): ?>
            <div class="table-responsive">
                <table id="danisandosyalaritablosu1" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                    <tr>
                        <th>Dosya Adı</th>
                        <th>Türü</th>
                        <th>Yüklenme Tarihi</th>
                        <th>Boyutu</th>
                        <th class="text-center">İşlem</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($dosya_listesi as $dosya): 
                        // Verileri güvenli bir şekilde alma ve varsayılan değer atama
                        $dosya_adi = htmlspecialchars($dosya['ad'] ?? 'Bilinmeyen Dosya');
                        $dosya_yolu = $dosya['yol'] ?? '#'; // Örn: dosyalar/musteri_dosyalari/123/dosya_ad.pdf
                        $benzersiz_ad = $dosya['benzersiz_ad'] ?? ''; 

                        $uzanti = pathinfo($dosya_adi, PATHINFO_EXTENSION);
                        $boyut = formatBytes($dosya['boyut'] ?? 0);

                        // Yükleme tarihini benzersiz addan çekme
                        $timestamp_str = explode('_', $benzersiz_ad)[0];
                        $yuklenme_tarihi = is_numeric($timestamp_str) ? date('d.m.Y H:i', (int)$timestamp_str) : 'Bilinmiyor';
                    ?>

                    <tr>
                        <td class="align-middle" data-sort="<?= $dosya_adi ?>"><?= $dosya_adi ?></td>
                        <td class="align-middle text-center" data-sort="<?= strtoupper($uzanti) ?>"><span class="badge bg-info text-dark"><?= strtoupper($uzanti) ?></span></td>
                        <td class="align-middle" data-sort="<?= $timestamp_str ?? 0 ?>"><?= $yuklenme_tarihi ?></td>
                        <td class="align-middle" data-sort="<?= $dosya['boyut'] ?? 0 ?>"><?= $boyut ?></td>
                        <td class="align-middle text-center">    
                            <div class="d-flex justify-content-center gap-1">
                                
                                <a href="../<?= htmlspecialchars($dosya_yolu) ?>" target="_blank" class="btn btn-info btn-sm" title="Görüntüle">
                                    <i class="fa fa-eye"></i>
                                </a>
                                -
                                <a href="../<?= htmlspecialchars($dosya_yolu) ?>" download="<?= $dosya_adi ?>" class="btn btn-success btn-sm" title="İndir">
                                    <i class="fas fa-download"></i>
                                </a>
                                -
                                <button type="button" class="btn btn-danger btn-sm" title="Sil" 
                                    data-toggle="modal" 
                                    data-target="#dosyaSilModal" 
                                    data-dosyayolu="<?= htmlspecialchars($dosya_yolu) ?>" 
                                    data-danisanid="<?= htmlspecialchars($danisan_id ?? '') ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
                // Sayfa yüklenir yüklenmez DataTable'ı etkinleştirmemek, sadece sekme görünür olduğunda etkinleştirmek takılmayı önleyebilir.
                // Örneğin: $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) { ... DataTable'ı burada başlatın ... });
            </script>

        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                <i class="fas fa-info-circle mr-2"></i>Bu danışana ait yüklenmiş **dosya bulunmamaktadır**. Yeni bir dosya yükleyebilirsiniz.
            </div>
        <?php endif; ?>

    </div>
</div>


                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->




  <!-- Danışan Kaydı Silme Sorusu Modal-->
  <div class="modal fade" id="danisanSilSorusuModal" tabindex="-1" role="dialog" aria-labelledby="danisanSilSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="danisanSilSorusuModal">Danışan Kaydı Silinecek</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Danışan kaydını silmek istediğinize emin misiniz?</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
            
            <a class="btn btn-danger" href="islemler/islem.php?danisansil=ok&danisan_id=<?php echo $danisanveri['danisan_id']; ?>">Sil</a>
          
          </div>
      </div>
    </div>
  </div>
  <!-- Danışan Kaydı Silme Sorusu Modal-->

   <!-- smsGonderilsinMiSorusuModal-->
  <div class="modal fade" id="smsGonderilsinMiSorusuModal" tabindex="-1" role="dialog" aria-labelledby="smsGonderilsinMiSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="smsGonderilsinMiSorusuModal">SMS Gönderimi</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <textarea class="form-control" rows="5" placeholder="Mesaj yazın..." style="margin-top: 10px; margin-bottom: 5px; height: 82px;"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
          <a class="btn btn-success" href="#">Gönder</a>
        </div>
      </div>
    </div>
  </div>
  <!-- smsGonderilsinMiSorusuModal-->
  
   <!-- mailGonderilsinMiSorusuModal-->
  <div class="modal fade" id="mailGonderilsinMiSorusuModal" tabindex="-1" role="dialog" aria-labelledby="mailGonderilsinMiSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="mailGonderilsinMiSorusuModal">Mail Gönderimi</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <textarea class="form-control" rows="5" placeholder="Mesaj yazın..." style="margin-top: 10px; margin-bottom: 5px; height: 82px;"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
          <a class="btn btn-success" href="#">Gönder</a>
        </div>
      </div>
    </div>
  </div>
  <!-- mailGonderilsinMiSorusuModal-->

  <!-- whatsappMesajiGonderilsinMiSorusuModal-->
  <div class="modal fade" id="whatsappMesajiGonderilsinMiSorusuModal" tabindex="-1" role="dialog" aria-labelledby="whatsappMesajiGonderilsinMiSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="whatsappMesajiGonderilsinMiSorusuModal">WhatsApp Mesajı Gönderimi</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <textarea class="form-control" rows="5" placeholder="Mesaj yazın..." style="margin-top: 10px; margin-bottom: 5px; height: 82px;"></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
          <a class="btn btn-success" href="#">Gönder</a>
        </div>
      </div>
    </div>
  </div>
  <!-- whatsappMesajiGonderilsinMiSorusuModal-->

<?php include 'footer.php'; ?>