<!-- Title -->
<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Periode Penilaian Capaian Kinerja</h2>
        <p>Daftar Periode Penilaian Capaian Kinerja Pegawai<i class="ion ion-md-help-circle-outline ml-5"
                data-toggle="tooltip" data-placement="top" title="" data-original-title="Kelola periode penilaian"></i>
        </p>
    </div>
    <?php if (!$this->session->userdata('ketua')) { ?>
        <div class="d-flex">
            <button type="button" class="btn btn-with-icon btn-primary btn-lg"
                onclick="formPeriodePK('<?= base64_encode($this->encryption->encrypt('-1')) ?>')"><i
                    class="dropdown-icon zmdi zmdi-plus"></i><span> Tambah
                    Periode</span></button>
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-xl-12">
        <!-- Page Alerts -->
        <div class="alert alert-primary alert-wth-icon fade show" role="alert">
            <span class="alert-icon-wrap"><i class="zmdi zmdi-info"></i></span> Kelola periode penilaian capaian kinerja
            pegawai di halaman ini.
        </div>
        <!-- /Page Alerts -->

        <div id="tabelPeriodePK">
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

        <div class="modal fade" id="modalPeriode" tabindex="-1" role="dialog" aria-labelledby="modalPeriode"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPeriodeJudul"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formPeriodePK">
                        <div class="modal-body">
                            <input type="hidden" id="periode_id" name="periode_id">
                            <div class="form-group">
                                <input type="text" class="form-control" id="nama_periode" name="nama_periode" required
                                    autocomplete="off" placeholder="">
                                <label class="label-float">Nama Periode <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-group">
                                <select name="tahun" id="tahun" class="form-control">
                                </select>
                                <label class="label-float">Tahun <span class="text-danger">*</span></label>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="hidden" name="periode_awal" id="periode_awal_val">
                                            <input type="text" class="form-control" id="periode_awal" autocomplete="off"
                                                readonly required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><i class="zmdi zmdi-calendar"></i></div>
                                            </div>
                                        </div>
                                        <label class="label-float">Periode Awal <span
                                                class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="hidden" name="periode_akhir" id="periode_akhir_val">
                                            <input type="text" class="form-control" id="periode_akhir"
                                                autocomplete="off" readonly required>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><i class="zmdi zmdi-calendar"></i></div>
                                            </div>
                                        </div>
                                        <label class="label-float">Periode Akhir <span
                                                class="text-danger">*</span></label>
                                    </div>
                                </div>
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
        loadTabelPeriodePK();

        let startYear = 2025;
        let endYear = new Date().getFullYear() + 1;

        for (let i = endYear; i >= startYear; i--) {
            $('#tahun').append(`<option value="${i}">${i}</option>`);
        }

        $('#tahun').select2();

        $('#periode_awal, #periode_akhir').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: 2100,
            drops: 'up',
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelClass: 'btn-secondary'
        });

        // EVENT saat pilih periode_awal
        $('#periode_awal').on('apply.daterangepicker', function (ev, picker) {
            let start = picker.startDate;

            // tampilkan format UI
            $(this).val(start.format('DD-MM-YYYY'));

            // simpan ke hidden (backend)
            $('#periode_awal_val').val(start.format('YYYY-MM-DD'));

            // set minimal tanggal di periode_akhir
            let endPicker = $('#periode_akhir').data('daterangepicker');
            endPicker.minDate = start;

            // kalau periode_akhir lebih kecil → reset
            if (endPicker.startDate.isBefore(start)) {
                endPicker.setStartDate(start);
                $('#periode_akhir').val(start.format('DD-MM-YYYY'));
                $('#periode_akhir_val').val(start.format('YYYY-MM-DD'));
            }
        });


        // EVENT saat pilih periode_akhir
        $('#periode_akhir').on('apply.daterangepicker', function (ev, picker) {
            let end = picker.startDate;

            let startVal = $('#periode_awal_val').val();

            // validasi tambahan (double safety)
            if (startVal && moment(end).isBefore(moment(startVal))) {
                alert('Periode akhir tidak boleh lebih kecil dari periode awal');
                return;
            }

            // tampilkan format UI
            $(this).val(end.format('DD-MM-YYYY'));

            // simpan ke hidden
            $('#periode_akhir_val').val(end.format('YYYY-MM-DD'));
        });
    });
</script>