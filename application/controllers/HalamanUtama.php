<?php

class HalamanUtama extends MY_Controller
{
    public function index()
    {
        #die(var_dump($this->session->all_userdata()));
        $data['peran'] = $this->session->userdata('peran');
        $data['page'] = 'dashboard';
        $data['sso'] = $this->config->item('sso_server');

        $this->load->view('layout', $data);
    }

    public function page($halaman)
    {
        // Amanin nama file view agar tidak sembarang file bisa diload
        $allowed = [
            'dashboard',
            'pck',
            'detail_pk',
            'validasi_pk',
            'penilaian_pck',
            'detail_pengisian_pck',
            'detail_evaluasi_pck',
            'monitoring',
            'panduan_penggunaan',
            'dokumentasi_teknis'
        ];

        if (in_array($halaman, $allowed)) {
            if ($halaman === 'dokumentasi_teknis' && $this->session->userdata('peran') !== 'admin') {
                show_404();
            }
            if ($halaman == 'detail_pk') {
                $id = $this->encryption->decrypt(base64_decode($this->input->get('id')));
                $data_pk = $this->model->get_seleksi_array('pk_periode', ['id' => $id]);
                if ($data_pk->num_rows() > 0) {
                    $data['periode'] = $data_pk->row();
                }
            } elseif ($halaman == 'validasi_pk') {
                if ($this->session->userdata('ketua')) {
                    $tahun = $this->input->get('id');

                    $data['staf'] = $this->model->get_seleksi_array('pk_periode', [
                        'tahun' => $tahun,
                        'jabatan_id_penilai' => $this->session->userdata('jab_id')
                    ])->result();

                    foreach ($data['staf'] as $key => $row) {
                        $encrypted = $this->encryption->encrypt($row->id);
                        $data['staf'][$key]->id = base64_encode($encrypted);
                    }

                } else {
                    $id = $this->encryption->decrypt(base64_decode($this->input->get('id')));
                    $data_pk = $this->model->get_seleksi_array('pk_periode', ['id' => $id]);
                    if ($data_pk->num_rows() > 0) {
                        $tahun = $data_pk->row()->tahun;
                        $data['staf'] = $this->model->get_seleksi_array('pk_periode', [
                            'tahun' => $tahun,
                            'jabatan_id_penilai' => $this->session->userdata('jab_id')
                        ])->result();

                        foreach ($data['staf'] as $key => $row) {
                            $encrypted = $this->encryption->encrypt($row->id);
                            $data['staf'][$key]->id = base64_encode($encrypted);
                        }
                    }
                }

            } elseif ($halaman == 'penilaian_pck') {
                $data['ketua'] = $this->session->userdata('ketua');
            } elseif ($halaman == 'detail_pengisian_pck') {
                $id = $this->encryption->decrypt(base64_decode($this->input->get('id')));
                $data_pck = $this->model->get_seleksi_array('ck_penilaian', ['id' => $id]);
                $data_periode = $this->model->get_seleksi_array('pk_periode', ['id' => $data_pck->row()->periode_id]);
                if ($data_pck->num_rows() > 0) {
                    $data['pengisian'] = $data_pck->row();
                }
                $data['periode_id'] = base64_encode($this->encryption->encrypt($data_pck->row()->periode_id));
                $data['nama_periode'] = $data_periode->row()->nama_periode;
                $data['tahun'] = $data_periode->row()->tahun;
                $data['indikator_kinerja'] = $this->model->ambil_indikator_pck($data_pck->row()->periode_id);
            } elseif ($halaman == 'detail_evaluasi_pck') {
                if ($this->session->userdata('ketua')) {
                    $raw_json = urldecode($this->input->get('id'));
                    $json_payload = json_decode($raw_json, true);
                    //die(var_dump($json_payload));

                    $bulan = $json_payload['bulan'];
                    $tahun = $json_payload['tahun'];

                    $data['staf'] = $this->model->ambil_daftar_pck_staf([
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                        'jabatan_penilai' => $this->session->userdata('jab_id')
                    ])->result();

                    $data['periode_id'] = $tahun;

                } else {
                    $id = $this->encryption->decrypt(base64_decode($this->input->get('id')));
                    $data_penilaian = $this->model->get_seleksi_array('ck_penilaian', ['id' => $id]);
                    $data_periode = $this->model->get_seleksi_array('pk_periode', ['id' => $data_penilaian->row()->periode_id]);

                    $tahun = $data_periode->row()->tahun;
                    $data['staf'] = $this->model->ambil_daftar_pck_staf([
                        'bulan' => $data_penilaian->row()->bulan,
                        'tahun' => $tahun,
                        'jabatan_penilai' => $this->session->userdata('jab_id')
                    ])->result();

                    $data['periode_id'] = base64_encode($this->encryption->encrypt($data_penilaian->row()->periode_id));
                }

                // Tambahkan statistik uraian tugas dinilai/belum dinilai per penilaian staf
                $penilaian_ids = array_map(function ($r) {
                    return $r->id;
                }, $data['staf']);
                $stat_uraian = $this->model->ambil_stat_uraian_tugas_penilaian($penilaian_ids);

                foreach ($data['staf'] as $key => $row) {
                    $pid = $row->id;
                    $data['staf'][$key]->uraian_sudah_dinilai = isset($stat_uraian[$pid]) ? $stat_uraian[$pid]['sudah_dinilai'] : 0;
                    $data['staf'][$key]->uraian_belum_dinilai = isset($stat_uraian[$pid]) ? $stat_uraian[$pid]['belum_dinilai'] : 0;
                    $encrypted = $this->encryption->encrypt($row->id);
                    $data['staf'][$key]->id = base64_encode($encrypted);
                }
            }

            $data['peran'] = $this->session->userdata('peran');
            $data['page'] = $halaman;
            // Dari navigasi SPA: loadPage('detail_pk', encodedId) → GET ?id=...
            $data['id_param'] = $this->input->get('id');
            $this->load->view($halaman, $data);
        } else {
            show_404();
        }
    }

    public function cek_token_sso()
    {
        $token = $this->input->cookie('sso_token');
        $cookie_domain = $this->config->item('sso_server');
        $sso_api = $cookie_domain . "api/cek_token?sso_token={$token}";
        $response = file_get_contents($sso_api);
        $data = json_decode($response, true);

        if ($data['status'] == 'success') {
            echo json_encode(['valid' => true]);
        } else {
            echo json_encode(['valid' => false, 'message' => 'Token Expired, Silakan login ulang', 'url' => $cookie_domain . 'login']);
        }
    }

    public function show_role()
    {
        $id = $this->input->post('id');
        $data = [
            "tabel" => "v_users",
            "kolom_seleksi" => "status_pegawai",
            "seleksi" => "1"
        ];

        $users = $this->apihelper->get('apiclient/get_data_seleksi', $data);

        $pegawai = array();
        if ($users['status_code'] === '200') {
            foreach ($users['response']['data'] as $item) {
                $pegawai[$item['userid']] = $item['fullname'];
            }
        }

        if ($id != '-1') {
            $query = $this->model->get_seleksi_array('peran', ['id' => $id]);

            echo json_encode(
                array(
                    'pegawai' => $users['response']['data'],
                    'role' => $pegawai,
                    'id' => $query->row()->id,
                    'editPegawai' => $query->row()->userid,
                    'editPeran' => $query->row()->role
                )
            );
        } else {
            $dataPeran = $this->model->get_data_peran();
            #die(var_dump($dataPeran));

            echo json_encode(
                array(
                    'pegawai' => $users['response']['data'],
                    'role' => $pegawai,
                    'data_peran' => $dataPeran
                )
            );
        }

        return;
    }

    public function simpan_peran()
    {
        $id = $this->input->post('id');
        $pegawai = $this->input->post('pegawai');
        $peran = $this->input->post('peran');

        if ($id) {
            $data = array(
                'userid' => $pegawai,
                'role' => $peran,
                'modified_by' => $this->session->userdata('fullname'),
                'modified_on' => date('Y-m-d H:i:s')
            );

            $query = $this->model->pembaharuan_data('peran', $data, 'id', $id);
        } else {
            $query = $this->model->get_seleksi_array('peran', ['userid' => $pegawai]);
            if ($query->num_rows() > 0) {
                echo json_encode(['success' => 2, 'message' => 'Pegawai tersebut sudah memiliki peran']);
            }

            $data = array(
                'userid' => $pegawai,
                'role' => $peran,
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $query = $this->model->simpan_data('peran', $data);
        }

        if ($query === 1) {
            echo json_encode(['success' => 1, 'message' => 'Penunjukan Peran Pegawai Berhasil']);
        } else {
            echo json_encode(['success' => 3, 'message' => 'Gagal Menunjuk Peran Pegawai']);
        }
    }

    public function aktif_peran()
    {
        $id = $this->input->post('id');

        $data = array(
            'hapus' => '0',
            'modified_by' => $this->session->userdata('username'),
            'modified_on' => date('Y-m-d H:i:s')
        );

        $query = $this->model->pembaharuan_data('peran', $data, 'id', $id);
        if ($query == '1') {
            echo json_encode(
                array(
                    'st' => '1'
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => '0'
                )
            );
        }
    }

    public function blok_peran()
    {
        $id = $this->input->post('id');

        $data = array(
            'hapus' => '1',
            'modified_by' => $this->session->userdata('username'),
            'modified_on' => date('Y-m-d H:i:s')
        );

        $query = $this->model->pembaharuan_data('peran', $data, 'id', $id);
        if ($query == '1') {
            echo json_encode(
                array(
                    'st' => '1'
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => '0'
                )
            );
        }
    }

    public function keluar()
    {
        $sso_server = $this->config->item('sso_server');
        $this->session->sess_destroy();
        redirect($sso_server . '/keluar');
    }

    public function get_statistik_dashboard()
    {
        $this->load->model('Model', 'model');
        $tanggal_hari_ini = date('Y-m-d');

        // Total peserta
        $total_peserta = $this->model->get_seleksi_array('register_peserta_magang')->num_rows();

        // Peserta aktif
        $peserta_aktif = $this->model->get_seleksi_array('register_peserta_magang', ['status' => '1'])->num_rows();

        // Peserta tidak aktif
        $peserta_tidak_aktif = $this->model->get_seleksi_array('register_peserta_magang', ['status' => '0'])->num_rows();

        // Presensi hari ini
        $presensi_hari_ini = $this->model->get_presensi_harian($tanggal_hari_ini);
        $sudah_presensi_masuk = 0;
        $sudah_presensi_pulang = 0;
        $belum_presensi = 0;

        foreach ($presensi_hari_ini as $row) {
            if ($row->status == '1') { // Hanya hitung peserta aktif
                if ($row->masuk) {
                    $sudah_presensi_masuk++;
                }
                if ($row->pulang) {
                    $sudah_presensi_pulang++;
                }
                if (!$row->masuk && !$row->pulang) {
                    $belum_presensi++;
                }
            }
        }

        // Data presensi 7 hari terakhir untuk chart
        $data_chart = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-$i days"));
            $hari = $this->tanggalhelper->convertDayDate($tanggal);
            $presensi_tgl = $this->model->get_presensi_harian($tanggal);
            $total_presensi = 0;
            foreach ($presensi_tgl as $row) {
                if ($row->status == '1' && ($row->masuk || $row->pulang)) {
                    $total_presensi++;
                }
            }
            $data_chart[] = [
                'tanggal' => date('d/m', strtotime($tanggal)),
                'hari' => explode(', ', $hari)[0], // Ambil nama hari saja
                'total' => $total_presensi
            ];
        }

        // Presensi terbaru (5 terakhir)
        $this->db->select('p.nama, pr.tgl, pr.masuk, pr.pulang');
        $this->db->from('register_presensi_magang pr');
        $this->db->join('register_peserta_magang p', 'p.id = pr.peserta_id', 'left');
        $this->db->where('p.status', '1');
        $this->db->order_by('pr.created_on', 'DESC');
        $this->db->limit(5);
        $presensi_terbaru = $this->db->get()->result();

        $data_presensi_terbaru = [];
        foreach ($presensi_terbaru as $row) {
            $data_presensi_terbaru[] = [
                'nama' => $row->nama,
                'tanggal' => $this->tanggalhelper->convertDayDate($row->tgl),
                'masuk' => $row->masuk ? $this->tanggalhelper->konversiJam($row->masuk) : '-',
                'pulang' => $row->pulang ? $this->tanggalhelper->konversiJam($row->pulang) : '-'
            ];
        }

        echo json_encode([
            'total_peserta' => $total_peserta,
            'peserta_aktif' => $peserta_aktif,
            'peserta_tidak_aktif' => $peserta_tidak_aktif,
            'sudah_presensi_masuk' => $sudah_presensi_masuk,
            'sudah_presensi_pulang' => $sudah_presensi_pulang,
            'belum_presensi' => $belum_presensi,
            'chart_data' => $data_chart,
            'presensi_terbaru' => $data_presensi_terbaru
        ]);
    }

    public function get_statistik_dashboard_pck()
    {
        $this->load->model('Model', 'model');

        $tahun = (int) date('Y');
        $bulan = (int) date('n');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $isKetua = (bool) $this->session->userdata('ketua');
        $pegawaiId = $this->session->userdata('pegawai_id');
        $jabId = $this->session->userdata('jab_id');

        $scope = $this->model->dashboard_scope_periode_ids([
            'ketua' => $isKetua,
            'pegawai_id' => $pegawaiId,
            'jab_id' => $jabId,
            'tahun' => $tahun
        ]);

        $list = $this->model->dashboard_list_perbandingan_bulan([
            'periode_ids' => $scope['periode_ids'],
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        $chart = $this->model->dashboard_chart_bulanan([
            'periode_ids' => $scope['periode_ids'],
            'tahun' => $tahun
        ]);

        $chart_status = $this->model->dashboard_chart_status_bulanan([
            'periode_ids' => $scope['periode_ids'],
            'tahun' => $tahun
        ]);

        $top = $this->model->dashboard_top_pegawai_bulan_ini([
            'periode_ids' => $scope['periode_ids'],
            'tahun' => $tahun,
            'bulan' => $bulan,
            'limit' => 10
        ]);

        $distribusi = $this->model->dashboard_distribusi_nilai_bulan_ini([
            'periode_ids' => $scope['periode_ids'],
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        header('Content-Type: application/json');
        echo json_encode([
            'tahun' => $tahun,
            'bulan' => $bulan,
            'bulan_nama' => $namaBulan[$bulan] ?? (string) $bulan,
            'ketua' => $isKetua,
            'scope' => $scope,
            'list' => $list,
            'chart' => $chart,
            'chart_status' => $chart_status,
            'top' => $top,
            'distribusi' => $distribusi
        ]);
    }

    /**
     * Data bersama untuk manual book (PDF & Word).
     */
    private function _manual_book_data()
    {
        $role = $this->session->userdata('role');
        $peran = $this->session->userdata('peran');

        return [
            'presensi_url' => site_url('presensi'),
            'nama_app' => $this->session->userdata('nama_client_app'),
            'deskripsi' => $this->session->userdata('deskripsi_client_app'),
            'satker' => $this->session->userdata('nama_pengadilan'),
            'tanggal_cetak' => date('d/m/Y H:i'),
            'is_admin_menu' => ($peran == 'admin' || in_array($role, ['super', 'validator_uk_satker', 'admin_satker'])),
        ];
    }

    /**
     * Unduh manual book PDF (dompdf) — kotak putus-putus untuk penanda tempat screenshot.
     */
    public function unduh_manual_pdf()
    {
        $data = $this->_manual_book_data();
        $html = $this->load->view('manual_book_pdf', $data, true);
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->set_option('isRemoteEnabled', false);
        $this->pdf->set_option('defaultFont', 'DejaVu Sans');
        $this->pdf->render();
        $this->pdf->stream('Manual_Book_LEUMANG_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    /**
     * Unduh manual book untuk diedit di Microsoft Word (HTML sebagai .doc).
     * Buka di Word, tempel screenshot di kotak, lalu Simpan sebagai .docx atau ekspor PDF dari Word.
     */
    public function unduh_manual_word()
    {
        $data = $this->_manual_book_data();
        $filename = 'Manual_Book_LEUMANG_' . date('Y-m-d') . '.doc';
        header('Content-Description: File Transfer');
        header('Content-Type: application/msword; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo "\xEF\xBB\xBF";
        $this->load->view('manual_book_word_html', $data);
    }
}