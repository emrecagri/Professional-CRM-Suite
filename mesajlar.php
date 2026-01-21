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
          <a href="mesajgonder.php" class="btn btn-primary btn-block mb-3">Yeni Mesaj</a>

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
        <!-- /.col -->
        <div class="col-md-9">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Gelen Kutusu</h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Mesaj Ara">
                  <div class="input-group-append">
                    <div class="btn btn-primary">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check1">
                        <label for="check1"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">5 dakika önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check2">
                        <label for="check2"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">28 dakika önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check3">
                        <label for="check3"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">11 saat önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check4">
                        <label for="check4"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">15 saat önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check5">
                        <label for="check5"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">Dün</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check6">
                        <label for="check6"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">2 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check7">
                        <label for="check7"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">2 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check8">
                        <label for="check8"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">2 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check9">
                        <label for="check9"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">2 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check10">
                        <label for="check10"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">2 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check11">
                        <label for="check11"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">4 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check12">
                        <label for="check12"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"></td>
                    <td class="mailbox-date">12 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check13">
                        <label for="check13"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star-o text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">12 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check14">
                        <label for="check14"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">14 gün önce</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="icheck-primary">
                        <input type="checkbox" value="" id="check15">
                        <label for="check15"></label>
                      </div>
                    </td>
                    <td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
                    <td class="mailbox-name"><a href="read-mail.html">Selim Kaya</a></td>
                    <td class="mailbox-subject"><b>Mesaj Konusu</b> - Mesajın içerik kısmı bu bölümde gözükecek...
                    </td>
                    <td class="mailbox-attachment"><i class="fas fa-paperclip"></i></td>
                    <td class="mailbox-date">15 gün önce</td>
                  </tr>
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" class="btn btn-default btn-sm checkbox-toggle">
                  <i class="far fa-square"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-reply"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-share"></i>
                  </button>
                </div>
                <!-- /.btn-group -->
                <button type="button" class="btn btn-default btn-sm">
                  <i class="fas fa-sync-alt"></i>
                </button>
                <div class="float-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
              </div>
            </div>
          </div>
          <!-- /.card -->
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