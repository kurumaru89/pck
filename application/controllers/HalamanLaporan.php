<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class HalamanLaporan extends MY_Controller
{
    public function get_data_preview_pk()
    {
        $periode_id = $this->input->post('periode_id');
        $lokasi = $this->input->post('lokasi');
        $tanggal = $this->input->post('tanggal');

        if (!$periode_id) {
            $response = array(
                'success' => 3,
                'message' => 'Periode ID tidak ditemukan'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }

        // Validasi lokasi dan tanggal
        if (empty($lokasi) || empty($tanggal)) {
            $response = array(
                'success' => 3,
                'message' => 'Lokasi dan Tanggal harus diisi'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }

        $periode = $this->model->get_seleksi_array('pk_periode', ['id' => $periode_id])->row();
        if (!$periode) {
            $response = array(
                'success' => false,
                'message' => 'Periode tidak ditemukan'
            );
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }

        $sasaran = $this->model->get_seleksi_array('pk_sasaran_kinerja', ['periode_id' => $periode_id])->result();
        $indikator = $this->model->ambil_indikator($periode_id);

        $html = $this->generate_preview_pk($periode, $sasaran, $indikator, $lokasi, $tanggal);

        $response = array(
            'success' => 1,
            'html' => $html
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function generate_preview_pk($periode, $sasaran, $indikator, $lokasi, $tanggal_input)
    {
        $bulan_nama = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUNI', 'JULI', 'AGUS', 'SEP', 'OKT', 'NOV', 'DES'];
        $bulan_nama_full = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Format tanggal
        if ($tanggal_input) {
            $tanggal_obj = DateTime::createFromFormat('Y-m-d', $tanggal_input);
            if ($tanggal_obj) {
                $tanggal = $tanggal_obj->format('d') . ' ' . $bulan_nama_full[(int) $tanggal_obj->format('n')] . ' ' . $tanggal_obj->format('Y');
            } else {
                $tanggal = date('d') . ' ' . $bulan_nama_full[date('n')] . ' ' . date('Y');
            }
        } else {
            $tanggal = date('d') . ' ' . $bulan_nama_full[date('n')] . ' ' . date('Y');
        }

        $html = '<div class="pk-container" style="padding: 20px; font-family: Arial, sans-serif;">';

        // Header
        $html .= '<div class="pk-header">';
        $html .= '<h3>FORMULIR PERJANJIAN KINERJA INDIVIDU</h3>';
        $html .= '<h3>' . $this->session->userdata('nama_satker') . '</h3>';
        $html .= '</div>';

        // Tabel PK
        $html .= '<table class="pk-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th rowspan="2" style="width: 3%;">NO</th>';
        $html .= '<th rowspan="2" style="width: 21%;">SASARAN KEGIATAN</th>';
        $html .= '<th rowspan="2" style="width: 3%;">NO</th>';
        $html .= '<th rowspan="2" style="width: 20%;">INDIKATOR KINERJA</th>';
        $html .= '<th rowspan="2" style="width: 4%;">TARGET MUTU</th>';
        $html .= '<th rowspan="2" style="width: 4%;">TARGET KUANTITAS</th>';
        $html .= '<th rowspan="2" style="width: 4%;">SATUAN</th>';
        $html .= '<th colspan="12" style="width: 30%;">WAKTU PENYELESAIAN</th>';
        $html .= '<th rowspan="2" style="width: 11%;">PAGU ANGGARAN<br>(dalam rupiah)</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        foreach ($bulan_nama as $idx => $bulan) {
            if ($idx > 0) {
                $html .= '<th style="width: 2.5%;">' . $bulan . '</th>';
            }
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $no_sasaran = 1;
        $no_indikator_global = 1;

        foreach ($sasaran as $s) {
            // Get IKI untuk Sasaran Kegiatan ini
            $ik = array_filter($indikator, function ($i) use ($s) {
                return $i->sasaran_id == $s->id;
            });

            $rowspan = count($ik);
            if ($rowspan == 0)
                $rowspan = 1;

            $first_row = true;
            $no_indikator = 1;

            foreach ($ik as $i) {
                $html .= '<tr>';

                if ($first_row) {
                    $html .= '<td rowspan="' . $rowspan . '" style="text-align: center; vertical-align: middle;">' . $no_sasaran . '</td>';
                    $html .= '<td rowspan="' . $rowspan . '" class="text-left" style="vertical-align: middle;">' . htmlspecialchars($s->nama_sasaran) . '</td>';
                    $first_row = false;
                }

                $html .= '<td style="text-align: center;">' . $no_indikator . '</td>';
                $html .= '<td class="text-left">' . htmlspecialchars($i->nama_indikator) . '</td>';

                // Target Mutu
                $target_mutu = isset($i->target_mutu) && !empty($i->target_mutu) ? htmlspecialchars($i->target_mutu) : '-';
                $html .= '<td style="text-align: center;">' . $target_mutu . '</td>';

                // Target Kuantitas
                $target_kuantitas = isset($i->target_kuantitas) && !empty($i->target_kuantitas) ? htmlspecialchars($i->target_kuantitas) : '-';
                $html .= '<td style="text-align: center;">' . $target_kuantitas . '</td>';

                // Satuan
                $satuan = isset($i->satuan) && !empty($i->satuan) ? htmlspecialchars($i->satuan) : '-';
                $html .= '<td style="text-align: center;">' . $satuan . '</td>';

                // Waktu Penyelesaian (Bulan)
                $bulan_penyelesaian = [];
                if (isset($i->bulan_penyelesaian) && !empty($i->bulan_penyelesaian)) {
                    $bulan_json = json_decode($i->bulan_penyelesaian, true);
                    if (is_array($bulan_json)) {
                        $bulan_penyelesaian = $bulan_json;
                    }
                }

                for ($b = 1; $b <= 12; $b++) {
                    $checked = in_array($b, $bulan_penyelesaian) ? '<span class="pk-checkbox">√</span>' : '';
                    $html .= '<td style="text-align: center;">' . $checked . '</td>';
                }

                // Pagu Anggaran
                $html .= '<td style="text-align: center;">-</td>';

                $html .= '</tr>';
                $no_indikator++;
                $no_indikator_global++;
            }

            // Jika tidak ada IKI untuk Sasaran Kegiatan ini
            if (count($ik) == 0) {
                $html .= '<tr>';
                $html .= '<td style="text-align: center;">' . $no_sasaran . '</td>';
                $html .= '<td class="text-left">' . htmlspecialchars($s->nama_sasaran) . '</td>';
                $html .= '<td colspan="15" style="text-align: center; color: #999;">Belum ada Indikator Kinerja</td>';
                $html .= '</tr>';
            }

            $no_sasaran++;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $nama_penilai = $periode->validator_nama;
        $nip_penilai = $periode->validator_nip;

        $link = $this->config->item('sso_server') . 'halamankartupegawai/kartu_pegawai/' . $periode->modified_by; // atau link apapun sesuai kebutuhan
        $logoPath = $this->session->userdata('logo_satker'); // path logo PNG kecil

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($link)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(100)
            ->margin(0)
            ->logoPath($logoPath)
            ->logoResizeToWidth(20)
            ->build();

        $ttd = $result->getDataUri();

        // Footer - Tanda Tangan
        $html .= '<div class="pk-footer">';
        /*
        $html .= '<div class="pk-signature">';
        $html .= '<table>';
        $html .= '<tr><td style="text-align: center; padding-top: 20px;"><strong>Atasan Pejabat Penilai</strong></td></tr>';
        $html .= '<tr><td style="text-align: center; padding-top: 50px;">' . htmlspecialchars($nama_atasan_penilai) . '</td></tr>';
        $html .= '<tr><td style="text-align: center; padding-top: 5px;">NIP. ' . htmlspecialchars($nip_atasan_penilai) . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        */
        $html .= '<div class="pk-signature">';
        $html .= '<table>';
        $html .= '<tr><td style="text-align: center; padding-top: 20px;"></td></tr>';
        $html .= '<tr><td style="text-align: center; padding-top: 50px;"></td></tr>';
        $html .= '<tr><td style="text-align: center; padding-top: 5px;"></td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="pk-signature">';
        $html .= '<table>';
        $html .= '<tr><td style="text-align: center;">' . htmlspecialchars($lokasi) . ', ' . $tanggal . '</td></tr>';
        $html .= '<tr><td style="text-align: center;"><strong>Pejabat Penilai</strong></td></tr>';
        $html .= '<tr><td style="text-align: center;"><img src="' . $ttd . '" style="width: 100px; height: 100px;"></td></tr>';
        $html .= '<tr><td style="text-align: center;">' . htmlspecialchars($nama_penilai) . '</td></tr>';
        $html .= '<tr><td style="text-align: center; padding-top: 5px;">NIP. ' . htmlspecialchars($nip_penilai) . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
    
    public function get_data_pck()
    {
        $penilaian_id = $this->security->xss_clean($this->input->post('penilaian_id'));

        // Get data penilaian
        $penilaian_query = $this->model->get_seleksi_array('ck_penilaian', [
            'id' => $penilaian_id
        ]);

        $penilaian = $penilaian_query->row();
        $periode_id = $penilaian->periode_id;
        $periode_data = $this->model->get_seleksi_array('pk_periode', ['id' => $periode_id]);

        $link = $this->config->item('sso_server') . 'halamankartupegawai/kartu_pegawai/' . $periode_data->row()->modified_by; // atau link apapun sesuai kebutuhan
        $logoPath = $this->session->userdata('logo_satker'); // path logo PNG kecil

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($link)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(100)
            ->margin(0)
            ->logoPath($logoPath)
            ->logoResizeToWidth(20)
            ->build();

        $ttd = $result->getDataUri();

        $data_penilaian = [
            'bulan' => $penilaian->bulan,
            'tahun' => $periode_data->row()->tahun,
            'nama' => $periode_data->row()->nama_pegawai,
            'nip' => $periode_data->row()->nip,
            'jabatan' => $penilaian->jabatan,
            'pangkat' => $penilaian->pangkat,
            'nilai' => $penilaian->nilai,
            'validator_nama' => $periode_data->row()->validator_nama,
            'validator_nip' => $periode_data->row()->validator_nip,
            'ttd' => $ttd
        ];

        // Get all PCK data untuk penilaian ini
        $indikator_list = $this->model->get_seleksi_array('v_capaian_indikator', ['penilaian_id' => $penilaian_id])->result();

        // Get uraian tugas untuk setiap PCK
        $uraian_tugas_data = array();
        foreach ($indikator_list as $pck) {
            $uraian = $this->model->get_seleksi_array('ck_uraian_tugas', ['capaian_id' => $pck->id]);
            if ($uraian) {
                $uraian_tugas_data[$pck->id] = $uraian->result();
            }
        }

        // Generate HTML untuk penilaian ini
        $html = $this->generate_pck_html($indikator_list, $uraian_tugas_data, $data_penilaian);

        $response = array(
            'success' => true,
            'html' => $html
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function generate_pck_html($pck_list, $uraian_tugas_data, $data_penilaian)
    {
        $bulan_nama_full = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $nama_bulan = isset($bulan_nama_full[$data_penilaian['bulan']]) ? $bulan_nama_full[$data_penilaian['bulan']] : '';

        // Tambahkan style untuk print landscape
        $html = '<style>
            @media print {
                @page {
                    size: A4 landscape;
                    margin: 1cm;
                }
                .pck-container {
                    padding: 10px;
                }
            }
        </style>';

        $html .= '<div class="pck-container" style="padding: 20px; font-family: Arial, sans-serif;">';

        // Header
        $html .= '<div class="pck-header" style="text-align: center; margin-bottom: 20px;">';
        $html .= '<h3 style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">FORMULIR PENILAIAN CAPAIAN KINERJA BULANAN PEGAWAI NEGERI SIPIL</h3>';
        $html .= '<h4 style="font-size: 12px; font-weight: bold; margin-bottom: 20px;">Bulan ' . $nama_bulan . ' ' . $data_penilaian['tahun'] . '</h4>';
        $html .= '</div>';

        // Info Pegawai
        $html .= '<div class="pk-info mb-3">';
        $html .= '<table style="width: 100%; font-size: 11px;">';
        $html .= '<tr>';
        $html .= '<td style="width: 20%;"><strong>Nama</strong></td>';
        $html .= '<td style="width: 2%;">:</td>';
        $html .= '<td>' . $data_penilaian['nama'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>NIP</strong></td>';
        $html .= '<td>:</td>';
        $html .= '<td>' . $data_penilaian['nip'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Pangkat/Gol. Ruang</strong></td>';
        $html .= '<td>:</td>';
        $html .= '<td>' . $data_penilaian['pangkat'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Jabatan</strong></td>';
        $html .= '<td>:</td>';
        $html .= '<td>' . $data_penilaian['jabatan'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td><strong>Unit Kerja</strong></td>';
        $html .= '<td>:</td>';
        $html .= '<td>' . $this->session->userdata('nama_pengadilan') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        // Group PCK by IKI (setiap IKI menjadi section terpisah)
        $iki_sections = array();
        $total_penghitungan = 0;
        $total_nilai_capaian = 0;
        $section_count = 0;

        foreach ($pck_list as $pck) {
            if (!isset($iki_sections[$pck->id])) {
                $iki_sections[$pck->id] = array(
                    'pck' => $pck,
                    'uraian_tugas' => isset($uraian_tugas_data[$pck->id]) ? $uraian_tugas_data[$pck->id] : array()
                );
            }
        }

        // Generate setiap section IKI
        foreach ($iki_sections as $section) {
            $pck = $section['pck'];
            $uraian_tugas = $section['uraian_tugas'];

            if (empty($pck->nama_indikator)) {
                continue;
            }

            $section_count++;
            $section_penghitungan = 0;
            $section_nilai_capaian = 0;

            // Header Section IKI
            $html .= '<div class="pck-section" style="margin-bottom: 30px;">';
            $html .= '<h5 style="font-size: 12px; font-weight: bold;">';
            $html .= 'INDIKATOR KINERJA : ' . htmlspecialchars($pck->nama_indikator);
            $html .= '</h5>';

            // Tabel Uraian Tugas
            $html .= '<table class="pck-table" style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px;">';

            // Header Tabel
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th rowspan="2" style="width: 3%; border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle;">NO</th>';
            $html .= '<th rowspan="2" style="width: 30%; border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle;">KEGIATAN TUGAS JABATAN</th>';
            $html .= '<th colspan="3" style="border: 1px solid #000; padding: 5px; text-align: center;">TARGET</th>';
            $html .= '<th colspan="3" style="border: 1px solid #000; padding: 5px; text-align: center;">REALISASI</th>';
            $html .= '<th rowspan="2" style="width: 10%; border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle;">NILAI CAPAIAN KINERJA</th>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center;">KUANT/OUTPUT</th>';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center;">SATUAN</th>';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center;">KUAL/MUTU</th>';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center;">KUANT/OUTPUT</th>';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center;">SATUAN</th>';
            $html .= '<th style="border: 1px solid #000; padding: 5px; text-align: center;">KUAL/MUTU</th>';
            $html .= '</tr>';
            $html .= '</thead>';

            // Body Tabel
            $html .= '<tbody>';
            $no = 1;
            $total_penghitungan_section = 0;

            foreach ($uraian_tugas as $uraian) {
                $target_kualitas = floatval($uraian->target_kualitas);
                $realisasi_kualitas = floatval($uraian->realisasi_kualitas);

                $html .= '<tr>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . $no++ . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: left;">' . htmlspecialchars($uraian->uraian_tugas) . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . htmlspecialchars($uraian->target_kuantitas) . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . htmlspecialchars($uraian->satuan) . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . $target_kualitas . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . htmlspecialchars($uraian->realisasi_kuantitas) . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . htmlspecialchars($uraian->satuan) . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . $realisasi_kualitas . '</td>';
                $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . number_format($uraian->nilai, 2, ',', '.') . '</td>';
                $html .= '</tr>';
            }

            // Summary row untuk section ini
            $html .= '<tr style="font-weight: bold;">';
            $html .= '<td colspan="8" style="border: 1px solid #000; padding: 5px; text-align: center;">NILAI CAPAIAN KINERJA</td>';
            $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . number_format($pck->capaian, 2, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        // Rekapitulasi
        $html .= '<div class="pck-rekap" style="margin-top: 30px;">';
        $html .= '<h5 style="font-size: 12px; font-weight: bold; margin-bottom: 10px; padding: 8px; border: 1px solid #000;">';
        $html .= 'REKAPITULASI PENILAIAN CAPAIAN KINERJA BULAN ' . strtoupper($nama_bulan) . ' ' . $data_penilaian['tahun'];
        $html .= '</h5>';

        $html .= '<table class="pck-table" style="width: 100%; border-collapse: collapse; font-size: 10px;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="width: 5%; border: 1px solid #000; padding: 5px; text-align: center;">NO</th>';
        $html .= '<th style="width: 60%; border: 1px solid #000; padding: 5px; text-align: center;">INDIKATOR KINERJA</th>';
        $html .= '<th style="width: 20%; border: 1px solid #000; padding: 5px; text-align: center;">NILAI CAPAIAN KINERJA</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $no_rekap = 1;
        foreach ($iki_sections as $section) {
            $pck = $section['pck'];
            $uraian_tugas = $section['uraian_tugas'];

            if (empty($pck->nama_indikator) || empty($uraian_tugas)) {
                continue;
            }

            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . $no_rekap++ . '</td>';
            $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: left;">' . htmlspecialchars($pck->nama_indikator) . '</td>';
            $html .= '<td style="border: 1px solid #000; padding: 5px; text-align: center;">' . number_format($pck->capaian, 2, ',', '.') . '</td>';
            $html .= '</tr>';
        }

        // Total rekapitulasi
        $avg_total = $data_penilaian['nilai'];
        if ($avg_total >= 90) {
            $keterangan = '(Sangat Baik)';
        } elseif ($avg_total >= 76) {
            $keterangan = '(Baik)';
        } elseif ($avg_total >= 61) {
            $keterangan = '(Cukup)';
        } else {
            $keterangan = '(Kurang)';
        }

        $html .= '<tr style="font-weight: bold;">';
        $html .= '<td colspan="2" style="border: 1px solid #000; padding: 5px; text-align: center;">HASIL CAPAIAN KINERJA BULAN ' . strtoupper($nama_bulan) . '</td>';
        $html .= '<td style="text-center">' . number_format($avg_total, 2, ',', '.') . ' ' . $keterangan . '</td>';
        $html .= '</tr>';

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        // Tanda Tangan
        $html .= '<div class="pck-signature">';
        $html .= '<table style="width: 100%; font-size: 11px;">';
        $html .= '<tr>';
        $html .= '<td style="width: 60%;"></td>'; // Kolom kiri kosong untuk push ke kanan
        $html .= '<td style="width: 40%; text-align: center; padding-top: 10px;">';


        $html .= '<strong>Pejabat Penilai</strong><br>';
        if ($data_penilaian['ttd']) {
            $html .= '<img src="' . $data_penilaian['ttd'] . '" style="width: 100px; height: 100px;"><br>';
        } else {
            $html .= '<div style="height: 100px; margin-top: -10px; margin-bottom: -10px;"></div>';
        }
        $html .= htmlspecialchars($data_penilaian['validator_nama']) . '<br>';
        $html .= 'NIP. ' . htmlspecialchars($data_penilaian['validator_nip']);
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}