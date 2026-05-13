<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Pengisian Capaian Kinerja</h2>
        <p>Detail Pengisian Capaian Kinerja Pegawai<i class="ion ion-md-help-circle-outline ml-5" data-toggle="tooltip"
                data-placement="top" title="" data-original-title="Need help about earning stats"></i></p>
    </div>
</div>

<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-light bg-transparent">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="pck">Periode Perjanjian Kinerja</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pengisian Capaian Kinerja</li>
    </ol>
</nav>

<div class="row">
    <div class="col">
        <div class="card card-primary card-outline">
            <div class="card-header card-header-action">
                <h3>
                    <i class="zmdi zmdi-file-text"></i> Informasi Periode Pengisian Capaian Kinerja
                </h3>
                <div class="d-flex align-items-center card-action-wrap">
                    <?php if ($pengisian->status > 0): ?>
                        <button type="button" class="btn btn-sm btn-success mr-2"
                            onclick="previewPCK(<?= $pengisian->id ?>)">
                            <i class="zmdi zmdi-file-text"></i> Cetak Capaian Kinerja
                        </button>
                    <?php endif; ?>
                    <a href="javascript:;" data-page="penilaian_pck" data-id="<?= $periode_id ?>"
                        class="btn btn-sm btn-secondary text-white">
                        <i class="zmdi zmdi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="pl-0 text-muted" width="40%">Nama Periode</th>
                                <td><span
                                        class="font-weight-bold text-dark"><?= htmlspecialchars($nama_periode); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th class="pl-0 text-muted">Tahun</th>
                                <td><?= htmlspecialchars($tahun); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="pl-0 text-muted">Bulan</th>
                                <td>
                                    <?php
                                    $namaBulan = [
                                        '',
                                        'Januari',
                                        'Februari',
                                        'Maret',
                                        'April',
                                        'Mei',
                                        'Juni',
                                        'Juli',
                                        'Agustus',
                                        'September',
                                        'Oktober',
                                        'November',
                                        'Desember'
                                    ];
                                    echo isset($pengisian->bulan) && $pengisian->bulan ? $namaBulan[$pengisian->bulan] : '-';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="pl-0 text-muted">Status</th>
                                <td>
                                    <?php if ($pengisian->status == 0): ?>
                                        <span class="badge badge-danger">Belum Post</span>
                                    <?php elseif ($pengisian->status == 1): ?>
                                        <span class="badge badge-success">Sudah Post</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <?php if ($pengisian->status == 0) {
            $class_status = 'danger';
            $pesan = 'Capaian Kinerja Bulan Ini Belum Diposting, Silakan Klik Tombol Posting';
        } elseif ($pengisian->status == 1) {
            $class_status = 'success';
            $pesan = 'Penilaian Kinerja Anda Sudah Diposting';
        }
        ?>
        <!-- Page Alerts -->
        <div class="alert alert-<?= $class_status ?> alert-wth-icon alert-dismissible fade show" role="alert">
            <span class="alert-icon-wrap"><i class="zmdi zmdi-help"></i></span> <?= $pesan ?>
        </div>
        <!-- /Page Alerts -->

        <div id="tabelDetailPCK">
            <div class="page-wrapper">
                <div class="page-content">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                    <div class="text-center">
                        <span>Memuat Data Detail Capaian Kinerja... Harap Tunggu Sebentar</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalIndikator" tabindex="-1" role="dialog" aria-labelledby="modalIndikator"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h5 class="modal-title text-white">
                            Pilih Indikator Kinerja Individu
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php if (isset($indikator_kinerja) && !empty($indikator_kinerja)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th width="30%">Sasaran Kegiatan</th>
                                            <th width="55%">Indikator Kinerja</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sasaran_counts = [];
                                        foreach ($indikator_kinerja as $row) {
                                            $key = (string) $row->sasaran_id;
                                            $sasaran_counts[$key] = isset($sasaran_counts[$key]) ? $sasaran_counts[$key] + 1 : 1;
                                        }
                                        $no = 1;
                                        $last_sasaran = null;
                                        foreach ($indikator_kinerja as $i):
                                            $sasaran = (string) $i->sasaran_id;
                                            $is_new_sasaran = ($sasaran !== $last_sasaran);
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?= $no++; ?>
                                                </td>
                                                <?php if ($is_new_sasaran): ?>
                                                    <td rowspan="<?= (int) $sasaran_counts[$sasaran]; ?>" class="align-middle">
                                                        <small class="text-muted">
                                                            <?= htmlspecialchars($i->nama_sasaran); ?>
                                                        </small>
                                                    </td>
                                                <?php endif; ?>
                                                <td>
                                                    <strong>
                                                        <?= htmlspecialchars($i->nama_indikator); ?>
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        onclick="pilihIndikator('<?= $i->id; ?>', '<?= $id_param ?>')"
                                                        title="Pilih Indikator">Pilih
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php $last_sasaran = $sasaran; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalUraianTugas" tabindex="-1" role="dialog"
            aria-labelledby="modalUraianTugasLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h5 class="modal-title text-white" id="judulUraianTugas">
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formUraianTugas">
                        <div class="modal-body">
                            <input type="hidden" id="uraian_id" name="uraian_id">
                            <input type="hidden" id="pck_id" name="pck_id">

                            <div class="form-group">
                                <label for="nama_iki_display">Indikator Kinerja</label>
                                <textarea type="text" class="form-control" id="nama_iki_display" rows="5"
                                    readonly></textarea>
                            </div>

                            <div class="form-group">
                                <label for="uraian_tugas">Uraian Tugas <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="uraian_tugas" name="uraian_tugas" rows="3"
                                    placeholder="Masukkan uraian tugas sesuai dengan pekerjaan yang dilakukan..."
                                    required></textarea>
                                <small class="form-text text-muted">Jelaskan secara detail pekerjaan yang telah
                                    dilaksanakan.</small>
                            </div>

                            <div class="form-group">
                                <label for="satuan">Satuan <span class="text-danger">*</span></label>
                                <input class="form-control" id="satuan" name="satuan"
                                    placeholder="Satuan Kegiatan (Laporan, Dokumen, Kegiatan)">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="target_kuantitas">Target Kuantitas <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="target_kuantitas"
                                            name="target_kuantitas" min="0" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="target_kuantitas">Realisasi Kuantitas <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="realisasi_kuantitas"
                                            name="realisasi_kuantitas" min="0" placeholder="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tautan">Tautan Bukti Dukung <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tautan" name="tautan"
                                    placeholder="Tautan Bukti Dukung atas Uraian Tugas" required>
                                <small class="form-text text-muted">Tautan Bukti Dukung Berupa <em>Link</em></small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Preview PCK -->
        <div class="modal fade" id="modal-preview-pck" tabindex="-1" role="dialog"
            aria-labelledby="modalPreviewPCKLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h4 class="modal-title text-white" id="modalPreviewPCKLabel">
                            <i class="fas fa-file-alt"></i> Preview Penilaian Capaian Kinerja (PCK)
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="preview-pck-content" style="max-height: 80vh; overflow-y: auto;">
                        <!-- Content akan di-load via AJAX -->
                        <div class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Memuat data...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>
                        <button type="button" class="btn btn-success" onclick="cetakPCK()">
                            Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadTabelDetailPCK('<?= $id_param ?>');
    });
</script>