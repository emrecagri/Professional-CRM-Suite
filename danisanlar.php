<?php include 'header.php'; ?>

<?php
/**
 * Yardımcı Fonksiyon: Dosya Boyutu Formatlama
 * Bu fonksiyonu döngü dışında tanımladık ki "Cannot redeclare" hatası almayalım.
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        if ($bytes <= 0) return '0 B'; 
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
?>
  
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Danışanlar</h1>
          </div><div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Danışanlar</li>
            </ol>
          </div></div></div></div>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <table id="danisanlartablosu1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Danışan No</th>
                    <th>Fotoğraf</th>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>Telefon</th>
                    <th>E-Posta</th>
                    <th>Dosya Sayısı</th>
                    <th>Kayıt Tarihi</th>
                    <th>İşlem</th>
                  </tr>
                  </thead>
                  <tbody>

                  <?php 
                    $say=0;
                    $danisansorgusu=$db->prepare("SELECT * FROM danisanlar WHERE kullanici_id = :kullanici_id ORDER BY danisan_id DESC");
                    $danisansorgusu->execute(array(':kullanici_id' => $_SESSION['kullanici_id']));

                    while ($danisanverileri=$danisansorgusu->fetch(PDO::FETCH_ASSOC)) { $say++;
                  ?>

                    <tr>
                      <td><?php echo $danisanverileri['danisan_id']; ?></td>
                      <td>
                        <img class="profile-user-img img-fluid img-circle"
                          src="<?php echo (!empty($danisanverileri['musteri_profil_fotografi'])) ? $danisanverileri['musteri_profil_fotografi'] : 'dist/img/avatar2.png'; ?>"
                          alt="Kullanıcı Profil Resmi"
                          style="width: 50px; height: 50px; object-fit: contain;">
                      </td>
                      <td><?php echo $danisanverileri['ad']; ?></td>
                      <td><?php echo $danisanverileri['soyad']; ?></td>
                      <td><?php echo $danisanverileri['telefon']; ?></td>
                      <td><?php echo $danisanverileri['eposta']; ?></td>
                      <td>
                        <?php
                        // --- HATA DÜZELTME BAŞLANGICI ---
                        
                        // 1. Veritabanından veriyi al
                        $jsonString = $danisanverileri['dosya_listesi'];
                        
                        // 2. Eğer veri NULL veya boş ise json_decode'a sokma, boş dizi ata
                        if (!empty($jsonString)) {
                            $dosyalar = json_decode($jsonString);
                        } else {
                            $dosyalar = []; // Boş ise boş dizi olsun
                        }
                        
                        // --- HATA DÜZELTME BİTİŞİ ---
                        
                        $dosyaSayisi = 0;
                        $toplamBoyut = 0;
                        
                        if (is_array($dosyalar) && !empty($dosyalar)) {
                            $dosyaSayisi = count($dosyalar);
                            foreach ($dosyalar as $dosya) {
                                if (isset($dosya->boyut)) {
                                    $toplamBoyut += (int)$dosya->boyut;
                                }
                            }
                        }
                        
                        $formatliBoyut = formatBytes($toplamBoyut);
                        echo $dosyaSayisi . " Dosya (" . $formatliBoyut . ")";
                        ?>
                      </td>
                      <td><?php echo $danisanverileri['kayit_tarihi']; ?></td>
                      <td>    
                        <div class="d-flex justify-content-left">
                          <form action="danisan-kaydi-duzenle.php" method="POST">
                            <input type="hidden" name="danisan_id" value="<?php echo $danisanverileri['danisan_id']; ?>">
                            <button type="submit" class="btn btn-success btn-sm btn-icon-split">
                              <span class="icon text-white-60">
                                <i class="fas fa-edit"></i>
                              </span>
                            </button>
                          </form>

                          <div class="modal fade" id="<?php echo $danisanverileri['danisan_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $danisanverileri['danisan_id']; ?>Label" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="<?php echo $danisanverileri['danisan_id']; ?>Label"><?php echo $danisanverileri['ad']; ?></h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                <p>Danışan kaydını silmek istiyor musunuz?</p>
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
                  
                  <?php } ?>

                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Danışan No</th>
                      <th>Fotoğraf</th>
                      <th>Ad</th>
                      <th>Soyad</th>
                      <th>Telefon</th>
                      <th>E-Posta</th>
                      <th>Dosya Sayısı</th>
                      <th>Kayıt Tarihi</th>
                      <th>İşlem</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  <script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <?php
  if (isset($_GET['durum'])) {
      if ($_GET['durum'] == "674062") {
          echo "<script>Swal.fire({icon: 'error', title: 'Hata!', text: 'Güncellenemedi.', showConfirmButton: true})</script>";
      }
      elseif ($_GET['durum'] == "703714") {
          echo "<script>Swal.fire({icon: 'success', title: 'Başarılı', text: 'Güncellendi.', showConfirmButton: true})</script>";
      }
      elseif ($_GET['durum'] == "905362") {
        echo "<script>Swal.fire({icon: 'success', title: 'Başarılı', text: 'Kaydedildi.', showConfirmButton: true})</script>";
      }
      elseif ($_GET['durum'] == "750372") {
        echo "<script>Swal.fire({icon: 'error', title: 'Hata!', text: 'Kaydedilemedi.', showConfirmButton: true})</script>";
      }
  }
  ?>
  <?php include 'footer.php'; ?>