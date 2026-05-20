<?php

class HalamanCapaianKinerja extends MY_Controller
{
    public function cek_detail_periode()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));
        $cek = $this->model->ambil_indikator($id);

        echo json_encode(['data_periode' => $cek]);
    }

    public function cek_status_periode()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('periodeId')));
        $cek_status = $this->model->get_seleksi_array('pk_periode', ['id' => $id])->row()->status;

        echo json_encode(['status_periode' => $cek_status]);
    }

    public function ajukan_persetujuan_pk()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
        $data = [
            'status' => 1,
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        ];

        $result = $this->model->pembaharuan_data('pk_periode', $data, 'id', $id);
        if ($result) {
            $data_periode = $this->model->get_seleksi_array('pk_periode', ['id' => $id]);
            $dataNotif = array(
                'jenis_pesan' => 'pck',
                'id_pemohon' => $this->session->userdata('pegawai_id'),
                'pesan' => 'Assalamualaikum Wr. Wb. Pegawai atas nama ' . $this->session->userdata('fullname') . ' telah mengajukan persetujuan Perjanjian Kinerja Tahunan, buka LITERASI MS BANDA ACEH untuk melakukan validasi, Terima Kasih',
                'id_tujuan' => $this->ambil_atasan_pegawai_id($data_periode->row()->jabatan_id_penilai),
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->model->kirim_notif($dataNotif);
            echo json_encode(['success' => 1, 'message' => 'Pengajuan Perjanjian Kinerja Berhasil']);
        } else {
            echo json_encode(['success' => 3, 'message' => 'Pengajuan Perjanjian Kinerja Gagal, Silakan Ulangi atau Hubungi Admin']);
        }
    }

    public function validasi_pk()
    {
        $periode_id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
        $data_validasi = array(
            'status' => 2,
            'validator_nama' => $this->session->userdata('fullname'),
            'validator_nip' => $this->session->userdata('username'),
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        );

        $result = $this->model->pembaharuan_data('pk_periode', $data_validasi, 'id', $periode_id);

        if ($result) {
            $data_periode = $this->model->get_seleksi_array('pk_periode', ['id' => $periode_id]);
            $dataNotif = array(
                'jenis_pesan' => 'pck',
                'id_pemohon' => $this->session->userdata('pegawai_id'),
                'pesan' => 'Assalamualaikum Wr. Wb. ' . $data_periode->row()->nama_pegawai . ', Perjanjian Kinerja anda telah divalidasi atasan.',
                'id_tujuan' => $data_periode->row()->created_by,
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->model->kirim_notif($dataNotif);

            if ($this->session->userdata('ketua')) {
                echo json_encode(array(
                    'success' => 1,
                    'message' => 'Perjanjian kinerja berhasil divalidasi.',
                    'ketua' => $this->session->userdata('ketua'),
                    'tahun' => $data_periode->row()->tahun
                ));
            } else {
                echo json_encode(array(
                    'success' => 1,
                    'message' => 'Perjanjian kinerja berhasil divalidasi.'
                ));
            }
        } else {
            echo json_encode(array(
                'success' => 3,
                'message' => 'Perjanjian kinerja gagal divalidasi.'
            ));
        }
    }

    public function batal_validasi()
    {
        $periode_id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
        $data_validasi = array(
            'status' => 1,
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        );

        $result = $this->model->pembaharuan_data('pk_periode', $data_validasi, 'id', $periode_id);

        if ($result) {
            echo json_encode(array(
                'success' => 1,
                'message' => 'Perjanjian kinerja berhasil divalidasi.'
            ));
        } else {
            echo json_encode(array(
                'success' => 3,
                'message' => 'Perjanjian kinerja gagal divalidasi.'
            ));
        }
    }

    public function kembalikan_pk_ke_draft()
    {
        $periode_id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
        $keterangan = $this->input->post('keterangan');

        if (empty($keterangan) || trim($keterangan) === '') {
            echo json_encode(array(
                'success' => 3,
                'message' => 'Keterangan wajib diisi untuk mengembalikan perjanjian ke draft pegawai.'
            ));
            return;
        }

        $keterangan = $this->input->post('keterangan');
        $data_validasi = array(
            'status' => 0,
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        );

        $result = $this->model->pembaharuan_data('pk_periode', $data_validasi, 'id', $periode_id);

        if ($result) {
            $data_periode = $this->model->get_seleksi_array('pk_periode', ['id' => $periode_id]);
            $dataNotif = array(
                'jenis_pesan' => 'pck',
                'id_pemohon' => $this->session->userdata('pegawai_id'),
                'pesan' => 'Assalamualaikum Wr. Wb. ' . $data_periode->row()->nama_pegawai . ', Perjanjian Kinerja anda telah dikembalikan ke draft oleh atasan. Dengan alasan ' . $keterangan,
                'id_tujuan' => $data_periode->row()->created_by,
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->model->kirim_notif($dataNotif);
            echo json_encode(array(
                'success' => 1,
                'message' => 'Perjanjian kinerja berhasil dikembalikan ke draft.'
            ));
        } else {
            echo json_encode(array(
                'success' => 3,
                'message' => 'Perjanjian kinerja gagal dikembalikan ke draft.'
            ));
        }
    }

    public function show_tabel_periode_pk()
    {
        $data = [];
        if ($this->session->userdata('ketua')) {
            $query = $this->model->get_periode_ketua()->result();

            foreach ($query as $row) {
                $data[] = [
                    'struktural' => $this->session->userdata('struktural'),
                    'tahun' => $row->tahun,
                ];
            }
        } else {
            $query = $this->model->get_seleksi_array('pk_periode', ['created_by' => $this->session->userdata('pegawai_id')], ['status' => 'DESC'])->result();
            foreach ($query as $row) {
                $data[] = [
                    'id' => base64_encode($this->encryption->encrypt($row->id)),
                    'nama_periode' => $row->nama_periode,
                    'nama_pegawai' => $row->nama_pegawai,
                    'periode_awal' => date('d-m-Y', strtotime($row->periode_awal)),
                    'periode_akhir' => date('d-m-Y', strtotime($row->periode_akhir)),
                    'jabatan_pegawai' => $row->jabatan_pegawai,
                    'id_jabatan_pegawai' => $row->id_jabatan_pegawai,
                    'jabatan_pegawai_sekarang' => $this->session->userdata('jab_id'),
                    'pegawai_id_sekarang' => $this->session->userdata('pegawai_id'),
                    'struktural' => $this->session->userdata('struktural'),
                    'ketua' => $this->session->userdata('ketua'),
                    'pegawai_id' => $row->created_by,
                    'tahun' => $row->tahun,
                    'status' => $row->status
                ];
            }
        }

        echo json_encode(['data_periode' => $data, 'ketua' => $this->session->userdata('ketua')]);
    }

    public function show_periode()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));

        $nama_periode = "";
        $tahun = "";
        $periode_awal = "";
        $periode_akhir = "";

        if ($id == '-1') {
            $judul = "Tambah Periode Perjanjian Kinerja";
        } else {
            $judul = "Edit Periode Perjanjian Kinerja";
            $queryPeriode = $this->model->get_seleksi_array('pk_periode', ['id' => $id]);
            $nama_periode = $queryPeriode->row()->nama_periode;
            $tahun = $queryPeriode->row()->tahun;
            $periode_awal = $queryPeriode->row()->periode_awal;
            $periode_akhir = $queryPeriode->row()->periode_akhir;
        }

        echo json_encode(
            array(
                'st' => 1,
                'id' => base64_encode($this->encryption->encrypt($id)),
                'judul' => $judul,
                'nama_periode' => $nama_periode,
                'tahun' => $tahun,
                'periode_awal' => $periode_awal,
                'periode_akhir' => $periode_akhir
            )
        );
        return;
    }

    public function simpan_periode()
    {
        $this->form_validation->set_rules('nama_periode', 'Nama Periode', 'required|regex_match[/^[a-zA-Z0-9\s\'\-\.\,]+$/]');
        $this->form_validation->set_rules('tahun', 'Tahun Periode', 'required');
        $this->form_validation->set_rules('periode_awal', 'Periode Awal', "required");
        $this->form_validation->set_rules('periode_akhir', 'Periode Akhir', "required");

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong',
            'regex_match' => '%s hanya boleh berisi huruf, angka, spasi, titik, koma, tanda hubung, dan apostrof.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('id')))),
            'nama_periode' => $this->security->xss_clean($this->input->post('nama_periode')),
            'tahun' => $this->security->xss_clean($this->input->post('tahun')),
            'periode_awal' => $this->security->xss_clean($this->input->post('periode_awal')),
            'periode_akhir' => $this->security->xss_clean($this->input->post('periode_akhir')),
        ];

        $result = $this->model->proses_simpan_periode_pk($data);
        if ($result['status']) {
            echo json_encode(['success' => 1, 'message' => $result['message']]);
        } else {
            echo json_encode(['success' => 3, 'message' => $result['message']]);
        }
    }

    public function hapus_periode()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));
        $hapus = $this->model->pembaharuan_data('pk_periode', ['hapus' => '1', 'modified_on' => date('Y-m-d H:i:s'), 'modified_by' => $this->session->userdata('pegawai_id')], 'id', $id);

        if ($hapus == 1) {
            echo json_encode(
                array(
                    'st' => 1
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => 0
                )
            );
        }

        return;
    }

    public function show_tabel_detail_pk()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));
        $periode = $this->model->get_seleksi_array('pk_periode', ['id' => $id])->row();
        $query = $this->model->get_seleksi_array('pk_sasaran_kinerja', ['periode_id' => $id], ['periode_id' => 'ASC'])->result();
        $indikator = $this->model->ambil_indikator($id);

        $get_sasaran_jabatan = 0;
        $cek_sasaran_jabatan = $this->model->get_seleksi_array('v_sasaran_kinerja', [
            'tahun' => $periode->tahun,
            'jabatan_id' => $this->session->userdata('jab_id')
        ]);
        if ($this->session->userdata('struktural') && $cek_sasaran_jabatan->num_rows() > 0) {
            $get_sasaran_jabatan = 1;
        }

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id' => $row->id,
                'nama_sasaran' => $row->nama_sasaran,
                'jabatan_id' => $row->jabatan_id,
                'jabatan_id_sekarang' => $this->session->userdata('jab_id')
            ];
        }

        echo json_encode([
            'data_sasaran' => $data,
            'status' => $periode->status,
            'get_sasaran_jabatan' => $get_sasaran_jabatan,
            'indikator' => $indikator
        ]);
    }

    public function show_sasaran()
    {
        $sasaranId = $this->input->post('sasaranId');
        $periodeId = $this->encryption->decrypt(base64_decode($this->input->post('periodeId')));

        $nama_sasaran = "";

        if ($sasaranId == '-1' || !$sasaranId) {
            $judul = "Tambah Sasaran Kegiatan";
        } else {
            $judul = "Edit Sasaran Kegiatan";
            $querySasaran = $this->model->get_seleksi_array('pk_sasaran_kinerja', ['id' => $sasaranId]);
            $nama_sasaran = $querySasaran->row()->nama_sasaran;
        }

        echo json_encode(
            array(
                'st' => 1,
                'sasaran_id' => base64_encode($this->encryption->encrypt($sasaranId)),
                'periode_id' => base64_encode($this->encryption->encrypt($periodeId)),
                'judul' => $judul,
                'nama_sasaran' => $nama_sasaran,
            )
        );
        return;
    }

    public function simpan_sasaran()
    {
        $this->form_validation->set_rules('nama_sasaran', 'Nama Sasaran Kegiatan', 'required|regex_match[/^[a-zA-Z0-9\s\'\-\.\,]+$/]');
        $this->form_validation->set_rules('periode_id', 'Periode Id', 'required');
        $this->form_validation->set_rules('sasaran_id', 'Sasaran Id', "required");

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong',
            'regex_match' => '%s hanya boleh berisi huruf, angka, spasi, titik, koma, tanda hubung, dan apostrof.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'periode_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('periode_id')))),
            'sasaran_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('sasaran_id')))),
            'nama_sasaran' => $this->security->xss_clean($this->input->post('nama_sasaran'))
        ];

        $result = $this->model->proses_simpan_sasaran_kegiatan($data);
        if ($result['status']) {
            echo json_encode(['success' => 1, 'message' => $result['message']]);
        } else {
            echo json_encode(['success' => 3, 'message' => $result['message']]);
        }
    }

    public function hapus_sasaran()
    {
        $id = $this->input->post('id');
        $get_sasaran = $this->model->get_seleksi_array('pk_sasaran_kinerja', ['id' => $id])->row();

        $hapus = $this->model->pembaharuan_data('pk_sasaran_kinerja', ['hapus' => '1', 'modified_on' => date('Y-m-d H:i:s'), 'modified_by' => $this->session->userdata('pegawai_id')], 'id', $id);
        if ($hapus == 1) {
            echo json_encode(
                array(
                    'st' => 1,
                    'periode_id' => base64_encode($this->encryption->encrypt($get_sasaran->periode_id))
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => 0
                )
            );
        }

        return;
    }

    public function show_indikator()
    {
        $indikatorId = $this->input->post('indikatorId');
        $sasaranId = $this->input->post('sasaranId');
        $periodeId = $this->encryption->decrypt(base64_decode($this->input->post('periodeId')));

        $nama_indikator = "";
        $target_kuantitas = "";
        $satuan = "";
        $bulan_penyelesaian = [];
        $anggaran = "";

        if ($indikatorId == '-1' || !$indikatorId) {
            $judul = "Tambah Indikator Kinerja";
        } else {
            $judul = "Edit Indikator Kinerja";
            $queryIndikator = $this->model->get_seleksi_array('pk_indikator_kinerja', ['id' => $indikatorId]);
            $nama_indikator = $queryIndikator->row()->nama_indikator;
            $target_kuantitas = $queryIndikator->row()->target_kuantitas;
            $satuan = $queryIndikator->row()->satuan;
            $bulan_penyelesaian = $queryIndikator->row()->bulan_penyelesaian;
            $anggaran = $queryIndikator->row()->anggaran;
        }

        echo json_encode(
            array(
                'st' => 1,
                'indikator_id' => base64_encode($this->encryption->encrypt($indikatorId)),
                'sasaran_id' => base64_encode($this->encryption->encrypt($sasaranId)),
                'periode_id' => base64_encode($this->encryption->encrypt($periodeId)),
                'judul' => $judul,
                'nama_indikator' => $nama_indikator,
                'target_kuantitas' => $target_kuantitas,
                'satuan' => $satuan,
                'bulan_penyelesaian' => $bulan_penyelesaian,
                'anggaran' => $anggaran
            )
        );
        return;
    }

    public function simpan_indikator()
    {
        $this->form_validation->set_rules('nama_indikator', 'Nama Sasaran Kegiatan', 'required|regex_match[/^[a-zA-Z0-9\s\'\-\.\,]+$/]');
        $this->form_validation->set_rules('periode_id', 'Periode Id', 'required');
        $this->form_validation->set_rules('indikator_id', 'Indikator Id', "required");
        $this->form_validation->set_rules('sasaran_id', 'Sasaran Id', "required");
        $this->form_validation->set_rules('target_kuantitas', 'Target Kuantitas', "required|numeric");
        $this->form_validation->set_rules('satuan', 'Satuan Indikator', "required");

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong',
            'regex_match' => '%s hanya boleh berisi huruf, angka, spasi, titik, koma, tanda hubung, dan apostrof.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $bulan_penyelesaian = $this->input->post('bulan_penyelesaian');

        // Validasi minimal pilih 1 bulan
        if (empty($bulan_penyelesaian) || !is_array($bulan_penyelesaian) || count($bulan_penyelesaian) == 0) {
            $response = array(
                'success' => 2,
                'message' => 'Pilih minimal 1 bulan penyelesaian'
            );
            echo json_encode($response);
            return;
        }

        // Convert array bulan ke JSON
        $bulan_json = json_encode($bulan_penyelesaian);

        $data = [
            'periode_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('periode_id')))),
            'sasaran_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('sasaran_id')))),
            'indikator_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('indikator_id')))),
            'nama_indikator' => $this->security->xss_clean($this->input->post('nama_indikator')),
            'target_kuantitas' => $this->security->xss_clean($this->input->post('target_kuantitas')),
            'satuan' => $this->security->xss_clean($this->input->post('satuan')),
            'bulan_penyelesaian' => $bulan_json,
            'anggaran' => $this->security->xss_clean($this->input->post('anggaran'))
        ];

        $result = $this->model->proses_simpan_indikator_kinerja($data);
        if ($result['status']) {
            echo json_encode(['success' => 1, 'message' => $result['message']]);
        } else {
            echo json_encode(['success' => 3, 'message' => $result['message']]);
        }
    }

    public function hapus_indikator()
    {
        $id = $this->input->post('id');
        $get_indikator = $this->model->get_seleksi_array('pk_indikator_kinerja', ['id' => $id])->row();
        $get_sasaran = $this->model->get_seleksi_array('pk_sasaran_kinerja', ['id' => $get_indikator->sasaran_id])->row();

        $hapus = $this->model->pembaharuan_data('pk_indikator_kinerja', ['hapus' => '1', 'modified_on' => date('Y-m-d H:i:s'), 'modified_by' => $this->session->userdata('pegawai_id')], 'id', $id);
        if ($hapus == 1) {
            echo json_encode(
                array(
                    'st' => 1,
                    'periode_id' => base64_encode($this->encryption->encrypt($get_sasaran->periode_id))
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => 0
                )
            );
        }

        return;
    }

    public function show_daftar_pck()
    {
        if ($this->session->userdata('ketua')) {
            $tahun = $this->input->post('periode_id');
            $query = $this->model->get_penilaian_ketua($tahun)->result();

            $data = [];
            foreach ($query as $row) {
                $data[] = [
                    'bulan' => $row->bulan,
                    'tahun' => $row->tahun
                ];
            }
        } else {
            $periode_id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
            $query = $this->model->get_seleksi_array('ck_penilaian', ['periode_id' => $periode_id])->result();

            $data = [];
            foreach ($query as $row) {
                $data[] = [
                    'id' => base64_encode($this->encryption->encrypt($row->id)),
                    'bulan' => $row->bulan,
                    'nilai' => $row->nilai,
                    'status' => $row->status
                ];
            }
        }

        echo json_encode(['data_periode' => $data, 'ketua' => $this->session->userdata('ketua')]);
    }

    public function simpan_periode_pck()
    {
        $periode_id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
        $bulan = $this->input->post('bulan');
        $namaBulan = [
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

        if (!$periode_id || !$bulan) {
            $response = array(
                'success' => 3,
                'message' => 'Periode ID dan Bulan harus diisi'
            );
            echo json_encode($response);
            return;
        }

        $bulan = intval($bulan);
        if ($bulan < 1 || $bulan > 12) {
            $response = array(
                'success' => false,
                'message' => 'Bulan harus antara 1-12'
            );
            echo json_encode($response);
            return;
        }

        $periode = $this->model->get_seleksi_array('pk_periode', ['id' => $periode_id])->row();
        $bulan_awal = date('n', strtotime($periode->periode_awal));
        $bulan_akhir = date('n', strtotime($periode->periode_akhir));
        if ($bulan <= $bulan_awal && $bulan >= $bulan_akhir) {
            $response = array(
                'success' => false,
                'message' => 'Bulan penilaian tidak termasuk dalam rentang periode perjanjian kinerja yang ditetapkan'
            );
            echo json_encode($response);
            return;
        }

        $data_penilaian = $this->model->get_seleksi_array('ck_penilaian', ['periode_id' => $periode_id, 'bulan' => $bulan]);
        if ($data_penilaian->num_rows() > 0) {
            $response = array(
                'success' => 3,
                'message' => 'Periode penilaian capaian kinerja untuk bulan ' . $namaBulan[$bulan] . ' sudah ada'
            );
            echo json_encode($response);
            return;
        }

        $dataPCK = array(
            'periode_id' => $periode_id,
            'bulan' => $bulan,
            'jabatan' => $this->session->userdata('jabatan'),
            'pangkat' => $this->session->userdata('pangkat') . ' | ' . $this->session->userdata('golongan'),
            'created_by' => $this->session->userdata('pegawai_id'),
            'created_on' => date('Y-m-d H:i:s')
        );

        $result = $this->model->simpan_data('ck_penilaian', $dataPCK);
        if ($result) {
            echo json_encode(['success' => 1, 'message' => 'Periode PCK Berhasil Ditambahkan']);
        } else {
            echo json_encode(['success' => 3, 'message' => 'Periode PCK Gagal Ditambahkan. Ulangi atau Hubungi Admin']);
        }
    }

    public function show_tabel_detail_pck()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));
        $indikator = $this->model->get_seleksi_array('ck_capaian_indikator', ['penilaian_id' => $id])->result();
        $penilaian = $this->model->get_seleksi_array('ck_penilaian', ['id' => $id])->row();

        $data = [];
        foreach ($indikator as $row) {
            $nama_indikator = $this->model->get_seleksi_array('pk_indikator_kinerja', ['id' => $row->indikator_id])->row()->nama_indikator;
            $data[] = [
                'id_raw' => $row->id,
                'id' => base64_encode($this->encryption->encrypt($row->id)),
                'nama_indikator' => $nama_indikator,
                'capaian' => $row->capaian
            ];
        }

        $data_uraian = [];
        $data_uraian_tugas = $this->model->ambil_uraian_tugas($id);
        foreach ($data_uraian_tugas as $row_uraian) {
            $data_uraian[] = [
                'id' => $row_uraian->id,
                'capaian_id' => $row_uraian->capaian_id,
                'uraian_tugas' => $row_uraian->uraian_tugas,
                'target_kuantitas' => $row_uraian->target_kuantitas,
                'target_kualitas' => $row_uraian->target_kualitas,
                'satuan' => $row_uraian->satuan,
                'realisasi_kuantitas' => $row_uraian->realisasi_kuantitas,
                'realisasi_kualitas' => $row_uraian->realisasi_kualitas,
                'nilai' => $row_uraian->nilai,
                'tautan' => $row_uraian->tautan
            ];
        }

        echo json_encode([
            'data_pck' => $data,
            'penilaian_id' => $this->input->post('id'),
            'status' => $penilaian->status,
            'data_uraian_tugas' => $data_uraian
        ]);
    }

    public function simpan_pck_indikator()
    {
        $penilaian_id = $this->encryption->decrypt(base64_decode($this->input->post('penilaian_id')));
        $indikator_id = $this->input->post('indikator_id');

        $cek_indikator = $this->model->get_seleksi_array('ck_capaian_indikator', ['penilaian_id' => $penilaian_id, 'indikator_id' => $indikator_id]);
        if ($cek_indikator->num_rows() > 0) {
            $response = array(
                'success' => 3,
                'message' => 'Indikator ini sudah dipilih untuk periode penilaian ini'
            );
            echo json_encode($response);
            return;
        }

        $dataIndikator = array(
            'penilaian_id' => $penilaian_id,
            'indikator_id' => $indikator_id,
            'created_by' => $this->session->userdata('pegawai_id'),
            'created_on' => date('Y-m-d H:i:s')
        );

        $result = $this->model->simpan_data('ck_capaian_indikator', $dataIndikator);
        if ($result) {
            echo json_encode(['success' => 1, 'message' => 'Indikator Kinerja Berhasil Ditambahkan']);
        } else {
            echo json_encode(['success' => 3, 'message' => 'Indikator Kinerja Gagal Ditambahkan. Ulangi atau Hubungi Admin']);
        }
    }

    public function hapus_capaian_indikator()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));
        $get_penilaian = $this->model->get_seleksi_array('ck_capaian_indikator', ['id' => $id])->row();

        $hapus = $this->model->pembaharuan_data('ck_capaian_indikator', ['hapus' => '1', 'modified_on' => date('Y-m-d H:i:s'), 'modified_by' => $this->session->userdata('pegawai_id')], 'id', $id);
        if ($hapus == 1) {
            echo json_encode(
                array(
                    'st' => 1,
                    'penilaian_id' => base64_encode($this->encryption->encrypt($get_penilaian->penilaian_id))
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => 0
                )
            );
        }

        return;
    }

    public function ambil_sasaran_jabatan_periode_ini()
    {
        $periode_id = $this->encryption->decrypt(base64_decode($this->input->post('periode_id')));
        $jabatan_id = $this->session->userdata('jab_id');

        if (!$periode_id) {
            $response = array(
                'success' => 3,
                'message' => 'Periode ID tidak ditemukan'
            );
            echo json_encode($response);
            return;
        }

        // Get periode saat ini untuk mendapatkan tahun
        $periode_sekarang = $this->model->get_seleksi_array('pk_periode', ['id' => $periode_id]);

        if (!$periode_sekarang) {
            $response = array(
                'success' => 3,
                'message' => 'Periode tidak ditemukan'
            );
            echo json_encode($response);
            return;
        }

        $tahun_sekarang = $periode_sekarang->row()->tahun;

        // Copy Sasaran Kegiatan dan IKI dari periode tahun sebelumnya (semua proses database di model)
        $result = $this->model->salin_sasaran_jabatan_periode_ini($periode_id, $jabatan_id, $tahun_sekarang);

        if ($result && $result['success']) {
            $response = array(
                'success' => 1,
                'message' => $result['message'],
                'jumlah_sasaran' => $result['jumlah_sasaran'],
                'jumlah_indikator' => $result['jumlah_indikator']
            );
        } else {
            $response = array(
                'success' => 3,
                'message' => isset($result['message']) ? $result['message'] : 'Gagal menyalin Sasaran Kegiatan dari periode sebelumnya. Silakan coba lagi atau hubungi administrator.'
            );
        }

        echo json_encode($response);
    }

    public function show_uraian_tugas()
    {
        $uraianId = $this->input->post('uraian_id');
        $pckId = $this->encryption->decrypt(base64_decode($this->input->post('pck_id')));

        $uraian_tugas = "";
        $target_kuantitas = "";
        $satuan = "";
        $realisasi_kuantitas = "";
        $tautan = "";

        $get_capaian_indikator = $this->model->get_seleksi_array('ck_capaian_indikator', ['id' => $pckId])->row();
        $get_indikator = $this->model->get_seleksi_array('pk_indikator_kinerja', ['id' => $get_capaian_indikator->indikator_id])->row();

        if ($uraianId == '-1' || !$uraianId) {
            $judul = "Tambah Uraian Tugas";
        } else {
            $judul = "Edit Uraian Tugas";
            $queryUraianTugas = $this->model->get_seleksi_array('ck_uraian_tugas', ['id' => $uraianId]);
            $uraian_tugas = $queryUraianTugas->row()->uraian_tugas;
            $target_kuantitas = $queryUraianTugas->row()->target_kuantitas;
            $satuan = $queryUraianTugas->row()->satuan;
            $realisasi_kuantitas = $queryUraianTugas->row()->realisasi_kuantitas;
            $tautan = $queryUraianTugas->row()->tautan;
        }

        echo json_encode(
            array(
                'st' => 1,
                'uraian_id' => base64_encode($this->encryption->encrypt($uraianId)),
                'pck_id' => base64_encode($this->encryption->encrypt($pckId)),
                'judul' => $judul,
                'nama_indikator' => $get_indikator->nama_indikator,
                'uraian_tugas' => $uraian_tugas,
                'target_kuantitas' => $target_kuantitas,
                'satuan' => $satuan,
                'realisasi_kuantitas' => $realisasi_kuantitas,
                'tautan' => $tautan
            )
        );
        return;
    }

    public function simpan_uraian_tugas()
    {
        $this->form_validation->set_rules('uraian_id', 'Uraian ID', 'required');
        $this->form_validation->set_rules('pck_id', 'PCK ID', 'required');
        $this->form_validation->set_rules('uraian_tugas', 'Uraian Tugas', "required");
        $this->form_validation->set_rules('satuan', 'Satuan', "required");
        $this->form_validation->set_rules('target_kuantitas', 'Target Kuantitas', "required|numeric");
        $this->form_validation->set_rules('realisasi_kuantitas', 'Realisasi Kuantitas', "required|numeric");
        $this->form_validation->set_rules('tautan', 'Tautan Bukti Dukung', "required");

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong.',
            'numeric' => '%s hanya boleh berupa angka.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'uraian_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('uraian_id')))),
            'pck_id' => $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('pck_id')))),
            'uraian_tugas' => $this->security->xss_clean($this->input->post('uraian_tugas')),
            'target_kuantitas' => $this->security->xss_clean($this->input->post('target_kuantitas')),
            'satuan' => $this->security->xss_clean($this->input->post('satuan')),
            'realisasi_kuantitas' => $this->security->xss_clean($this->input->post('realisasi_kuantitas')),
            'tautan' => $this->security->xss_clean($this->input->post('tautan'))
        ];

        $penilaian_id = $this->model->get_seleksi_array('ck_capaian_indikator', [
            'id' => $this->encryption->decrypt(base64_decode($this->input->post('pck_id')))
        ])->row()->penilaian_id;

        $result = $this->model->proses_simpan_uraian_tugas($data);
        if ($result['status']) {
            $pck_data = $this->model->get_seleksi_array('v_uraian_tugas', [
                'capaian_id' => $this->encryption->decrypt(base64_decode($this->input->post('pck_id')))
            ])->row();

            $dataNotif = array(
                'jenis_pesan' => 'pck',
                'id_pemohon' => $this->session->userdata('pegawai_id'),
                'pesan' => 'Assalamualaikum Wr. Wb. Pegawai atas nama ' . $this->session->userdata('fullname') . ' telah mengisi uraian tugas, silakan melakukan penilaian melalui LITERASI MS BANDA ACEH, Terima Kasih',
                'id_tujuan' => $this->ambil_atasan_pegawai_id($pck_data->jabatan_penilai),
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->model->kirim_notif($dataNotif);
            echo json_encode([
                'success' => 1,
                'message' => $result['message'],
                'penilaian_id' => base64_encode($this->encryption->encrypt($penilaian_id))
            ]);
        } else {
            echo json_encode(['success' => 3, 'message' => $result['message']]);
        }
    }

    public function hapus_uraian_tugas()
    {
        $id = $this->input->post('id');

        $hapus = $this->model->pembaharuan_data('ck_uraian_tugas', ['hapus' => '1', 'modified_on' => date('Y-m-d H:i:s'), 'modified_by' => $this->session->userdata('pegawai_id')], 'id', $id);
        if ($hapus == 1) {
            echo json_encode(
                array(
                    'st' => 1
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => 0
                )
            );
        }

        return;
    }

    public function show_tabel_pck_staf()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));
        die(var_dump($id));
    }

    public function show_penilaian_uraian()
    {
        $uraian_id = $this->input->post('uraian_id');
        $get_data_uraian = $this->model->get_seleksi_array('ck_uraian_tugas', ['id' => $uraian_id]);
        if ($get_data_uraian->num_rows() > 0) {
            $uraian_tugas = $get_data_uraian->row()->uraian_tugas;
            $target = $get_data_uraian->row()->target_kuantitas;
            $realisasi = $get_data_uraian->row()->realisasi_kuantitas;
            $kualitas = $get_data_uraian->row()->realisasi_kualitas;
            $tautan = $get_data_uraian->row()->tautan;

            echo json_encode([
                'st' => 1,
                'judul' => 'Penilaian Uraian Tugas',
                'uraian_id' => $uraian_id,
                'uraian_tugas' => $uraian_tugas,
                'target' => $target,
                'realisasi' => $realisasi,
                'kualitas' => $kualitas,
                'tautan' => $tautan
            ]);
        } else {
            echo json_encode([
                'st' => 0
            ]);
        }
    }

    public function simpan_nilai_uraian_tugas()
    {
        $this->form_validation->set_rules('uraian_id', 'Uraian ID', 'required');
        $this->form_validation->set_rules('penilaian_id', 'Penilaian ID', 'required');
        $this->form_validation->set_rules('realisasi_kualitas', 'Mutu Realisasi', "required|numeric");

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong.',
            'numeric' => '%s hanya boleh berupa angka.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $data = [
            'uraian_id' => $this->security->xss_clean($this->input->post('uraian_id')),
            'realisasi_kualitas' => $this->security->xss_clean($this->input->post('realisasi_kualitas')),
        ];

        $result = $this->model->proses_simpan_nilai_uraian_tugas($data);
        if ($result['status']) {
            $penilaian_id_raw = $this->encryption->decrypt(base64_decode($this->input->post('penilaian_id')));
            $stat = $this->model->ambil_stat_uraian_tugas_penilaian([$penilaian_id_raw]);
            $stat_row = isset($stat[$penilaian_id_raw]) ? $stat[$penilaian_id_raw] : ['sudah_dinilai' => 0, 'belum_dinilai' => 0];

            $uraian_data = $this->model->get_seleksi_array('ck_uraian_tugas', ['id' => $this->input->post('uraian_id')])->row();
            $dataNotif = array(
                'jenis_pesan' => 'pck',
                'id_pemohon' => $this->session->userdata('pegawai_id'),
                'pesan' => 'Assalamualaikum Wr. Wb. Tugas anda telah diberi nilai oleh atasan, Terima Kasih',
                'id_tujuan' => $uraian_data->created_by,
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->model->kirim_notif($dataNotif);
            echo json_encode([
                'success' => 1,
                'message' => $result['message'],
                'uraian_sudah_dinilai' => (int) $stat_row['sudah_dinilai'],
                'uraian_belum_dinilai' => (int) $stat_row['belum_dinilai'],
            ]);
        } else {
            echo json_encode(['success' => 3, 'message' => $result['message']]);
        }
    }

    public function hapus_nilai_uraian_tugas()
    {
        $uraian_id = $this->security->xss_clean($this->input->post('uraian_id'));

        $hapus = $this->model->proses_hapus_nilai_uraian_tugas($uraian_id);
        if ($hapus == 1) {
            $penilaian_id_raw = $this->encryption->decrypt(base64_decode($this->input->post('penilaian_id')));
            $stat = $this->model->ambil_stat_uraian_tugas_penilaian([$penilaian_id_raw]);
            $stat_row = isset($stat[$penilaian_id_raw]) ? $stat[$penilaian_id_raw] : ['sudah_dinilai' => 0, 'belum_dinilai' => 0];

            $uraian_data = $this->model->get_seleksi_array('ck_uraian_tugas', ['id' => $this->input->post('uraian_id')])->row();
            $dataNotif = array(
                'jenis_pesan' => 'pck',
                'id_pemohon' => $this->session->userdata('pegawai_id'),
                'pesan' => 'Assalamualaikum Wr. Wb. Tugas anda telah diberi nilai oleh atasan, Terima Kasih',
                'id_tujuan' => $uraian_data->created_by,
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $this->model->kirim_notif($dataNotif);
            echo json_encode([
                'st' => 1,
                'uraian_sudah_dinilai' => (int) $stat_row['sudah_dinilai'],
                'uraian_belum_dinilai' => (int) $stat_row['belum_dinilai'],
            ]);
        } else {
            echo json_encode([
                'st' => 0
            ]);
        }
    }

    public function get_data_penilaian_pck()
    {
        $penilaian_id = $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('penilaian_id'))));
        $get_data_penilaian = $this->model->proses_pengambilan_data_penilaian($penilaian_id);
        if ($get_data_penilaian['status']) {
            echo json_encode([
                'success' => 1,
                'nilai' => $get_data_penilaian['nilai']
            ]);
        } else {
            echo json_encode([
                'success' => 0,
                'pesan' => $get_data_penilaian['pesan']
            ]);
        }
    }

    public function posting_penilaian_pck()
    {
        $penilaian_id = $this->security->xss_clean($this->encryption->decrypt(base64_decode($this->input->post('penilaian_id'))));
        $dataPenilaian = [
            'nilai' => $this->security->xss_clean($this->input->post('nilai')),
            'status' => 1,
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        ];

        $query = $this->model->pembaharuan_data('ck_penilaian', $dataPenilaian, 'id', $penilaian_id);
        if ($query) {
            echo json_encode([
                'success' => 1,
                'message' => 'Berhasil Posting Nilai PCK, PCK Anda Sudah Valid'
            ]);
        } else {
            echo json_encode([
                'success' => 3,
                'message' => 'Gagal Posting Nilai PCK, Ulangi atau Hubungi Admin'
            ]);
        }
    }

    public function monitoring_penilaian_filters()
    {
        if (!in_array($this->session->userdata('peran'), ['admin', 'operator'])) {
            show_404();
        }

        $nama = $this->model->monitoring_nama_options();
        $tahun = $this->model->monitoring_tahun_options();
        $bulan = $this->model->monitoring_bulan_options();

        header('Content-Type: application/json');
        echo json_encode([
            'nama' => $nama,
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);
    }

    public function monitoring_penilaian_dt()
    {
        if (!in_array($this->session->userdata('peran'), ['admin', 'operator'])) {
            show_404();
        }

        $draw = (int) $this->input->post('draw');
        $start = (int) $this->input->post('start');
        $length = (int) $this->input->post('length');

        $searchValue = $this->input->post('search');
        $searchValue = is_array($searchValue) ? ($searchValue['value'] ?? '') : '';

        $order = $this->input->post('order');
        $orderColumnIdx = 0;
        $orderDir = 'desc';
        if (is_array($order) && count($order) > 0) {
            $orderColumnIdx = (int) ($order[0]['column'] ?? 0);
            $orderDir = strtolower($order[0]['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        }

        $filters = [
            'nama' => $this->security->xss_clean($this->input->post('filterNama')),
            'tahun' => $this->security->xss_clean($this->input->post('filterTahun')),
            'bulan' => $this->security->xss_clean($this->input->post('filterBulan')),
        ];

        $params = [
            'start' => max(0, $start),
            'length' => ($length > 0 ? $length : 10),
            'search' => $this->security->xss_clean($searchValue),
            'order_column' => $orderColumnIdx,
            'order_dir' => $orderDir,
            'filters' => $filters,
        ];

        $result = $this->model->monitoring_penilaian_datatable($params);

        header('Content-Type: application/json');
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $result['data']
        ]);
    }

    private function ambil_atasan_pegawai_id($jabatan_id)
    {
        $params = [
            'tabel' => 'v_plh',
            'kolom_seleksi' => 'plh_id_jabatan',
            'seleksi' => $jabatan_id
        ];

        $result = $this->apihelper->get('apiclient/get_data_seleksi', $params);

        if ($result['status_code'] === 200 && $result['response']['status'] === 'success') {
            $user_data = $result['response']['data'][0];
            if ($user_data['pegawai_id']) {
                return $user_data['pegawai_id'];
            } else {
                $params = [
                    'tabel' => 'v_users',
                    'kolom_seleksi' => 'jab_id',
                    'seleksi' => $jabatan_id,
                    'kolom_seleksi2' => 'status_pegawai',
                    'seleksi2' => 1
                ];

                $result = $this->apihelper->get('apiclient/get_data_seleksi2', $params);

                if ($result['status_code'] === 200 && $result['response']['status'] === 'success') {
                    $user_data = $result['response']['data'][0];
                    return $user_data['pegawai_id'];
                }
            }
        }
    }
}