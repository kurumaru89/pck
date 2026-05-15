<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Penilaian Capaian Kinerja</h2>
        <p>Daftar Penilaian Capaian Kinerja Pegawai<i class="ion ion-md-help-circle-outline ml-5" data-toggle="tooltip"
                data-placement="top" title="" data-original-title="Need help about earning stats"></i></p>
    </div>
</div>

<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="pck">Periode Perjanjian Kinerja</a></li>
        <li class="breadcrumb-item active" aria-current="page">Penilaian Capaian Kinerja</li>
    </ol>
</nav>

<div class="row">
    <div class="col">
        <div class="d-flex mb-0 flex-wrap justify-content-end">
            <div class="btn-group btn-group-sm btn-group-rounded mb-15 mr-15" role="group">
                <button type="button" data-page="pck" class="btn btn-secondary"><i class="zmdi zmdi-arrow-left"></i>
                    Kembali</button>
                <?php if (!$this->session->userdata('ketua')) { ?>
                    <button type="button" class="btn btn-primary" onclick="modalPeriodePenilaian('<?= $id_param ?>')"><i
                            class="zmdi zmdi-calendar-alt"></i> Tambah Periode Penilaian</button>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs" id="penilaianTabs" role="tablist">
                    <?php if (!$ketua): ?>
                        <li class="nav-item">
                            <a class="nav-link active" id="pengisian-tab" data-toggle="tab" href="#pengisian" role="tab"
                                aria-controls="pengisian" aria-selected="true">Pengisian Capaian Kinerja
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" id="evaluasi-tab" data-toggle="tab" href="#evaluasi" role="tab"
                            aria-controls="evaluasi" aria-selected="false">Evaluasi Kinerja Bawahan
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="penilaianTabsContent">
                    <?php if (!$ketua): ?>
                        <div class="tab-pane fade show active" id="pengisian" role="tabpanel"
                            aria-labelledby="pengisian-tab">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Daftar Periode Penilaian Capaian Kinerja</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" id="tabelPeriodePCK">
                                    <!-- Skeleton loading -->
                                    <div class="periode-pk-skeleton">
                                        <div class="skeleton-card">
                                            <div class="skeleton-card-header">
                                                <div class="skeleton skeleton-card-title"></div>
                                            </div>
                                            <div class="skeleton-table-section">
                                                <div class="skeleton skeleton-table-head"></div>
                                                <div class="skeleton skeleton-table-row"></div>
                                                <div class="skeleton skeleton-table-row"></div>
                                                <div class="skeleton skeleton-table-row"></div>
                                                <div class="skeleton skeleton-table-row"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="tab-pane fade<?php if ($ketua): ?> show active<?php endif; ?>" id="evaluasi"
                        role="tabpanel" aria-labelledby="evaluasi-tab">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Daftar Periode Evaluasi Kinerja Bawahan</h5>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" id="tabelPenilaianPCK">
                                <!-- Skeleton loading -->
                                <div class="periode-pk-skeleton">
                                    <div class="skeleton-card">
                                        <div class="skeleton-card-header">
                                            <div class="skeleton skeleton-card-title"></div>
                                        </div>
                                        <div class="skeleton-table-section">
                                            <div class="skeleton skeleton-table-head"></div>
                                            <div class="skeleton skeleton-table-row"></div>
                                            <div class="skeleton skeleton-table-row"></div>
                                            <div class="skeleton skeleton-table-row"></div>
                                            <div class="skeleton skeleton-table-row"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPeriodePCK" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h5 class="modal-title text-white" id="modalPeriodeTitle">Tambah Periode Penilaian</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formPeriodePCK">
                        <div class="modal-body">
                            <input type="hidden" id="periode_id" name="periode_id">
                            <div class="form-group">
                                <label>Periode Penilaian <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="bulan" name="bulan" required>
                                    <option value="">-- Pilih Periode --</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadPCK('<?= $id_param ?>');
    });

    $(function () {
        $(".select2").select2();
    });
</script>