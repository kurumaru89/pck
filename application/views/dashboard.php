<!-- Title -->
<div class="hk-pg-header">
    <div>
        <h2 class="hk-pg-title font-weight-600 mb-10">Beranda</h2>
        <p>Elektronik Penilaian Capaian Kinerja<i
                class="ion ion-md-help-circle-outline ml-5" data-toggle="tooltip" data-placement="top"
                title="Need help about earning stats"></i></p>
    </div>
</div>
<!-- /Title -->

<!-- Row -->
<div class="row">
    <div class="col-xl-12">
        <!-- Page Alerts -->
        <div class="alert alert-primary alert-wth-icon alert-dismissible fade show" role="alert">
            <span class="alert-icon-wrap"><i class="zmdi zmdi-help"></i></span> Selamat Datang di Aplikasi Penilaian
            Capaian Kinerja Pegawai MS Banda Aceh
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <!-- /Page Alerts -->
    </div>
</div>
<!-- /Row -->

<div class="row">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Capaian Kinerja Bulan Ini</h5>
                <small class="text-muted">Perbandingan akan muncul jika bulan sebelumnya tersedia.</small>
            </div>
            <div class="card-body">
                <div id="dashboard-pck-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="mt-2">Memuat statistik...</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="tabelDashboardPCK" style="display:none">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:5%">No</th>
                                <th>Nama</th>
                                <th style="width:12%">Status</th>
                                <th style="width:14%">Nilai</th>
                                <th style="width:14%">Bulan Lalu</th>
                                <th style="width:16%">Perbandingan</th>
                                <th style="width:12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="dashboard-pck-empty" class="alert alert-info" style="display:none">
                    Belum ada data capaian kinerja pada bulan ini untuk scope Anda.
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Grafik Capaian Kinerja Bulanan</h5>
                <small class="text-muted">Rata-rata nilai Posted per bulan (tahun berjalan).</small>
            </div>
            <div class="card-body">
                <div id="chartCapaianBulanan" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Posted vs Draft per Bulan</h5>
                <small class="text-muted">Jumlah penilaian per bulan (tahun berjalan).</small>
            </div>
            <div class="card-body">
                <div id="chartStatusBulanan" style="height: 320px;"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Distribusi Nilai (Bulan Ini)</h5>
                <small class="text-muted">Berdasarkan penilaian berstatus Posted.</small>
            </div>
            <div class="card-body">
                <div id="chartDistribusiNilai" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 10 Nilai Tertinggi (Bulan Ini)</h5>
                <small class="text-muted">Hanya dari penilaian Posted.</small>
            </div>
            <div class="card-body">
                <div id="chartTopPegawai" style="height: 360px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadDashboardStatistikPCK();
    });

    function badgeStatus(status) {
        if (String(status) === '1') return '<span class="badge badge-success">Posted</span>';
        return '<span class="badge badge-warning">Draft</span>';
    }

    function badgePerbandingan(val) {
        if (!val) return '-';
        if (val === 'naik') return '<span class="badge badge-success">Meningkat</span>';
        if (val === 'turun') return '<span class="badge badge-danger">Menurun</span>';
        return '<span class="badge badge-secondary">Tetap</span>';
    }

    function loadDashboardStatistikPCK() {
        $('#dashboard-pck-empty').hide();
        $('#tabelDashboardPCK').hide();
        $('#dashboard-pck-loading').show();

        $.ajax({
            url: 'get_statistik_dashboard_pck',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                const rows = (res && res.list && Array.isArray(res.list.data)) ? res.list.data : [];
                const $tbody = $('#tabelDashboardPCK tbody');
                $tbody.html('');

                if (!rows || rows.length === 0) {
                    $('#dashboard-pck-loading').hide();
                    $('#dashboard-pck-empty').show();
                } else {
                    rows.forEach((r, idx) => {
                        const nilaiNow = (r.nilai_bulan_ini === null || typeof r.nilai_bulan_ini === 'undefined') ? '-' : Number(r.nilai_bulan_ini).toFixed(2);
                        const nilaiPrev = (r.nilai_bulan_lalu === null || typeof r.nilai_bulan_lalu === 'undefined') ? '-' : Number(r.nilai_bulan_lalu).toFixed(2);

                        const aksi = (String(r.status) === '1')
                            ? `<button type="button" class="btn btn-sm btn-outline-info" onclick="previewPCK(${r.penilaian_id}, ${r.status})"><i class="zmdi zmdi-eye"></i> Preview</button>`
                            : `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="previewPCK(${r.penilaian_id}, ${r.status})"><i class="zmdi zmdi-eye"></i> Preview</button>`;

                        $tbody.append(`
                            <tr>
                                <td>${idx + 1}</td>
                                <td>${r.nama || '-'}</td>
                                <td class="text-center">${badgeStatus(r.status)}</td>
                                <td class="text-center">${nilaiNow}</td>
                                <td class="text-center">${nilaiPrev}</td>
                                <td class="text-center">${badgePerbandingan(r.perbandingan)}</td>
                                <td class="text-center">${aksi}</td>
                            </tr>
                        `);
                    });

                    $('#dashboard-pck-loading').hide();
                    $('#tabelDashboardPCK').show();
                }

                renderChartBulanan(res && res.chart ? res.chart : null);
                renderChartStatusBulanan(res && res.chart_status ? res.chart_status : null);
                renderChartDistribusi(res && res.distribusi ? res.distribusi : null);
                renderChartTop(res && res.top ? res.top : []);
            },
            error: function () {
                $('#dashboard-pck-loading').hide();
                $('#dashboard-pck-empty').show().text('Gagal memuat statistik dashboard. Silakan refresh.');
            }
        });
    }

    function renderChartBulanan(chart) {
        if (typeof echarts === 'undefined') return;
        const el = document.getElementById('chartCapaianBulanan');
        if (!el) return;

        const labels = (chart && Array.isArray(chart.labels)) ? chart.labels : [];
        const avg = (chart && Array.isArray(chart.avg)) ? chart.avg : [];
        const count = (chart && Array.isArray(chart.count)) ? chart.count : [];

        const myChart = echarts.init(el);
        myChart.setOption({
            tooltip: {
                trigger: 'axis',
                formatter: function (params) {
                    if (!params || params.length === 0) return '';
                    const p = params[0];
                    const i = p.dataIndex;
                    const v = (p.value === null || typeof p.value === 'undefined') ? '-' : p.value;
                    const c = (count && typeof count[i] !== 'undefined') ? count[i] : 0;
                    return `${p.axisValue}<br/>Rata-rata: <b>${v}</b><br/>Jumlah posted: <b>${c}</b>`;
                }
            },
            grid: { left: 40, right: 20, top: 20, bottom: 30 },
            xAxis: { type: 'category', data: labels },
            yAxis: { type: 'value', min: 0, max: 100 },
            series: [{
                name: 'Rata-rata Nilai',
                type: 'line',
                smooth: true,
                data: avg,
                connectNulls: true
            }]
        });

        window.addEventListener('resize', function () {
            myChart.resize();
        });
    }

    function renderChartStatusBulanan(chart) {
        if (typeof echarts === 'undefined') return;
        const el = document.getElementById('chartStatusBulanan');
        if (!el) return;

        const labels = (chart && Array.isArray(chart.labels)) ? chart.labels : [];
        const posted = (chart && Array.isArray(chart.posted)) ? chart.posted : [];
        const draft = (chart && Array.isArray(chart.draft)) ? chart.draft : [];

        const myChart = echarts.init(el);
        myChart.setOption({
            tooltip: { trigger: 'axis' },
            legend: { data: ['Posted', 'Draft'] },
            grid: { left: 40, right: 20, top: 30, bottom: 30 },
            xAxis: { type: 'category', data: labels },
            yAxis: { type: 'value' },
            series: [
                { name: 'Posted', type: 'bar', stack: 'total', data: posted },
                { name: 'Draft', type: 'bar', stack: 'total', data: draft }
            ]
        });
        window.addEventListener('resize', function () { myChart.resize(); });
    }

    function renderChartDistribusi(dist) {
        if (typeof echarts === 'undefined') return;
        const el = document.getElementById('chartDistribusiNilai');
        if (!el) return;

        const labels = (dist && Array.isArray(dist.labels)) ? dist.labels : [];
        const counts = (dist && Array.isArray(dist.counts)) ? dist.counts : [];

        const myChart = echarts.init(el);
        myChart.setOption({
            tooltip: { trigger: 'axis' },
            grid: { left: 40, right: 20, top: 20, bottom: 30 },
            xAxis: { type: 'category', data: labels },
            yAxis: { type: 'value' },
            series: [{
                name: 'Jumlah Pegawai',
                type: 'bar',
                data: counts
            }]
        });
        window.addEventListener('resize', function () { myChart.resize(); });
    }

    function renderChartTop(top) {
        if (typeof echarts === 'undefined') return;
        const el = document.getElementById('chartTopPegawai');
        if (!el) return;

        const data = Array.isArray(top) ? top : [];
        const names = data.map(x => x.nama);
        const values = data.map(x => x.nilai);

        const myChart = echarts.init(el);
        myChart.setOption({
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
            grid: { left: 120, right: 20, top: 20, bottom: 30 },
            xAxis: { type: 'value', min: 0, max: 100 },
            yAxis: { type: 'category', data: names, inverse: true },
            series: [{
                name: 'Nilai',
                type: 'bar',
                data: values
            }]
        });
        window.addEventListener('resize', function () { myChart.resize(); });
    }
</script>

<!-- Modal Preview PCK (untuk tombol Preview di dashboard) -->
<div class="modal fade" id="modal-preview-pck" tabindex="-1" role="dialog" aria-labelledby="modalPreviewPCKLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white" id="modalPreviewPCKLabel">
                    Preview Penilaian Capaian Kinerja (PCK)
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