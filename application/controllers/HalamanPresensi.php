<?php

class HalamanPresensi extends CI_Controller
{
    public function presensi()
    {
        $this->load->view('presensi');
    }

    public function show_presensi()
    {
        $hari = $this->tanggalhelper->convertDayDate(date('Y-m-d', time()));
        $jam = date('H:i:s');

        $this->load->model('Model', 'model');

        $peserta = array();
        $peserta[''] = 'Pilih Peserta Magang';
        $get_peserta = $this->model->get_seleksi_array('register_peserta_magang', ['status' => '1'])->result_array();
        foreach ($get_peserta as $row) {
            $peserta[$row['id']] = $row['nama'];
        }

        $peserta_magang = form_dropdown('peserta', $peserta, '', 'id="peserta" class="form-control select2" onchange="bukaDetailPresensi()"');

        echo json_encode(
            array(
                'st' => 1,
                'hari' => $hari,
                'jam' => $this->tanggalhelper->konversiJam($jam),
                'peserta' => $peserta_magang
            )
        );
        return;
    }

    public function show_detail_presensi()
    {
        $this->load->model('Model', 'model');
        $id = $this->input->post('id');

        $get_token = $this->model->get_seleksi_array('register_peserta_magang', ['id' => $id])->row()->token;

        $cekPresensi = $this->model->get_seleksi_array('register_presensi_magang', ['peserta_id' => $id, 'tgl' => date('Y-m-d')]);

        if ($cekPresensi->num_rows() == 0) {
            echo json_encode(
                array(
                    'st' => 1,
                    'token' => $get_token,
                    'jam_masuk' => '',
                    'jam_pulang' => ''
                )
            );
        } else {
            echo json_encode(
                array(
                    'st' => 1,
                    'token' => $get_token,
                    'jam_masuk' => $this->tanggalhelper->konversiJam($cekPresensi->row()->masuk),
                    'jam_pulang' => $this->tanggalhelper->konversiJam($cekPresensi->row()->pulang)
                )
            );
        }
    }

    public function simpan_presensi()
    {
        $this->form_validation->set_rules('peserta', 'Id Peserta', 'required|max_length[8]');
        $this->form_validation->set_rules('jam', 'Jam Presensi', 'required|max_length[9]');
        $this->form_validation->set_rules('foto', 'Foto Diri', 'required');

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong',
            'max_length' => '%s Maksimal %s karakter',
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $this->load->model('Model', 'model');
        $peserta = $this->security->xss_clean($this->input->post('peserta'));
        $jam = $this->security->xss_clean($this->input->post('jam'));
        $foto = $this->input->post('foto');

        $data = base64_decode($foto);
        // bikin nama file unik
        $filename = 'foto_' . time() . '.jpg';
        $path = FCPATH . 'uploads/foto/' . $peserta . '/' . $filename;
        $dbpath = 'uploads/foto/' . $peserta . '/' . $filename;
        // pastikan folder ada
        if (!is_dir(FCPATH . 'uploads/foto/' . $peserta . '/')) {
            mkdir(FCPATH . 'uploads/foto/' . $peserta . '/', 0777, true);
        }

        // simpan file ke folder
        file_put_contents($path, $data);

        $jamSekarang = strtotime($jam);
        $jamTengahHari = strtotime('12:00:00');
        if ($this->session->userdata('fullname')) {
            $fullname = $this->session->userdata('fullname');
        } else {
            $cekPeserta = $this->model->get_seleksi_array('register_peserta_magang', ['id' => $peserta])->row();
            $fullname = $cekPeserta->nama;
        }

        $cekPresensi = $this->model->get_seleksi_array('register_presensi_magang', ['peserta_id' => $peserta, 'tgl' => date('Y-m-d')]);

        if ($cekPresensi->num_rows() > 0) {
            if ($jamSekarang <= $jamTengahHari) { // Before 12:00 PM
                $querySimpan = 2;
            } else { // After 12:00 PM, check out
                $id = $cekPresensi->row()->id;
                $dataPengguna = array(
                    'pulang' => $jam,
                    'foto_pulang' => $dbpath,
                    'modified_by' => $fullname,
                    'modified_on' => date('Y-m-d H:i:s')
                );
                $querySimpan = $this->model->pembaharuan_data('register_presensi_magang', $dataPengguna, 'id', $id);
            }
        } else {
            if ($jamSekarang <= $jamTengahHari) { // Before 12:00 PM
                $dataPengguna = array(
                    'peserta_id' => $peserta,
                    'masuk' => $jam,
                    'foto_masuk' => $dbpath,
                    'tgl' => date('Y-m-d'),
                    'created_on' => date('Y-m-d H:i:s'),
                    'created_by' => $fullname
                );
            } else { // After 12:00 PM
                $dataPengguna = array(
                    'peserta_id' => $peserta,
                    'pulang' => $jam,
                    'foto_pulang' => $dbpath,
                    'tgl' => date('Y-m-d'),
                    'created_on' => date('Y-m-d H:i:s'),
                    'created_by' => $fullname
                );
            }
            $querySimpan = $this->model->simpan_data('register_presensi_magang', $dataPengguna);
        }

        if ($querySimpan == 1) {
            echo json_encode(['success' => 1, 'message' => 'Presensi Berhasil Di Simpan']);
        } elseif ($querySimpan == 2) {
            echo json_encode(['success' => 2, 'message' => 'Anda Sudah Presensi Kedatangan Hari ini']);
        } else {
            echo json_encode(['success' => 2, 'message' => 'Presensi Gagal Simpan, Silakan Ulangi Lagi']);
        }
    }

    public function get_lokasi()
    {
        $result = $this->apihelper->get('apiclient/get_lokasi');
        if ($result['status_code'] === 200 && $result['response']['status'] === 'success') {
            $lokasi = $result['response']['data'][0];
            if ($lokasi) {
                $lokasi['koordinat'] = json_decode($lokasi['polygon_json']); // decode dulu biar rapi
            }
        }

        echo json_encode($lokasi);
    }

    public function simpan_perangkat()
    {
        $this->load->model('Model', 'model');
        $id = $this->input->post('id');
        $token = md5(uniqid());
        $data = [
            'token' => $token
        ];

        $result = $this->model->pembaharuan_data('register_peserta_magang', $data, 'id', $id);

        if ($result == 1) {
            $cookie_domain = $this->config->item('cookie_domain');
            setcookie(
                'presensi_token',
                $token,
                [
                    'expires' => time() + (86500 * 30 * 12),
                    'path' => '/',
                    'domain' => $cookie_domain, // pastikan subdomain
                    'secure' => $this->config->item('cookie_secure'),
                    'httponly' => true,
                    'samesite' => 'Lax', // atau 'Strict'
                ]
            );

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
}