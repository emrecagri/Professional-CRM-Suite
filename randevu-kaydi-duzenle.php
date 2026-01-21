<?php include 'header.php'; ?>

<?php
if (isset($_POST['randevu_id'])) {
  $randevusorgu=$db->prepare("SELECT * FROM randevular where randevu_id=:randevu_id");
  $randevusorgu->execute(array(
    'randevu_id' => $_POST['randevu_id']
  ));
  $randevuveri=$randevusorgu->fetch(PDO::FETCH_ASSOC);

} else {
  header("Location: randevular.php");
} 



$danisansorgu=$db->prepare("SELECT * FROM danisanlar WHERE danisan_id=:danisan_id");
$danisansorgu->execute(array(
  'danisan_id' => $randevuveri['danisan_id'] ?? 0
));
$danisanveri=$danisansorgu->fetch(PDO::FETCH_ASSOC);

?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Randevu Kayıt Düzenleme</h1>
          </div><div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Randevu Kayıt Düzenleme</li>
            </ol>
          </div></div></div></div>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="dist/img/avatar2.png" alt="Kullanıcı Profil Resmi">
                </div>

                <h3 class="profile-username text-center">Sn. 
                    
                <?php 

                echo ($danisanveri['ad'] ?? '')." ".($danisanveri['soyad'] ?? ''); 
                
                ?>
                
                </h3>

                <p class="text-muted text-center">ile randevu bilgileri</p>

                <ul class="list-group list-group-unbordered mb-3">

                  <li class="list-group-item">
                    <b>Danışan No</b> <a class="float-right"><?php echo $danisanveri['danisan_id'] ?? ''; ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>İlk Kayıt Tarihi</b> <a class="float-right"><?php echo $danisanveri['kayit_tarihi'] ?? ''; ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>Telefon</b> <a class="float-right"><?php echo $danisanveri['telefon'] ?? ''; ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>E-Posta</b> <a class="float-right"><?php echo $danisanveri['eposta'] ?? ''; ?></a>
                  </li>

                  
                </ul>

                <a href="#" data-toggle="modal" data-target="#randevuMailGonderilsinMiSorusuModal" class="btn btn-info btn-block"><b>Randevu Bilgisini Mail Gönder</b></a>
                <a href="#" data-toggle="modal" data-target="#randevuWhatsappMesajiGonderilsinMiSorusuModal" class="btn btn-success btn-block"><b>Randevu Bilgisini WhatsApp'a Gönder</b></a>
                <a href="#" data-toggle="modal" data-target="#randevuSilSorusuModal" class="btn btn-danger btn-block"><b>Randevu Kaydını Sil</b></a>
              </div>
              </div>
            </div>
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">

                  <li class="nav-item"><a class="nav-link active" href="#randevubilgileri" data-toggle="tab">Randevu Bilgileri</a></li>

                </ul>
              </div><div class="card-body">
                <div class="tab-content">
                  
                  
                  
                  

                  <div class="tab-pane active" id="randevubilgileri">
                    <form class="form-horizontal" action="islemler/islem.php" method="POST" enctype="multipart/form-data" data-parsley-validate>
                      
                      <div class="form-group row">
                        <label for="randevu_tarihi" class="col-sm-2 col-form-label">Randevu Tarihi</label>
                        <div class="col-sm-10">
                          <input type="datetime-local" class="form-control" id="randevu_tarihi" name="randevu_tarihi" value="<?php echo $randevuveri['randevu_tarihi'] ?? ''; ?>" >
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="randevu_suresi" class="col-sm-2 col-form-label">Randevu Süresi</label>
                        <div class="input-group col-sm-10"">
                          <input type="number" class="form-control" id="randevu_suresi" name="randevu_suresi" placeholder="Süre Belirlenmedi"
                                value="<?php echo htmlspecialchars($randevuveri['randevu_suresi'] ?? '', ENT_QUOTES); ?>" min="1">
                          <div class="input-group-append">
                            <span class="input-group-text">dakika</span>
                          </div>
                        </div>
                      </div>


                      <div class="form-group row">
                        <label for="randevu_tarihi" class="col-sm-2 col-form-label">Randevu Durumu</label>
                        <div class="col-sm-10">
                          <select class="form-control" style="width: 100%;" id="randevu_durumu" name="randevu_durumu">
                            <option value="Onaylanmadı" <?php echo (($randevuveri['randevu_durumu'] ?? '') == 'Onaylanmadı') ? 'selected' : ''; ?>>Onaylanmadı</option>
                            <option value="Onaylandı" <?php echo (($randevuveri['randevu_durumu'] ?? '') == 'Onaylandı') ? 'selected' : ''; ?>>Onaylandı</option>
                            <option value="Yapıldı" <?php echo (($randevuveri['randevu_durumu'] ?? '') == 'Yapıldı') ? 'selected' : ''; ?>>Yapıldı</option>
                            <option value="İptal Edildi" <?php echo (($randevuveri['randevu_durumu'] ?? '') == 'İptal Edildi') ? 'selected' : ''; ?>>İptal Edildi</option>
                          </select>
                        </div>
                      </div>


                      <div class="form-group row">
                        <label for="randevu_ucreti" class="col-sm-2 col-form-label">Randevu Ücreti</label>
                        <div class="input-group col-sm-10"">
                          <input type="number" class="form-control" id="randevu_ucreti" name="randevu_ucreti"
                                value="<?php echo htmlspecialchars($randevuveri['randevu_ucreti'] ?? '', ENT_QUOTES); ?>" placeholder="Ücret Belirlenmedi" step="0.01" min="0">
                          <div class="input-group-append">
                            <span class="input-group-text">₺</span>
                          </div>
                        </div>
                      </div>


                      <div class="form-group row">
                        <label for="randevu_notu" class="col-sm-2 col-form-label">Not</label>
                        <div class="col-sm-10">
                        <textarea id="summernote" name="randevu_notu" style="display: none;"><?php echo $randevuveri['randevu_notu'] ?? ''; ?></textarea>
                        </div>
                      </div>


                      <input type="hidden" id="randevu_id" name="randevu_id" value="<?php echo $randevuveri['randevu_id'] ?? ''; ?>">

                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" name="randevu_duzenle" class="btn btn-success">Kaydet</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  </div>
                </div></div>
            </div>
          </div>
        </div></section>
    </div>
  <div class="modal fade" id="randevuSilSorusuModal" tabindex="-1" role="dialog" aria-labelledby="randevuSilSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="randevuSilSorusuModal">Randevu Kaydı Silinecek</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Randevu kaydını silmek istediğinize emin misiniz?</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
            
            <a class="btn btn-danger" href="islemler/islem.php?randevusil=ok&randevu_id=<?php echo $randevuveri['randevu_id'] ?? ''; ?>">Sil</a>
          
          </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="randevuMailGonderilsinMiSorusuModal" tabindex="-1" role="dialog" aria-labelledby="randevuMailGonderilsinMiSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="randevuMailGonderilsinMiSorusuModal">Mail Gönderimi</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          Aşağıdaki metin mail olarak gönderilecektir.
          <textarea class="form-control" rows="5" placeholder="Hazır mesaj bulunmamaktadır." style="margin-top: 15px; margin-bottom: 5px; height: 82px;"></textarea>
          Göndermeden önce metinde değişiklik yapabilirsiniz.
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
          <a class="btn btn-success" href="#">Gönder</a>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="randevuWhatsappMesajiGonderilsinMiSorusuModal" tabindex="-1" role="dialog" aria-labelledby="randevuWhatsappMesajiGonderilsinMiSorusuModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="randevuWhatsappMesajiGonderilsinMiSorusuModal">WhatsApp Mesajı Gönderimi</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          Aşağıdaki metin WhatsApp mesajı olarak gönderilecektir.
          <textarea class="form-control" rows="5" placeholder="Hazır mesaj bulunmamaktadır." style="margin-top: 15px; margin-bottom: 5px; height: 82px;"></textarea>
          Göndermeden önce metinde değişiklik yapabilirsiniz.
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Vazgeç</button>
          <a class="btn btn-success" href="#">Gönder</a>
        </div>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>