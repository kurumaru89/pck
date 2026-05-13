<?php

class HalamanMagang extends MY_Controller
{
    public function show_tabel_peserta_magang()
    {
        $query = $this->model->get_seleksi_array('register_peserta_magang', '', ['status' => 'DESC'])->result();

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id' => base64_encode($this->encryption->encrypt($row->id)),
                'nim' => $row->nim,
                'nama' => $row->nama,
                'nama_pt' => $row->nama_pt,
                'program_studi' => $row->program_studi,
                'status' => $row->status
            ];
        }

        echo json_encode(['data_peserta' => $data]);
    }

    public function show_peserta()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));

        $nim = "";
        $nama = "";
        $nama_pt = "";
        $program_studi = "";
        $hp = '';
        $status = array('2' => "Pilih Status Peserta", '1' => 'Aktif', '0' => 'Tidak Aktif');

        if ($id == '-1') {
            $judul = "TAMBAH DATA PESERTA MAGANG";
            $aktif = form_dropdown('status', $status, '', 'class="form-select bg-light" id="status"');
        } else {
            $judul = "DETAIL AGENDA RAPAT";
            $queryPeserta = $this->model->get_seleksi_array('register_peserta_magang', ['id' => $id]);
            $nim = $queryPeserta->row()->nim;
            $nama = $queryPeserta->row()->nama;
            $nama_pt = $queryPeserta->row()->nama_pt;
            $program_studi = $queryPeserta->row()->program_studi;
            $stat = $queryPeserta->row()->status;
            $hp = $queryPeserta->row()->hp;
            $aktif = form_dropdown('status', $status, $stat, 'class="form-select bg-light" id="status"');
        }

        echo json_encode(
            array(
                'st' => 1,
                'id' => $id,
                'judul' => $judul,
                'nama' => $nama,
                'nim' => $nim,
                'nama_pt' => $nama_pt,
                'program_studi' => $program_studi,
                'hp' => $hp,
                'status' => $aktif
            )
        );
        return;
    }

    public function simpan_peserta()
    {
        $this->form_validation->set_rules('nim', 'Nomor Induk Mahasiswa', 'required|numeric');
        $this->form_validation->set_rules('hp', 'Nomor Handphone', 'required|numeric');
        $this->form_validation->set_rules('nama', 'Nama Peserta', "required|regex_match[/^[a-zA-Z\s\'\-\.\,]+$/]");
        $this->form_validation->set_rules('nama_pt', 'Nama Perguruan Tinggi', "required|regex_match[/^[a-zA-Z\s\'\-\.\,]+$/]");
        $this->form_validation->set_rules('prodi', 'Nama Program Studi', "required|regex_match[/^[a-zA-Z\s\'\-\.\,]+$/]");

        $this->form_validation->set_message([
            'required' => '%s Tidak Boleh Kosong',
            'numeric' => '%s Hanya Boleh Angka',
            'regex_match' => '%s hanya boleh berisi huruf, angka, spasi, titik, koma, tanda hubung, dan apostrof.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => 2, 'message' => validation_errors()]);
            return;
        }

        $status = $this->security->xss_clean($this->input->post('status'));
        if ($status == '2') {
            echo json_encode(['success' => 2, 'message' => 'Status Peserta Belum Dipilih']);
            return;
        }

        $data = [
            'id' => $this->security->xss_clean($this->input->post('id')),
            'nim' => $this->security->xss_clean($this->input->post('nim')),
            'nama' => $this->security->xss_clean($this->input->post('nama')),
            'nama_pt' => $this->security->xss_clean($this->input->post('nama_pt')),
            'prodi' => $this->security->xss_clean($this->input->post('prodi')),
            'hp' => $this->security->xss_clean($this->input->post('hp')),
            'status' => $status
        ];

        $result = $this->model->proses_simpan_peserta_magang($data);
        if ($result['status']) {
            echo json_encode(['success' => 1, 'message' => $result['message']]);
        } else {
            echo json_encode(['success' => 3, 'message' => $result['message']]);
        }

    }
    
    public function reset_perangkat()
    {
        $id = $this->encryption->decrypt(base64_decode($this->input->post('id')));

        $query = $this->model->get_seleksi_array('register_peserta_magang', ['id' => $id]);
        if ($query->num_rows() > 0) {
            $data = [
                'token' => null,
                'modified_on' => date('Y-m-d H:i:s'),
                'modified_by' => $this->session->userdata('fullname')
            ];

            $reset = $this->model->pembaharuan_data('register_peserta_magang', $data, 'id', $id);
            if ($reset == '1') {
                $st = 1;
            } else {
                $st = 2;
            }
        } else {
            $st = 2;
        }
        echo json_encode(
            array(
                'st' => $st,
            )
        );
        return;
    }
}