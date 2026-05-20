<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Detail Perjanjian Kinerja</h2>
        <p>Detail Periode Perjanjian Kinerja Pegawai<i class="ion ion-md-help-circle-outline ml-5"
                data-toggle="tooltip" data-placement="top" title="" data-original-title="Kelola periode penilaian"></i>
        </p>
    </div>
</div>

<nav class="hk-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:;" data-page="pck">Periode Perjanjian Kinerja</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Perjanjian Kinerja</li>
    </ol>
</nav>
<div class="row">
    <div class="col">
        <?php if (isset($periode) && $periode): ?>
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Informasi Periode
                    </h3>
                    <div class="d-flex align-items-center card-action-wrap">
                        <?php if ($periode->status == 2): ?>
                            <button type="button" class="btn btn-sm btn-success mr-2" onclick="previewPK(<?= $periode->id ?>)">
                                <i class="zmdi zmdi-file-text"></i> Cetak Perjanjian Kinerja
                            </button>
                        <?php endif; ?>
                        <a href="javascript:;" data-page="pck" class="btn btn-sm btn-secondary">
                            <i class="zmdi zmdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-0">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th class="pl-0 text-muted" width="40%">Nama Periode</th>
                                    <td><span class="font-weight-bold">
                                            <?= htmlspecialchars($periode->nama_periode); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="pl-0 text-muted">Tahun</th>
                                    <td>
                                        <?= htmlspecialchars($periode->tahun); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="pl-0 text-muted">Periode</th>
                                    <td class="Indikator">
                                        <?= !empty($periode->periode_awal) ? date('d-m-Y', strtotime($periode->periode_awal)) : '<em class="text-muted">-</em>'; ?>
                                        <span class="mx-1">s/d</span>
                                        <?= !empty($periode->periode_akhir) ? date('d-m-Y', strtotime($periode->periode_akhir)) : '<em class="text-muted">-</em>'; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th class="pl-0 text-muted" width="40%">Dibuat</th>
                                    <td>
                                        <i class="far fa-clock mr-1 text-primary"></i>
                                        <?= date('d M Y H:i', strtotime($periode->created_on)); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="pl-0 text-muted">Status</th>
                                    <td>
                                        <?php if ($periode->status == 0): ?>
                                            <span class="badge badge-danger">Draft</span>
                                        <?php elseif ($periode->status == 1): ?>
                                            <span class="badge badge-warning">Pengajuan</span>
                                        <?php elseif ($periode->status == 2): ?>
                                            <span class="badge badge-success">Persetujuan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<div class="row">
    <div class="col">
        <?php if ($periode->status == 0) {
            $class_status = 'danger';
            $pesan = 'Penilaian Kinerja Anda Belum Disetujui Atasan, Silakan Ajukan Persetujuan';
        } elseif ($periode->status == 1) {
            $class_status = 'warning';
            $pesan = 'Penilaian Kinerja Anda Sudah Diajukan, Sedang Proses Telaah Atasan';
        } else {
            $class_status = 'success';
            $pesan = 'Penilaian Kinerja Anda Sudah Disetujui';
        }
        ?>
        <!-- Page Alerts -->
        <div class="alert alert-<?= $class_status ?> alert-wth-icon alert-dismissible fade show" role="alert">
            <span class="alert-icon-wrap"><i class="zmdi zmdi-help"></i></span> <?= $pesan ?>
        </div>
        <!-- /Page Alerts -->

        <div id="tabelDetailPK">
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

        <div class="modal fade" id="modalSasaran" tabindex="-1" role="dialog" aria-labelledby="modalSasaran"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h4 class="modal-title text-white" id="modalSasaranJudul"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formSasaran">
                        <div class="modal-body">
                            <!-- startForm-->
                            <input type="hidden" id="Terlaksananya Peningkatan Kompetensi Pegawai" name="periode_id">
                            <input type="hidden" id="sasaran_id" name="sasaran_id">
                            <div class="form-group">
                                <label class="form-label" for="nama_rhk">Sasaran Kegiatan :</label>
                                <textarea class="form-control" id="nama_sasaran" name="nama_sasaran" rows="3"
                                    placeholder="Deskripsi Sasaran Kegiatan" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type=" button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalIndikator" tabindex="-1" role="dialog" aria-labelledby="modalIndikator"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h4 class="modal-title text-white" id="modalIndikatorJudul"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formIndikator">
                        <div class="modal-body">
                            <input type="hidden" id="periode_id_indikator" name="periode_id">
                            <input type="hidden" id="sasaran_id_indikator" name="sasaran_id">
                            <input type="hidden" id="indikator_id" name="indikator_id">
                            <div class="form-group">
                                <label class="form-label" for="nama_rhk">Indikator Kinerja :</label>
                                <textarea class="form-control" id="nama_indikator" name="nama_indikator" rows="3"
                                    placeholder="Deskripsi Indikator Kinerja Individu" required></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="target_mutu">Target Kuantitas :</label>
                                <input type="number" class="form-control" id="target_kuantitas" name="target_kuantitas"
                                    placeholder="Diisi Dengan Jumlah Target (Contoh : 10))" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="target_mutu">Satuan :</label>
                                <input type="text" class="form-control" id="satuan" name="satuan"
                                    placeholder="Diisi Dengan Satuan Target (Contoh : Laporan)" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Waktu Penyelesaian :</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-info" onclick="pilihSemuaBulan()">
                                        <i class="zmdi zmdi-check-square"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary"
                                        onclick="batalPilihSemuaBulan()">
                                        <i class="zmdi zmdi-square-o"></i> Batal Pilih Semua
                                    </button>
                                </div>
                                <div class="row">
                                    <?php
                                    $bulan = [
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
                                    foreach ($bulan as $index => $nama_bulan):
                                        $bulan_value = $index + 1;
                                        ?>
                                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input bulan-checkbox" type="checkbox"
                                                    name="bulan_penyelesaian[]" value="<?= $bulan_value ?>"
                                                    id="bulan_<?= $bulan_value ?>">
                                                <label class="form-check-label" for="bulan_<?= $bulan_value ?>">
                                                    <?= $nama_bulan ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="target_mutu">Pagu Anggaran :</label>
                                <input type="text" class="form-control" id="anggaran" name="anggaran"
                                    placeholder="Diisi Dengan Jumlah Pagu Anggaran dalam Rupiah (Contoh : 10))">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalLokasiTanggal" tabindex="-1" role="dialog" aria-labelledby="modalLokasiTanggal"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h4 class="modal-title text-white" id="modalInputLokasiLabel">
                            <i class="fas fa-map-marker-alt"></i> Masukkan Lokasi dan Tanggal
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formLokasiTanggal">
                            <input type="hidden" id="periode_id_pk" name="periode_id">
                            <div class="form-group">
                                <label class="form-label" for="lokasi_pk">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lokasi_pk" name="lokasi"
                                    placeholder="Contoh: Jakarta" required>
                                <small class="form-text text-muted">Masukkan lokasi penandatanganan dokumen</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="tanggal_pk">Tanggal <span class="text-danger">*</span></label>
                                <input type="hidden" class="form-control" id="tanggal_pk_val" name="tanggal" required>
                                <input type="text" class="form-control" id="tanggal_pk" required>
                                <small class="form-text text-muted">Pilih tanggal penandatanganan dokumen</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary" onclick="lanjutkanPreviewPK()">
                            Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPreviewPK" tabindex="-1" role="dialog" aria-labelledby="modalPreviewPK"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 95%;">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-dark">
                        <h4 class="modal-title text-white">
                            <i class="fas fa-file-pdf"></i> Preview Perjanjian Kinerja
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="previewPK" style="max-height: 80vh; overflow-y: auto;">
                        <!-- Skeleton loading placeholder -->
                        <div class="pk-skeleton">
                            <div class="skeleton-header-pk">
                                <div class="skeleton-logo"></div>
                                <div class="skeleton-title-block">
                                    <div class="skeleton skeleton-title"></div>
                                    <div class="skeleton skeleton-subtitle"></div>
                                </div>
                            </div>
                            <div class="skeleton-divider"></div>
                            <div class="skeleton-info-row">
                                <div class="skeleton-info-item">
                                    <div class="skeleton skeleton-label"></div>
                                    <div class="skeleton skeleton-value"></div>
                                </div>
                                <div class="skeleton-info-item">
                                    <div class="skeleton skeleton-label"></div>
                                    <div class="skeleton skeleton-value"></div>
                                </div>
                                <div class="skeleton-info-item">
                                    <div class="skeleton skeleton-label"></div>
                                    <div class="skeleton skeleton-value"></div>
                                </div>
                            </div>
                            <div class="skeleton-table-wrap">
                                <div class="skeleton skeleton-table-header"></div>
                                <div class="skeleton skeleton-row-pk"></div>
                                <div class="skeleton skeleton-row-pk"></div>
                                <div class="skeleton skeleton-row-pk"></div>
                                <div class="skeleton skeleton-row-pk"></div>
                                <div class="skeleton skeleton-row-pk"></div>
                            </div>
                            <div class="skeleton-footer-pk">
                                <div class="skeleton skeleton-footer-row"></div>
                                <div class="skeleton skeleton-footer-row"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cetakPK()">
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
        loadTabelDetailPK('<?= $id_param ?>');
    });

    function pilihSemuaBulan() {
        $('.bulan-checkbox').prop('checked', true);
    }

    function batalPilihSemuaBulan() {
        $('.bulan-checkbox').prop('checked', false);
    }

    $(function () {
        $('#tanggal_pk').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: 2100,
            locale: {
                format: 'DD-MM-YYYY'
            },
            cancelClass: 'btn-secondary'
        });

        $('#tanggal_pk').on('apply.daterangepicker', function (ev, picker) {
            let start = picker.startDate;

            // tampilkan format UI
            $(this).val(start.format('DD-MM-YYYY'));

            // simpan ke hidden (backend)
            $('#tanggal_pk_val').val(start.format('YYYY-MM-DD'));
        });

        $('#anggaran').on('input', function () {
            let value = $(this).val().replace(/\D/g, ''); // hapus selain angka
            $(this).val(formatRupiah(value));
        });
    });

    function cetakPK() {
        var printContent = document.getElementById('previewPK').innerHTML;

        // Buat window baru untuk print
        var printWindow = window.open('', '_blank');
        printWindow.document.open();
        printWindow.document.write('<!DOCTYPE html>');
        printWindow.document.write('<html><head>');
        printWindow.document.write('<title>Perjanjian Kinerja</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }');
        printWindow.document.write('.pk-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 20px; }');
        printWindow.document.write('.pk-table th, .pk-table td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle; }');
        printWindow.document.write('.pk-table th { background-color: #f0f0f0; font-weight: bold; }');
        printWindow.document.write('.pk-table .text-left { text-align: left; }');
        printWindow.document.write('.pk-table .text-right { text-align: right; }');
        printWindow.document.write('.pk-header { text-align: center; margin-bottom: 20px; }');
        printWindow.document.write('.pk-header h3 { font-size: 14px; font-weight: bold; margin-bottom: 5px; }');
        printWindow.document.write('.pk-header h4 { font-size: 12px; font-weight: bold; margin-bottom: 20px; }');
        printWindow.document.write('.pk-info { margin-bottom: 20px; }');
        printWindow.document.write('.pk-info table { width: 100%; font-size: 11px; }');
        printWindow.document.write('.pk-info td { padding: 3px 10px; }');
        printWindow.document.write('.pk-footer { margin-top: 40px; display: flex; justify-content: space-between; }');
        printWindow.document.write('.pk-signature { width: 45%; text-align: center; }');
        printWindow.document.write('.pk-signature table { width: 100%; font-size: 11px; }');
        printWindow.document.write('.pk-checkbox { font-size: 14px; font-weight: bold; }');
        printWindow.document.write('@media print { @page { size: A4 landscape; margin: 1cm; } }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Tunggu konten dimuat, lalu print
        printWindow.onload = function () {
            printWindow.print();
        };
    }
</script>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #preview-pk-content,
        #preview-pk-content * {
            visibility: visible;
        }

        #preview-pk-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }
    }

    .pk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-bottom: 20px;
    }

    .pk-table th,
    .pk-table td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
        vertical-align: middle;
    }

    .pk-table th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    .pk-table .text-left {
        text-align: left;
    }

    .pk-table .text-right {
        text-align: right;
    }

    .pk-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .pk-header h3 {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .pk-header h4 {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .pk-info {
        margin-bottom: 20px;
    }

    .pk-info table {
        width: 100%;
        font-size: 11px;
    }

    .pk-info td {
        padding: 3px 10px;
    }

    .pk-footer {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }

    .pk-signature {
        width: 45%;
        text-align: center;
    }

    .pk-signature table {
        width: 100%;
        font-size: 11px;
    }

    .pk-checkbox {
        font-size: 14px;
        font-weight: bold;
    }
</style>