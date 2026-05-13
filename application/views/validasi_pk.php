<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-light bg-transparent">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="pck">Periode Perjanjian KinerjaK</a></li>
        <li class="breadcrumb-item active" aria-current="page">Persetujuan Perjanjian Kinerja</li>
    </ol>
</nav>

<div class="row">
    <div class="col">
        <?php if (isset($staf) && count($staf) > 0): ?>
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Persetujuan Perjanjian Kinerja Pegawai
                    </h3>
                    <div class="d-flex align-items-center card-action-wrap">
                        <a href="javascript:;" data-page="pck" class="btn btn-sm btn-secondary text-white">
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
                                $status = (int) $row->status;
                                $status_text = 'Status tidak diketahui';
                                $badge_class = 'badge-secondary';
                                if ($status === 0) {
                                    $status_text = 'Draft';
                                    $badge_class = 'badge-danger';
                                } elseif ($status === 1) {
                                    $status_text = 'Belum persetujuan';
                                    $badge_class = 'badge-warning text-dark';
                                } elseif ($status === 2) {
                                    $status_text = 'Sudah persetujuan';
                                    $badge_class = 'badge-success';
                                }
                                $nama = $row->nama_pegawai;
                                $jab = $row->jabatan_pegawai;
                                $opt_label = htmlspecialchars($nama . ' — ' . $status_text, ENT_QUOTES, 'UTF-8');
                                ?>
                                <option value="<?= $row->id; ?>"
                                    data-nama="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-jabatan="<?= htmlspecialchars($jab, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-status-text="<?= htmlspecialchars($status_text, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-badge-class="<?= htmlspecialchars($badge_class, ENT_QUOTES, 'UTF-8'); ?>">
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
                <p>Anda Tidak Memiliki Staf atau Staf Belum Membuat Perjanjian Kinerja.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <div id="tabelDetailPKStaf"></div>
    </div>
</div>

<script>
    $(document).ready(function () {
        initValidasiPkStafSelect();
    });

    /**
 * Dropdown pegawai di halaman persetujuan PK (validasi_pk) — Select2 + badge status.
 */
    function initValidasiPkStafSelect() {
        var $staf = $('#staf');
        if (!$staf.length) {
            return;
        }

        function formatStafOption(state) {
            if (!state.id) {
                return $('<span class="text-muted">').text(state.text);
            }
            var $opt = $(state.element);
            var nama = $opt.attr('data-nama') || state.text;
            var jbtn = $opt.attr('data-jabatan') || '';
            var stext = $opt.attr('data-status-text') || '';
            var badgeClass = $opt.attr('data-badge-class') || 'badge-secondary';

            var $wrap = $('<div class="select2-staf-option py-1">');
            var $top = $('<div class="d-flex align-items-center justify-content-between flex-wrap">');
            $top.append($('<strong class="mr-2 mb-0">').text(nama));
            $top.append($('<span class="badge ' + badgeClass + ' badge-pill mb-0">').text(stext));
            $wrap.append($top);
            if (jbtn) {
                $wrap.append($('<div class="small mt-1 text-truncate" style="max-width:100%">').text(jbtn));
            }
            return $wrap;
        }

        function formatStafSelection(state) {
            if (!state.id) {
                return $('<span class="text-muted">').text(state.text);
            }
            var $opt = $(state.element);
            var nama = $opt.attr('data-nama') || state.text;
            var stext = $opt.attr('data-status-text') || '';
            var badgeClass = $opt.attr('data-badge-class') || 'badge-secondary';

            var $row = $('<span class="d-inline-flex align-items-center flex-wrap">');
            $row.append($('<span class="mr-2">').text(nama));
            $row.append($('<span class="badge ' + badgeClass + ' badge-pill" style="font-size:0.72rem;">').text(stext));
            return $row;
        }

        if ($staf.data('select2')) {
            $staf.select2('destroy');
        }

        $staf.select2({
            width: '100%',
            placeholder: '-- Pilih pegawai --',
            allowClear: true,
            dropdownAutoWidth: false,
            theme: 'default',
            templateResult: formatStafOption,
            templateSelection: formatStafSelection
        });

        $staf.off('change.validasiPk');
        $staf.on('change.validasiPk', function () {
            if (typeof loadDetailPKStaf === 'function') {
                loadDetailPKStaf(document.getElementById('staf').value);
            }
        });
    }
</script>