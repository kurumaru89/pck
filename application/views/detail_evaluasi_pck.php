<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="pck">Periode Perjanjian Kinerja</a></li>
        <li class="breadcrumb-item"><a href="javascript:;" data-page="penilaian_pck" data-id="<?= $periode_id ?>">Penilaian Capaian Kinerja</a></li>
        <li class="breadcrumb-item active" aria-current="page">Evaluasi Capaian Kinerja Pegawai</li>
    </ol>
</nav>

<div class="row">
    <div class="col">
        <?php if (isset($staf) && count($staf) > 0): ?>
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Evaluasi Capaian Kinerja Pegawai
                    </h3>
                    <div class="d-flex align-items-center card-action-wrap">
                        <a href="javascript:;" data-page="penilaian_pck" data-id="<?= $periode_id ?>"
                            class="btn btn-sm btn-secondary text-white">
                            <i class="zmdi zmdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="staf" class="d-flex align-items-center font-weight-600 mb-2">
                            <i class="zmdi zmdi-accounts-alt text-primary mr-2 fs-18"></i>
                            Pilih pegawai
                        </label>
                        <p class="text-muted font-14 mb-2">Cari nama atau jabatan di kotak pencarian.</p>
                        <select class="form-control" id="staf" name="staf" data-placeholder="-- Pilih pegawai --">
                            <option value="">&nbsp;</option>
                            <?php foreach ($staf as $row):
                                $nama = $row->nama_pegawai;
                                $jab = $row->jabatan;
                                $opt_label = htmlspecialchars($nama . ' — ' . $jab, ENT_QUOTES, 'UTF-8');
                                ?>
                                <option value="<?= $row->id; ?>"
                                    data-nama="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-jabatan="<?= htmlspecialchars($jab, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-uraian-sudah="<?= isset($row->uraian_sudah_dinilai) ? (int) $row->uraian_sudah_dinilai : 0; ?>"
                                    data-uraian-belum="<?= isset($row->uraian_belum_dinilai) ? (int) $row->uraian_belum_dinilai : 0; ?>">
                                    <?= $opt_label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading mb-10">Informasi !</h4>
                <hr class="hr-soft-info">
                <p>Anda Tidak Memiliki Staf atau Staf Belum Mengisi Penilaian Capaian Kinerja Bulan Ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <div id="tabelDetailPCKStaf">
            <!-- Skeleton loading -->
            <div class="tabel-detail-pk-skeleton">
                <div class="skeleton-card-header">
                    <div class="skeleton skeleton-card-title"></div>
                    <div class="skeleton-btn-group">
                        <div class="skeleton skeleton-btn"></div>
                        <div class="skeleton skeleton-btn"></div>
                    </div>
                </div>
                <div class="skeleton-table-section">
                    <div class="skeleton skeleton-table-head"></div>
                    <div class="skeleton skeleton-table-row"></div>
                    <div class="skeleton skeleton-table-row"></div>
                    <div class="skeleton skeleton-table-row"></div>
                    <div class="skeleton skeleton-table-row"></div>
                    <div class="skeleton skeleton-table-row"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNilaiUraianTugas" tabindex="-1" role="dialog"
        aria-labelledby="modalNilaiUraianTugasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-dark">
                    <h5 class="modal-title text-white" id="judulUraianTugas">
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formNilaiUraianTugas">
                    <div class="modal-body">
                        <input type="hidden" id="uraian_id" name="uraian_id">
                        <input type="hidden" id="penilaian_id" name="penilaian_id">

                        <div class="form-group">
                            <label for="uraian_tugas">Uraian Tugas</label>
                            <textarea class="form-control filled-input" id="uraian_tugas" rows="3" readonly></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="target_kuantitas">Target</label>
                                    <input type="number" class="form-control filled-input" id="target_kuantitas"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="realisasi_kuantitas">Realisasi</label>
                                    <input type="number" class="form-control filled-input" id="realisasi_kuantitas"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div id="tautan"></div>
                        </div>

                        <div class="form-group">
                            <label for="realisasi_kualitas">Mutu Realisasi <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="realisasi_kualitas" name="realisasi_kualitas"
                                min="0" max="100" placeholder="0-100" required>
                            <span class="text-mute">Diisi dengan kualitas hasil uraian tugas yang dikerjakan</span>
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
</div>

<script>
    $(document).ready(function () {
        initEvaluasiPckStafSelect();
    });

    /**
    * Dropdown staf untuk halaman evaluasi PCK (detail_evaluasi_pck) — Select2 + info uraian dinilai/belum.
    */
    function initEvaluasiPckStafSelect() {
        var $staf = $('#staf');
        if (!$staf.length) {
            return;
        }

        function fmtOption(state) {
            if (!state.id) {
                return $('<span class="text-muted">').text(state.text);
            }

            var $opt = $(state.element);
            var nama = $opt.attr('data-nama') || state.text;
            var jbtn = $opt.attr('data-jabatan') || '';
            var sudah = parseInt($opt.attr('data-uraian-sudah') || '0', 10);
            var belum = parseInt($opt.attr('data-uraian-belum') || '0', 10);

            var $wrap = $('<div class="select2-staf-option py-1">');
            var $top = $('<div class="d-flex align-items-center justify-content-between flex-wrap">');
            $top.append($('<strong class="mr-2 mb-0">').text(nama));
            if (jbtn) {
                $top.append($('<span class="small mr-2" style="max-width:100%">').text(jbtn));
            }
            $wrap.append($top);

            var $meta = $('<div class="d-flex align-items-center flex-wrap mt-1">');

            $meta.append($('<span class="badge badge-success border mr-1" style="font-size:0.72rem;">').text('Dinilai: ' + sudah));
            $meta.append($('<span class="badge badge-danger border" style="font-size:0.72rem;">').text('Belum: ' + belum));
            $wrap.append($meta);

            return $wrap;
        }

        function fmtSelection(state) {
            if (!state.id) {
                return $('<span class="text-muted">').text(state.text);
            }
            var $opt = $(state.element);
            var nama = $opt.attr('data-nama') || state.text;
            var sudah = parseInt($opt.attr('data-uraian-sudah') || '0', 10);
            var belum = parseInt($opt.attr('data-uraian-belum') || '0', 10);

            var $row = $('<span class="d-inline-flex align-items-center flex-wrap">');
            $row.append($('<span class="mr-2">').text(nama));
            $row.append($('<span class="badge badge-success border mr-1" style="font-size:0.72rem;">').text('Dinilai: ' + sudah));
            $row.append($('<span class="badge badge-danger border" style="font-size:0.72rem;">').text('Belum: ' + belum));
            return $row;
        }

        if ($staf.data('select2')) {
            $staf.select2('destroy');
        }

        $staf.select2({
            width: '100%',
            placeholder: $staf.data('placeholder') || '-- Pilih pegawai --',
            allowClear: true,
            templateResult: fmtOption,
            templateSelection: fmtSelection
        });

        $staf.off('change.validasiPk');
        $staf.on('change.validasiPk', function () {
            if (typeof loadDetailPCKStaf === 'function') {
                loadDetailPCKStaf(document.getElementById('staf').value);
            }
        });
    }
</script>