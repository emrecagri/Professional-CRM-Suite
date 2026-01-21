<?php include 'header.php'; ?>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Mesajlar</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
              <li class="breadcrumb-item active">Mesajlar</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

















   <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="mesajlar.php" class="btn btn-primary btn-block mb-3">Gelen Kutusuna Dön</a>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Klasörler</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item active">
                  <a href="#" class="nav-link">
                    <i class="fas fa-inbox"></i> Gelen Kutusu
                    <span class="badge bg-primary float-right">12</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-envelope"></i> Gönderilenler
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-file-alt"></i> Taslaklar
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="fas fa-filter"></i> Spam
                    <span class="badge bg-warning float-right">65</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-trash-alt"></i> Çöp Kutusu
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Kaynaklar</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-danger"></i>
                    Web Sitesi
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-warning"></i> Instagram
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle text-primary"></i>
                    Mail
                  </a>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
          <div class="col-md-9">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Yeni Mesaj Gönder</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="form-group">
                  <input class="form-control" placeholder="Kime">
                </div>
                <div class="form-group">
                  <input class="form-control" placeholder="Konu">
                </div>
                <div class="form-group">
                    <textarea id="summernote" class="form-control" style="height: 300px">
                      <h1><u>Mesaj Başlığı</u></h1>
                      <h4>Alt başlık</h4>
                      <p>Örnek mesaj içeriği.</p>
                      <ul>
                        <li>Liste maddesi bir</li>
                        <li>Liste maddesi iki</li>
                        <li>Liste maddesi üç</li>
                      </ul>
                      <p>Teşekkürler,</p>
                      <p>İsim Soyisim</p>
                    </textarea>
                </div>
                <div class="form-group">
                  <div class="btn btn-default btn-file">
                    <i class="fas fa-paperclip"></i> Ek Yükle
                    <input type="file" name="attachment">
                  </div>
                  <!-- <p class="help-block">Max. 32MB</p> -->
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <div class="float-right">
                  <button type="button" class="btn btn-default"><i class="fas fa-pencil-alt"></i> Taslak Olarak Kaydet</button>
                  <button type="submit" class="btn btn-primary"><i class="far fa-envelope"></i> Gönder</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Sil</button>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>


















      
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include 'footer.php'; ?>