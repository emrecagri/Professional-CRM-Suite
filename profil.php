<?php include 'header.php'; ?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Profil</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Profil</li>
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
                         src="<?php echo (!empty($diyetisyenveri['diyetisyen_resim'])) ? $diyetisyenveri['diyetisyen_resim'] : 'dist/img/avatar2.png'; ?>"
                         alt="Kullanıcı Profil Resmi"
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>

                <h3 class="profile-username text-center">Sn. <?php echo $diyetisyenveri['diyetisyen_isim']." ".$diyetisyenveri['diyetisyen_soyisim']; ?></h3>

                <p class="text-muted text-center">Danışman</p>

                <ul class="list-group list-group-unbordered mb-3">

                  

                  <li class="list-group-item">
                    <b>Başlangıç Tarihi</b> <a class="float-right"><?php echo $diyetisyenveri['diyetisyen_hesap_baslangic']; ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>Yenileme Tarihi</b> <a class="float-right"><?php echo $diyetisyenveri['diyetisyen_hesap_bitis']; ?></a>
                  </li>

                  <li class="list-group-item">
                    <b>Aktif Paket</b> <a class="float-right"><?php echo $diyetisyenveri['diyetisyen_hesap_paketi']; ?></a>
                  </li>
                  
                  <li class="list-group-item">
                    <b>Destek Talepleri</b> <a class="float-right">12</a>
                  </li>
                  
                </ul>

                <a href="https://panel.loruv.com/" class="btn btn-primary btn-block"><b>Teknik Destek Al</b></a>
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

                  <li class="nav-item"><a class="nav-link active" href="#profilayarlari" data-toggle="tab">Profil Ayarları</a></li>
                  <li class="nav-item"><a class="nav-link" href="#sifreayarlari" data-toggle="tab">Şifre Ayarları</a></li>

                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  
                  
                  
                  

<div class="tab-pane active" id="profilayarlari">
                <form class="form-horizontal" action="islemler/islem.php" method="POST" enctype="multipart/form-data" data-parsley-validate>
                  <div class="form-group row">
                    <label for="diyetisyen_resim" class="col-sm-2 col-form-label">Profil Fotoğrafı</label>
                    <div class="col-sm-10">
                      <input class="form-control-file" id="diyetisyen_resim" name="diyetisyen_resim" type="file">
                      <small class="form-text text-muted">Sadece yeni bir fotoğraf yüklerseniz mevcut fotoğraf değişir.</small>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="diyetisyen_isim" class="col-sm-2 col-form-label">Ad</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="diyetisyen_isim" name="diyetisyen_isim" value="<?php echo $diyetisyenveri['diyetisyen_isim']; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="diyetisyen_soyisim" class="col-sm-2 col-form-label">Soyad</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="diyetisyen_soyisim" name="diyetisyen_soyisim" value="<?php echo $diyetisyenveri['diyetisyen_soyisim']; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="diyetisyen_mail" class="col-sm-2 col-form-label">E-Posta</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="diyetisyen_mail" name="diyetisyen_mail" value="<?php echo $diyetisyenveri['diyetisyen_mail']; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="diyetisyen_tel" class="col-sm-2 col-form-label">Telefon</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="diyetisyen_tel" name="diyetisyen_tel" value="<?php echo $diyetisyenveri['diyetisyen_tel']; ?>">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button type="submit" name="profilguncelle" class="btn btn-success">Kaydet</button>
                    </div>
                  </div>
                </form>
              </div>
<div class="tab-pane" id="sifreayarlari">
                <form class="form-horizontal" action="islemler/islem.php" method="POST" data-parsley-validate>
                  
                  <div class="form-group row">
                    <label for="diyetisyen_sifre" class="col-sm-2 col-form-label">Yeni Şifre</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="diyetisyen_sifre" name="diyetisyen_sifre" type="password">
                      
                      <small class="form-text text-muted mt-2">Şifre Kriterleri:</small>
                      <ul id="sifre-kriterleri" class="list-unstyled small" style="line-height: 1.6;">
                        <li id="kural-uzunluk" class="text-danger">❌ En az 12 karakter</li>
                        <li id="kural-buyuk-harf" class="text-danger">❌ En az 2 büyük harf (A-Z)</li>
                        <li id="kural-kucuk-harf" class="text-danger">❌ En az 2 küçük harf (a-z)</li>
                        <li id="kural-sayi" class="text-danger">❌ En az 2 sayı (0-9)</li>
                        <li id="kural-ozel" class="text-danger">❌ En az 2 özel karakter (!@#...)</li>
                        <li id="kural-ardisik-harf" class="text-danger">❌ Ardışık harf içermemeli (abc, cba)</li>
                        <li id="kural-ardisik-sayi" class="text-danger">❌ Ardışık sayı içermemeli (123, 321)</li>
                        <li id="kural-yil" class="text-danger">❌ Yıl (19xx, 20xx) içermemeli</li>
                      </ul>
                      </div>
                  </div>
                  
                  <div class="form-group row">
                    <label for="diyetisyen_sifre_tekrar" class="col-sm-2 col-form-label">Şifre Tekrar</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="diyetisyen_sifre_tekrar" name="diyetisyen_sifre_tekrar" type="password">
                      <small id="sifre-uyari-mesaji" class="form-text"></small>
                    </div>
                  </div>
                  
                  <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                      <button type="submit" name="sifreguncelle" id="sifre-guncelle-btn" class="btn btn-success" disabled>Şifreyi Güncelle</button>
                    </div>
                  </div>
                </form>

                <script>
                  // Elementleri seç
                  const sifreInput = document.getElementById('diyetisyen_sifre');
                  const sifreTekrarInput = document.getElementById('diyetisyen_sifre_tekrar');
                  const submitButton = document.getElementById('sifre-guncelle-btn');
                  const matchMessage = document.getElementById('sifre-uyari-mesaji');

                  // Kural listesi elementleri
                  const kurallar = {
                    uzunluk: document.getElementById('kural-uzunluk'),
                    buyukHarf: document.getElementById('kural-buyuk-harf'),
                    kucukHarf: document.getElementById('kural-kucuk-harf'),
                    sayi: document.getElementById('kural-sayi'),
                    ozel: document.getElementById('kural-ozel'),
                    ardisikHarf: document.getElementById('kural-ardisik-harf'),
                    ardisikSayi: document.getElementById('kural-ardisik-sayi'),
                    yil: document.getElementById('kural-yil')
                  };

                  // --- YARDIMCI KONTROL FONKSİYONLARI ---

                  // Kural 6: Ardışık harf kontrolü (örn: abc, cba)
                  function checkSequentialLetters(s) {
                    s = s.toLowerCase();
                    for (let i = 0; i < s.length - 2; i++) {
                      let c1 = s.charCodeAt(i);
                      let c2 = s.charCodeAt(i + 1);
                      let c3 = s.charCodeAt(i + 2);
                      if (c1 >= 97 && c1 <= 122) { // Sadece harfleri kontrol et
                        if (c1 + 1 === c2 && c2 + 1 === c3) return false; // abc
                        if (c1 - 1 === c2 && c2 - 1 === c3) return false; // cba
                      }
                    }
                    return true;
                  }

                  // Kural 7: Ardışık sayı kontrolü (örn: 123, 321)
                  function checkSequentialNumbers(s) {
                    for (let i = 0; i < s.length - 2; i++) {
                      let c1 = s.charCodeAt(i);
                      let c2 = s.charCodeAt(i + 1);
                      let c3 = s.charCodeAt(i + 2);
                      if (c1 >= 48 && c1 <= 57) { // Sadece sayıları kontrol et
                        if (c1 + 1 === c2 && c2 + 1 === c3) return false; // 123
                        if (c1 - 1 === c2 && c2 - 1 === c3) return false; // 321
                      }
                    }
                    return true;
                  }

                  // Kural 8: Yıl kontrolü (19xx veya 20xx)
                  function checkContainsYear(s) {
                    return !/(19[0-9]{2}|20[0-9]{2})/.test(s);
                  }

                  // --- KURAL LİSTESİNİ GÜNCELLEME ---
                  function updateRuleUI(element, isValid) {
                    if (isValid) {
                      element.textContent = '✅ ' + element.textContent.substring(2);
                      element.className = 'text-success';
                    } else {
                      element.textContent = '❌ ' + element.textContent.substring(2);
                      element.className = 'text-danger';
                    }
                  }

                  // --- ANA KONTROL FONKSİYONU ---
                  function validatePasswords() {
                    const sifre1 = sifreInput.value;
                    const sifre2 = sifreTekrarInput.value;

                    // 1. ŞİFRE GÜVENLİK KONTROLÜ
                    const checks = {
                      uzunluk: sifre1.length >= 12,
                      buyukHarf: (sifre1.match(/[A-Z]/g) || []).length >= 2,
                      kucukHarf: (sifre1.match(/[a-z]/g) || []).length >= 2,
                      sayi: (sifre1.match(/[0-9]/g) || []).length >= 2,
                      ozel: (sifre1.match(/[!@#$%^&*(),.?":{}|<>_\[\]-]/g) || []).length >= 2,
                      ardisikHarf: checkSequentialLetters(sifre1),
                      ardisikSayi: checkSequentialNumbers(sifre1),
                      yil: checkContainsYear(sifre1)
                    };

                    // UI (Kriter Listesi) güncelle
                    updateRuleUI(kurallar.uzunluk, checks.uzunluk);
                    updateRuleUI(kurallar.buyukHarf, checks.buyukHarf);
                    updateRuleUI(kurallar.kucukHarf, checks.kucukHarf);
                    updateRuleUI(kurallar.sayi, checks.sayi);
                    updateRuleUI(kurallar.ozel, checks.ozel);
                    updateRuleUI(kurallar.ardisikHarf, checks.ardisikHarf);
                    updateRuleUI(kurallar.ardisikSayi, checks.ardisikSayi);
                    updateRuleUI(kurallar.yil, checks.yil);

                    // Tüm kurallar geçerli mi?
                    const isStrong = Object.values(checks).every(Boolean);

                    // 2. ŞİFRE EŞLEŞME KONTROLÜ
                    let isMatched = false;
                    if (isStrong && sifre2.length > 0) {
                      if (sifre1 === sifre2) {
                        matchMessage.textContent = 'Şifreler eşleşti.';
                        matchMessage.className = 'form-text text-success';
                        isMatched = true;
                      } else {
                        matchMessage.textContent = 'Şifreler eşleşmiyor!';
                        matchMessage.className = 'form-text text-danger';
                        isMatched = false;
                      }
                    } else {
                      matchMessage.textContent = ''; // Güçlü değilse veya tekrar alanı boşsa mesajı temizle
                      isMatched = false;
                    }
                    
                    // 3. BUTON KONTROLÜ
                    // Buton sadece şifre güçlüyse VE şifreler eşleşiyorsa aktif olur
                    submitButton.disabled = !(isStrong && isMatched);
                  }

                  // Kullanıcı her tuşa bastığında (veya yapıştırdığında) kontrol et
                  sifreInput.addEventListener('input', validatePasswords);
                  sifreTekrarInput.addEventListener('input', validatePasswords);
                </script>
                </div>
                  <!-- /.tab-pane -->




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

  <!-- sweetalert2 başlangıç -->
  <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <?php
  if (isset($_GET['durum']) && $_GET['durum'] == "290603") {
      echo "
      <script>
          Swal.fire({
          icon: 'error',
          title: 'Hata! Profil Güncellenemedi.',
          text: 'Hata Kodu: 290603',
          showConfirmButton: true,
          confirmButtonText: 'Tamam'
          })
      </script>
      ";
  }
  if (isset($_GET['durum']) && $_GET['durum'] == "726241") {
      echo "
      <script>
          Swal.fire({
          icon: 'success',
          title: 'İşlem Başarılı',
          text: 'Profil Başarıyla Güncellendi.',
          showConfirmButton: true,
          confirmButtonText: 'Tamam'
          })
      </script>
      ";
  }
  ?>
  <!-- sweetalert2 bitiş -->

<?php include 'footer.php'; ?>