var result = config.result;
var pesan = config.pesan;

$(function () {

    $(document).on('submit', '#formPeran', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);

        $.ajax({
            url: 'simpan_peran',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                ModalRole('-1');
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });

    $(document).off('submit', '#formPeriodePK').on('submit', '#formPeriodePK', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);

        $.ajax({
            url: 'simpan_periode',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                if (res.success == '1') {
                    $('#modalPeriode').modal('hide');
                    loadTabelPeriodePK();
                }
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });

    $(document).off('submit', '#formSasaran').on('submit', '#formSasaran', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);
        let periode_id = formData.get('periode_id');

        $.ajax({
            url: 'simpan_sasaran',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                if (res.success == '1') {
                    $('#modalSasaran').modal('hide');
                    loadTabelDetailPK(periode_id);
                }
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });

    $(document).off('submit', '#formIndikator').on('submit', '#formIndikator', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);
        let periode_id = formData.get('periode_id');

        $.ajax({
            url: 'simpan_indikator',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                if (res.success == '1') {
                    $('#modalIndikator').modal('hide');
                    loadTabelDetailPK(periode_id);
                }
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });

    $(document).off('submit', '#formPeriodePCK').on('submit', '#formPeriodePCK', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);
        let periode_id = formData.get('periode_id');

        $.ajax({
            url: 'simpan_periode_pck',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                if (res && res.success == '1') {
                    $('#modalPeriodePCK').modal('hide');
                    $('#modalPeriodePCK').one('hidden.bs.modal', function () {
                        loadPage('penilaian_pck', periode_id);
                    });
                }
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });

    $(document).off('submit', '#formNilaiUraianTugas').on('submit', '#formNilaiUraianTugas', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);
        let penilaian_id = formData.get('penilaian_id');

        $.ajax({
            url: 'simpan_nilai_uraian_tugas',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                if (res && res.success == '1') {
                    $('#modalNilaiUraianTugas').modal('hide');
                    $('#modalNilaiUraianTugas').one('hidden.bs.modal', function () {
                        // Update tabel detail
                        if (typeof loadDetailPCKStaf === 'function') {
                            loadDetailPCKStaf(penilaian_id);
                        }

                        // Update angka "dinilai/belum" pada opsi select2 tanpa reload halaman
                        var $staf = $('#staf');
                        var $opt = $staf.find('option[value="' + penilaian_id + '"]');
                        if ($opt.length) {
                            if (res.uraian_sudah_dinilai !== undefined) $opt.attr('data-uraian-sudah', res.uraian_sudah_dinilai);
                            if (res.uraian_belum_dinilai !== undefined) $opt.attr('data-uraian-belum', res.uraian_belum_dinilai);
                            // refresh tampilan select2 (selection)
                            $staf.trigger('change.select2');
                        }
                    });
                }
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });

    $(document).off('submit', '#formUraianTugas').on('submit', '#formUraianTugas', function (e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);

        $.ajax({
            url: 'simpan_uraian_tugas',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                notifikasi(res.message, res.success);
                if (res && res.success == '1') {
                    $('#modalUraianTugas').modal('hide');
                    $('#modalUraianTugas').one('hidden.bs.modal', function () {
                        loadTabelDetailPCK(res.penilaian_id);
                    });
                }
            },
            error: function () {
                notifikasi('Terjadi kesalahan saat menyimpan data.', 4);
            }
        });
    });
});

function notifikasi(pesan, result) {
    // Use modern toast system from modern-ui.js
    if (typeof ModernUI !== 'undefined') {
        if (result == '1') {
            ModernUI.toastSuccess(pesan);
        } else if (result == '2') {
            ModernUI.toastWarning(pesan);
        } else if (result == '3') {
            ModernUI.toastDanger(pesan);
        } else {
            ModernUI.toastInfo(pesan);
        }
        return false;
    }
    // Fallback to old toast if modern-ui not loaded
    var heading, kelas;
    if (result == '1') {
        heading = 'Berhasil';
        kelas = 'jq-toast-success';
    } else if (result == '2') {
        heading = 'Peringatan';
        kelas = 'jq-toast-warning';
    } else if (result == '3') {
        heading = 'Galat';
        kelas = 'jq-toast-danger';
    } else {
        heading = 'Informasi';
        kelas = 'jq-toast-info';
    }
    $.toast().reset('all');
    $("body").removeAttr('class');
    $.toast({
        heading: heading,
        text: '<p>' + pesan + '</p>',
        position: 'top-right',
        loaderBg: '#7a5449',
        class: kelas,
        hideAfter: 3500,
        stack: 6,
        showHideTransition: 'fade'
    });
    return false;
}

function info(pesan) {
    Swal.fire({
        title: '<h4>Perhatian</h4>',
        html: pesan,
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function loadPage(page, id) {
    // Guard: prevent double-load within 800ms
    var now = Date.now();
    if (window._lastLoadPage && (now - window._lastLoadPage) < 800) {
        return;
    }
    window._lastLoadPage = now;

    cekToken();
    var $app = $('#app');

    // Fade out current content
    $app.css({ opacity: '0', transform: 'translateY(8px)', transition: 'opacity 200ms ease-in, transform 200ms ease-in' });

    setTimeout(function () {
        // Show skeleton loader
        ModernUI.showSkeleton($app.get(0));
        $app.css({ opacity: '1', transform: 'translateY(0)' });

        var params = {};
        if (id !== undefined && id !== null && id !== '') {
            params.id = id;
        }

        $.get("halamanutama/page/" + encodeURIComponent(page), params, function (data) {
            // Fade in new content
            $app.css({ opacity: '0', transform: 'translateY(16px)', transition: 'opacity 0ms' });

            $app.html(data);

            // Animate in
            requestAnimationFrame(function () {
                $app.css({
                    opacity: '1',
                    transform: 'translateY(0)',
                    transition: 'opacity 350ms ease-out, transform 350ms ease-out'
                });
                // Trigger content animations
                ModernUI.animateContent($app.get(0));
                ModernUI.setActiveNavLink(page);
            });

            // Scroll to top smoothly
            $('html, body').animate({ scrollTop: 0 }, 300);

        }).fail(function () {
            $app.html(`
            <div class="glass-card animate-fade-in-up">
                <div class="glass-card-body text-center">
                    <div style="font-size:48px;margin-bottom:16px;opacity:0.3">&#xE8E5;</div>
                    <h4 style="color:var(--text-primary);margin-bottom:8px">Halaman tidak ditemukan</h4>
                    <p style="color:var(--text-muted)">Silakan coba navigasi lain atau refresh halaman.</p>
                    <button class="btn-glass-primary btn-glass-sm mt-3" onclick="loadPage('dashboard')">
                        <i class="material-icons" style="font-size:14px">&#xE88A;</i> Kembali ke Beranda
                    </button>
                </div>
            </div>
            `);
            $app.css({ opacity: '1', transform: 'translateY(0)' });
        });

    }, 200);

    // Update active nav
    ModernUI.setActiveNavLink(page);
}

function cekToken() {
    $.ajax({
        url: 'cek_token',
        type: 'POST',
        dataType: 'json',
        success: function (res) {
            if (!res.valid) {
                alert(res.message);
                window.location.href = res.url;
            }
        }
    });
}

function ModalRole(id) {
    $('#role-pegawai').modal('show');
    $('#btnBatal').hide();
    if (id != '-1') {
        $('#tabel-role').html('');
        $('#btnBatal').show();
    }

    $.post('show_role',
        { id: id },
        function (response) {
            try {
                const json = JSON.parse(response); // pastikan response valid JSON
                $('#pegawai_').html('');

                let html = `<select class="form-control select2" id="pegawai" name="pegawai" style="width:100%">`;
                json.pegawai.forEach(row => {
                    html += `<option value="${row.userid}" data-nama="${row.fullname}" data-jabatan="${row.jabatan}">${row.fullname}</option>`;
                });
                html += `</select>`;
                $('#pegawai_').append(html);

                $('#peran_').html('');
                let role = `<select class="form-control select2" id="peran" name="peran" style="width:100%">`;
                role += `<option value="operator">Operator Kepegawaian</option>`;
                role += `</select>`;
                $('#peran_').append(role);

                $('#pegawai').select2({
                    dropdownParent: $('#role-pegawai .modal-content'),
                    width: '100%',
                    placeholder: "Pilih pegawai",
                    templateResult: formatPegawaiOption,
                    templateSelection: formatPegawaiSelection
                });

                $('#peran').select2({
                    dropdownParent: $('#role-pegawai .modal-content'),
                    width: '100%',
                    placeholder: "Pilih Peran"
                });

                if (id != '-1') {
                    $('#id').val('');

                    $('#id').val(json.id);
                    $('#pegawai').val(json.editPegawai).trigger('change');
                    $('#peran').val(json.editPeran).trigger('change');

                    $('#pegawai').on('select2:opening select2:selecting', function (e) {
                        e.preventDefault(); // mencegah dropdown terbuka
                    });
                } else {
                    $('#tabel-role').html('');

                    let data = `
                    <div class="table-responsive">
                    <table id="tabelPeran" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead><tbody>`;
                    json.data_peran.forEach(row => {
                        if (`${row.peran}` == 'operator') {
                            var peran_ = 'Operator Kepegawaian';
                        }
                        data += `
                        <tr>
                            <td>${row.nama}</td>
                            <td>`;

                        if (`${row.hapus}` == '0') {
                            data += `<span class='badge badge-success'>${peran_}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-warning" id="editPeran" onclick="ModalRole('${row.id}')" title="Edit Peran">
                                    <i class="zmdi zmdi-edit"></i>
								</button>

                                <button type="button" class="btn btn-outline-danger" id="hapusPeran" onclick="blokPeran('${row.id}')" title="Blok Pegawai">
                                    <i class="zmdi zmdi-block"></i>
								</button>`;
                        } else {
                            data += `<span class='badge badge-secondary'>${peran_}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-success" id="hapusPeran" onclick="aktifPeran('${row.id}')" title="Aktifkan Pegawai">
                                    <i class="zmdi zmdi-check-circle"></i>
								</button>`;
                        }
                        data += `
                            </td>
                        </tr>`;
                    });
                    data += `
                        </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <span class='badge badge-success'>Aktif</span>
                        <span class='badge badge-secondary'>Non-aktif</span>
                    </div>
                    </div>`;
                    $('#tabel-role').append(data);
                    $("#tabelPeran").DataTable({
                        lengthChange: false
                    });
                }
            } catch (e) {
                $('#pegawai_').html('<div class="alert alert-danger">Gagal memuat data pegawai.</div>');
            }
        }
    );
}

function aktifPeran(id) {
    Swal.fire({
        title: "Yakin ingin mengaktifkan kembali peran pegawai?",
        text: "Data peran ini akan diaktifkan perannya.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, aktifkan!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            // Eksekusi penghapusan setelah konfirmasi
            $.post('aktif_peran', { id: id }, function (response) {
                Swal.fire("Berhasil!", "Peran telah diaktifkan.", "success");
                ModalRole('-1');
            }).fail(function () {
                Swal.fire("Gagal", "Terjadi kesalahan saat mengaktifkan data.", "error");
            });
        }
    });
}

function blokPeran(id) {
    Swal.fire({
        title: "Yakin ingin menonaktifkan peran pegawai?",
        text: "Data peran ini akan dinonaktifkan perannya.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, nonaktifkan!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            // Eksekusi penghapusan setelah konfirmasi
            $.post('blok_peran', { id: id }, function (response) {
                Swal.fire("Berhasil!", "Peran telah dinonaktifkan.", "success");
                ModalRole('-1');
            }).fail(function () {
                Swal.fire("Gagal", "Terjadi kesalahan saat menghapus data.", "error");
            });
        }
    });
}

function formatPegawaiOption(option) {
    if (!option.id) return option.text;

    const nama = $(option.element).data('nama');
    const jabatan = $(option.element).data('jabatan');

    return $(`
        <div style="line-height:1.2">
            <div style="font-weight:bold;">${nama}</div>
            <div style="font-size:12px;">${jabatan}</div>
        </div>
    `);
}

function formatPegawaiSelection(option) {
    if (!option.id) return option.text;

    const nama = $(option.element).data('nama');
    const jabatan = $(option.element).data('jabatan');

    return `${nama} > ${jabatan}`;
}

function loadTabelPeriodePK() {
    $.post('show_tabel_periode_pk', function (response) {
        try {
            const json = JSON.parse(response); // Pastikan server kirim JSON valid

            $('#tabelPeriodePK').html(''); // kosongkan wrapper

            if (!json.data_periode || json.data_periode.length === 0) {
                // Kalau kosong
                $('#tabelPeriodePK').html(`
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading mb-10">Informasi !</h4>
                        <hr class="hr-soft-info">
                        <p>Belum Ada Periode Perjanjian Kinerja. Terima kasih.</p>
                    </div>
                `);
                return;
            }

            let data = ``;

            if (json.ketua) {
                data = `
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                        <table id="tabelPeriodePKData" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="no" width="5%">#</th>
                                    <th width="40%" style="text-align: center;">Tahun Periode</th>
                                    <th width="55%" style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                json.data_periode.forEach((row, index) => {
                    let tombolValidasi = ``;
                    if (row.struktural) {
                        tombolValidasi = `
                            <button type="button" class="btn btn-outline-primary mr-1 mb-1" data-page="validasi_pk" data-id="${row.tahun}">
                                <i class="zmdi zmdi-view-list mr-1"></i> PK Bawahan
                            </button>
                        `
                    }

                    // Baris tabel
                    data += `
                        <tr>
                            <td class="no">${index + 1}</td>
                            <td style="text-align: center;">
                                <h3>
                                    <span class="badge badge-primary">${row.tahun}</span>
                                </h3>
                            </td>
                            <td style="text-align: center;">
                                ${tombolValidasi}
                                <button type="button" class="btn btn-outline-primary mr-1 mb-1" data-page="penilaian_pck" data-id="${row.tahun}">
                                    <i class="zmdi zmdi-check-all mr-1"></i> Penilaian
                                </button>
                            </td>
                        </tr>
                    `;
                });

                data += `
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                `;
            } else {
                // Kalau ada data, buat tabelnya
                data = `
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                        <table id="tabelPeriodePKData" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="no" width="5%">#</th>
                                    <th width="40%">Nama Periode</th>
                                    <th width="30%" style="text-align: center;">Tahun Periode</th>
                                    <th width="25%" style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                json.data_periode.forEach((row, index) => {
                    let statusBadge = '';
                    if (row.status == '1') {
                        statusBadge = '<span class="badge badge-warning radius-30">Proses Pengajuan</span>';
                    } else if (row.status == '2') {
                        statusBadge = '<span class="badge badge-success radius-30">Sudah Persetujuan</span>';
                    } else {
                        statusBadge = '<span class="badge badge-danger radius-30">Belum Persetujuan</span>';
                    }

                    let tombolEdit = ``;
                    if (row.pegawai_id == row.pegawai_id_sekarang) {
                        tombolEdit = `
                            <button type="button" class="btn btn-sm btn-warning" onclick="formPeriodePK('${row.id}')" title="Edit">
                                <i class="zmdi zmdi-edit"></i> Edit
                            </button>
                        `;
                    }

                    let tombolHapus = ``;
                    if (row.id_jabatan_pegawai == row.jabatan_pegawai_sekarang) {
                        tombolHapus = `
                            <button type="button" class="btn btn-sm btn-danger" onclick="hapusPeriodePK('${row.id}')" title="Edit">
                                <i class="zmdi zmdi-delete"></i> Hapus
                            </button>
                        `;
                    }

                    let tombolDetail = ``;
                    if (!row.ketua) {
                        tombolDetail = `
                            <button type="button" class="btn btn-outline-primary mr-1 mb-1" data-page="detail_pk" data-id="${row.id}">
                                <i class="zmdi zmdi-view-list mr-1"></i> Detail
                            </button>
                        `;
                    }

                    let tombolValidasi = ``;
                    if (row.struktural) {
                        tombolValidasi = `
                            <button type="button" class="btn btn-outline-primary mr-1 mb-1" data-page="validasi_pk" data-id="${row.id}">
                                <i class="zmdi zmdi-view-list mr-1"></i> PK Bawahan
                            </button>
                        `
                    }

                    // Baris tabel
                    data += `
                        <tr>
                            <td class="no">${index + 1}</td>
                            <td class="text-dark">
                                <div class="mb-2">
                                    <span class="font-weight-bold" style="font-size: 1.1rem;">${row.nama_periode}</span>
                                    ${statusBadge}
                                </div>
                                <div class="d-flex flex-wrap align-items-center mb-1">
                                    <i class="zmdi zmdi-calendar text-primary mr-1"></i>
                                    <span class="text-muted small mr-1">Periode:</span>
                                    <span>
                                        <span class="badge badge-light py-1 px-2 border mr-1">
                                            ${row.periode_awal}
                                        </span>
                                        <span class="text-muted mx-1">s/d</span>
                                            <span class="badge badge-light py-1 px-2 border">
                                            ${row.periode_akhir}
                                        </span>
                                    </span>
                                </div>
                                <div class="d-flex flex-wrap align-items-center mb-2">
                                    <i class="zmdi zmdi-case text-primary mr-1"></i>
                                    <span class="text-muted small mr-1">Jabatan:</span>
                                    <span class="font-italic">${row.jabatan_pegawai}</span>
                                </div>
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    ${tombolDetail}
                                    ${tombolValidasi}
                                    <button type="button" class="btn btn-outline-primary mr-1 mb-1" data-page="penilaian_pck" data-id="${row.id}">
                                        <i class="zmdi zmdi-check-all mr-1"></i> Penilaian
                                    </button>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <h3>
                                    <span class="badge badge-primary">${row.tahun}</span>
                                </h3>
                            </td>
                            <td style="text-align: center;">
                                ${tombolEdit}
                                ${tombolHapus}
                            </td>
                        </tr>
                    `;
                });

                data += `
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                `;
            }



            $('#tabelPeriodePK').append(data);

        } catch (e) {
            $('#tabelPeriodePK').html('<div class="alert alert-danger">Gagal memuat data periode penilaian.</div>');
        }
    });
}

function loadTabelDetailPK(id) {
    $.post('show_tabel_detail_pk', { id: id }, function (response) {
        try {
            const json = JSON.parse(response); // Pastikan server kirim JSON valid

            $('#tabelDetailPK').html(''); // kosongkan skeleton, ganti dengan data

            let tombolKepala = ``;
            if (json.status == 0) {
                tombolKepala = `
                    <button type="button" class="btn btn-sm btn-warning mr-2" onclick="pengajuanPK('${id}')"
                        data-toggle="tooltip-primary" data-placement="top" data-original-title="Pengajuan Atasan">
                        <i class="zmdi zmdi-upload"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary mr-2" onclick="modalSasaran('-1', '${id}')"
                        data-toggle="tooltip" data-placement="top" data-original-title="Tambah Sasaran">
                    <i class="zmdi zmdi-plus-circle"></i>
                    </button>
                `
            }

            if (!json.data_sasaran || json.data_sasaran.length === 0) {
                tombolKepala += `
                    <button type="button" class="btn btn-primary btn-sm" onclick="generatePeriodeBaru()"
                        data-toggle="tooltip" data-placement="top" data-original-title="Salin PK Sebelumnya">
                        <i class="zmdi zmdi-copy"></i>
                    </button>
                `;
            }

            // Kalau ada data, buat tabelnya
            let data = `
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Perjanjian Kinerja Individu
                    </h3>
                    <div class="d-flex align-items-center card-action-wrap">
                        ${tombolKepala}
                    </div>
                </div>
                <div class="card-body">
            `;

            if (!json.data_sasaran || json.data_sasaran.length === 0) {
                //
                if (json.get_sasaran_jabatan)
                    ambilSasaranJabatan(id);
                // Kalau kosong
                data += `
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading mb-10">Informasi !</h4>
                        <hr class="hr-soft-info">
                        <p>Anda Belum Mengisi Perjanjian Kinerja Periode Ini.</p>
                    </div>
                </div>
            </div>
            `;

            } else {
                data += `
                <div class="table-responsive">
                    <table id="tabelDetailPKData" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sasaran Kegiatan</th>
                                <th>Indikator Kinerja</th>
                            </tr>
                        </thead>
                        <tbody>
                `

                json.data_sasaran.forEach((row_sasaran, index_sasaran) => {

                    let tombolSasaran = ``;
                    if (row_sasaran.jabatan_id == row_sasaran.jabatan_id_sekarang && json.status == 0) {
                        tombolSasaran = `
                            <div class="btn-group mt-2" role="group">
                                <button type="button" class="btn btn-sm btn-warning"
                                    onclick="modalSasaran('${row_sasaran.id}', '${id}')"
                                    data-toggle="tooltip" data-placement="top" data-original-title="Edit Sasaran">
                                    <i class="zmdi zmdi-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="hapusSasaranKegiatan('${row_sasaran.id}')"
                                    data-toggle="tooltip" data-placement="top" data-original-title="Hapus Sasaran">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </div>
                        `;
                    }

                    let dataIndikator = ``;
                    if (json.indikator || json.indikator.length > 0) {
                        json.indikator.forEach((row_indikator, index) => {
                            // parsing dan mapping bulan dulu
                            let bulan_selected = '';

                            try {
                                const bulan_array = JSON.parse(row_indikator.bulan_penyelesaian || '[]');

                                if (Array.isArray(bulan_array) && bulan_array.length > 0) {
                                    const bulan_nama = {
                                        1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr',
                                        5: 'Mei', 6: 'Jun', 7: 'Jul', 8: 'Agu',
                                        9: 'Sep', 10: 'Okt', 11: 'Nov', 12: 'Des'
                                    };

                                    bulan_selected = bulan_array
                                        .map(b => bulan_nama[b] ?? b)
                                        .join(', ');
                                }
                            } catch (e) {
                                bulan_selected = '';
                            }

                            let tombolIndikator = ``;
                            if (row_sasaran.id == row_indikator.sasaran_id) {
                                if (row_sasaran.jabatan_id == row_sasaran.jabatan_id_sekarang && json.status == 0) {
                                    tombolIndikator = `
                                        <div class="btn-group mt-2" role="group">
                                            <button type="button" class="btn btn-sm btn-warning"
                                                onclick="modalIndikator('${row_indikator.id}', '${row_sasaran.id}', '${id}')"
                                                data-toggle="tooltip" data-placement="top" data-original-title="Edit Indikator">
                                                <i class="zmdi zmdi-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="hapusIndikatorKinerja('${row_indikator.id}')"
                                                data-toggle="tooltip" data-placement="top" data-original-title="Hapus Indikator">
                                                <i class="zmdi zmdi-delete"></i>
                                            </button>
                                        </div>
                                    `;
                                }

                                let anggaran = `-`;
                                if (row_indikator.anggaran) {
                                    anggaran = new Intl.NumberFormat('id-ID').format(row_indikator.anggaran);
                                }

                                dataIndikator += `
                                    <li>
                                        <strong>${row_indikator.nama_indikator}</strong>
                                        <div class="row">
                                            <div class="col-4">
                                                <small>
                                                    <i class="zmdi zmdi-gps-dot"></i> Target Mutu : ${row_indikator.target_mutu}
                                                </small>
                                            </div>
                                            <div class="col-4">
                                                <small>
                                                    <i class="zmdi zmdi-arrows"></i> Target Kuantitas : ${row_indikator.target_kuantitas}
                                                </small>
                                            </div>
                                            <div class="col-4">
                                                <small>
                                                    <i class="zmdi zmdi-local-activity"></i> Satuan : ${row_indikator.satuan}
                                                </small>
                                            </div>
                                        </div>
                                        <small>
                                            Bulan : ${bulan_selected}
                                        </small><br>
                                        <small>
                                            Pagu Anggaran : Rp${anggaran}
                                        </small><br>
                                        ${tombolIndikator}
                                    </li>
                                `
                            }
                        });
                    }

                    if (row_sasaran.jabatan_id == row_sasaran.jabatan_id_sekarang && json.status == 0) {
                        dataIndikator += `
                            <button type="button" class="btn btn-sm btn-primary mt-2"
                                onclick="modalIndikator('-1', '${row_sasaran.id}', '${id}')">
                                <i class="zmdi zmdi-plus"></i> Tambah Indikator Kinerja
                            </button>
                        `;
                    }

                    // Baris tabel
                    data += `
                        <tr>
                            <td>${index_sasaran + 1}</td>
                            <td class="indikator">
                                ${row_sasaran.nama_sasaran}<br>
                                ${tombolSasaran}
                            </td>
                            <td>
                                <ul class="list-ul">
                                    ${dataIndikator}
                                </ul>
                            </td>
                        </tr>
                    `;
                });

                data += `
                        </tbody>
                    </table>
                    </div>
                </div>
                `;
            }

            $('#tabelDetailPK').append(data);
            // Elemen ini dirender dinamis via AJAX, jadi tooltip perlu di-init ulang.
            $('#tabelDetailPK [data-toggle="tooltip"]').tooltip();
            $('#tabelDetailPK [data-toggle="tooltip-primary"]').tooltip({
                template: '<div class="tooltip tooltip-primary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            });

        } catch (e) {
            $('#tabelDetailPK').html('<div class="alert alert-danger">Gagal memuat data periode penilaian.</div>');
        }
    });
}

function loadTabelDetailPCK(id) {
    $.post('show_tabel_detail_pck', { id: id }, function (response) {
        try {
            const json = JSON.parse(response); // Pastikan server kirim JSON valid

            $('#tabelDetailPCK').html(''); // kosongkan wrapper

            let tombolKepala = ``;
            if (json.status == 0) {
                tombolKepala = `
                    <button type="button" class="btn btn-success mr-2" onclick="postingPCK('${id}')" 
                        data-toggle="tooltip-primary" data-placement="top" data-original-title="Posting PCK">
                        <i class="zmdi zmdi-upload"></i> 
                    </button>
                    <button type="button" class="btn btn-primary mr-2" onclick="modalIndikator('-1', '${id}')" 
                        data-toggle="tooltip" data-placement="top" data-original-title="Tambah Indikator">
                    <i class="zmdi zmdi-plus-circle"></i> 
                    </button>
                `
            }

            // Kalau ada data, buat tabelnya
            let data = `
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Detail Penilaian Capaian Kinerja Pegawai
                    </h3>
                    <div class="d-flex align-items-center card-action-wrap">
                        ${tombolKepala}
                    </div>
                </div>
                <div class="card-body">
            `;

            if (!json.data_pck || json.data_pck.length === 0) {
                // Kalau kosong
                data += `
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading mb-10">Informasi !</h4>
                        <hr class="hr-soft-info">
                        <p>Anda Belum Mengisi Penilaian Capaian Kinerja Periode Ini.</p>
                    </div>
                </div>
            </div>
            `;

            } else {
                json.data_pck.forEach((row_pck, index_pck) => {
                    let tombolIndikator = ``;
                    if (json.status == 0) {
                        tombolIndikator = `
                            <button type="button" class="btn btn-sm btn-primary"
                                onclick="modalUraianTugas('${row_pck.id}', '-1')"
                                title="Tambah Uraian Tugas">
                                <i class="zmdi zmdi-plus"></i> Tambah Uraian Tugas
                            </button>
                        `;

                        if (row_pck.capaian == 0) {
                            tombolIndikator += `
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="hapusCapaianIndikator('${row_pck.id}')" title="Hapus Indikator Kinerja">
                                    <i class="zmdi zmdi-delete"></i> Hapus Indikator Kinerja
                                </button>                            
                            `;
                        }
                    }

                    const uraianTugasAll = Array.isArray(json.data_uraian_tugas) ? json.data_uraian_tugas : [];
                    const uraianTugas = uraianTugasAll.filter((t) => String(t.capaian_id) === String(row_pck.id_raw));

                    data += `
                        <div class="table-responsive mb-4">
                            <h5 class="p-2">
                                <strong>Indikator Kinerja : ${row_pck.nama_indikator}</strong>
                            </h5>
                            <div class="mb-2">
                                ${tombolIndikator}
                            </div>
                            <table class="table table-bordered table-striped table-sm mb-0">
                                <thead class="thead-primary">
                                    <tr>
                                        <th rowspan="2" width="5%" class="text-center align-middle">
                                            <strong>#</strong>
                                        </th>
                                        <th rowspan="2" width="35%" class="text-center align-middle">
                                            <strong>KEGIATAN TUGAS JABATAN</strong>
                                        </th>
                                        <th colspan="3" width="25%" class="text-center">
                                            <strong>TARGET</strong>
                                        </th>
                                        <th colspan="3" width="25%" class="text-center">
                                            <strong>REALISASI</strong>
                                        </th>
                                        <th rowspan="2" width="10%" class="text-center align-middle">
                                            <strong>NILAI CAPAIAN KINERJA</strong>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><strong>KUANT / OUTPUT</strong></th>
                                        <th class="text-center"><strong>SATUAN</strong></th>
                                        <th class="text-center"><strong>KUAL / MUTU</strong></th>
                                        <th class="text-center"><strong>KUANT / OUTPUT</strong></th>
                                        <th class="text-center"><strong>SATUAN</strong></th>
                                        <th class="text-center"><strong>KUAL / MUTU</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (uraianTugas.length === 0) {
                        data += `
                            <tr>
                                <td colspan="9">
                                    <div class="alert alert-info mb-0" role="alert">
                                        Belum Ada Uraian Tugas.
                                    </div>
                                </td>
                            </tr>
                        `;
                    } else {
                        uraianTugas.forEach((row_uraian_tugas, idx) => {
                            let tombolUraianTugas = ``;
                            if (json.status == 0 && row_uraian_tugas.realisasi_kualitas == null) {
                                tombolUraianTugas = `
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="modalUraianTugas('${row_pck.id}', '${row_uraian_tugas.id}')"
                                        title="Edit Uraian Tugas">
                                        <i class="zmdi zmdi-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="hapusUraianTugas('${json.penilaian_id}', '${row_uraian_tugas.id}')"
                                        title="Hapus Uraian Tugas">
                                        <i class="zmdi zmdi-delete"></i>
                                    </button>
                                `;
                            }

                            const tautan = row_uraian_tugas.tautan ? `
                                <a href="${row_uraian_tugas.tautan}" target="_blank" rel="noopener noreferrer" class="ml-2 text-white">
                                    <i class="zmdi zmdi-link"></i> Bukti Dukung
                                </a>
                            ` : ``;

                            data += `
                                <tr>
                                    <td class="text-center align-middle">
                                        ${idx + 1}
                                    </td>
                                    <td class="indikator">
                                        ${row_uraian_tugas.uraian_tugas || ''}
                                        <span class="badge badge-pill badge-primary">${tautan}</span>
                                        ${tombolUraianTugas ? `<div class="mt-2">${tombolUraianTugas}</div>` : ``}
                                    </td>
                                    <td class="text-center">${row_uraian_tugas.target_kuantitas || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.satuan || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.target_kualitas || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.realisasi_kuantitas || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.satuan || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.realisasi_kualitas || '0'}</td>
                                    <td class="text-center">${row_uraian_tugas.nilai || ''}</td>
                                </tr>
                            `;
                        });
                    }

                    data += `
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <strong>NILAI CAPAIAN KINERJA</strong>
                                    </td>
                                    <td class="text-center">
                                        ${row_pck.capaian}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                });

                data += `
                    </div>
                </div>
                `;
            }

            $('#tabelDetailPCK').append(data);
            $('#tabelDetailPCK [data-toggle="tooltip"]').tooltip();
            $('#tabelDetailPCK [data-toggle="tooltip-primary"]').tooltip({
                template: '<div class="tooltip tooltip-primary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            });
        } catch (e) {
            $('#tabelDetailPCK').html('<div class="alert alert-danger">Gagal memuat data PCK.</div>');
        }
    });
}

function pilihIndikator(indikator_id, penilaian_id) {
    $.ajax({
        url: 'simpan_pck_indikator',
        type: 'POST',
        data: {
            indikator_id: indikator_id,
            penilaian_id: penilaian_id
        },
        dataType: 'json',
        beforeSend: function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menyimpan Indikator Kinerja',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        },
        success: function (response) {
            if (response.success) {
                notifikasi(response.message, response.success);
                if (response.success == 1) {
                    loadTabelDetailPCK(penilaian_id);
                }
            }
        },
        error: function (xhr) {
            var errorMsg = 'Terjadi kesalahan saat memilih Indikator Kinerja.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }

            notifikasi(errorMsg, 4);
        },
        complete: function () {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        }
    });
}

function hapusPeriodePK(id) {
    Swal.fire({
        title: "HAPUS PERIODE PERJANJIAN KINERJA",
        html: "Apa Anda Yakin Akan Menghapus Perjanjian Kinerja Periode Ini? Periode kerja yang dihapus tidak bisa dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('hapus_periode', { id: id }, function (response) {
                var json = jQuery.parseJSON(response);
                if (json.st == 1) {
                    notifikasi('Anda Sudah Menghapus Periode Perjanjian Kinerja', 1);
                    loadTabelPeriodePK();
                } else {
                    notifikasi('Anda Gagal Menghapus Perjanjian Kinerja, Silakan Ulangi Lagi atau Hubungi Admin', 3);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Menghapus Periode Perjanjian Kinerja', 4);
        }
    });
}

function hapusSasaranKegiatan(id) {
    Swal.fire({
        title: "HAPUS SASARAN KEGIATAN",
        html: "Apa Anda Yakin Akan Menghapus Sasaran Kegiatan Ini? Sasaran kegiatan yang dihapus tidak bisa dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('hapus_sasaran', { id: id }, function (response) {
                var json = jQuery.parseJSON(response);
                if (json.st == 1) {
                    notifikasi('Anda Sudah Menghapus Sasaran Kegiatan', 1);
                    loadTabelDetailPK(json.periode_id);
                } else {
                    notifikasi('AnAnda Gagal Menghapus Sasaran Kegiatan, Silakan Ulangi Lagi, atau Hubungi Admin', 3);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Menghapus Sasaran Kegiatan', 4);
        }
    });
}

function hapusIndikatorKinerja(id) {
    Swal.fire({
        title: "HAPUS INDIKATOR KINERJA",
        html: "Apa Anda Yakin Akan Menghapus Indikator Kinerja Ini? Indikator kinerja yang dihapus tidak bisa dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('hapus_indikator', { id: id }, function (response) {
                var json = jQuery.parseJSON(response);
                if (json.st == 1) {
                    notifikasi('Anda Sudah Menghapus Indikator Kinerja', 1);
                    loadTabelDetailPK(json.periode_id);
                } else {
                    notifikasi('AnAnda Gagal Menghapus Indikator Kinerja, Silakan Ulangi Lagi, atau Hubungi Admin', 3);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Menghapus Indikator Kinerja', 4);
        }
    });
}

function hapusCapaianIndikator(id) {
    Swal.fire({
        title: "HAPUS INDIKATOR KINERJA",
        html: "Apa Anda Yakin Akan Menghapus Indikator Kinerja Ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('hapus_capaian_indikator', { id: id }, function (response) {
                var json = jQuery.parseJSON(response);
                if (json.st == 1) {
                    notifikasi('Anda Sudah Menghapus Indikator Kinerja', 1);
                    loadTabelDetailPCK(json.penilaian_id);
                } else {
                    notifikasi('AnAnda Gagal Menghapus Indikator Kinerja, Silakan Ulangi Lagi, atau Hubungi Admin', 3);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Menghapus Indikator Kinerja', 4);
        }
    });
}

function hapusUraianTugas(penilaian_id, id) {
    Swal.fire({
        title: "HAPUS INDIKATOR KINERJA",
        html: "Apa Anda Yakin Akan Menghapus Indikator Kinerja Ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('hapus_uraian_tugas', { id: id }, function (response) {
                var json = jQuery.parseJSON(response);
                if (json.st == 1) {
                    notifikasi('Anda Sudah Menghapus Uraian Tugas', 1);
                    loadTabelDetailPCK(penilaian_id);
                } else {
                    notifikasi('AnAnda Gagal Menghapus Uraian Tugas, Silakan Ulangi Lagi, atau Hubungi Admin', 3);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Menghapus Uraian Tugas', 4);
        }
    });
}

function hapusPenilaian(penilaian_id, uraian_id) {
    Swal.fire({
        title: "HAPUS PENILAIAN URAIAN TUGAS INI",
        html: "Apa Anda Yakin Akan Menghapus Penilaian Uraian Tugas Ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('hapus_nilai_uraian_tugas', { uraian_id: uraian_id, penilaian_id: penilaian_id }, function (response) {
                var json = jQuery.parseJSON(response);
                if (json.st == 1) {
                    notifikasi('Anda Sudah Menghapus Nilai Uraian Tugas Ini', 1);
                    // Update tabel detail
                    if (typeof loadDetailPCKStaf === 'function') {
                        loadDetailPCKStaf(penilaian_id);
                    }

                    // Update angka "dinilai/belum" pada opsi select2 tanpa reload halaman
                    var $staf = $('#staf');
                    var $opt = $staf.find('option[value="' + penilaian_id + '"]');
                    if ($opt.length) {
                        if (json.uraian_sudah_dinilai !== undefined) $opt.attr('data-uraian-sudah', json.uraian_sudah_dinilai);
                        if (json.uraian_belum_dinilai !== undefined) $opt.attr('data-uraian-belum', json.uraian_belum_dinilai);
                        // refresh tampilan select2 (selection)
                        $staf.trigger('change.select2');
                    }
                } else {
                    notifikasi('Anda Gagal Menghapus Nilai Uraian Tugas, Silakan Ulangi Lagi, atau Hubungi Admin', 3);
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Menghapus Uraian Tugas', 4);
        }
    });
}

function ambilSasaranJabatan(id) {
    Swal.fire({
        title: 'Sasaran Kegiatan Sudah Tersedia',
        text: 'Sasaran Kegiatan tahun ini sudah dibuat oleh pejabat sebelumnya. Apakah Anda ingin mengambil Sasaran Kegiatan tersebut?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ambil Sasaran Kegiatan',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            // Panggil fungsi yang SUDAH ADA
            $.ajax({
                url: "ambil_sasaran_jabatan_periode_ini",
                type: "POST",
                dataType: "json",
                data: {
                    periode_id: id
                },
                beforeSend: function () {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang menyalin Sasaran Kegiatan Jabatan dari periode ini',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                },
                success: function (res) {
                    Swal.close();
                    notifikasi(res.message, res.success);
                    loadTabelDetailPK(id);
                },
                error: function (xhr) {
                    Swal.close();
                    var errorMsg = 'Terjadi kesalahan saat proses generate.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += xhr.responseJSON.message;
                    }

                    notifikasi(errorMsg, 4);
                }
            });
        }
    });
}

function formPeriodePK(id) {
    $("#periode_id").val('');
    $("#modalPeriodeJudul").html('');
    $("#tahun").val('');
    $("#nama_periode").val('');
    $("#periode_awal").val('');
    $("#periode_akhir").val('');
    
    $.post('show_periode', {
        id: id
    }, function (response) {
        var json = jQuery.parseJSON(response);
        if (json.st == 1) {
            $("#periode_id").val(json.id);
            $("#modalPeriodeJudul").html(json.judul);
            $("#tahun").val(json.tahun).trigger('change');;
            $("#nama_periode").val(json.nama_periode);
            $("#periode_awal").val(json.periode_awal);
            $("#periode_akhir").val(json.periode_akhir);

            initFloatingLabel('#nama_periode');
            initFloatingLabel('#periode_akhir');
            initFloatingLabel('#periode_awal');
            initFloatingLabel('#tahun');
        }

        $('#modalPeriode').modal('show');
    });
}

function modalSasaran(sasaranId, periodeId) {
    $("#sasaran_id").val('');
    $("#periode_id").val('');
    $("#modalSasaranJudul").html('');
    $("#nama_sasaran").val('');

    $.post('show_sasaran', {
        sasaranId: sasaranId,
        periodeId: periodeId
    }, function (response) {
        var json = jQuery.parseJSON(response);
        if (json.st == 1) {
            $("#sasaran_id").val(json.sasaran_id);
            $("#periode_id").val(json.periode_id);
            $("#modalSasaranJudul").html(json.judul);
            $("#nama_sasaran").val(json.nama_sasaran);
            initFloatingLabel('#nama_sasaran');
        }
        $('#modalSasaran').modal('show');
    });
}

function modalIndikator(indikatorId, sasaranId, periodeId) {
    $("#indikator_id").val('');
    $("#periode_id_indikator").val('');
    $("#sasaran_id_indikator").val('');
    $("#modalIndikatorJudul").html('');
    $("#target_kuantitas").val('');
    $("#satuan").val('');
    $("#nama_indikator").val('');
    $('.bulan-checkbox').prop('checked', false);
    $("#anggaran").val('');

    $.post('show_indikator', {
        indikatorId: indikatorId,
        sasaranId: sasaranId,
        periodeId: periodeId
    }, function (response) {
        var json = jQuery.parseJSON(response);
        if (json.st == 1) {
            $("#indikator_id").val(json.indikator_id);
            $("#periode_id_indikator").val(json.periode_id);
            $("#sasaran_id_indikator").val(json.sasaran_id);
            $("#modalIndikatorJudul").html(json.judul);
            $("#nama_indikator").val(json.nama_indikator);
            $('#target_kuantitas').val(json.target_kuantitas);
            $("#satuan").val(json.satuan);
            $("#anggaran").val(formatRupiah(json.anggaran));

            // Set checkbox bulan jika ada
            if (json.bulan_penyelesaian) {
                var bulanArray = json.bulan_penyelesaian;
                if (typeof bulanArray === 'string') {
                    try {
                        bulanArray = JSON.parse(bulanArray);
                    } catch (e) {
                        bulanArray = [];
                    }
                }
                if (Array.isArray(bulanArray)) {
                    bulanArray.forEach(function (bulan) {
                        $('#bulan_' + bulan).prop('checked', true);
                    });
                }
            }

            initFloatingLabel('#nama_indikator');
            initFloatingLabel('#target_kuantitas');
            initFloatingLabel('#satuan');
            initFloatingLabel('#anggaran');
        }

        $('#modalIndikator').modal('show');
    });
}

function modalUraianTugas(pck_id, uraian_id) {
    // Set nilai form
    $('#judulUraianTugas').html('');
    $('#pck_id').val('');
    $('#uraian_id').val('');
    $('#nama_iki_display').val('');
    $('#uraian_tugas').val('');
    $('#target_kuantitas').val('');
    $('#satuan').val('');
    $('#realisasi_kuantitas').val('');
    $('#tautan').val('');

    $.post('show_uraian_tugas', {
        pck_id: pck_id,
        uraian_id: uraian_id
    }, function (response) {
        var json = jQuery.parseJSON(response);
        if (json.st == 1) {
            $('#judulUraianTugas').html(json.judul);
            $('#pck_id').val(json.pck_id);
            $('#uraian_id').val(json.uraian_id);
            $('#nama_iki_display').val(json.nama_indikator);
            $('#uraian_tugas').val(json.uraian_tugas);
            $('#target_kuantitas').val(json.target_kuantitas);
            $('#satuan').val(json.satuan);
            $('#realisasi_kuantitas').val(json.realisasi_kuantitas);
            $('#tautan').val(json.tautan);

            initFloatingLabel('#nama_iki_display');
            initFloatingLabel('#uraian_tugas');
            initFloatingLabel('#target_kuantitas');
            initFloatingLabel('#satuan');
            initFloatingLabel('#realisasi_kuantitas');
            initFloatingLabel('#tautan');

        } else if (json.st == 0) {
            pesan('PERINGATAN', json.msg, '');
        }
    });
    // Tampilkan modal
    $('#modalUraianTugas').modal('show');
}

function pengajuanPK(periodeId) {
    $.ajax({
        url: 'cek_detail_periode',
        type: 'POST',
        dataType: 'json',
        data: { id: periodeId },
        success: function (json) {
            if (!json.data_periode || json.data_periode.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Detail Perjanjian Kinerja Anda masih kosong atau belum lengkap, silakan lengkapi Perjanjian Kinerja terlebih dahulu sebelum memproses pengajuan.',
                    icon: 'warning'
                });
                return;
            }

            Swal.fire({
                title: 'Ajukan Perjanjian Kinerja?',
                text: 'Apakah Anda yakin ingin mengajukan Perjanjian Kinerja ke atasan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
                allowOutsideClick: false
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: 'ajukan_persetujuan_pk',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        periode_id: periodeId
                    },
                    beforeSend: function () {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang proses pengajuan',
                            allowOutsideClick: false,
                            didOpen: function () {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function (res) {
                        Swal.close();
                        if (res && res.success) {
                            notifikasi(res.message, res.success);
                            if (res.success == '1') {
                                // Gunakan parameter fungsi, bukan identifier global.
                                loadTabelDetailPK(periodeId);
                            }
                        }
                    },
                    error: function () {
                        notifikasi('Terjadi kesalahan saat menghubungi server', 4);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menghubungi server'
                        });
                    }
                });
            });
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memeriksa detail perjanjian kinerja'
            });
        }
    });
}

function loadPCK(periodeId) {
    $.post('show_daftar_pck', { periode_id: periodeId }, function (response) {
        try {
            const json = JSON.parse(response); // Pastikan server kirim JSON valid

            $('#tabelPeriodePCK').html(''); // kosongkan wrapper
            $('#tabelPenilaianPCK').html(''); // kosongkan wrapper

            if (!json.data_periode || json.data_periode.length === 0) {
                // Kalau kosong
                if (!json.ketua) {
                    $('#tabelPeriodePCK').html(`
                        <div class="alert alert-info" role="alert">
                            <h4 class="alert-heading mb-10">Informasi !</h4>
                            <hr class="hr-soft-info">
                            <p>Belum ada Periode Penilaian Perjanjian Kinerja. Silakan tambah periode terlebih dahulu, Terima kasih.</p>
                        </div>
                    `);
                }

                $('#tabelPenilaianPCK').html(`
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading mb-10">Informasi !</h4>
                        <hr class="hr-soft-info">
                        <p>Belum ada Periode Penilaian Perjanjian Kinerja. Silakan tambah periode terlebih dahulu, Terima kasih.</p>
                    </div>
                `);
                return;
            }

            const namaBulan = [
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

            let data = ``;

            if (!json.ketua) {
                // Kalau ada data, buat tabelnya
                data = `
                    <div class="table-responsive">
                        <table id="tabelPeriodePCKData" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="25%">Bulan</th>
                                    <th width="25%" class="text-center">Nilai</th>
                                    <th width="25%" class="text-center">Status</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                json.data_periode.forEach((row, index) => {
                    let statusBadge = '';
                    if (row.status == '1') {
                        statusBadge = '<span class="badge badge-success radius-30">Sudah Posting</span>';
                    } else {
                        statusBadge = '<span class="badge badge-danger radius-30">Draft</span>';
                    }

                    let bulanText = (row.bulan && namaBulan[row.bulan])
                        ? namaBulan[row.bulan]
                        : '-';

                    // Baris tabel
                    data += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${bulanText}</td>
                            <td class="text-center">${row.nilai}</td>
                            <td class="text-center">${statusBadge}</td>
                            <td style="text-align: center;">
                                <div class="btn-group" role="group">
                                    <button data-page="detail_pengisian_pck" data-id="${row.id}" class="btn btn-sm btn-primary" title="Detail">
                                        <i class="zmdi zmdi-eye mr-1"></i> Pengisian
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                data += `
                        </tbody>
                    </table>
                </div>
                `;
            }

            // Kalau ada data, buat tabelnya
            let data_penilaian = `
            <div class="table-responsive">
                <table id="tabelPenilaianPCKData" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="35%">Bulan</th>
                            <th width="60%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            json.data_periode.forEach((row, index) => {

                let bulanText = (row.bulan && namaBulan[row.bulan])
                    ? namaBulan[row.bulan]
                    : '-';

                let data_nilai;
                if (json.ketua) {
                    let data_raw = {
                        bulan: row.bulan,
                        tahun: row.tahun
                    };

                    // JSON mengandung tanda kutip, jadi harus di-encode agar aman ditaruh ke HTML attribute
                    data_nilai = encodeURIComponent(JSON.stringify(data_raw));
                } else {
                    data_nilai = row.id;
                }

                // Baris tabel
                data_penilaian += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${bulanText}</td>
                        <td style="text-align: center;">
                            <div class="btn-group" role="group">
                                <button data-page="detail_evaluasi_pck" data-id="${data_nilai}" class="btn btn-sm btn-primary" title="Penilaian">
                                    <i class="zmdi zmdi-eye mr-1"></i> Penilaian
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            data_penilaian += `
                    </tbody>
                </table>
            </div>
            `;

            $('#tabelPeriodePCK').append(data);
            $('#tabelPenilaianPCK').append(data_penilaian);

        } catch (e) {
            $('#tabelPeriodePCK').html('<div class="alert alert-danger">Gagal memuat data periode penilaian.</div>');
            $('#tabelPenilaianPCK').html('<div class="alert alert-danger">Gagal memuat data periode penilaian.</div>');
        }
    });
}

function formatRupiah(angka) {
    return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function loadDetailPKStaf(id) {
    // Tampilkan skeleton saat select berubah
    $('#skeletonDetailPKStaf').show();

    $.post('show_tabel_detail_pk', { id: id }, function (response) {
        try {
            const json = JSON.parse(response); // Pastikan server kirim JSON valid

            $('#tabelDetailPKStaf').html(''); // kosongkan skeleton wrapper
            let tombolKepala = ``;
            if (json.status == 1) {
                tombolKepala = `
                    <button type="button" class="btn btn-sm btn-warning mr-2" onclick="persetujuanPK('${id}')" 
                        data-toggle="tooltip-primary" data-placement="top" data-original-title="Persetujuan Perjanjian Kinerja">
                        <i class="zmdi zmdi-badge-check"></i>
                    </button>
                `;
            } else if (json.status == 2) {
                tombolKepala = `
                <button type="button" class="btn btn-sm btn-secondary mr-2" onclick="batalPersetujuanPK('${id}')" 
                    data-toggle="tooltip-primary" data-placement="top" data-original-title="Batalkan Validasi Perjanjian Kinerja">
                    <i class="zmdi zmdi-undo"></i>
                </button>
            `;
            }

            // Kalau ada data, buat tabelnya
            let data = `
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Detail Perjanjian Kinerja Individu
                    </h3>
                    <div class="d-flex align-items-center card-action-wrap">
                        ${tombolKepala}
                    </div>
                </div>
                <div class="card-body">
            `;

            data += `
                <div class="table-responsive">
                    <table id="tabelDetailPKStafData" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="40%">Sasaran Kegiatan</th>
                                <th width="55%">Indikator Kinerja</th>
                            </tr>
                        </thead>
                        <tbody>
                `

            json.data_sasaran.forEach((row_sasaran, index_sasaran) => {

                let dataIndikator = ``;
                if (json.indikator || json.indikator.length > 0) {
                    json.indikator.forEach((row_indikator, index) => {
                        // parsing dan mapping bulan dulu
                        let bulan_selected = '';

                        try {
                            const bulan_array = JSON.parse(row_indikator.bulan_penyelesaian || '[]');

                            if (Array.isArray(bulan_array) && bulan_array.length > 0) {
                                const bulan_nama = {
                                    1: 'Jan', 2: 'Feb', 3: 'Mar', 4: 'Apr',
                                    5: 'Mei', 6: 'Jun', 7: 'Jul', 8: 'Agu',
                                    9: 'Sep', 10: 'Okt', 11: 'Nov', 12: 'Des'
                                };

                                bulan_selected = bulan_array
                                    .map(b => bulan_nama[b] ?? b)
                                    .join(', ');
                            }
                        } catch (e) {
                            bulan_selected = '';
                        }

                        if (row_sasaran.id == row_indikator.sasaran_id) {
                            let anggaran = `-`;
                            if (row_indikator.anggaran) {
                                anggaran = new Intl.NumberFormat('id-ID').format(row_indikator.anggaran);
                            }

                            dataIndikator += `
                                <li>
                                    <strong>${row_indikator.nama_indikator}</strong>
                                    <div class="row">
                                        <div class="col-4">
                                            <small>
                                                <i class="zmdi zmdi-gps-dot"></i> Target Mutu : ${row_indikator.target_mutu}
                                            </small>
                                        </div>
                                        <div class="col-4">
                                            <small>
                                                <i class="zmdi zmdi-arrows"></i> Target Kuantitas : ${row_indikator.target_kuantitas}
                                            </small>
                                        </div>
                                        <div class="col-4">
                                            <small>
                                                <i class="zmdi zmdi-local-activity"></i> Satuan : ${row_indikator.satuan}
                                            </small>
                                        </div>
                                    </div>
                                    <small>
                                        Bulan : ${bulan_selected}
                                    </small><br>
                                    <small>
                                        Pagu Anggaran : Rp${anggaran}
                                    </small>
                                </li>
                            `;
                        }
                    });
                }

                // Baris tabel
                data += `
                    <tr>
                        <td width="5%">${index_sasaran + 1}</td>
                        <td class="indikator" width="40%">
                            ${row_sasaran.nama_sasaran}
                        </td>
                        <td width="55%">
                            <ul class="list-ul">
                                ${dataIndikator}
                            </ul>
                        </td>
                    </tr>
                `;
            });

            data += `
                    </tbody>
                </table>
                </div>
            </div>
            `;

            $('#tabelDetailPKStaf').append(data);
            // Elemen ini dirender dinamis via AJAX, jadi tooltip perlu di-init ulang.
            $('#tabelDetailPKStaf [data-toggle="tooltip"]').tooltip();
            $('#tabelDetailPKStaf [data-toggle="tooltip-primary"]').tooltip({
                template: '<div class="tooltip tooltip-primary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            });

        } catch (e) {
            $('#tabelDetailPKStaf').html('<div class="alert alert-danger">Gagal memuat data detail perjanjian kinerja.</div>');
        }
    });
}

function loadDetailPCKStaf(id) {
    // Tampilkan skeleton saat select berubah
    $('#skeletonDetailPCKStaf').show();

    $.post('show_tabel_detail_pck', { id: id }, function (response) {
        try {
            const json = JSON.parse(response); // Pastikan server kirim JSON valid

            $('#tabelDetailPCKStaf').html(''); // kosongkan wrapper

            // Kalau ada data, buat tabelnya
            let data = `
            <div class="card card-primary card-outline">
                <div class="card-header card-header-action">
                    <h3>
                        <i class="zmdi zmdi-file-text"></i> Detail Penilaian Capaian Kinerja Pegawai
                    </h3>
                </div>
                <div class="card-body">
            `;

            if (!json.data_pck || json.data_pck.length === 0) {
                // Kalau kosong
                data += `
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading mb-10">Informasi !</h4>
                        <hr class="hr-soft-info">
                        <p>Pegawai Belum Mengisi Penilaian Capaian Kinerja Periode Ini.</p>
                    </div>
                </div>
            </div>
            `;

            } else {
                json.data_pck.forEach((row_pck, index_pck) => {

                    const uraianTugasAll = Array.isArray(json.data_uraian_tugas) ? json.data_uraian_tugas : [];
                    const uraianTugas = uraianTugasAll.filter((t) => String(t.capaian_id) === String(row_pck.id_raw));

                    let thAksi = ``;
                    let tfootSpan = 8;
                    if (json.status == 0) {
                        thAksi = `
                        <th rowspan="2" width="10%" class="text-center align-middle">
                            <strong>AKSI</strong>
                        </th>
                        `;
                        tfootSpan = 9;
                    }

                    data += `
                        <div class="table-responsive mb-4">
                            <h5 class="p-2">
                                <strong>Indikator Kinerja : ${row_pck.nama_indikator}</strong>
                            </h5>
                            <table class="table table-bordered table-striped table-sm mb-0">
                                <thead class="thead-primary">
                                    <tr>
                                        <th rowspan="2" width="5%" class="text-center align-middle">
                                            <strong>#</strong>
                                        </th>
                                        <th rowspan="2" width="35%" class="text-center align-middle">
                                            <strong>KEGIATAN TUGAS JABATAN</strong>
                                        </th>
                                        <th colspan="3" width="25%" class="text-center">
                                            <strong>TARGET</strong>
                                        </th>
                                        <th colspan="3" width="25%" class="text-center">
                                            <strong>REALISASI</strong>
                                        </th>
                                        ${thAksi}                    
                                        <th rowspan="2" width="10%" class="text-center align-middle">
                                            <strong>NILAI CAPAIAN KINERJA</strong>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><strong>KUANT / OUTPUT</strong></th>
                                        <th class="text-center"><strong>SATUAN</strong></th>
                                        <th class="text-center"><strong>KUAL / MUTU</strong></th>
                                        <th class="text-center"><strong>KUANT / OUTPUT</strong></th>
                                        <th class="text-center"><strong>SATUAN</strong></th>
                                        <th class="text-center"><strong>KUAL / MUTU</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (uraianTugas.length === 0) {
                        data += `
                            <tr>
                                <td colspan="9">
                                    <div class="alert alert-info mb-0" role="alert">
                                        Belum Ada Uraian Tugas.
                                    </div>
                                </td>
                            </tr>
                        `;
                    } else {
                        uraianTugas.forEach((row_uraian_tugas, idx) => {
                            let tombolPenilaian = ``;
                            if (json.status == 0) {
                                tombolPenilaian += `
                                    <td class="text-center">
                                `;

                                tombolPenilaian += `
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="modalPenilaian('${id}', '${row_uraian_tugas.id}')"
                                        data-toggle="tooltip-primary" data-placement="top" data-original-title="Berikan Penilaian">
                                        <i class="zmdi zmdi-gesture"></i>
                                    </button>
                                `;

                                if (row_uraian_tugas.realisasi_kualitas) {
                                    tombolPenilaian += `
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="hapusPenilaian('${id}', '${row_uraian_tugas.id}')"
                                            data-toggle="tooltip-danger" data-placement="top" data-original-title="Hapus Penilaian">
                                            <i class="zmdi zmdi-delete"></i>
                                        </button>
                                    `;
                                }

                                tombolPenilaian += `</td>`;
                            }

                            const tautan = row_uraian_tugas.tautan ? `
                                <a href="${row_uraian_tugas.tautan}" target="_blank" rel="noopener noreferrer" class="ml-2 text-white">
                                    <i class="zmdi zmdi-link"></i> Bukti Dukung
                                </a>
                            ` : ``;

                            let nilai_pck = ``;
                            if (row_uraian_tugas.realisasi_kualitas) {
                                nilai_pck = `
                                    <span class="badge badge-pill badge-success">${row_uraian_tugas.realisasi_kualitas}</span>
                                `;
                            } else {
                                nilai_pck = `
                                    <span class="badge badge-pill badge-warning">Belum Dinilai</span>
                                `;
                            }


                            data += `
                                <tr>
                                    <td class="text-center align-middle">
                                        ${idx + 1}
                                    </td>
                                    <td class="indikator">
                                        ${row_uraian_tugas.uraian_tugas || ''}<br>
                                        <span class="badge badge-pill badge-primary">${tautan}</span>
                                    </td>
                                    <td class="text-center">${row_uraian_tugas.target_kuantitas || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.satuan || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.target_kualitas || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.realisasi_kuantitas || ''}</td>
                                    <td class="text-center">${row_uraian_tugas.satuan || ''}</td>
                                    <td class="text-center">${nilai_pck}</td>
                                    ${tombolPenilaian}
                                    <td class="text-center">${row_uraian_tugas.nilai || ''}</td>
                                </tr>
                            `;
                        });
                    }

                    data += `
                                <tr>
                                    <td colspan="${tfootSpan}" class="text-center">
                                        <strong>NILAI CAPAIAN KINERJA</strong>
                                    </td>
                                    <td class="text-center">
                                        ${row_pck.capaian}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                });

                data += `
                    </div>
                </div>
                `;
            }

            $('#tabelDetailPCKStaf').append(data);
            $('#tabelDetailPCKStaf [data-toggle="tooltip-danger"]').tooltip({
                template: '<div class="tooltip tooltip-danger" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            }); $('#tabelDetailPCKStaf [data-toggle="tooltip-primary"]').tooltip({
                template: '<div class="tooltip tooltip-primary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            });
        } catch (e) {
            $('#tabelDetailPCKStaf').html('<div class="alert alert-danger">Gagal memuat data PCK.</div>');
        }
    });
}

function persetujuanPK(id) {
    Swal.fire({
        title: 'Persetujuan Penilaian Kinerja Tahunan',
        html: `
            <div class="text-left">
                <div class="form-group">
                    <label for="swal_aksi_validasi" class="font-weight-bold">Pilih Aksi :</label>
                        <select class="form-control" id="swal_aksi_validasi" onchange="toggleKeterangan()">
                            <option value="">-- Pilih Aksi --</option>
                            <option value="validasi"
                                data-badge-class="badge badge-success"
                                data-badge-text="Validasi">Berikan Persetujuan</option>
                            <option value="kembalikan"
                                data-badge-class="badge badge-warning text-dark"
                                data-badge-text="Draft">Kembalikan ke Draft</option>
                    </select>
                </div>
                <div class="form-group" id="swal_keterangan_group" style="display: none;">
                    <label for="swal_keterangan" class="font-weight-bold">Keterangan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="swal_keterangan" rows="4" 
                        placeholder="Masukkan alasan kembalikan Perjanjian Kinerja ke Draft..."></textarea>
                    <small class="form-text text-muted">Keterangan wajib diisi untuk mengembalikan ke draft.</small>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        didOpen: () => {
            // Buat select lebih cantik (Select2 + badge)
            const $select = $('#swal_aksi_validasi');
            const $swalContainer = $('.swal2-container');

            try {
                if ($select.data('select2')) $select.select2('destroy');
            } catch (e) {
                // ignore
            }

            $select.select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                dropdownParent: $swalContainer,
                // Supaya templateResult/templateSelection yang berupa string HTML benar-benar dirender
                // (bukan ditampilkan literal sebagai teks "<span ...>").
                escapeMarkup: function (markup) {
                    return markup;
                },
                templateResult: function (state) {
                    if (!state.id) return state.text;
                    const $el = $(state.element);
                    const badgeClass = $el.data('badge-class') || 'badge badge-secondary';
                    const badgeText = $el.data('badge-text') || '';
                    return `
                        <span class="${badgeClass}" style="font-size:0.75rem; padding: .2rem .5rem; margin-right:.5rem;">${badgeText}</span>
                        <span>${state.text}</span>
                    `;
                },
                templateSelection: function (state) {
                    if (!state.id) return state.text;
                    const $el = $(state.element);
                    const badgeClass = $el.data('badge-class') || 'badge badge-secondary';
                    const badgeText = $el.data('badge-text') || '';
                    return `
                        <span class="${badgeClass}" style="font-size:0.75rem; padding: .2rem .5rem; margin-right:.5rem;">${badgeText}</span>
                        <span>${state.text}</span>
                    `;
                }
            });

            // Pastikan field keterangan sesuai pilihan
            toggleKeterangan();

            // Focus
            document.getElementById('swal_aksi_validasi').focus();
        },
        preConfirm: () => {
            const aksi = document.getElementById('swal_aksi_validasi').value;
            const keterangan = document.getElementById('swal_keterangan').value;

            if (!aksi) {
                notifikasi('Aksi belum dipilih', 3);
                return false;
            }

            if (aksi === 'kembalikan') {
                if (!keterangan || keterangan.trim() === '') {
                    notifikasi('Keterangan wajib diisi untuk membatalkan validasi perjanjian kinerja ke pegawai', 3)
                    return false;
                }
                return {
                    aksi: aksi,
                    keterangan: keterangan.trim()
                };
            } else {
                return {
                    aksi: aksi,
                    keterangan: null
                };
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const aksi = result.value.aksi;
            const keterangan = result.value.keterangan;

            if (aksi === 'validasi') {
                // Validasi Penilaian (Status: 1 -> 2)
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang proses validasi perjanjian kinerja',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: 'validasi_pk',
                    type: 'POST',
                    data: {
                        periode_id: id
                    },
                    dataType: 'json',
                    success: function (res) {
                        notifikasi(res.message, res.success);
                        if (res && res.success == '1') {
                            // Reload halaman agar opsi staf + statusnya ikut ter-update dari DB
                            if (res.ketua)
                                loadPage('validasi_pk', res.tahun);
                            else
                                loadPage('validasi_pk', id);
                        }
                    },
                    error: function () {
                        notifikasi('Terjadi kesalahan saat validasi perjanjian kinerja', 4);
                    },
                    complete: function () {
                        Swal.close();
                    }
                });
            } else if (aksi === 'kembalikan') {
                // Kembalikan ke Pegawai (Status: 1 -> 0)
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang membatalkan perjanjian kinerja pegawai',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: 'kembalikan_pk_ke_draft',
                    type: 'POST',
                    data: {
                        periode_id: id,
                        keterangan: keterangan
                    },
                    dataType: 'json',
                    success: function (res) {
                        notifikasi(res.message, res.success);
                        if (res && res.success == '1') {
                            // Reload halaman agar opsi staf + statusnya ikut ter-update dari DB
                            loadPage('validasi_pk', id);
                        }
                    },
                    error: function () {
                        notifikasi('Terjadi kesalahan saat mengembalikan perjanjian kinerja ke pegawai', 4);
                    },
                    complete: function () {
                        Swal.close();
                    }
                });
            }
        }
    });
}

function batalPersetujuanPK(id) {
    Swal.fire({
        title: "BATALKAN VALIDASI PERJANJIAN KINERJA",
        html: "Apa Anda Yakin Akan Membatalkan Persetujuan Perjanjian Kinerja Ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Batalkan !",
        cancelButtonText: "Tidak !"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'batal_validasi',
                type: 'POST',
                data: {
                    periode_id: id
                },
                dataType: 'json',
                success: function (res) {
                    notifikasi(res.message, res.success);
                    if (res && res.success == '1') {
                        // Reload halaman agar opsi staf + statusnya ikut ter-update dari DB
                        loadPage('validasi_pk', id);
                    }
                },
                error: function () {
                    notifikasi('Terjadi kesalahan saat mengembalikan perjanjian kinerja ke pegawai', 4);
                },
                complete: function () {
                    Swal.close();
                }
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            notifikasi('Anda Batal Membatalkan Perjanjian Kinerja', 4);
        }
    });
}

function toggleKeterangan() {
    const aksi = document.getElementById('swal_aksi_validasi').value;
    const keteranganGroup = document.getElementById('swal_keterangan_group');
    const keteranganInput = document.getElementById('swal_keterangan');

    if (aksi === 'kembalikan') {
        keteranganGroup.style.display = 'block';
        keteranganInput.required = true;
    } else {
        keteranganGroup.style.display = 'none';
        keteranganInput.required = false;
        keteranganInput.value = '';
    }
}

function previewPK(periodeId) {
    $('#modalLokasiTanggal').modal('show');
    $('#periode_id_pk').val(periodeId);
}

function lanjutkanPreviewPK() {
    var lokasi = $('#lokasi_pk').val().trim();
    var tanggal = $('#tanggal_pk').val();
    var periode_id = $('#periode_id_pk').val();

    if (!lokasi) {
        notifikasi('Lokasi harus diisi', 2);
        $('#lokasi_pk').focus();
        return;
    }

    if (!tanggal) {
        notifikasi('Tanggal harus diisi', 2);
        $('#tanggal_pk').focus();
        return;
    }

    $('#modalLokasiTanggal').modal('hide');
    $('#modalPreviewPK').modal('show');
    // Skeleton sudah ada di dalam #previewPK, biarkan tampil saat AJAX loading
    // Tidak perlu .html('') di sini agar skeleton tetap terlihat

    // Load data PK dengan lokasi dan tanggal
    $.ajax({
        url: 'get_data_preview_pk',
        type: 'POST',
        dataType: 'json',
        data: {
            periode_id: periode_id,
            lokasi: lokasi,
            tanggal: tanggal
        },
        success: function (response) {
            if (response.success) {
                $('#previewPK').html(response.html);
            } else {
                $('#previewPK').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + (response.message || 'Gagal memuat data PK') + '</div>');
            }
        },
        error: function () {
            $('#previewPK').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat data</div>');
        }
    });
}

function modalPeriodePenilaian(periodeId) {
    $.post('cek_status_periode', {
        periodeId: periodeId
    }, function (response) {
        var json = jQuery.parseJSON(response);
        if (json.status_periode > 0) {
            $('#modalPeriodePCK').modal('show');
            $('#periode_id').val(periodeId);
        } else {
            notifikasi('Perjanjian Kinerja Anda Periode ini belum disetujui atasan, silakan melakukan pengajuan persetujuan perjanjian kinerja terlebih dahulu.', 2)
        }
    });
}

function modalPenilaian(penilaian_id, uraian_id) {
    $.post('show_penilaian_uraian', {
        uraian_id: uraian_id
    }, function (response) {
        var json = jQuery.parseJSON(response);
        if (json.st == 1) {
            $('#modalNilaiUraianTugas').modal('show');

            $("#judulUraianTugas").html('');
            $("#uraian_id").val('');
            $("#penilaian_id").val('');
            $("#uraian_tugas").val('');
            $("#target_kuantitas").val('');
            $("#realisasi_kuantitas").val('');
            $("#tautan").html('');
            $("#realisasi_kualitas").val('');

            $("#judulUraianTugas").html(json.judul);
            $("#uraian_id").val(json.uraian_id);
            $("#penilaian_id").val(penilaian_id);
            $("#uraian_tugas").val(json.uraian_tugas);
            $("#target_kuantitas").val(json.target);
            $("#realisasi_kuantitas").val(json.realisasi);
            $("#tautan").append('<a href="' + json.tautan + '" target="_blank">Bukti Dukung</a>');
            $("#realisasi_kualitas").val(json.kualitas);

        } else {
            notifikasi('Ada kesalahan, silakan ulangi atau hubungi Admin', 4);
        }
    });
}

function postingPCK(penilaian_id) {
    $.ajax({
        url: 'get_data_penilaian_pck',
        type: 'POST',
        dataType: 'json',
        data: {
            penilaian_id: penilaian_id
        },

        success: function (response) {

            if (response.success) {

                Swal.fire({
                    title: 'Nilai PCK anda ' + response.nilai + '<br>Kirim Penilaian?',
                    text: 'Data penilaian yang sudah dikirim tidak dapat diubah kembali.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {

                    if (result.isConfirmed) {

                        // proses posting di sini
                        $.ajax({
                            url: 'posting_penilaian_pck',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                penilaian_id: penilaian_id,
                                nilai: response.nilai
                            },

                            beforeSend: function () {
                                Swal.fire({
                                    title: 'Memproses...',
                                    text: 'Sedang memposting penilaian',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },

                            success: function (res) {
                                notifikasi(res.message, res.success);
                                if (res.success == 1) {
                                    loadTabelDetailPCK(penilaian_id);
                                }
                            },

                            error: function () {
                                notifikasi('Terjadi kesalahan saat mengirim penilaian.', 4);
                            },
                            complete: function () {
                                Swal.close();
                            }
                        });
                    }
                });

            } else {
                notifikasi(response.pesan, 3)
            }
        },

        error: function () {
            notifikasi('Terjadi kesalahan saat memposting PCK, silakan hubungi Admin', 4);
        }
    });
}

function initMonitoringPenilaian() {
    const $nama = $('#filterNama');
    const $tahun = $('#filterTahun');
    const $bulan = $('#filterBulan');

    $.get('monitoring_penilaian_filters', function (res) {
        try {
            if (res && Array.isArray(res.nama)) {
                res.nama.forEach(n => {
                    $nama.append(`<option value="${String(n).replace(/"/g, '&quot;')}">${n}</option>`);
                });
            }
            if (res && Array.isArray(res.tahun)) {
                res.tahun.forEach(t => {
                    $tahun.append(`<option value="${String(t).replace(/"/g, '&quot;')}">${t}</option>`);
                });
            }
            if (res && Array.isArray(res.bulan)) {
                res.bulan.forEach(b => {
                    $bulan.append(`<option value="${String(b.id).replace(/"/g, '&quot;')}">${b.text}</option>`);
                });
            }
        } catch (e) {
            notifikasi(e, 4);
        }

        $(".select2").select2({ width: '100%' });
    }, 'json');

    initFloatingLabel('#filterNama');
    initFloatingLabel('#filterTahun');
    initFloatingLabel('#filterBulan');

    const table = $('#tabelMonitoringPenilaian').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        order: [[6, 'desc']],
        ajax: {
            url: 'monitoring_penilaian_dt',
            type: 'POST',
            data: function (d) {
                d.filterNama = $nama.val();
                d.filterTahun = $tahun.val();
                d.filterBulan = $bulan.val();
            }
        },
        columnDefs: [
            { targets: [0, 7], orderable: false },
            { targets: [4, 7], searchable: false }
        ],
        initComplete: function () {
            // Hide skeleton & show table
            $('#monitoring-table-wrap .monitoring-table-skeleton').addClass('hidden');
            $('#tabelMonitoringPenilaian').show();
        }
    });

    $nama.on('change', function () { table.ajax.reload(null, true); });
    $tahun.on('change', function () { table.ajax.reload(null, true); });
    $bulan.on('change', function () { table.ajax.reload(null, true); });
}

function previewPCK(penilaianId, status) {
    const $btnCetak = $('#btnCetakPCK');
    const isPosted = String(status) === '1';
    if (!isPosted) {
        if (typeof notifikasi === 'function') {
            notifikasi('Penilaian Capaian Kinerja Pegawai Belum Diposting', 2);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Informasi',
                html: 'Penilaian Capaian Kinerja Pegawai Belum Diposting',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Penilaian Capaian Kinerja Pegawai Belum Diposting');
        }
        return;
    }

    if ($btnCetak.length) {
        $btnCetak.show();
    }

    $('#modal-preview-pck').modal('show');
    // Skeleton sudah ada di dalam #preview-pck-content — tampil saat AJAX loading

    $.ajax({
        url: 'get_data_pck',
        type: 'POST',
        dataType: 'json',
        data: {
            penilaian_id: penilaianId
        },
        success: function (response) {
            if (response.success) {
                $('#preview-pck-content').html(response.html);
            } else {
                $('#preview-pck-content').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + (response.message || 'Gagal memuat data PCK') + '</div>');
            }
        },
        error: function () {
            $('#preview-pck-content').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan saat memuat data</div>');
        }
    });
}

function cetakPCK() {
    var printContentEl = document.getElementById('preview-pck-content');
    if (!printContentEl) return;
    var printContent = printContentEl.innerHTML;

    var printWindow = window.open('', '_blank');
    if (!printWindow) return;
    printWindow.document.open();
    printWindow.document.write('<!DOCTYPE html>');
    printWindow.document.write('<html><head>');
    printWindow.document.write('<title>Penilaian Capaian Kinerja</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }');
    printWindow.document.write('.pck-container { padding: 20px; }');
    printWindow.document.write('.pck-header { text-align: center; margin-bottom: 20px; }');
    printWindow.document.write('.pck-header h3 { font-size: 14px; font-weight: bold; margin-bottom: 5px; }');
    printWindow.document.write('.pck-header h4 { font-size: 12px; font-weight: bold; margin-bottom: 20px; }');
    printWindow.document.write('.pck-info { margin-bottom: 20px; }');
    printWindow.document.write('.pck-info table { width: 100%; font-size: 11px; border-collapse: collapse; }');
    printWindow.document.write('.pck-info td { padding: 5px; border: 1px solid #000; }');
    printWindow.document.write('.pck-section { margin-bottom: 30px; }');
    printWindow.document.write('.pck-section h5 { font-size: 12px; font-weight: bold; margin-bottom: 10px; background-color: #e8f4f8; padding: 8px; border: 1px solid #000; }');
    printWindow.document.write('.pck-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: -10px; margin-bottom: 5px; }');
    printWindow.document.write('.pck-table th, .pck-table td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle; }');
    printWindow.document.write('.pck-table th { background-color: #d9ead3; font-weight: bold; }');
    printWindow.document.write('.pck-table tbody tr:nth-child(even) { background-color: #f9f9f9; }');
    printWindow.document.write('.pck-rekap { margin-top: 30px; }');
    printWindow.document.write('.pck-rekap h5 { font-size: 12px; font-weight: bold; margin-bottom: 10px; background-color: #f4cccc; padding: 8px; border: 1px solid #000; }');
    printWindow.document.write('.pck-signature { margin-top: 10px; text-align: right; }');
    printWindow.document.write('.pck-container-all { padding: 20px; }');
    printWindow.document.write('@media print { @page { size: A4 portrait; margin: 1cm; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function () {
        printWindow.print();
    };
}

function initFloatingLabel(selector) {

    const element = $(selector);

    function toggleLabel(el) {
        const group = $(el).closest('.form-group');

        if ($(el).val() && $(el).val() !== '') {
            group.addClass('active');
        } else {
            group.removeClass('active');
        }
    }

    /* INPUT & TEXTAREA */
    element.on('focus blur input change', function () {
        toggleLabel(this);
    });

    /* SELECT2 */
    element.on('select2:open', function () {
        $(this).closest('.form-group').addClass('active');
    });

    element.on('select2:close', function () {
        toggleLabel(this);
    });

    element.on('change', function () {
        toggleLabel(this);
    });

    /* INIT AWAL */
    element.each(function () {
        toggleLabel(this);
    });
}