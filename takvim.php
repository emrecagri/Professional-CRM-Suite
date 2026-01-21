<?php


require_once __DIR__ . '/islemler/baglan.php'; // $db = new PDO(...)

if (isset($_GET['feed'])) {
  header('Content-Type: application/json; charset=utf-8');
  try {
    $kullaniciId = isset($_GET['kullanici_id']) ? (int)$_GET['kullanici_id'] : 0;

    // Randevu + Danışan bilgileri
    $sql = "
      SELECT
        r.randevu_id                           AS id,
        r.kullanici_id,
        r.danisan_id,
        r.randevu_tarihi                       AS start_dt,
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

    $st = $kullaniciId ? ($db->prepare($sql)) : ($db->prepare($sql));
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

      // Başlık: Danışan Ad Soyad (telefon opsiyonel)
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

        // Detayda kullanacağımız alanları extendedProps'a koyuyoruz
        'extendedProps'   => [

          'durum'      => $r['randevu_durumu'],
          'not'        => $r['randevu_notu'],
          'sure_dk'    => (int)$r['sure_dk'],
          'adsoyad'    => $adsoyad,
          'telefon'    => $r['danisan_telefon'] ?? null,
          'eposta'     => $r['danisan_eposta'] ?? null,
          'start_raw'  => $r['start_dt'], // JS'te TR biçimi için
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

<link rel="stylesheet" href="plugins/fullcalendar/main.min.css">
<script src="plugins/fullcalendar/main.min.js"></script>
<script src="plugins/fullcalendar/locales-all.min.js"></script>

<?php include 'header.php'; ?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Takvim</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
            <li class="breadcrumb-item active">Takvim</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="card card-primary mt-3">
        <div class="card-header">
          <h3 class="card-title mb-0">Randevu Takvimi</h3>
        </div>
        <div class="card-body">
          <div id="calendar"></div>
        </div>
      </div>

      <script>
      document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var Calendar = FullCalendar.Calendar;

        const feedUrl = window.location.pathname + '?feed=1';

        function trTarihSaat(d) {
          // d: Date objesi
          const tarih = d.toLocaleDateString('tr-TR', { year:'numeric', month:'long', day:'numeric', weekday:'long' });
          const saat  = d.toLocaleTimeString('tr-TR', { hour:'2-digit', minute:'2-digit' });
          return { tarih, saat };
        }

        var calendar = new Calendar(calendarEl, {
          headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
          themeSystem: 'bootstrap',
          initialView: 'dayGridMonth',
          navLinks: true,
          editable: false,
          droppable: false,
          locale     : 'tr', 
          events: { url: feedUrl },

          eventClick: function (info) {
            var p = info.event.extendedProps || {};
            var startDate = info.event.start; // Date objesi
            var fmt = trTarihSaat(startDate);

            var satirlar = [
              'Danışan: ' + (p.adsoyad || info.event.title || '-'),
              p.telefon ? ('Telefon: ' + p.telefon) : null,
              p.eposta ? ('E-posta: ' + p.eposta) : null, // istersen aç
              'Tarih: ' + fmt.tarih,
              'Saat: ' + fmt.saat,
              'Süre: ' + (p.sure_dk ?? '-') + ' dk',
              'Durum: ' + (p.durum ?? '-')
             // 'Not: ' + (p.not ?? '-')
            ].filter(Boolean);

            alert(satirlar.join('\n'));
          }
        });

        calendar.render();
      });
      </script>

    </div>
  </section>
</div>

<?php include 'footer.php'; ?>