<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Monitoring Penilaian Capaian Kinerja Pegawai</h2>
        <p>Daftar Penilaian Capaian Kinerja Pegawai<i class="ion ion-md-help-circle-outline ml-5"
                data-toggle="tooltip" data-placement="top" title=""
                data-original-title="Need help about earning stats"></i></p>
    </div>
</div>

<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-light bg-transparent">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="dashboard">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Monitoring Penilaian</li>
    </ol>
</nav>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Nama</label>
                            <select id="filterNama" class="form-control select2" style="width:100%">
                                <option value="">Semua</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tahun</label>
                            <select id="filterTahun" class="form-control select2" style="width:100%">
                                <option value="">Semua</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Bulan</label>
                            <select id="filterBulan" class="form-control select2" style="width:100%">
                                <option value="">Semua</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelMonitoringPenilaian" class="table table-striped table-bordered table-hover w-100">
                        <thead>
                            <tr>
                                <th style="width:5%">No</th>
                                <th>Nama</th>
                                <th style="width:10%">Tahun</th>
                                <th style="width:12%">Bulan</th>
                                <th style="width:12%">Status</th>
                                <th style="width:12%">Nilai</th>
                                <th style="width:18%">Dibuat</th>
                                <th style="width:14%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview PCK -->
<div class="modal fade" id="modal-preview-pck" tabindex="-1" role="dialog" aria-labelledby="modalPreviewPCKLabel"
    aria-hidden="true">
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
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
                <button type="button" id="btnCetakPCK" class="btn btn-success" onclick="cetakPCK()">
                    Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        if (typeof initMonitoringPenilaian === 'function') {
            initMonitoringPenilaian();
        }
        $(".select2").select2({ width: '100%', allowClear: true });
    });
</script>