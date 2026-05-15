<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Dokumentasi Teknis (Admin)</h2>
        <p>Dokumentasi internal untuk pemeliharaan, pengembangan, dan audit teknis aplikasi.</p>
    </div>
</div>

<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="dashboard">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dokumentasi Teknis</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info" role="alert">
            <b>Akses terbatas.</b> Halaman ini hanya dapat dilihat oleh <b>Admin</b>.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-15">1) Arsitektur Singkat</h5>
                <ul class="pl-3">
                    <li><b>Framework</b>: CodeIgniter 3.</li>
                    <li><b>Layout utama</b>: <code>application/views/layout.php</code> memuat container <code>#app</code>.</li>
                    <li><b>SPA loader</b>: <code>assets/js/pck.js</code> fungsi <code>loadPage(page, id)</code> memanggil
                        <code>GET halamanutama/page/&lt;page&gt;?id=...</code> dan menyuntikkan view ke <code>#app</code>.</li>
                    <li><b>Whitelist view</b>: <code>HalamanUtama::page()</code> membatasi halaman yang boleh dimuat.</li>
                </ul>

                <hr>

                <h5 class="mb-15">2) Autentikasi SSO & Session</h5>
                <ul class="pl-3">
                    <li>Semua controller utama extend <code>MY_Controller</code> (<code>application/core/MY_Controller.php</code>).</li>
                    <li>Jika belum login, aplikasi mengambil cookie <code>sso_token</code> lalu memvalidasi ke API SSO
                        (<code>{$this->config->item('sso_server')}api/cek_token</code>).</li>
                    <li>Frontend juga memanggil <code>cek_token</code> sebelum memuat halaman via SPA untuk memastikan token masih valid.</li>
                </ul>

                <hr>

                <h5 class="mb-15">3) Otorisasi (Role/Peran)</h5>
                <ul class="pl-3">
                    <li><b>peran</b> diset di <code>MY_Controller</code>: role SSO tertentu dipetakan menjadi <code>admin</code>,
                        selain itu dicek tabel lokal <code>peran</code> untuk mendapatkan <code>operator</code> atau lainnya.</li>
                    <li><b>Monitoring</b> dibatasi untuk <code>admin</code> dan <code>operator</code>.</li>
                    <li><b>Dokumentasi Teknis</b> dibatasi untuk <code>admin</code> melalui guard di <code>HalamanUtama::page()</code>.</li>
                </ul>

                <hr>

                <h5 class="mb-15">4) Endpoint/Route Penting</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 28%;">Route</th>
                                <th style="width: 32%;">Controller</th>
                                <th>Ringkasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>show_tabel_periode_pk</code></td>
                                <td><code>HalamanCapaianKinerja/show_tabel_periode_pk</code></td>
                                <td>Daftar PK tahunan (periode) untuk pegawai / atasan.</td>
                            </tr>
                            <tr>
                                <td><code>show_sasaran</code>, <code>simpan_sasaran</code>, <code>hapus_sasaran</code></td>
                                <td><code>HalamanCapaianKinerja</code></td>
                                <td>CRUD Sasaran Kinerja pada periode PK.</td>
                            </tr>
                            <tr>
                                <td><code>show_indikator</code>, <code>simpan_indikator</code>, <code>hapus_indikator</code></td>
                                <td><code>HalamanCapaianKinerja</code></td>
                                <td>CRUD Indikator Kinerja pada Sasaran.</td>
                            </tr>
                            <tr>
                                <td><code>ajukan_persetujuan_pk</code>, <code>validasi_pk</code>, <code>kembalikan_pk_ke_draft</code></td>
                                <td><code>HalamanCapaianKinerja</code></td>
                                <td>Workflow pengajuan dan validasi PK.</td>
                            </tr>
                            <tr>
                                <td><code>show_daftar_pck</code>, <code>simpan_periode_pck</code></td>
                                <td><code>HalamanCapaianKinerja</code></td>
                                <td>Membuat penilaian bulanan (<code>ck_penilaian</code>).</td>
                            </tr>
                            <tr>
                                <td><code>simpan_uraian_tugas</code>, <code>simpan_nilai_uraian_tugas</code>, <code>posting_penilaian_pck</code></td>
                                <td><code>HalamanCapaianKinerja</code></td>
                                <td>Pengisian uraian tugas, penilaian, dan posting (finalisasi).</td>
                            </tr>
                            <tr>
                                <td><code>get_data_pck</code></td>
                                <td><code>HalamanLaporan/get_data_pck</code></td>
                                <td>Generate HTML preview PCK + siap cetak (dipakai modal preview).</td>
                            </tr>
                            <tr>
                                <td><code>monitoring_penilaian_dt</code></td>
                                <td><code>HalamanCapaianKinerja/monitoring_penilaian_dt</code></td>
                                <td>DataTables server-side untuk monitoring Draft/Posted.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <h5 class="mb-15">5) Struktur Data Inti</h5>
                <ul class="pl-3 mb-0">
                    <li><code>pk_periode</code>: periode PK tahunan (status draft/ajukan/valid, penilai, validator).</li>
                    <li><code>pk_sasaran_kinerja</code>: sasaran per periode & jabatan.</li>
                    <li><code>pk_indikator_kinerja</code>: indikator per sasaran (target, satuan, waktu, anggaran).</li>
                    <li><code>ck_penilaian</code>: penilaian bulanan (bulan, nilai, status draft/posted).</li>
                    <li><code>ck_capaian_indikator</code>: indikator yang dinilai pada bulan tsb + capaian.</li>
                    <li><code>ck_uraian_tugas</code>: uraian tugas + target/realisasi/nilai/tautan bukti.</li>
                    <li><code>v_capaian_indikator</code>, <code>v_uraian_tugas</code>: view bantu untuk laporan/query.</li>
                </ul>

                <hr>

                <h5 class="mb-15">6) Dependensi Frontend</h5>
                <ul class="pl-3 mb-0">
                    <li>jQuery, Bootstrap</li>
                    <li>DataTables (server-side), Select2</li>
                    <li>SweetAlert2, jQuery Toast</li>
                </ul>

                <hr>

                <h5 class="mb-15">7) Catatan Teknis / Checklist Hardening</h5>
                <ul class="pl-3 mb-0">
                    <li>Pastikan endpoint manajemen peran memiliki guard <code>peran == admin</code> (bukan hanya menu).</li>
                    <li>Hapus kode debugging yang tersisa (mis. <code>die(var_dump(...))</code>) bila masih ada.</li>
                    <li>Standarkan response JSON & error handling untuk endpoint AJAX.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
