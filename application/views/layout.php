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

    <!-- Google Fonts - non-blocking with JS injection -->
    <script>
        // Load fonts non-render-blocking
        (function() {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap';
            document.head.appendChild(link);
        })();
    </script>

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Morris Charts CSS -->
    <link href="assets/plugins/morris.js/morris.css" rel="stylesheet" type="text/css" defer/>

    <link href="assets/plugins/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" defer>
    <link href="assets/plugins/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" defer>

    <!-- Toggles CSS -->
    <link href="assets/plugins/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css" >
    <!-- Modern UI Glassmorphism CSS -->
    <link href="assets/css/modern-ui.css" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Particle Background -->
    <canvas id="particle-canvas"></canvas>

    <!-- Page Loading Overlay -->
    <div id="page-loader">
        <div class="page-loader-inner">
            <div class="page-loader-spinner"></div>
            <div class="page-loader-text">Memuat...</div>
        </div>
    </div>

    <!-- Preloader -->
    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>
    <!-- /Preloader -->

    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-alt-nav" id="hkWrapper">

        <!-- Mobile Slide-In Overlay -->
        <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileMenu()"></div>

        <!-- Mobile Sidebar -->
        <nav class="mobile-sidebar" id="mobileSidebar">
            <!-- Sidebar Header -->
            <div class="mobile-sidebar-header">
                <div class="mobile-user-info">
                    <div class="mobile-avatar">
                        <img src="<?= $this->session->userdata('foto') ?>" alt="user" class="avatar-img rounded-circle">
                        <span class="badge badge-success badge-indicator"></span>
                    </div>
                    <div class="mobile-user-text">
                        <span class="mobile-user-name"><?= $this->session->userdata('fullname') ?></span>
                        <span class="mobile-user-role"><?= ucfirst($this->session->userdata('jabatan')) ?></span>
                    </div>
                </div>
                <button class="mobile-sidebar-close" onclick="closeMobileMenu()" aria-label="Tutup menu">
                    <i class="zmdi zmdi-close"></i>
                </button>
            </div>

            <!-- Sidebar Nav -->
            <div class="mobile-sidebar-nav">
                <div class="mobile-nav-section-label">Menu Utama</div>
                <a class="mobile-nav-item" href="javascript:;" data-page="dashboard"
                    onclick="navigateMobile('dashboard')">
                    <i class="zmdi zmdi-home"></i>
                    <span>Beranda</span>
                </a>
                <a class="mobile-nav-item" href="javascript:;" data-page="pck" onclick="navigateMobile('pck')">
                    <i class="zmdi zmdi-trending-up"></i>
                    <span>Capaian Kinerja</span>
                </a>
                <?php if (in_array($this->session->userdata('peran'), ['admin', 'operator'])) { ?>
                    <a class="mobile-nav-item" href="javascript:;" data-page="monitoring"
                        onclick="navigateMobile('monitoring')">
                        <i class="zmdi zmdi-eye"></i>
                        <span>Monitoring</span>
                    </a>
                <?php } ?>
                <a class="mobile-nav-item" href="javascript:;" data-page="panduan_penggunaan"
                    onclick="navigateMobile('panduan_penggunaan')">
                    <i class="zmdi zmdi-book"></i>
                    <span>Panduan</span>
                </a>
                <?php if ($this->session->userdata('peran') == 'admin') { ?>
                    <a class="mobile-nav-item" href="javascript:;" data-page="dokumentasi_teknis"
                        onclick="navigateMobile('dokumentasi_teknis')">
                        <i class="zmdi zmdi-code"></i>
                        <span>Dokumentasi</span>
                    </a>
                <?php } ?>

                <div class="mobile-nav-divider"></div>
                <div class="mobile-nav-section-label">Akun</div>

                <?php if (in_array($this->session->userdata('peran'), ['admin'])) { ?>
                    <a class="mobile-nav-item" href="javascript:;" onclick="ModalRole('-1'); closeMobileMenu();">
                        <i class="zmdi zmdi-accounts-alt"></i>
                        <span>Peran</span>
                    </a>
                <?php } ?>
                <a class="mobile-nav-item" href="<?= $this->config->item('sso_server') ?>">
                    <i class="zmdi zmdi-swap"></i>
                    <span>Pindah Layanan</span>
                </a>
                <a class="mobile-nav-item mobile-nav-logout" href="keluar">
                    <i class="zmdi zmdi-power"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </nav>

        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-xl fixed-top hk-navbar">
            <!-- Mobile: Brand + Hamburger + Avatar row -->
            <div class="mobile-navbar-row">
                <!-- Hamburger Toggle -->
                <button class="mobile-menu-toggle" onclick="openMobileMenu()" aria-label="Buka menu">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- Brand -->
                <a class="navbar-brand" href="javascript:;" data-page="dashboard">
                    <img class="brand-img d-inline-block" src="assets/img/pck.png" width="40" height="40" alt="brand">
                    <span class="brand-text">PCK</span>
                </a>

                <!-- User Avatar (mobile) -->
                <div class="mobile-user-avatar" onclick="toggleMobileUserMenu()">
                    <img src="<?= $this->session->userdata('foto') ?>" alt="user" class="avatar-img rounded-circle">
                    <span class="badge badge-success badge-indicator"></span>
                </div>
            </div>

            <!-- Desktop Nav -->
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

            <!-- Desktop User Menu -->
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

        <!-- Mobile User Dropdown -->
        <div class="mobile-user-dropdown" id="mobileUserDropdown">
            <a class="mobile-user-dropdown-item" href="<?= $this->config->item('sso_server') ?>">
                <i class="zmdi zmdi-replay"></i><span>Pindah Layanan</span>
            </a>
            <?php if (in_array($this->session->userdata('peran'), ['admin'])) { ?>
                <a class="mobile-user-dropdown-item" href="javascript:;" onclick="ModalRole('-1'); closeMobileUserMenu();">
                    <i class="zmdi zmdi-accounts-alt"></i><span>Peran</span>
                </a>
            <?php } ?>
            <div class="mobile-user-dropdown-divider"></div>
            <a class="mobile-user-dropdown-item mobile-logout" href="keluar">
                <i class="zmdi zmdi-power"></i><span>Keluar</span>
            </a>
        </div>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
            <!-- Container -->
            <div id="app" class="container mt-xl-50 mt-sm-30 mt-15 p-4">
            </div>
            <!-- /Container -->

            <!-- Footer -->
            <div class="hk-footer-wrap container fixed-footer">
                <footer class="footer">
                    <p>Copyright © 2026. All right reserved.</p>
                </footer>
            </div>
            <!-- /Footer -->
        </div>
        <!-- /Main Content -->
    </div>
    <!-- /HK Wrapper -->

    <!-- Theme Toggle Floating Button -->
    <button id="theme-toggle" class="theme-toggle-btn" onclick="toggleTheme()" title="Switch to light theme" aria-label="Toggle dark/light theme">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
    </button>

    <div class="modal fade" id="role-pegawai" tabindex="-1" role="dialog" aria-labelledby="role-pegawaiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="judul">Daftar Petugas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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

    <!-- jQuery -->
    <script src="assets/plugins/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="assets/plugins/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="assets/js/jquery.slimscroll.js" defer></script>

    <!-- Fancy Dropdown JS -->
    <script src="assets/js/dropdown-bootstrap-extended.js" defer></script>

    <!-- FeatherIcons JavaScript -->
    <script src="assets/js/feather.min.js" defer></script>

    <!-- Toggles JavaScript -->
    <script src="assets/plugins/jquery-toggles/toggles.min.js" defer></script>

    <!-- Counter Animation JavaScript -->
    <script src="assets/plugins/waypoints/lib/jquery.waypoints.min.js" defer></script>
    <script src="assets/plugins/jquery.counterup/jquery.counterup.min.js" defer></script>

    <!-- Easy pie chart JS -->
    <script src="assets/plugins/easy-pie-chart/dist/jquery.easypiechart.min.js" defer></script>

    <!-- Sparkline JavaScript -->
    <script src="assets/plugins/jquery.sparkline/dist/jquery.sparkline.min.js" defer></script>

    <!-- Morris Charts JavaScript -->
    <script src="assets/plugins/raphael/raphael.min.js" defer></script>
    <script src="assets/plugins/morris.js/morris.min.js" defer></script>

    <!-- EChartJS JavaScript -->
    <script src="assets/plugins/echarts/dist/echarts-en.min.js" defer></script>

    <!-- Peity JavaScript -->
    <script src="assets/plugins/peity/jquery.peity.min.js" defer></script>

    <script src="assets/plugins/moment/min/moment.min.js" defer></script>
    <script src="assets/plugins/daterangepicker/daterangepicker.js" defer></script>
    <script src="assets/plugins/select2/dist/js/select2.full.min.js" defer></script>
    <script src="assets/plugins/jquery-toast-plugin/dist/jquery.toast.min.js" defer></script>

    <script src="assets/plugins/sweetalert2/sweetalert2.all.min.js" defer></script>

    <script src="assets/plugins/datatables.net/js/jquery.dataTables.min.js" defer></script>
    <script src="assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="assets/plugins/datatables.net-dt/js/dataTables.dataTables.min.js" defer></script>
    <script src="assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js" defer></script>
    <script src="assets/plugins/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js" defer></script>
    <script src="assets/plugins/datatables.net-buttons/js/buttons.flash.min.js" defer></script>

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
        // ── Mobile Sidebar Functions ──
        function openMobileMenu() {
            document.getElementById('mobileOverlay').classList.add('active');
            document.getElementById('mobileSidebar').classList.add('open');
            document.body.style.overflow = 'hidden';
            document.body.classList.add('mobile-menu-open');
            // Animate hamburger to X
            var toggle = document.querySelector('.mobile-menu-toggle .hamburger-icon');
            if (toggle) toggle.classList.add('open');
        }

        function closeMobileMenu() {
            document.getElementById('mobileOverlay').classList.remove('active');
            document.getElementById('mobileSidebar').classList.remove('open');
            document.body.style.overflow = '';
            document.body.classList.remove('mobile-menu-open');
            // Reset hamburger
            var toggle = document.querySelector('.mobile-menu-toggle .hamburger-icon');
            if (toggle) toggle.classList.remove('open');
        }

        function navigateMobile(page) {
            closeMobileMenu();
            setTimeout(function () {
                loadPage(page);
            }, 300);
        }

        function toggleMobileUserMenu() {
            var dropdown = document.getElementById('mobileUserDropdown');
            dropdown.classList.toggle('active');
        }

        function closeMobileUserMenu() {
            var dropdown = document.getElementById('mobileUserDropdown');
            dropdown.classList.remove('active');
        }

        // ── Theme Toggle (Dark/Light) ──
        function toggleTheme() {
            document.body.classList.toggle('light-theme');
            var isLight = document.body.classList.contains('light-theme');

            // Save preference
            localStorage.setItem('pck-theme', isLight ? 'light' : 'dark');

            // Update icon
            var btn = document.getElementById('theme-toggle');
            if (btn) {
                if (isLight) {
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
                    btn.title = 'Switch to dark theme';
                } else {
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
                    btn.title = 'Switch to light theme';
                }
            }
        }

        // Load saved theme on page load
        (function() {
            var saved = localStorage.getItem('pck-theme');
            if (saved === 'light') {
                document.body.classList.add('light-theme');
                var btn = document.getElementById('theme-toggle');
                if (btn) {
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
                    btn.title = 'Switch to dark theme';
                }
            }
        })();

        // Close mobile user menu on outside click
        document.addEventListener('click', function (e) {
            var dropdown = document.getElementById('mobileUserDropdown');
            var avatar = document.querySelector('.mobile-user-avatar');
            if (dropdown && avatar && !dropdown.contains(e.target) && !avatar.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        $(document).ready(function () {
            // ── Auto-fix: move ALL modals outside #hkWrapper to fix backdrop stacking ──
            $(document).on('show.bs.modal', '.modal', function () {
                var $modal = $(this);
                var $backdrop = $('.modal-backdrop');

                // Move modal to <body> if it's inside #hkWrapper
                if ($modal.closest('#hkWrapper').length) {
                    $modal.detach().appendTo('body');
                }

                // Ensure backdrop is also at <body> level
                if ($backdrop.length && $backdrop.parent().is('#hkWrapper, .hk-wrapper')) {
                    $backdrop.detach().appendTo('body');
                }
            });

            // Also handle nested modal backdrops after transition
            $(document).on('shown.bs.modal', '.modal', function () {
                $('.modal-backdrop').each(function () {
                    if (!$(this).parent().is('body')) {
                        $(this).detach().appendTo('body');
                    }
                });
            });

            // Load page
            loadPage('dashboard');

            // Auto-show notification from flashdata
            if (result && result !== '-1' && pesan) {
                setTimeout(function () {
                    notifikasi(pesan, result);
                }, 500);
            }

            // Navigasi SPA - satu handler untuk desktop & mobile
            $(document).on('click', '[data-page]', function (e) {
                e.preventDefault();
                // Tutup mobile menu jika terbuka
                closeMobileMenu();
                let page = $(this).data('page');
                let id = $(this).data('id');
                loadPage(page, id);
            });

            // Close mobile menu on window resize to desktop
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1200) {
                    closeMobileMenu();
                }
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

    <!-- Modern UI JS -->
    <script src="assets/js/modern-ui.js"></script>

    <script src="assets/js/pck.js"></script>
</body>

</html>