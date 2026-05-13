<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'HalamanUtama';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['show_role'] = 'HalamanUtama/show_role';
$route['simpan_peran'] = 'HalamanUtama/simpan_peran';
$route['aktif_peran'] = 'HalamanUtama/aktif_peran';
$route['blok_peran'] = 'HalamanUtama/blok_peran';
$route['cek_token'] = 'HalamanUtama/cek_token_sso';
$route['cek_status_periode'] = 'HalamanCapaianKinerja/cek_status_periode';

$route['show_tabel_periode_pk'] = 'HalamanCapaianKinerja/show_tabel_periode_pk';
$route['show_tabel_detail_pk'] = 'HalamanCapaianKinerja/show_tabel_detail_pk';
$route['show_periode'] = 'HalamanCapaianKinerja/show_periode';
$route['simpan_periode'] = 'HalamanCapaianKinerja/simpan_periode';
$route['hapus_periode'] = 'HalamanCapaianKinerja/hapus_periode';

$route['show_sasaran'] = 'HalamanCapaianKinerja/show_sasaran';
$route['simpan_sasaran'] = 'HalamanCapaianKinerja/simpan_sasaran';
$route['hapus_sasaran'] = 'HalamanCapaianKinerja/hapus_sasaran';
$route['ambil_sasaran_jabatan_periode_ini'] = 'HalamanCapaianKinerja/ambil_sasaran_jabatan_periode_ini';

$route['show_indikator'] = 'HalamanCapaianKinerja/show_indikator';
$route['simpan_indikator'] = 'HalamanCapaianKinerja/simpan_indikator';
$route['hapus_indikator'] = 'HalamanCapaianKinerja/hapus_indikator';

$route['cek_detail_periode'] = 'HalamanCapaianKinerja/cek_detail_periode';
$route['ajukan_persetujuan_pk'] = 'HalamanCapaianKinerja/ajukan_persetujuan_pk';
$route['validasi_pk'] = 'HalamanCapaianKinerja/validasi_pk';
$route['kembalikan_pk_ke_draft'] = 'HalamanCapaianKinerja/kembalikan_pk_ke_draft';
$route['batal_validasi'] = 'HalamanCapaianKinerja/batal_validasi';

$route['get_data_preview_pk'] = 'HalamanLaporan/get_data_preview_pk';

$route['show_daftar_pck'] = 'HalamanCapaianKinerja/show_daftar_pck';
$route['simpan_periode_pck'] = 'HalamanCapaianKinerja/simpan_periode_pck';
$route['show_tabel_detail_pck'] = 'HalamanCapaianKinerja/show_tabel_detail_pck';
$route['simpan_pck_indikator'] = 'HalamanCapaianKinerja/simpan_pck_indikator';
$route['hapus_capaian_indikator'] = 'HalamanCapaianKinerja/hapus_capaian_indikator';

$route['show_uraian_tugas'] = 'HalamanCapaianKinerja/show_uraian_tugas';
$route['simpan_uraian_tugas'] = 'HalamanCapaianKinerja/simpan_uraian_tugas';
$route['hapus_uraian_tugas'] = 'HalamanCapaianKinerja/hapus_uraian_tugas';

$route['show_tabel_pck_staf'] = 'HalamanCapaianKinerja/show_tabel_pck_staf';
$route['show_penilaian_uraian'] = 'HalamanCapaianKinerja/show_penilaian_uraian';
$route['simpan_nilai_uraian_tugas'] = 'HalamanCapaianKinerja/simpan_nilai_uraian_tugas';
$route['hapus_nilai_uraian_tugas'] = 'HalamanCapaianKinerja/hapus_nilai_uraian_tugas';
$route['get_data_penilaian_pck'] = 'HalamanCapaianKinerja/get_data_penilaian_pck';
$route['posting_penilaian_pck'] = 'HalamanCapaianKinerja/posting_penilaian_pck';
$route['monitoring_penilaian_dt'] = 'HalamanCapaianKinerja/monitoring_penilaian_dt';
$route['monitoring_penilaian_filters'] = 'HalamanCapaianKinerja/monitoring_penilaian_filters';
$route['get_data_pck'] = 'HalamanLaporan/get_data_pck';

$route['get_statistik_dashboard'] = 'HalamanUtama/get_statistik_dashboard';
$route['get_statistik_dashboard_pck'] = 'HalamanUtama/get_statistik_dashboard_pck';

$route['keluar'] = 'HalamanUtama/keluar';

$route['unduh_manual_pdf'] = 'HalamanUtama/unduh_manual_pdf';
$route['unduh_manual_word'] = 'HalamanUtama/unduh_manual_word';
