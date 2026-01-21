 <?php
// takvim için

require_once __DIR__ . '/islemler/baglan.php'; // $db = new PDO(...)

if (isset($_GET['feed'])) {
  header('Content-Type: application/json; charset=utf-8');
  try {
    $kullaniciId = isset($_GET['kullanici_id']) ? (int)$_GET['kullanici_id'] : 0;

    // Randevu + Danışan bilgileri
    $sql = "
      SELECT
        r.randevu_id                            AS id,
        r.kullanici_id,
        r.danisan_id,
        r.randevu_tarihi                        AS start_dt,
        COALESCE(NULLIF(r.randevu_suresi,0),30) AS sure_dk,
        r.randevu_durumu,
        r.randevu_notu,
        d.ad        AS danisan_ad,
        d.soyad     AS danisan_soyad,
        d.telefon   AS danisan_telefon,
        d.eposta    AS danisan_eposta
      FROM randevular r
      LEFT JOIN danisanlar d ON d.danisan_id = r.danisan_id
      " . ($kullaniciId ? "WHERE r.kullanici_id = :kid" : "") . "
      ORDER BY r.randevu_tarihi DESC
    ";

    $st = $db->prepare($sql);
    if ($kullaniciId) $st->bindValue(':kid', $kullaniciId, PDO::PARAM_INT);
    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $renkHaritasi = [
      'Onaylandı'     => ['#00a65a', '#00a65a'],
      'Onaylanmadı'   => ['#f56954', '#f56954'],
      'Beklemede'     => ['#f39c12', '#f39c12'],
      'Yapıldı'       => ['#0073b7', '#0073b7'],
      'İptal Edildi'  => ['#6c757d', '#6c757d'],
    ];

    $events = [];
    foreach ($rows as $r) {
      if (empty($r['start_dt'])) continue;
      $ts = strtotime($r['start_dt']);
      if ($ts === false) continue;

      // Başlık: Danışan Ad Soyad
      $adsoyad = trim(($r['danisan_ad'] ?? '').' '.($r['danisan_soyad'] ?? ''));
      $title   = $adsoyad !== '' ? $adsoyad : 'Danışan';

      $bg = $renkHaritasi[$r['randevu_durumu']][0] ?? '#3c8dbc';
      $bd = $renkHaritasi[$r['randevu_durumu']][1] ?? '#3c8dbc';

      $events[] = [
        'id'              => (string)$r['id'],
        'title'           => $title,
        'start'           => date('c', $ts),
        'end'             => date('c', strtotime('+'.(int)$r['sure_dk'].' minutes', $ts)),
        'allDay'          => false,
        'backgroundColor' => $bg,
        'borderColor'     => $bd,
        'extendedProps'   => [
          // İSTENMEDİ: 'kullanici_id' veya 'danisan_id' göndermiyoruz
          'durum'      => $r['randevu_durumu'],
          'not'        => $r['randevu_notu'],
          'sure_dk'    => (int)$r['sure_dk'],
          'adsoyad'    => $adsoyad,
          'telefon'    => $r['danisan_telefon'] ?? null,
          'eposta'     => $r['danisan_eposta'] ?? null,
          'start_raw'  => $r['start_dt'],
        ],
      ];
    }

    echo json_encode($events, JSON_UNESCAPED_UNICODE);
  } catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Beklenmeyen bir hata oluştu.']);
  }
  exit;
}
?>

<!-- FullCalendar CSS -->
<link rel="stylesheet" href="plugins/fullcalendar/main.min.css">
<!-- FullCalendar Bootstrap tema eklentisi CSS (opsiyonel ama tema için önerilir) -->
<link rel="stylesheet" href="plugins/fullcalendar/bootstrap/main.min.css">

<!-- FullCalendar JS -->
<script src="plugins/fullcalendar/main.min.js"></script>
<script src="plugins/fullcalendar/locales-all.min.js"></script>
<!-- FullCalendar Bootstrap tema eklentisi JS -->
<script src="plugins/fullcalendar/bootstrap/main.min.js"></script>

<?php include 'header.php'; ?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Genel Panel</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Genel Panel</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>2</h3>
                <p>Mesajlar</p>
              </div>
              <div class="icon"><i class="far fa-envelope"></i></div>
              <a href="mesajlar.php" class="small-box-footer">Mesajlar Gör <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <?php
                  $danisansayisisorgu = $db->prepare("SELECT 1 FROM danisanlar WHERE kullanici_id = :kullanici_id");
                  $danisansayisisorgu->bindParam(':kullanici_id', $_SESSION['kullanici_id']);
                  $danisansayisisorgu->execute();
                  $danisansayisi = $danisansayisisorgu->rowCount();
                ?>
                <h3><?php echo (int)$danisansayisi; ?></h3>
                <p>Danışan</p>
              </div>
              <div class="icon"><i class="fa-regular fa-address-card"></i></div>
              <a href="danisanlar.php" class="small-box-footer">Danışanlar Gör <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <?php
                  $danisansayisisorgu = $db->prepare("SELECT 1 FROM randevular WHERE kullanici_id = :kullanici_id");
                  $danisansayisisorgu->bindParam(':kullanici_id', $_SESSION['kullanici_id']);
                  $danisansayisisorgu->execute();
                  $danisansayisi = $danisansayisisorgu->rowCount();
                ?>
                <h3><?php echo (int)$danisansayisi; ?></h3>
                <p>Randevu</p>
              </div>
              <div class="icon"><i class="far fa-clock"></i></div>
              <a href="randevular.php" class="small-box-footer">Randevuları Gör <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <?php
                  $danisansayisisorgu = $db->prepare("SELECT 1 FROM yapilacaklar WHERE kullanici_id = :kullanici_id");
                  $danisansayisisorgu->bindParam(':kullanici_id', $_SESSION['kullanici_id']);
                  $danisansayisisorgu->execute();
                  $danisansayisi = $danisansayisisorgu->rowCount();
                ?>
                <h3><?php echo (int)$danisansayisi; ?></h3>
                <p>Yapılacaklar</p>
              </div>
              <div class="icon"><i class="fa-solid fa-clipboard-check"></i></div>
              <a href="#" class="small-box-footer">Yapılacakları Gör <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">


            <!-- TAKVİM KARTI -->
            <div class="card">
              <div class="card-header ui-sortable-handle" style="cursor: move;">
                <h3 class="card-title"><i class="far fa-calendar-alt"></i> Takvim</h3>
                <div class="card-tools">
                  <div class="btn-group">
                    <button id="btnYeniRandevu" type="button" class="btn btn-primary btn-sm">
                      <i class="fas fa-plus"></i> Yeni Randevu
                    </button>
                    <button id="btnTamEkran" type="button" class="btn btn-outline-secondary btn-sm">
                      <i class="fas fa-expand"></i> Tam Ekran
                    </button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="calendar"></div>
              </div>
            </div>

          </section>


          <!-- right col -->
          <section class="col-lg-5 connectedSortable">

            <div class="card bg-gradient-success">
              <div class="card-header border-0">
                <h3 class="card-title"><i class="far fa-calendar-alt"></i> Yaklaşan Randevular</h3>
                <div class="card-tools">
                  <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                      <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                      <a href="#" class="dropdown-item">Yeni randevu oluştur</a>
                      <div class="dropdown-divider"></div>
                      <a href="#" class="dropdown-item">Tüm randevuları gör</a>
                    </div>
                  </div>
                  <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body pt-0">

                <?php
                date_default_timezone_set('Europe/Istanbul');
                $start = (new DateTime('today'))->format('Y-m-d 00:00:00');

                $sql = "
                SELECT
                  r.randevu_id,
                  r.randevu_tarihi,
                  r.randevu_durumu,
                  r.randevu_suresi,
                  d.ad,
                  d.soyad
                FROM randevular r
                LEFT JOIN danisanlar d ON d.danisan_id = r.danisan_id
                WHERE r.randevu_tarihi >= :start
                  AND r.randevu_durumu NOT IN ('Yapıldı','İptal Edildi')
                ORDER BY r.randevu_tarihi ASC
                LIMIT 50
                ";
                $stmt = $db->prepare($sql);
                $stmt->execute([':start' => $start]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                function tr_tarih_gunlu(DateTime $dt): string {
                  static $ay  = [1=>'Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
                  static $gun = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];
                  return (int)$dt->format('j') . ' ' . $ay[(int)$dt->format('n')] . ' ' . $gun[(int)$dt->format('w')];
                }
                function onayMetni(string $durum): string {
                  if ($durum === 'Onaylandı')   return 'onaylı';
                  if ($durum === 'Onaylanmadı') return 'onaysız';
                  return mb_strtolower($durum);
                }

                $bugun   = new DateTime('today');
                $yarin   = new DateTime('tomorrow');
                $mesajlar = [];
                $bugunVar = false;

                foreach ($rows as $r) {
                  $dt   = new DateTime($r['randevu_tarihi']);
                  $saat = $dt->format('H:i');
                  $sure = (int)$r['randevu_suresi'];
                  $onay = onayMetni($r['randevu_durumu']);
                  $adsoyad = trim(($r['ad'] ?? '').' '.($r['soyad'] ?? ''));

                  if ($dt->format('Y-m-d') === $bugun->format('Y-m-d')) {
                    $bugunVar = true;
                    $mesajlar[] = "Bugün saat {$saat}'te {$adsoyad} ile {$sure}dk'lık {$onay} randevunuz var.";
                  } elseif ($dt->format('Y-m-d') === $yarin->format('Y-m-d')) {
                    $mesajlar[] = "Yarın saat {$saat}'te {$adsoyad} ile {$sure}dk'lık {$onay} randevunuz var.";
                  } else {
                    $mesajlar[] = tr_tarih_gunlu($dt) . " günü saat {$saat}'te {$adsoyad} ile {$sure}dk'lık randevunuz var.";
                  }
                }

                if (!$bugunVar) {
                  array_unshift($mesajlar, "<p>Bugün için randevunuz yok.</p>");
                }

                foreach ($mesajlar as $m) {
                  echo "<p>{$m}</p>";
                }
                ?>

              </div>
            </div>





            <!-- TO DO List -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="far fa-list-alt"></i> Yapılacaklar Listesi</h3>
                <div class="card-tools">
                  <ul class="pagination pagination-sm">
                    <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                    <li class="page-item"><a href="#" class="page-link">3</a></li>
                    <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                  </ul>
                </div>
              </div>
              <div class="card-body">

                <?php
                $sorgu = $db->prepare("
                  SELECT id, kullanici_id, baslik, aciklama, bitis_tarihi, oncelik, durum, siralama
                  FROM yapilacaklar
                  WHERE silinme_tarihi IS NULL
                  ORDER BY siralama ASC, id DESC
                ");
                $sorgu->execute();
                $gorevler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

                function insan_okunur_sure_php(?string $bitis_tarihi): string {
                  if (!$bitis_tarihi) return '';
                  try { $simdi = new DateTime(); $bitis = new DateTime($bitis_tarihi); } catch (Exception $e) { return ''; }
                  $fark_saniye = $bitis->getTimestamp() - $simdi->getTimestamp();
                  $mutlak = abs($fark_saniye);
                  if ($mutlak < 3600) {
                    $dakika = max(1, (int)round($mutlak/60));
                    return $fark_saniye >= 0 ? "$dakika dakika" : "$dakika dakika önce";
                  } elseif ($mutlak < 86400) {
                    $saat = max(1, (int)round($mutlak/3600));
                    return $fark_saniye >= 0 ? "$saat saat" : "$saat saat önce";
                  } else {
                    $gun = max(1, (int)round($mutlak/86400));
                    return $fark_saniye >= 0 ? "$gun gün" : "$gun gün önce";
                  }
                }

                function rozet_sinifi_php(array $g): string {
                  if (($g['durum'] ?? '') === 'tamamlandi') return 'badge badge-secondary';
                  switch ((int)$g['oncelik']) {
                    case 2: return 'badge badge-danger';
                    case 1: return 'badge badge-warning';
                    default: return 'badge badge-success';
                  }
                }
                ?>

                <ul class="todo-list" data-widget="todo-list">
                <?php foreach ($gorevler as $g):
                  $g_id        = (int)$g['id'];
                  $metin       = htmlspecialchars($g['baslik'] ?? '');
                  $aciklama    = $g['aciklama'] ? nl2br(htmlspecialchars($g['aciklama'])) : '';
                  $isaretli    = ($g['durum'] ?? '') === 'tamamlandi' ? 'checked' : '';
                  $yazi_sinifi = ($g['durum'] ?? '') === 'tamamlandi' ? 'text completed' : 'text';
                  $rozet_sinifi= rozet_sinifi_php($g);
                  $sure_metin  = !empty($g['bitis_tarihi']) ? insan_okunur_sure_php($g['bitis_tarihi']) : '';
                ?>
                  <li data-id="<?= $g_id ?>"
                      data-oncelik="<?= (int)$g['oncelik'] ?>"
                      data-bitis="<?= htmlspecialchars($g['bitis_tarihi'] ?? '') ?>"
                      data-durum="<?= htmlspecialchars($g['durum'] ?? '') ?>">
                    <span class="handle"><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i></span>

                    <div class="icheck-primary d-inline ml-2">
                      <input type="checkbox" id="todoCheck<?= $g_id ?>" value="<?= $g_id ?>" <?= $isaretli ?>>
                      <label for="todoCheck<?= $g_id ?>"></label>
                    </div>

                    <span class="<?= $yazi_sinifi ?>"><?= $metin ?></span>

                    <?php if ($sure_metin): ?>
                      <small class="<?= $rozet_sinifi ?>"><i class="far fa-clock"></i> <?= $sure_metin ?></small>
                    <?php elseif (($g['durum'] ?? '') === 'tamamlandi'): ?>
                      <small class="badge badge-secondary"><i class="far fa-check-circle"></i> Tamamlandı</small>
                    <?php endif; ?>

                    <div class="tools">
                      <button type="button"
                              class="btn btn-sm btn-link p-0"
                              data-toggle="modal"
                              data-target="#gorevGuncelleModal"
                              data-id="<?= $g_id ?>"
                              data-baslik="<?= htmlspecialchars($g['baslik'] ?? '') ?>"
                              data-aciklama="<?= htmlspecialchars($g['aciklama'] ?? '') ?>"
                              data-bitis="<?= htmlspecialchars($g['bitis_tarihi'] ?? '') ?>"
                              data-oncelik="<?= (int)$g['oncelik'] ?>">
                        <i class="fas fa-edit" title="Düzenle" style="color:#6c757d"></i>
                      </button>
                      |
                      <form action="islemler/islem.php" method="POST" style="display:inline" onsubmit="return confirm('Silinsin mi?')">
                        <input type="hidden" name="gorev_id" value="<?= $g_id ?>">
                        <button type="submit" name="gorev_sil"
                                style="background:none;border:0;padding:0;margin:0;cursor:pointer">
                          <i class="fas fa-trash" title="Sil" style="color:#6c757d"></i>
                        </button>
                      </form>
                    </div>

                    <?php if ($aciklama): ?>
                      <div style="margin-top:6px;padding-left:56px;color:#6c757d;font-size:.9em;"><?= $aciklama ?></div>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
                </ul>

              </div>
              <div class="card-footer clearfix">
                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#gorevEkleModal">
                  <i class="fas fa-plus"></i> Ekle
                </button>
              </div>
            </div>

            <!-- gorevEkleModal (TEK KEZ) -->
            <div class="modal fade" id="gorevEkleModal" tabindex="-1" role="dialog" aria-labelledby="gorevEkleBaslik" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <form action="islemler/islem.php" method="POST">
                    <div class="modal-header">
                      <h5 class="modal-title" id="gorevEkleBaslik">Yeni Görev Ekle</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label for="baslik">Başlık</label>
                        <input type="text" class="form-control" id="baslik" name="baslik" required>
                      </div>
                      <div class="form-group">
                        <label for="aciklama">Açıklama</label>
                        <textarea class="form-control" id="aciklama" name="aciklama"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="bitis_tarihi">Bitiş Tarihi</label>
                        <input type="datetime-local" class="form-control" id="bitis_tarihi" name="bitis_tarihi">
                      </div>
                      <div class="form-group">
                        <label for="oncelik">Öncelik</label>
                        <select class="form-control" id="oncelik" name="oncelik">
                          <option value="0">Normal</option>
                          <option value="1">Önemli</option>
                          <option value="2">Acil</option>
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                      <button type="submit" class="btn btn-primary" name="gorev_ekle">Kaydet</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </section>
        </div>
      </div>
    </section>
  </div>



<!-- Görev Güncelle Modal (TEK KEZ) -->
<div class="modal fade" id="gorevGuncelleModal" tabindex="-1" role="dialog" aria-labelledby="gorevGuncelleBaslik" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="islemler/islem.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="gorevGuncelleBaslik">Görev Güncelle</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="gorev_id" id="guncelle_gorev_id">
          <div class="form-group">
            <label for="guncelle_baslik">Başlık</label>
            <input type="text" class="form-control" id="guncelle_baslik" name="baslik" required>
          </div>
          <div class="form-group">
            <label for="guncelle_aciklama">Açıklama</label>
            <textarea class="form-control" id="guncelle_aciklama" name="aciklama"></textarea>
          </div>
          <div class="form-group">
            <label for="guncelle_bitis">Bitiş Tarihi</label>
            <input type="datetime-local" class="form-control" id="guncelle_bitis" name="bitis_tarihi">
          </div>
          <div class="form-group">
            <label for="guncelle_oncelik">Öncelik</label>
            <select class="form-control" id="guncelle_oncelik" name="oncelik">
              <option value="0">Normal</option>
              <option value="1">Önemli</option>
              <option value="2">Acil</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-primary" name="gorev_guncelle">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Event Detay Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailBaslik" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventDetailBaslik">Randevu Detayı</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Danışan</dt><dd class="col-sm-8" id="ev_adsoyad">-</dd>
          <dt class="col-sm-4">Tarih</dt><dd class="col-sm-8" id="ev_tarih">-</dd>
          <dt class="col-sm-4">Saat</dt><dd class="col-sm-8" id="ev_saat">-</dd>
          <dt class="col-sm-4">Süre</dt><dd class="col-sm-8" id="ev_sure">-</dd>
          <dt class="col-sm-4">Durum</dt><dd class="col-sm-8" id="ev_durum"><span class="badge badge-secondary">-</span></dd>
          <dt class="col-sm-4">Telefon</dt><dd class="col-sm-8" id="ev_tel">-</dd>
          <dt class="col-sm-4">E-posta</dt><dd class="col-sm-8" id="ev_eposta">-</dd>
          <!--<dt class="col-sm-4">Not</dt><dd class="col-sm-8" id="ev_not">-</dd>-->
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
<script>
// Görev güncelle modalini TEK modal üzerinden besle
$('#gorevGuncelleModal').on('show.bs.modal', function (event) {
  var buton = $(event.relatedTarget);
  var id        = buton.data('id');
  var baslik    = buton.data('baslik') || '';
  var aciklama  = buton.data('aciklama') || '';
  var bitis     = buton.data('bitis') || '';
  var oncelik   = buton.data('oncelik');

  var modal = $(this);
  modal.find('#guncelle_gorev_id').val(id);
  modal.find('#guncelle_baslik').val(baslik);
  modal.find('#guncelle_aciklama').val(aciklama);
  modal.find('#guncelle_bitis').val(bitis ? String(bitis).replace(' ','T') : '');
  modal.find('#guncelle_oncelik').val(oncelik);
});

// TAKVİM
document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');
  var Calendar = FullCalendar.Calendar;

  const feedUrl = window.location.pathname + '?feed=1';

  function trTarihSaat(d) {
    const tarih = d.toLocaleDateString('tr-TR', { year:'numeric', month:'long', day:'numeric', weekday:'long' });
    const saat  = d.toLocaleTimeString('tr-TR', { hour:'2-digit', minute:'2-digit' });
    return { tarih, saat };
  }

  var calendar = new Calendar(calendarEl, {
    headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
    themeSystem   : 'bootstrap',
    bootstrapFontAwesome: {
      prev: 'fa-chevron-left',
      next: 'fa-chevron-right'
    },
    initialView  : 'dayGridMonth',
    navLinks     : true,
    editable     : false,
    droppable    : false,
    locale       : 'tr',
    events       : { url: feedUrl },

    eventClick: function (info) {
      var p = info.event.extendedProps || {};
      var startDate = info.event.start;
      var fmt = trTarihSaat(startDate);

      // Modal alanlarını doldur
      $('#ev_adsoyad').text(p.adsoyad || info.event.title || '-');
      $('#ev_tarih').text(fmt.tarih);
      $('#ev_saat').text(fmt.saat);
      $('#ev_sure').text((p.sure_dk ?? '-') + ' dk');
      $('#ev_durum').html('<span class="badge badge-info">'+ (p.durum ?? '-') +'</span>');
      $('#ev_tel').text(p.telefon || '-');
      $('#ev_eposta').text(p.eposta || '-');
      // $('#ev_not').text(p.not || '-');

      $('#eventDetailModal').modal('show');
    }
  });

  calendar.render();

  // Tam ekran basit davranış: kartı genişlet
  document.getElementById('btnTamEkran').addEventListener('click', function(){
    const card = calendarEl.closest('.card');
    if (!card) return;
    card.classList.toggle('fullscreen-card');
    setTimeout(() => calendar.updateSize(), 150);
  });


  // Yeni randevu tık davranışı: yeni-randevu.php'ye git
  document.getElementById('btnYeniRandevu').addEventListener('click', function (e) {
    e.preventDefault();
    window.location.href = 'yeni-randevu.php';
  });
});
</script>

<style>
/* Küçük görsel rötuşlar */
.todo-list .text.completed { text-decoration: line-through; color:#6c757d; }
.fullscreen-card {
  position: fixed !important;
  z-index: 1060;
  top: 0; left: 0; right: 0; bottom: 0;
  width: 100vw; height: 100vh;
  margin: 0 !important;
  border-radius: 0 !important;
}
.fullscreen-card .card-body { height: calc(100vh - 60px); }
.fullscreen-card #calendar { height: 100%; }
</style>