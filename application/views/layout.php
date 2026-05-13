<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?= $this->session->userdata('nama_client_app') ?> | <?= $this->session->userdata('deskripsi_client_app') ?>
    </title>
    <meta name="description" content="Penilaian Capaian Kinerja Bulanan" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/pck.png">
    <link rel="icon" href="assets/img/pck.png" type="image/x-icon">

    <!-- Morris Charts CSS -->
    <link href="assets/plugins/morris.js/morris.css" rel="stylesheet" type="text/css" />

    <link href="assets/plugins/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css">

    <!-- Toggles CSS -->
    <link href="assets/plugins/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Preloader -->
    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>
    <!-- /Preloader -->

    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-alt-nav">

        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-xl fixed-top hk-navbar hk-navbar-alt navbar-dark">
            <a class="navbar-toggle-btn nav-link-hover navbar-toggler" href="javascript:void(0);" data-toggle="collapse"
                data-target="#navbarCollapseAlt" aria-controls="navbarCollapseAlt" aria-expanded="false"
                aria-label="Toggle navigation"><span class="feather-icon"><i data-feather="menu"></i></span></a>
            <a class="navbar-brand" href="javascript:;" data-page="dashboard">
                <img class="brand-img d-inline-block" src="assets/img/pck.png" width="50" height="50" alt="brand">
            </a>
            <div class="collapse navbar-collapse" id="navbarCollapseAlt">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:;" data-page="dashboard">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:;" data-page="pck">Capaian Kinerja</a>
                    </li>
                    <?php if (in_array($this->session->userdata('peran'), ['admin', 'operator'])) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:;" data-page="monitoring">Monitoring Capaian Kinerja</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:;" data-page="panduan_penggunaan">Panduan</a>
                    </li>
                    <?php if ($this->session->userdata('peran') == 'admin') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:;" data-page="dokumentasi_teknis">Dokumentasi Teknis</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <ul class="navbar-nav hk-navbar-content">
                <li class="nav-item dropdown dropdown-authentication">
                    <a class="nav-link dropdown-toggle no-caret" href="#" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            <div class="media-img-wrap">
                                <div class="avatar">
                                    <img src="<?= $this->session->userdata('foto') ?>" alt="user"
                                        class="avatar-img rounded-circle">
                                </div>
                                <span class="badge badge-success badge-indicator"></span>
                            </div>
                            <div class="media-body">
                                <span><?= $this->session->userdata('fullname') ?><i
                                        class="zmdi zmdi-chevron-down"></i></span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" data-dropdown-in="flipInX"
                        data-dropdown-out="flipOutX">
                        <a class="dropdown-item" href="<?= $this->config->item('sso_server') ?>"><i
                                class="dropdown-icon zmdi zmdi-replay"></i><span>Pindah Layanan</span></a>
                        <?php if (in_array($this->session->userdata('peran'), ['admin'])) { ?>
                            <a class="dropdown-item" onclick="ModalRole('-1')"><i
                                    class="dropdown-icon zmdi zmdi-accounts-alt"></i><span>Peran</span></a>
                        <?php } ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="keluar"><i
                                class="dropdown-icon zmdi zmdi-power"></i><span>Keluar</span></a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /Top Navbar -->

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
            <!-- Container -->
            <div id="app" class="container mt-xl-50 mt-sm-30 mt-15">
            </div>
            <!-- /Container -->

            <div class="modal fade" id="role-pegawai" tabindex="-1" role="dialog" aria-labelledby="role-pegawaiLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-dark">
                            <h5 class="modal-title text-white" id="judul">Daftar Petugas</h5>
                        </div>
                        <form method="POST" id="formPeran">
                            <input type="hidden" id="id" name="id">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label">Pilih Pegawai : </label>
                                    <div id="pegawai_">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pilih Peran : </label>
                                    <div id="peran_"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" id="btnBatal" onclick="ModalRole('-1')"
                                    class="btn btn-danger">Batal</button>
                            </div>
                        </form>
                        <div class="modal-body" id="tabel-role"></div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <!-- Footer -->
            <div class="hk-footer-wrap container">
                <footer class="footer">
                    <div class="row">
                        <div class="col-12">
                            <p>Copyright © 2026. All right reserved.</p>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- /Footer -->
        </div>
        <!-- /Main Content -->

    </div>
    <!-- /HK Wrapper -->

    <!-- jQuery -->
    <script src="assets/plugins/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="assets/plugins/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="assets/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="assets/js/dropdown-bootstrap-extended.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Toggles JavaScript -->
    <script src="assets/plugins/jquery-toggles/toggles.min.js"></script>

    <!-- Counter Animation JavaScript -->
    <script src="assets/plugins/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/plugins/jquery.counterup/jquery.counterup.min.js"></script>

    <!-- Easy pie chart JS -->
    <script src="assets/plugins/easy-pie-chart/dist/jquery.easypiechart.min.js"></script>

    <!-- Sparkline JavaScript -->
    <script src="assets/plugins/jquery.sparkline/dist/jquery.sparkline.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="assets/plugins/raphael/raphael.min.js"></script>
    <script src="assets/plugins/morris.js/morris.min.js"></script>

    <!-- EChartJS JavaScript -->
    <script src="assets/plugins/echarts/dist/echarts-en.min.js"></script>

    <!-- Peity JavaScript -->
    <script src="assets/plugins/peity/jquery.peity.min.js"></script>

    <script src="assets/plugins/moment/min/moment.min.js"></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="assets/plugins/select2/dist/js/select2.full.min.js"></script>
    <script src="assets/plugins/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <script src="assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <script src="assets/plugins/datatables.net/js/jquery.dataTables.min.js" defer></script>
    <script src="assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="assets/plugins/datatables.net-dt/js/dataTables.dataTables.min.js" defer></script>
    <script src="assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js" defer></script>
    <script src="assets/plugins/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js" defer></script>
    <script src="assets/plugins/datatables.net-buttons/js/buttons.flash.min.js" defer></script>

    <!-- Init JavaScript -->
    <script src="assets/js/init.js"></script>

    <?php
    if ($this->session->flashdata('info')) {
        $result = $this->session->flashdata('info');
        if ($result == '1') {
            $pesan = $this->session->flashdata('pesan_sukses');
        } elseif ($result == '2') {
            $pesan = $this->session->flashdata('pesan_gagal');
        } else {
            $pesan = $this->session->flashdata('pesan_gagal');
        }
    } else {
        $result = "-1";
        $pesan = "";
    }
    ?>

    <script>
        $(document).ready(function () {
            // Load page
            loadPage('dashboard');

            // Navigasi SPA
            $(document).on('click', '[data-page]', function (e) {
                e.preventDefault();
                $('.wrapper').removeClass('toggled');
                let page = $(this).data('page');
                // Opsional: id dari data-id (untuk GET ?id=... ke halamanutama/page/...)
                let id = $(this).data('id');
                loadPage(page, id);
            });
        });
    </script>

    <script type="text/javascript">
        var config = {
            peran: '<?= $peran ?>',
            result: '<?= $result ?>',
            pesan: '<?= $pesan ?>'
        };
    </script>

    <script src="assets/js/pck.js"></script>
</body>

</html>