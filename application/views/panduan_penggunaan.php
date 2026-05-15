<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Panduan Penggunaan Aplikasi</h2>
        <p>Panduan singkat penggunaan modul PK & PCK untuk semua pengguna.</p>
    </div>
</div>

<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="dashboard">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Panduan Penggunaan</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-15">1) Masuk Aplikasi</h5>
                <ul class="pl-3">
                    <li>Login menggunakan SSO (token disimpan sebagai cookie <code>sso_token</code>).</li>
                    <li>Jika token kadaluarsa, Anda akan diarahkan untuk login ulang.</li>
                </ul>

                <hr>

                <h5 class="mb-15">2) Navigasi (Tanpa Reload Halaman)</h5>
                <ul class="pl-3">
                    <li>Klik menu di atas (Beranda / Capaian Kinerja / Monitoring) untuk berpindah halaman.</li>
                    <li>Halaman dimuat secara SPA (AJAX) sehingga tidak refresh penuh.</li>
                </ul>

                <hr>

                <h5 class="mb-15">3) Mengelola Perjanjian Kinerja (PK Tahunan)</h5>
                <ol class="pl-3">
                    <li>Buka menu <b>Capaian Kinerja</b>.</li>
                    <li>Tambah Periode PK (tahun + rentang tanggal) lalu simpan.</li>
                    <li>Masuk ke <b>Detail</b> periode, kemudian buat <b>Sasaran Kinerja</b>.</li>
                    <li>Tambahkan <b>Indikator Kinerja</b> pada setiap Sasaran.</li>
                    <li>Jika sudah lengkap, lakukan <b>Ajukan Persetujuan</b> kepada atasan.</li>
                </ol>

                <hr>

                <h5 class="mb-15">4) Validasi PK (Atasan/Pejabat Penilai)</h5>
                <ul class="pl-3">
                    <li>Atasan dapat melihat PK bawahan dan melakukan <b>Validasi</b>.</li>
                    <li>Jika perlu revisi, PK dapat dikembalikan ke <b>Draft</b> beserta alasan.</li>
                </ul>

                <hr>

                <h5 class="mb-15">5) Mengisi PCK Bulanan</h5>
                <ol class="pl-3">
                    <li>Buka halaman <b>Penilaian Capaian Kinerja</b> pada periode yang dipilih.</li>
                    <li>Tambah periode penilaian (bulan).</li>
                    <li>Pilih indikator yang dinilai pada bulan tersebut.</li>
                    <li>Isi uraian tugas, target, realisasi, dan tautan bukti dukung.</li>
                </ol>

                <hr>

                <h5 class="mb-15">6) Penilaian & Posting</h5>
                <ul class="pl-3">
                    <li>Pejabat penilai memberi nilai mutu/realisasi pada uraian tugas.</li>
                    <li>Setelah lengkap, nilai PCK dapat diposting (menjadi <b>Posted</b> dan tidak dapat diubah kembali).</li>
                </ul>

                <hr>

                <h5 class="mb-15">7) Preview & Cetak</h5>
                <ul class="pl-3">
                    <li>Jika status sudah <b>Posted</b>, tersedia fitur <b>Preview</b> dan <b>Cetak</b> laporan PCK.</li>
                </ul>

                <hr>

                <h5 class="mb-15">8) Monitoring Penilaian (Admin/Operator)</h5>
                <ul class="pl-3">
                    <li>Menu <b>Monitoring</b> menampilkan semua data penilaian (Draft & Posted).</li>
                    <li>Gunakan filter <b>Nama</b>, <b>Tahun</b>, <b>Bulan</b>, serta pencarian/sort/paging (server-side).</li>
                    <li>Untuk status Draft, saat klik Preview akan muncul pemberitahuan belum diposting.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
