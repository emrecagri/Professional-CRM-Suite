<?php include 'header.php'; ?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Yeni Danışan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Yeni Danışan</li>
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
            <h3 class="card-title">Danışan Ekle</h3>
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
<form action="islemler/islem.php" method="POST" enctype="multipart/form-data" data-parsley-validate>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="ad">Ad</label>
                    <input type="text" class="form-control" id="ad" name="ad" placeholder="Ad">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="soyad">Soyad</label>
                    <input type="text" class="form-control" id="soyad" name="soyad" placeholder="Soyad">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="tc_kimlik_no">TC Kimlik No</label>
                    <input type="number" class="form-control" id="tc_kimlik_no" name="tc_kimlik_no" placeholder="TC Kimlik No">
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="eposta">E-Posta</label>
                    <input type="text" class="form-control" id="eposta" name="eposta" placeholder="E-Posta">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="telefon">Telefon</label>
                    <input type="number" class="form-control" id="telefon" name="telefon" placeholder="Telefon">
                  </div>
                  <div class="form-group col-md-4">
                    <label for="musteri_profil_foto">Profil Fotoğrafı</label>
                    <input type="file" class="form-control-file" id="musteri_profil_foto" name="musteri_profil_foto">
                  </div>
                </div>
                <div class="form-row">

                  <div class="form-group col-md-6">
                    <label for="adres">Adres</label>
                    <input type="text" class="form-control" id="adres" name="adres" placeholder="Cadde, Apartman…">
                  </div>

                  <div class="form-group col-md-2">
                    <label for="adres_il">İl</label>
                    <select class="form-control" id="adres_il" name="adres_il">
                      <option value="">İl seçiniz…</option>
                    </select>
                  </div>

                  <div class="form-group col-md-2">
                    <label for="adres_ilce">İlçe</label>
                    <select class="form-control" id="adres_ilce" name="adres_ilce" disabled>
                      <option value="">Önce il seçiniz…</option>
                    </select>
                  </div>

                  <div class="form-group col-md-2">
                    <label for="adres_mahalle">Mahalle</label>
                    <select class="form-control" id="adres_mahalle" name="adres_mahalle" disabled>
                      <option value="">Önce ilçe seçiniz…</option>
                    </select>
                  </div>


                </div>

                <script>
                  // --- JSONdan html ve js ile il ilçe mahalle verisi çekme ---
                  const PATHS = {
                    sehirler: 'plugins/il-ilce-mahalle-json-listesi-2022/sehirler.json',
                    ilceler:  'plugins/il-ilce-mahalle-json-listesi-2022/ilceler.json',
                    mahalleler: [
                      'plugins/il-ilce-mahalle-json-listesi-2022/mahalleler-1.json',
                      'plugins/il-ilce-mahalle-json-listesi-2022/mahalleler-2.json',
                      'plugins/il-ilce-mahalle-json-listesi-2022/mahalleler-3.json',
                      'plugins/il-ilce-mahalle-json-listesi-2022/mahalleler-4.json'
                    ]
                  };

                  // Belleğe alınacak veriler
                  let SEHIRLER = [];
                  let ILCELER  = [];
                  let MAHALLELER = [];   // Tümü birleşik
                  const MAHALLE_BY_ILCE = {}; // İlçeID -> mahalle listesi

                  // Elemanlar
                  const ilSel      = document.getElementById('adres_il');
                  const ilceSel    = document.getElementById('adres_ilce');
                  const mahalleSel = document.getElementById('adres_mahalle');

                  // Yardımcılar
                  async function getJSON(url){
                    const r = await fetch(url, { cache: 'no-store' });
                    if(!r.ok) throw new Error(url + ' yüklenemedi');
                    return r.json();
                  }

                  // DİKKAT: option.value = İSİM gönderilecek; option.dataset.id = ID filtreleme için tutulur
                  function fillSelectByName(selectEl, rows, placeholder, idKey, nameKey){
                    selectEl.innerHTML = '';
                    selectEl.append(new Option(placeholder || 'Seçiniz…', ''));
                    for(const row of rows){
                      const name = row[nameKey];
                      const id   = row[idKey];
                      const opt  = new Option(name, name); // value = İSİM 54533
                      opt.dataset.id = String(id);         // data-id = ID (filtre için)
                      selectEl.append(opt);
                    }
                    selectEl.disabled = rows.length === 0;
                  }

                  // Mahalleleri ilçe bazında indeksle
                  function indexMahalleByIlce(){
                    for(const m of MAHALLELER){
                      const k = String(m.ilce_id);
                      (MAHALLE_BY_ILCE[k] ||= []).push(m);
                    }
                  }

                  // Olaylar
                  ilSel.addEventListener('change', () => {
                    // Seçili ilin ID’sini option’dan data-id ile alıyoruz
                    const sid = ilSel.selectedOptions[0]?.dataset.id || '';
                    fillSelectByName(ilceSel, [], 'Önce il seçiniz…', 'ilce_id', 'ilce_adi');
                    fillSelectByName(mahalleSel, [], 'Önce ilçe seçiniz…', 'mahalle_id', 'mahalle_adi');
                    mahalleSel.disabled = true;

                    if(!sid){ ilceSel.disabled = true; return; }

                    const ilceRows = ILCELER
                      .filter(i => String(i.sehir_id) === String(sid))
                      .sort((a,b) => a.ilce_adi.localeCompare(b.ilce_adi, 'tr'));

                    fillSelectByName(ilceSel, ilceRows, 'İlçe seçiniz…', 'ilce_id', 'ilce_adi');
                  });

                  ilceSel.addEventListener('change', () => {
                    const iid = ilceSel.selectedOptions[0]?.dataset.id || '';
                    fillSelectByName(mahalleSel, [], 'Önce ilçe seçiniz…', 'mahalle_id', 'mahalle_adi');
                    if(!iid){ mahalleSel.disabled = true; return; }

                    const mahalleRows = (MAHALLE_BY_ILCE[iid] || [])
                      .sort((a,b) => a.mahalle_adi.localeCompare(b.mahalle_adi, 'tr'));

                    fillSelectByName(mahalleSel, mahalleRows, 'Mahalle seçiniz…', 'mahalle_id', 'mahalle_adi');
                  });

                  // Başlat
                  (async function init(){
                    try{
                      // Şehirler
                      SEHIRLER = await getJSON(PATHS.sehirler); // [{sehir_id,sehir_adi}]
                      SEHIRLER.sort((a,b) => a.sehir_adi.localeCompare(b.sehir_adi, 'tr'));
                      fillSelectByName(ilSel, SEHIRLER, 'İl seçiniz…', 'sehir_id', 'sehir_adi');

                      // İlçeler
                      ILCELER = await getJSON(PATHS.ilceler); // [{ilce_id,ilce_adi,sehir_id,...}]

                      // Mahalleler (parçaları birleştir)
                      const parts = await Promise.all(PATHS.mahalleler.map(getJSON));
                      MAHALLELER = parts.flat(); // [{mahalle_id, mahalle_adi, ilce_id, ...}]
                      indexMahalleByIlce();
                    }catch(e){
                      console.error(e);
                      alert('Adres verileri yüklenemedi. Loruv ile iletişime geçiniz.');
                    }
                  })();
                </script>







                <div class="form-group">
                  <label for="danisan_not">Varsa Not</label><textarea id="summernote" name="danisan_not" style="display: none;"></textarea>
                </div>
                <button type="submit" name="yeni_danisan" class="btn btn-primary">Kaydet</button>
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