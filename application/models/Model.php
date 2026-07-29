<?php

class Model extends CI_Model
{
    private $db_sso;

    public function __construct()
    {
        parent::__construct();

        // Inisialisasi variabel private dengan nilai dari session
        $this->db_sso = $this->session->userdata('sso_db');
    }

    private function add_audittrail($action, $title, $table, $descrip)
    {

        $params = [
            'tabel' => 'sys_audittrail',
            'data' => [
                'datetime' => date("Y-m-d H:i:s"),
                'ipaddress' => $this->input->ip_address(),
                'action' => $action,
                'title' => $title,
                'tablename' => $table,
                'description' => $descrip,
                'username' => $this->session->userdata('username')
            ]
        ];

        $this->apihelper->post('apiclient/simpan_data', $params);
    }

    public function cek_aplikasi($id)
    {
        $params = [
            'tabel' => 'ref_client_app',
            'kolom_seleksi' => 'id',
            'seleksi' => $id
        ];

        $result = $this->apihelper->get('apiclient/get_data_seleksi', $params);

        if ($result['status_code'] === 200 && $result['response']['status'] === 'success') {
            $user_data = $result['response']['data'][0];
            $this->session->set_userdata(
                [
                    'nama_client_app' => $user_data['nama_app'],
                    'deskripsi_client_app' => $user_data['deskripsi']
                ]
            );
        }
    }

    public function kirim_notif($data)
    {
        $params = [
            'tabel' => 'sys_notif',
            'data' => $data
        ];

        $this->apihelper->post('apiclient/simpan_data', $params);
    }

    public function get_data_peran()
    {
        $this->db->select('l.id AS id, u.userid AS userid, u.fullname AS nama, l.role AS peran, l.hapus AS hapus');
        $this->db->from('peran l');
        $this->db->join($this->db_sso . '.v_users u', 'l.userid = u.userid', 'left');
        $this->db->order_by('l.id', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_seleksi($tabel, $kolom_seleksi, $seleksi)
    {
        try {
            $this->db->where($kolom_seleksi, $seleksi);
            return $this->db->get($tabel);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function get_seleksi_array($tabel, $where = [], $order_by = [])
    {
        try {
            $this->db->where('hapus', '0');

            // multiple where
            if (!empty($where)) {
                foreach ($where as $kolom => $nilai) {
                    $this->db->where($kolom, $nilai);
                }
            }

            // multiple order by
            if (!empty($order_by)) {
                foreach ($order_by as $kolom => $arah) {
                    $this->db->order_by($kolom, $arah); // ASC / DESC
                }
            }

            return $this->db->get($tabel);
        } catch (Exception $e) {
            return 0;
        }
    }

    public function get_presensi_harian($tanggal)
    {
        $this->db->select('p.id AS id, p.nama AS nama, pr.id AS presensi_id, pr.masuk AS masuk, pr.pulang AS pulang, p.status AS status, pr.ket AS keterangan');
        $this->db->from('register_peserta_magang p');
        $this->db->join(
            'register_presensi_magang pr',
            "p.id = pr.peserta_id AND DATE(pr.tgl) = " . $this->db->escape($tanggal),
            'left'
        );
        $this->db->order_by('status', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function simpan_data($tabel, $data)
    {
        try {
            $this->db->insert($tabel, $data);
            $title = "Simpan Data <br />Update tabel <b>" . $tabel . "</b>[]";
            $descrip = null;
            $this->add_audittrail("INSERT", $title, $tabel, $descrip);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function pembaharuan_data($tabel, $data, $kolom_seleksi, $seleksi)
    {
        try {
            $this->db->where($kolom_seleksi, $seleksi);
            $this->db->update($tabel, $data);
            $title = "Pembaharuan Data <br />Update tabel <b>" . $tabel . "</b>[Pada kolom<b>" . $kolom_seleksi . "</b>]";
            $descrip = null;
            $this->add_audittrail("UPDATE", $title, $tabel, $descrip);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function get_periode_ketua()
    {
        $this->db->select('*');
        $this->db->from('pk_periode');
        $this->db->where('jabatan_id_penilai', '1');
        $this->db->where('hapus', 0);
        $this->db->group_by('tahun');

        return $this->db->get();
    }

    public function get_penilaian_ketua($tahun)
    {
        $this->db->select('pe.tahun AS tahun, p.bulan AS bulan');
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'left');
        $this->db->where('pe.jabatan_id_penilai', '1');
        $this->db->where('pe.tahun', $tahun);
        $this->db->where('p.hapus', 0);
        $this->db->order_by('p.bulan', 'ASC');
        $this->db->group_by('p.bulan');

        return $this->db->get();
    }

    public function proses_simpan_periode_pk($data)
    {
        $dataPeserta = [
            'nama_periode' => $data['nama_periode'],
            'tahun' => $data['tahun'],
            'periode_awal' => $data['periode_awal'],
            'periode_akhir' => $data['periode_akhir']
        ];

        if (!$this->session->userdata('id_jabatan_atasan')) {
            return [
                'status' => false,
                'message' => 'Atasan belum dipilih, silakan pilih atasan melalui menu Pengaturan di SSO'
            ];
        }

        if (!$data['id'] || $data['id'] == '-1') {
            $cek_range_tahun = $this->check_tahun_exists_in_range(
                $this->session->userdata('pegawai_id'),
                $data['tahun'],
                $data['periode_awal'],
                $data['periode_akhir'],
                $data['id']
            );

            if ($cek_range_tahun) {
                return [
                    'status' => false,
                    'message' => 'Periode penilaian dengan tahun ' . $data['tahun'] . ' yang berada dalam rentang tanggal tersebut sudah ada. Silakan pilih tahun atau rentang periode lain, atau edit periode yang sudah ada.'
                ];
            }

            $dataPeserta['nama_pegawai'] = $this->session->userdata('fullname');
            $dataPeserta['id_jabatan_pegawai'] = $this->session->userdata('jab_id');
            $dataPeserta['jabatan_pegawai'] = $this->session->userdata('jabatan');
            $dataPeserta['jabatan_id_penilai'] = $this->session->userdata('id_jabatan_atasan');
            $dataPeserta['nip'] = $this->session->userdata('nip');
            $dataPeserta['created_on'] = date('Y-m-d H:i:s');
            $dataPeserta['created_by'] = $this->session->userdata('pegawai_id');
            $query = $this->simpan_data('pk_periode', $dataPeserta);
        } else {
            $dataPeserta['modified_on'] = date('Y-m-d H:i:s');
            $dataPeserta['modified_by'] = $this->session->userdata('pegawai_id');
            $query = $this->pembaharuan_data('pk_periode', $dataPeserta, 'id', $data['id']);
        }

        if ($query == 1) {
            if ($data['id'] == '-1')
                return ['status' => true, 'message' => 'Periode Perjanjian Kinerja Berhasil Disimpan'];
            else
                return ['status' => true, 'message' => 'Periode Perjanjian Kinerja Berhasil Diperbarui'];
        } else
            return ['status' => false, 'message' => 'Gagal Simpan Periode Perjanjian Kinerja, Silakan Ulangi Lagi'];
    }

    public function proses_simpan_sasaran_kegiatan($data)
    {
        $dataSasaran = [
            'nama_sasaran' => $data['nama_sasaran']
        ];

        if (!$data['sasaran_id'] || $data['sasaran_id'] == '-1') {
            $dataSasaran['jabatan_id'] = $this->session->userdata('jab_id');
            $dataSasaran['periode_id'] = $data['periode_id'];
            $dataSasaran['created_on'] = date('Y-m-d H:i:s');
            $dataSasaran['created_by'] = $this->session->userdata('pegawai_id');
            $query = $this->simpan_data('pk_sasaran_kinerja', $dataSasaran);
        } else {
            $dataSasaran['modified_on'] = date('Y-m-d H:i:s');
            $dataSasaran['modified_by'] = $this->session->userdata('pegawai_id');
            $query = $this->pembaharuan_data('pk_sasaran_kinerja', $dataSasaran, 'id', $data['sasaran_id']);
        }

        if ($query == 1) {
            if (!$data['sasaran_id'] || $data['sasaran_id'] == '-1')
                return ['status' => true, 'message' => 'Sasaran Kinerja Berhasil Disimpan'];
            else
                return ['status' => true, 'message' => 'Sasaran Kinerja Berhasil Diperbarui'];
        } else
            return ['status' => false, 'message' => 'Gagal Simpan Sasaran Kinerja, Silakan Ulangi Lagi'];
    }

    public function proses_simpan_indikator_kinerja($data)
    {
        $dataIndikator = [
            'sasaran_id' => $data['sasaran_id'],
            'nama_indikator' => $data['nama_indikator'],
            'target_kuantitas' => $data['target_kuantitas'],
            'satuan' => $data['satuan'],
            'bulan_penyelesaian' => $data['bulan_penyelesaian'],
            'anggaran' => str_replace('.', '', $data['anggaran'])
        ];

        if (!$data['indikator_id'] || $data['indikator_id'] == '-1') {
            $dataIndikator['created_on'] = date('Y-m-d H:i:s');
            $dataIndikator['created_by'] = $this->session->userdata('pegawai_id');
            $query = $this->simpan_data('pk_indikator_kinerja', $dataIndikator);
        } else {
            $dataIndikator['modified_on'] = date('Y-m-d H:i:s');
            $dataIndikator['modified_by'] = $this->session->userdata('pegawai_id');
            $query = $this->pembaharuan_data('pk_indikator_kinerja', $dataIndikator, 'id', $data['indikator_id']);
        }

        if ($query == 1) {
            if (!$data['indikator_id'] || $data['indikator_id'] == '-1')
                return ['status' => true, 'message' => 'Indikator Kinerja Berhasil Disimpan'];
            else
                return ['status' => true, 'message' => 'Indikator Kinerja Berhasil Diperbarui'];
        } else
            return ['status' => false, 'message' => 'Gagal Simpan Indikator Kinerja, Silakan Ulangi Lagi'];
    }

    public function proses_simpan_uraian_tugas($data)
    {
        $this->db->trans_start();
        $dataUraian = [
            'capaian_id' => $data['pck_id'],
            'uraian_tugas' => $data['uraian_tugas'],
            'target_kuantitas' => $data['target_kuantitas'],
            'satuan' => $data['satuan'],
            'realisasi_kuantitas' => $data['realisasi_kuantitas'],
            'realisasi_kualitas' => $data['realisasi_kualitas'],
            'tautan' => $data['tautan']
        ];

        $mutu = $data['realisasi_kualitas'];
        $target = 100;
        $nilai = ((int) $target + (int) $mutu) / 2;
        $dataUraian['nilai'] = $nilai;

        if (!$data['uraian_id'] || $data['uraian_id'] == '-1') {
            $dataUraian['created_on'] = date('Y-m-d H:i:s');
            $dataUraian['created_by'] = $this->session->userdata('pegawai_id');
            $query = $this->simpan_data('ck_uraian_tugas', $dataUraian);
        } else {
            $dataUraian['modified_on'] = date('Y-m-d H:i:s');
            $dataUraian['modified_by'] = $this->session->userdata('pegawai_id');
            $query = $this->pembaharuan_data('ck_uraian_tugas', $dataUraian, 'id', $data['uraian_id']);
        }

        $queryCapaian = $this->update_rata_rata_pck($data['pck_id']);

        $this->db->trans_complete();

        if ($query == 1 && $queryCapaian == 1) {
            if (!$data['uraian_id'] || $data['uraian_id'] == '-1') {
                return ['status' => true, 'message' => 'Uraian Tugas Berhasil Disimpan'];
            } else
                return ['status' => true, 'message' => 'Uraian Tugas Berhasil Diperbarui'];
        } else
            return ['status' => false, 'message' => 'Gagal Simpan Uraian Tugas, Silakan Ulangi Lagi'];
    }

    public function proses_simpan_nilai_uraian_tugas($data)
    {
        $uraian_id = $data['uraian_id'];
        $mutu = $data['realisasi_kualitas'];
        $uraian_data = $this->get_seleksi_array('ck_uraian_tugas', ['id' => $uraian_id])->row();

        $nilai = ((int) $uraian_data->target_kualitas + (int) $mutu) / 2;
        $this->db->trans_start();
        $dataNilai = [
            'realisasi_kualitas' => $mutu,
            'nilai' => $nilai,
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        ];

        $query = $this->pembaharuan_data('ck_uraian_tugas', $dataNilai, 'id', $uraian_id);

        $capaian_id = $uraian_data->capaian_id;

        $queryCapaian = $this->update_rata_rata_pck($capaian_id);
        $queryPenilaian = $this->update_penilaian($capaian_id);
        $this->db->trans_complete();

        if ($query == 1) {
            if ($queryCapaian == 1 && $queryPenilaian == 1)
                return ['status' => true, 'message' => 'Uraian Tugas Berhasil Disimpan, Capaian Indikator Berhasil Diperbarui'];
            else
                return ['status' => true, 'message' => 'Uraian Tugas Berhasil Disimpan, Capaian Indikator Gagal Diperbarui'];
        } else
            return ['status' => false, 'message' => 'Gagal Simpan Uraian Tugas, Silakan Ulangi Lagi'];
    }

    public function update_rata_rata_pck($capaian_id)
    {
        // Hitung rata-rata nilai capaian
        $this->db->select_avg('nilai', 'rata_rata');
        $this->db->where('capaian_id', $capaian_id);
        $this->db->where('hapus', 0);
        $query = $this->db->get('ck_uraian_tugas')->row();

        $rata_rata = round($query->rata_rata, 2);

        return $this->pembaharuan_data('ck_capaian_indikator', ['capaian' => $rata_rata], 'id', $capaian_id);
    }

    public function update_penilaian($capaian_id)
    {
        $this->db->trans_start();
        $this->db->select('penilaian_id');
        $this->db->where('id', $capaian_id);
        $this->db->where('hapus', 0);
        $penilaian_id = $this->db->get('ck_capaian_indikator')->row()->penilaian_id;

        $this->db->select_avg('capaian', 'penilaian');
        $this->db->where('penilaian_id', $penilaian_id);
        $this->db->where('hapus', 0);
        $query_update = $this->db->get('ck_capaian_indikator')->row();

        $rata_rata = round($query_update->penilaian, 2);

        $query_nilai = $this->pembaharuan_data('ck_penilaian', ['nilai' => $rata_rata], 'id', $penilaian_id);

        $this->db->trans_complete();
        return $query_nilai;
    }

    public function proses_hapus_nilai_uraian_tugas($uraian_id)
    {
        $uraian_data = $this->get_seleksi_array('ck_uraian_tugas', ['id' => $uraian_id])->row();
        $capaian_id = $uraian_data->capaian_id;

        $this->db->trans_start();
        $dataNilai = [
            'realisasi_kualitas' => null,
            'nilai' => '0.00',
            'modified_by' => $this->session->userdata('pegawai_id'),
            'modified_on' => date('Y-m-d H:i:s')
        ];

        $query = $this->pembaharuan_data('ck_uraian_tugas', $dataNilai, 'id', $uraian_id);
        $queryCapaian = $this->update_rata_rata_pck($capaian_id);

        $this->db->trans_complete();

        if ($query == 1 && $queryCapaian == 1)
            return 1;
        else
            return 0;
    }

    public function proses_pengambilan_data_penilaian($penilaian_id)
    {
        $this->db->select('*');
        $this->db->from('v_uraian_tugas');
        $this->db->where('uraian_hapus', 0);
        $this->db->where('capaian_indikator_hapus', 0);
        $this->db->where('periode_hapus', 0);
        $this->db->where('penilaian_id', $penilaian_id);
        $this->db->where('realisasi_kualitas IS NULL');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return [
                'status' => 0,
                'pesan' => 'Ada uraian tugas yang belum dinilai, harap diperiksa kembali'
            ];
        } else {
            $this->db->select_avg('capaian', 'nilai_pck');
            $this->db->where('penilaian_id', $penilaian_id);
            $this->db->where('hapus', 0);
            $query = $this->db->get('ck_capaian_indikator')->row();

            $nilai_pck = round($query->nilai_pck, 2);
            return [
                'status' => 1,
                'nilai' => $nilai_pck
            ];
        }
    }

    public function proses_simpan_edit_presensi($data)
    {

        if ($data['presensi_id'] <> '-1') {
            $dataPresensi = array(
                'tgl' => $data['tanggal'],
                'peserta_id' => $data['peserta_id'],
                'masuk' => $data['jam_datang'],
                'pulang' => $data['jam_pulang'],
                'ket' => $data['ket'],
                'modified_by' => $this->session->userdata('fullname'),
                'modified_on' => date('Y-m-d H:i:s')
            );

            $querySimpan = $this->pembaharuan_data('register_presensi_magang', $dataPresensi, 'id', $data['presensi_id']);

        } else {
            $dataPresensi = array(
                'tgl' => $data['tanggal'],
                'peserta_id' => $data['peserta_id'],
                'masuk' => $data['jam_datang'],
                'pulang' => $data['jam_pulang'],
                'ket' => $data['ket'],
                'created_by' => $this->session->userdata('fullname'),
                'created_on' => date('Y-m-d H:i:s')
            );

            $querySimpan = $this->simpan_data('register_presensi_magang', $dataPresensi);
        }

        if ($querySimpan == 1) {
            if ($data['presensi_id'] == '-1')
                return ['status' => true, 'message' => 'Presensi Peserta Magang Berhasil Disimpan'];
            else
                return ['status' => true, 'message' => 'Presensi Peserta Magang Berhasil Diperbarui'];
        } else
            return ['status' => false, 'message' => 'Gagal Simpan Presensi Peserta, Silakan Ulangi Lagi'];
    }

    public function ambil_indikator($periode_id)
    {
        $this->db->select('i.*, s.nama_sasaran, p.nama_periode, p.tahun');
        $this->db->from('pk_indikator_kinerja AS i');
        $this->db->join('pk_sasaran_kinerja AS s', 's.id = i.sasaran_id', 'inner');
        $this->db->join('pk_periode AS p', 'p.id = s.periode_id', 'inner');
        $this->db->where('p.id', $periode_id);
        $this->db->where('i.hapus', 0);
        $this->db->where('s.hapus', 0);
        $this->db->where('p.hapus', 0);
        $this->db->order_by('i.created_on', 'ASC');

        return $this->db->get()->result();
    }

    public function ambil_indikator_pck($periode_id)
    {
        $this->db->select('i.*, s.nama_sasaran, p.nama_periode, p.tahun');
        $this->db->from('pk_indikator_kinerja AS i');
        $this->db->join('pk_sasaran_kinerja AS s', 's.id = i.sasaran_id', 'inner');
        $this->db->join('pk_periode AS p', 'p.id = s.periode_id', 'inner');
        $this->db->where('p.id', $periode_id);
        $this->db->where('i.hapus', 0);
        $this->db->where('s.hapus', 0);
        $this->db->where('p.hapus', 0);
        $this->db->order_by('i.sasaran_id', 'ASC');

        return $this->db->get()->result();
    }

    public function ambil_uraian_tugas($penilaian_id)
    {
        $this->db->select('u.*');
        $this->db->from('ck_uraian_tugas u');
        $this->db->join('ck_capaian_indikator c', 'u.capaian_id = c.id', 'left');
        $this->db->join('ck_penilaian p', 'c.penilaian_id = p.id', 'left');
        $this->db->where('c.penilaian_id', $penilaian_id);
        $this->db->where('u.hapus', 0);
        $this->db->where('c.hapus', 0);
        $this->db->where('p.hapus', 0);
        $this->db->order_by('c.indikator_id', 'ASC');

        return $this->db->get()->result();
    }

    public function salin_sasaran_jabatan_periode_ini($periode_id, $jabatan_id, $tahun_sekarang)
    {
        // Mulai transaction
        $this->db->trans_start();

        try {
            // 1. Ambil semua Sasaran Kegiatan jabatan dari periode tahun ini
            $this->db->select('s.id, s.periode_id, p.tahun, s.jabatan_id, s.nama_sasaran');
            $this->db->from('pk_sasaran_kinerja s');
            $this->db->join('pk_periode p', 's.periode_id = p.id', 'left');
            $this->db->where('s.jabatan_id', $jabatan_id);
            $this->db->where('p.tahun', $tahun_sekarang);
            $this->db->where('s.hapus', '0');
            $this->db->where('p.hapus', '0');
            $sasaran_jabatan = $this->db->get()->result();

            // Mapping untuk menyimpan relasi rhk_id lama -> rhk_id baru
            $sasaran_id_mapping = array();
            $jumlah_sasaran = 0;
            $jumlah_indikator = 0;

            // 2. Copy setiap Sasaran Kegiatan ke periode saat ini
            foreach ($sasaran_jabatan as $sasaran) {
                $data_sasaran_baru = array(
                    'periode_id' => $periode_id,
                    'jabatan_id' => isset($sasaran->jabatan_id) ? $sasaran->jabatan_id : null,
                    'nama_sasaran' => $sasaran->nama_sasaran,
                    'created_by' => $this->session->userdata('pegawai_id'),
                    'created_on' => date('Y-m-d H:i:s')
                );

                $this->db->insert('pk_sasaran_kinerja', $data_sasaran_baru);
                $sasaran_id_baru = $this->db->insert_id();

                if (!$sasaran_id_baru) {
                    throw new Exception('Gagal menyalin Sasaran Kegiatan: ' . $sasaran->nama_sasaran);
                }

                // Simpan mapping rhk_id lama -> rhk_id baru
                $sasaran_id_mapping[$sasaran->id] = $sasaran_id_baru;
                $jumlah_sasaran++;
            }

            // 3. Copy semua IKI dari periode tahun sebelumnya berdasarkan Sasaran Kegiatan yang sudah di-copy
            if (!empty($sasaran_id_mapping)) {
                // Ambil semua IKI dari periode tahun sebelumnya
                $this->db->select('*');
                $this->db->from('pk_indikator_kinerja');
                $this->db->where('sasaran_id IN (' . implode(',', array_keys($sasaran_id_mapping)) . ')');
                $this->db->where('hapus', '0');
                $indikator_lama = $this->db->get()->result();

                // Copy setiap IKI ke periode saat ini dengan rhk_id yang baru
                foreach ($indikator_lama as $indikator) {
                    // Cek apakah rhk_id lama ada di mapping (artinya Sasaran Kegiatan-nya sudah di-copy)
                    if (isset($sasaran_id_mapping[$indikator->sasaran_id])) {
                        $data_indikator_baru = array(
                            'sasaran_id' => $sasaran_id_mapping[$indikator->sasaran_id], // Gunakan rhk_id baru
                            'nama_indikator' => $indikator->nama_indikator,
                            'target_kuantitas' => $indikator->target_kuantitas,
                            'satuan' => $indikator->satuan,
                            'bulan_penyelesaian' => $indikator->bulan_penyelesaian,
                            'anggaran' => $indikator->anggaran,
                            'created_by' => $this->session->userdata('pegawai_id'),
                            'created_on' => date('Y-m-d H:i:s')
                        );

                        $this->db->insert('pk_indikator_kinerja', $data_indikator_baru);
                        $indikator_id_baru = $this->db->insert_id();

                        if (!$indikator_id_baru) {
                            throw new Exception('Gagal menyalin Indikator Kinerja: ' . $indikator->nama_indikator);
                        }

                        $jumlah_indikator++;
                    }
                }
            }

            // Selesaikan transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // Return array dengan informasi lengkap
            return array(
                'success' => 1,
                'jumlah_sasaran' => $jumlah_sasaran,
                'jumlah_indikator' => $jumlah_indikator,
                'message' => 'Berhasil menyalin ' . $jumlah_sasaran . ' Sasaran Kegiatan Jabatan dan ' . $jumlah_indikator . ' Indikator Kinerja dari periode tahun ini'
            );

        } catch (Exception $e) {
            // Rollback transaction jika ada error
            $this->db->trans_rollback();
            return array(
                'success' => 3,
                'message' => 'Gagal menyalin Sasaran Kegiatan Jabatan: ' . $e->getMessage()
            );
        }
    }

    public function salin_pk_periode_sebelumnya($data)
    {
        $tahun = $data->row()->tahun;
        $pegawai_id = $this->session->userdata('pegawai_id');
        $created_by = $data->row()->created_by;

        $periode_sebelumnya = $this->get_periode_tahun_sebelumnya($pegawai_id, $tahun);

        if (!$periode_sebelumnya) {
            return array(
                'success' => 3,
                'message' => 'Tidak ditemukan Perjanjian Kinerja periode sebelumnya.'
            );
        }

        $this->db->trans_start();
        try {
            // 1. Ambil semua Sasaran Kegiatan dari periode tahun sebelumnya
            $this->db->select('*');
            $this->db->from('pk_sasaran_kinerja');
            $this->db->where('periode_id', $periode_sebelumnya->id);
            $this->db->where('hapus', 0);
            $sasaran_lama = $this->db->get()->result();

            // Mapping untuk menyimpan relasi sasaran_id lama -> sasaran_id baru
            $sasaran_id_mapping = array();
            $jumlah_sasaran = 0;
            $jumlah_indikator = 0;

            // Jika created_by tidak diberikan, gunakan pegawai_id sebagai fallback
            if (!$created_by) {
                $created_by = $pegawai_id;
            }

            // 2. Copy setiap Sasaran Kegiatan ke periode saat ini
            foreach ($sasaran_lama as $sasaran) {
                $data_sasaran_baru = array(
                    'periode_id' => $data->row()->id,
                    'jabatan_id' => isset($sasaran->jabatan_id) ? $sasaran->jabatan_id : null,
                    'nama_sasaran' => $sasaran->nama_sasaran,
                    'created_by' => $created_by,
                    'created_on' => date('Y-m-d H:i:s')
                );

                $this->db->insert('pk_sasaran_kinerja', $data_sasaran_baru);
                $sasaran_id_baru = $this->db->insert_id();

                if (!$sasaran_id_baru) {
                    throw new Exception('Gagal menyalin Sasaran Kegiatan: ' . $sasaran->nama_sasaran);
                }

                // Simpan mapping rhk_id lama -> rhk_id baru
                $sasaran_id_mapping[$sasaran->id] = $sasaran_id_baru;
                $jumlah_sasaran++;
            }

            // 3. Copy semua Indikator dari periode tahun sebelumnya berdasarkan Sasaran Kegiatan yang sudah di-copy
            if (!empty($sasaran_id_mapping)) {
                // Ambil semua IKI dari periode tahun sebelumnya
                $this->db->select('*');
                $this->db->from('pk_indikator_kinerja');
                $this->db->where('sasaran_id IN (' . implode(',', array_keys($sasaran_id_mapping)) . ')');
                $this->db->where('hapus', 0);
                $indikator_lama = $this->db->get()->result();

                // Copy setiap indikator ke periode saat ini dengan sasaran_id yang baru
                foreach ($indikator_lama as $indikator) {
                    // Cek apakah sasaran_id lama ada di mapping (artinya Sasaran Kegiatan-nya sudah di-copy)
                    if (isset($sasaran_id_mapping[$indikator->sasaran_id])) {
                        $data_indikator_baru = array(
                            'sasaran_id' => $sasaran_id_mapping[$indikator->sasaran_id], // Gunakan sasaran_id baru
                            'nama_indikator' => $indikator->nama_indikator,
                            'target_mutu' => $indikator->target_mutu,
                            'target_kuantitas' => $indikator->target_kuantitas,
                            'satuan' => $indikator->satuan,
                            'bulan_penyelesaian' => $indikator->bulan_penyelesaian,
                            'anggaran' => $indikator->anggaran,
                            'created_by' => $created_by,
                            'created_on' => date('Y-m-d H:i:s')
                        );

                        $this->db->insert('pk_indikator_kinerja', $data_indikator_baru);
                        $indikator_id_baru = $this->db->insert_id();

                        if (!$indikator_id_baru) {
                            throw new Exception('Gagal menyalin IKI: ' . $indikator->nama_indikator);
                        }

                        $jumlah_indikator++;
                    }
                }
            }

            // Selesaikan transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            // Return array dengan informasi lengkap
            return array(
                'success' => true,
                'jumlah_sasaran' => $jumlah_sasaran,
                'jumlah_indikator' => $jumlah_indikator,
                'message' => 'Berhasil menyalin ' . $jumlah_sasaran . ' Sasaran Kegiatan dan ' . $jumlah_indikator . ' Indikator Kinerja dari periode tahun sebelumnya'
            );

        } catch (Exception $e) {
            // Rollback transaction jika ada error
            $this->db->trans_rollback();
            return array(
                'success' => false,
                'message' => 'Gagal menyalin Perjanjian Kinerja periode lalu: ' . $e->getMessage()
            );
        }
    }

    public function get_periode_tahun_sebelumnya($pegawai_id, $tahun_sekarang)
    {
        $tahun_sebelumnya = $tahun_sekarang - 1;

        $this->db->select('*');
        $this->db->from('pk_periode');
        $this->db->where('created_by', $pegawai_id);
        $this->db->where('tahun', $tahun_sebelumnya);
        $this->db->where('hapus', 0);
        $this->db->order_by('tahun', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function salin_pck_dari_bulan_sebelumnya($periode_id_sekarang, $penilaian_id_sekarang, $bulan_sekarang, $pegawai_id)
    {
        // Validasi ownership
        if (!$this->penilaian_belongs_to_pegawai($penilaian_id_sekarang, $pegawai_id)) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan aksi ini.'
            ];
        }

        $bulan_sekarang = (int) $bulan_sekarang;
        $prev_bulan = ($bulan_sekarang <= 1) ? 12 : ($bulan_sekarang - 1);
        $periode_id = (int) $periode_id_sekarang;

        $penilaian_prev_id = $this->get_penilaian_id_safe($periode_id, $prev_bulan);
        if (!$penilaian_prev_id) {
            return [
                'success' => false,
                'message' => 'Periode sebelumnya tidak ada / belum dibuat.'
            ];
        }

        // Ambil Indikator yang sudah dipilih (PCK) pada periode sebelumnya, beserta id PCK untuk copy uraian_tugas
        $this->db->select('pck.id AS pck_id, pck.indikator_id');
        $this->db->from('ck_capaian_indikator pck');
        $this->db->where('pck.penilaian_id', $penilaian_prev_id);
        $this->db->where('pck.hapus', 0, false);
        $prev_rows = $this->db->get()->result();

        if (!$prev_rows || count($prev_rows) === 0) {
            return [
                'success' => false,
                'message' => 'Tidak ada IKI pada periode sebelumnya / belum melakukan pengisian.'
            ];
        }

        // Indikator yang sudah ada pada periode sekarang
        $this->db->select('indikator_id');
        $this->db->from('ck_capaian_indikator');
        $this->db->where('penilaian_id', $penilaian_id_sekarang);
        $this->db->where('hapus', 0, false);
        $existing_rows = $this->db->get()->result();
        $existing = [];
        foreach ($existing_rows as $er) {
            $existing[(int) $er->indikator_id] = true;
        }

        $copied = 0;
        $skipped = 0;
        $uraian_copied = 0;

        $this->db->trans_start();
        foreach ($prev_rows as $r) {
            $indikator_id = (int) $r->indikator_id;
            $old_pck_id = (int) $r->pck_id;
            if (!$indikator_id) {
                continue;
            }
            if (isset($existing[$indikator_id])) {
                $skipped++;
                continue;
            }

            $ok = $this->db->insert('ck_capaian_indikator', [
                'indikator_id' => $indikator_id,
                'penilaian_id' => $penilaian_id_sekarang,
                'created_by' => $pegawai_id,
                'created_on' => date('Y-m-d H:i:s'),
            ]);

            if ($ok) {
                $new_pck_id = $this->db->insert_id();
                $copied++;

                // Copy uraian_tugas dari PCK periode sebelumnya (termasuk target, realisasi, nilai)
                $uraian_list = $this->get_seleksi_array('ck_uraian_tugas', ['capaian_id' => $old_pck_id])->result();
                if ($uraian_list) {
                    foreach ($uraian_list as $u) {
                        $uraian_data = [
                            'capaian_id' => $new_pck_id,
                            'uraian_tugas' => $u->uraian_tugas,
                            'satuan' => isset($u->satuan) ? $u->satuan : null,
                            'target_kuantitas' => isset($u->target_kuantitas) ? $u->target_kuantitas : null,
                            'target_kualitas' => isset($u->target_kualitas) ? $u->target_kualitas : null,
                            'realisasi_kuantitas' => isset($u->realisasi_kuantitas) ? $u->realisasi_kuantitas : null,
                            'realisasi_kualitas' => isset($u->realisasi_kualitas) ? $u->realisasi_kualitas : null,
                            'nilai' => isset($u->nilai) ? $u->nilai : null,
                            'created_by' => $pegawai_id,
                            'created_on' => date('Y-m-d H:i:s'),
                        ];

                        if ($this->create_uraian_tugas_pck($uraian_data)) {
                            $uraian_copied++;
                        }
                    }
                }
            }
        }
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            return [
                'success' => 3,
                'message' => 'Gagal mengambil IKI dari periode sebelumnya. Silakan coba lagi.'
            ];
        }

        if ($copied === 0) {
            return [
                'success' => 2,
                'message' => 'IKI periode sebelumnya sudah ada semua pada periode ini (tidak ada yang disalin).'
            ];
        }

        $msg = 'Berhasil mengambil ' . $copied . ' Indikator dari periode sebelumnya.';
        if ($uraian_copied > 0) {
            $msg .= ' Sebanyak ' . $uraian_copied . ' uraian tugas (termasuk target, realisasi, nilai) turut disalin.';
        }

        return [
            'success' => 1,
            'message' => $msg,
            'copied' => $copied,
            'uraian_copied' => $uraian_copied,
            'skipped' => $skipped,
            'prev_bulan' => $prev_bulan,
            'prev_periode_id' => $periode_id,
        ];
    }

    public function create_uraian_tugas_pck($data)
    {
        try {
            $this->db->trans_start();
            $capaian_id = $data['capaian_id'];

            $this->db->insert('ck_uraian_tugas', $data);
            $this->update_rata_rata_pck($capaian_id);
            $this->db->trans_complete();
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function ambil_daftar_pck_staf($data)
    {
        $this->db->select('p.*, pe.nama_pegawai, pe.tahun AS tahun, pe.jabatan_id_penilai, pe.hapus');
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('pe.tahun', $data['tahun']);
        $this->db->where('p.bulan', $data['bulan']);
        $this->db->where('pe.jabatan_id_penilai', $data['jabatan_penilai']);
        $this->db->where('pe.hapus', 0);
        $this->db->where('p.hapus', 0);

        return $this->db->get();
    }

    public function monitoring_nama_options()
    {
        $this->db->select('pe.nama_pegawai AS nama_pegawai');
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);
        $this->db->group_by('pe.nama_pegawai');
        $this->db->order_by('pe.nama_pegawai', 'asc');
        $rows = $this->db->get()->result();

        $out = [];
        foreach ($rows as $r) {
            if ($r->nama_pegawai !== null && $r->nama_pegawai !== '') {
                $out[] = $r->nama_pegawai;
            }
        }
        return $out;
    }

    public function monitoring_tahun_options()
    {
        $this->db->select('pe.tahun AS tahun');
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);
        $this->db->group_by('pe.tahun');
        $this->db->order_by('pe.tahun', 'desc');
        $rows = $this->db->get()->result();

        $out = [];
        foreach ($rows as $r) {
            if ($r->tahun !== null && $r->tahun !== '') {
                $out[] = (string) $r->tahun;
            }
        }
        return $out;
    }

    public function monitoring_bulan_options()
    {
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

        $this->db->select('p.bulan AS bulan');
        $this->db->from('ck_penilaian p');
        $this->db->where('p.hapus', 0);
        $this->db->group_by('p.bulan');
        $this->db->order_by('p.bulan', 'asc');
        $rows = $this->db->get()->result();

        $out = [];
        foreach ($rows as $r) {
            $b = (int) $r->bulan;
            if ($b >= 1 && $b <= 12) {
                $out[] = [
                    'id' => (string) $b,
                    'text' => $namaBulan[$b]
                ];
            }
        }
        return $out;
    }

    private function _monitoring_penilaian_base_query($filters, $search)
    {
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);

        if (!empty($filters['nama'])) {
            $this->db->where('pe.nama_pegawai', $filters['nama']);
        }
        if (!empty($filters['tahun'])) {
            $this->db->where('pe.tahun', $filters['tahun']);
        }
        if (!empty($filters['bulan'])) {
            $this->db->where('p.bulan', (int) $filters['bulan']);
        }

        if ($search !== null && $search !== '') {
            $this->db->group_start();
            $this->db->like('pe.nama_pegawai', $search);
            $this->db->or_like('pe.tahun', $search);
            $this->db->or_like('p.bulan', $search);
            $this->db->or_like('p.jabatan', $search);
            $this->db->group_end();
        }
    }

    public function monitoring_penilaian_datatable($params)
    {
        $filters = $params['filters'] ?? ['nama' => '', 'tahun' => '', 'bulan' => ''];
        $search = $params['search'] ?? '';

        $orderMap = [
            0 => 'p.id',
            1 => 'pe.nama_pegawai',
            2 => 'pe.tahun',
            3 => 'p.bulan',
            4 => 'p.status',
            5 => 'p.nilai',
            6 => 'p.created_on',
        ];

        // Total (tanpa search, tanpa paging, tetap hormati filter)
        $this->db->select('COUNT(1) AS cnt', false);
        $this->_monitoring_penilaian_base_query($filters, '');
        $recordsTotal = (int) ($this->db->get()->row()->cnt ?? 0);

        // Filtered (dengan search)
        $this->db->select('COUNT(1) AS cnt', false);
        $this->_monitoring_penilaian_base_query($filters, $search);
        $recordsFiltered = (int) ($this->db->get()->row()->cnt ?? 0);

        // Data
        $this->db->select('p.id, p.bulan, p.status, p.nilai, p.created_on, pe.tahun, pe.nama_pegawai');
        $this->_monitoring_penilaian_base_query($filters, $search);

        $orderIdx = (int) ($params['order_column'] ?? 6);
        $orderDir = ($params['order_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $orderBy = $orderMap[$orderIdx] ?? 'p.created_on';
        $this->db->order_by($orderBy, $orderDir);

        $start = (int) ($params['start'] ?? 0);
        $length = (int) ($params['length'] ?? 10);
        $this->db->limit($length, $start);
        $rows = $this->db->get()->result();

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

        $data = [];
        $no = $start + 1;
        foreach ($rows as $r) {
            $bulanNum = (int) $r->bulan;
            $bulanText = $namaBulan[$bulanNum] ?? (string) $r->bulan;

            if ((string) $r->status === '1')
                $statusBadge = '<span class="badge badge-warning">Proses Pengajuan</span>';
            else if ((string) $r->status === '2')
                $statusBadge = '<span class="badge badge-success">Disetujui</span>';
            else
                $statusBadge = '<span class="badge badge-danger">Draft</span>';

            if ((string) $r->status === '2')
                $nilaiText = ($r->nilai === null || $r->nilai === '') ? '-' : $r->nilai;
            else
                $nilaiText = '-';
            $createdOn = $r->created_on ? date('d/m/Y H:i', strtotime($r->created_on)) : '-';

            $aksi = '
                <button type="button" class="btn btn-sm btn-outline-info" onclick="previewPCK(' . (int) $r->id . ',' . (int) $r->status . ')">
                    <i class="zmdi zmdi-eye"></i> Preview
                </button>
            ';

            $data[] = [
                $no++,
                $r->nama_pegawai ?? '-',
                $r->tahun ?? '-',
                $bulanText,
                $statusBadge,
                $nilaiText,
                $createdOn,
                $aksi
            ];
        }

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];
    }

    public function dashboard_scope_periode_ids($ctx)
    {
        $tahun = (int) ($ctx['tahun'] ?? date('Y'));
        $ketua = !empty($ctx['ketua']);
        $pegawai_id = $ctx['pegawai_id'] ?? null;
        $jab_id = $ctx['jab_id'] ?? null;

        $this->db->select('id, nama_pegawai');
        $this->db->from('pk_periode');
        $this->db->where('hapus', 0);
        $this->db->where('tahun', $tahun);

        if (!$ketua) {
            $this->db->group_start();
            if ($pegawai_id !== null && $pegawai_id !== '') {
                $this->db->where('created_by', $pegawai_id);
            }
            if ($jab_id !== null && $jab_id !== '') {
                $this->db->or_where('jabatan_id_penilai', $jab_id);
            }
            $this->db->group_end();
        }

        $this->db->order_by('nama_pegawai', 'asc');
        $rows = $this->db->get()->result();

        $periode_ids = [];
        foreach ($rows as $r) {
            $periode_ids[] = (int) $r->id;
        }

        return [
            'tahun' => $tahun,
            'periode_ids' => array_values(array_unique($periode_ids)),
            'jumlah_periode' => count($periode_ids),
        ];
    }

    public function dashboard_list_perbandingan_bulan($params)
    {
        $tahun = (int) ($params['tahun'] ?? date('Y'));
        $bulan = (int) ($params['bulan'] ?? date('n'));
        $periode_ids = $params['periode_ids'] ?? [];

        if (!is_array($periode_ids) || count($periode_ids) === 0) {
            return [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'data' => []
            ];
        }

        $bulan_lalu = $bulan - 1;
        if ($bulan_lalu < 1) {
            // Sesuai kebutuhan: kalau tidak ada bulan sebelumnya (Januari), tidak lakukan perbandingan.
            $bulan_lalu = 0;
        }

        $this->db->select("
            pe.id AS periode_id,
            pe.nama_pegawai AS nama,
            p.id AS penilaian_id,
            p.status AS status,
            p.nilai AS nilai_bulan_ini,
            pl.nilai AS nilai_bulan_lalu
        ", false);
        $this->db->from('pk_periode pe');
        $this->db->join('ck_penilaian p', 'p.periode_id = pe.id AND p.bulan = ' . (int) $bulan . ' AND p.hapus = 0', 'inner');
        if ($bulan_lalu > 0) {
            $this->db->join('ck_penilaian pl', 'pl.periode_id = pe.id AND pl.bulan = ' . (int) $bulan_lalu . ' AND pl.hapus = 0', 'left');
        } else {
            $this->db->join('ck_penilaian pl', '1=0', 'left');
        }

        $this->db->where('pe.hapus', 0);
        $this->db->where('pe.tahun', $tahun);
        $this->db->where_in('pe.id', $periode_ids);
        $this->db->order_by('pe.nama_pegawai', 'asc');

        $rows = $this->db->get()->result();

        $out = [];
        foreach ($rows as $r) {
            $nilaiNow = $r->nilai_bulan_ini;
            $nilaiPrev = $r->nilai_bulan_lalu;

            $compare = null;
            if ($nilaiPrev !== null && $nilaiPrev !== '' && $nilaiNow !== null && $nilaiNow !== '') {
                $n = (float) $nilaiNow;
                $p = (float) $nilaiPrev;
                if ($n > $p) {
                    $compare = 'naik';
                } elseif ($n < $p) {
                    $compare = 'turun';
                } else {
                    $compare = 'tetap';
                }
            }

            $out[] = [
                'periode_id' => (int) $r->periode_id,
                'penilaian_id' => (int) $r->penilaian_id,
                'nama' => $r->nama ?? '-',
                'status' => (int) $r->status,
                'nilai_bulan_ini' => ($nilaiNow === null || $nilaiNow === '') ? null : (float) $nilaiNow,
                'nilai_bulan_lalu' => ($nilaiPrev === null || $nilaiPrev === '') ? null : (float) $nilaiPrev,
                'perbandingan' => $compare,
            ];
        }

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulan_lalu' => $bulan_lalu,
            'data' => $out
        ];
    }

    public function dashboard_chart_bulanan($params)
    {
        $tahun = (int) ($params['tahun'] ?? date('Y'));
        $periode_ids = $params['periode_ids'] ?? [];

        $bulan_nama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $labels = [];
        for ($b = 1; $b <= 12; $b++) {
            $labels[] = $bulan_nama[$b];
        }

        $seriesAvg = array_fill(0, 12, null);
        $seriesCount = array_fill(0, 12, 0);

        if (!is_array($periode_ids) || count($periode_ids) === 0) {
            return [
                'tahun' => $tahun,
                'labels' => $labels,
                'avg' => $seriesAvg,
                'count' => $seriesCount
            ];
        }

        $this->db->select('p.bulan AS bulan, AVG(p.nilai) AS avg_nilai, COUNT(1) AS jumlah', false);
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);
        $this->db->where('pe.tahun', $tahun);
        $this->db->where('p.status', 1);
        $this->db->where('p.nilai IS NOT NULL', null, false);
        $this->db->where_in('p.periode_id', $periode_ids);
        $this->db->group_by('p.bulan');
        $this->db->order_by('p.bulan', 'asc');
        $rows = $this->db->get()->result();

        foreach ($rows as $r) {
            $idx = ((int) $r->bulan) - 1;
            if ($idx >= 0 && $idx < 12) {
                $seriesAvg[$idx] = round((float) $r->avg_nilai, 2);
                $seriesCount[$idx] = (int) $r->jumlah;
            }
        }

        return [
            'tahun' => $tahun,
            'labels' => $labels,
            'avg' => $seriesAvg,
            'count' => $seriesCount
        ];
    }

    public function dashboard_chart_status_bulanan($params)
    {
        $tahun = (int) ($params['tahun'] ?? date('Y'));
        $periode_ids = $params['periode_ids'] ?? [];

        $bulan_nama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $labels = [];
        for ($b = 1; $b <= 12; $b++) {
            $labels[] = $bulan_nama[$b];
        }

        $posted = array_fill(0, 12, 0);
        $draft = array_fill(0, 12, 0);

        if (!is_array($periode_ids) || count($periode_ids) === 0) {
            return [
                'tahun' => $tahun,
                'labels' => $labels,
                'posted' => $posted,
                'draft' => $draft
            ];
        }

        $this->db->select('p.bulan AS bulan, p.status AS status, COUNT(1) AS jumlah', false);
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);
        $this->db->where('pe.tahun', $tahun);
        $this->db->where_in('p.periode_id', $periode_ids);
        $this->db->group_by(['p.bulan', 'p.status']);
        $this->db->order_by('p.bulan', 'asc');
        $rows = $this->db->get()->result();

        foreach ($rows as $r) {
            $idx = ((int) $r->bulan) - 1;
            if ($idx >= 0 && $idx < 12) {
                if ((int) $r->status === 1) {
                    $posted[$idx] = (int) $r->jumlah;
                } else {
                    $draft[$idx] = (int) $r->jumlah;
                }
            }
        }

        return [
            'tahun' => $tahun,
            'labels' => $labels,
            'posted' => $posted,
            'draft' => $draft
        ];
    }

    public function dashboard_top_pegawai_bulan_ini($params)
    {
        $tahun = (int) ($params['tahun'] ?? date('Y'));
        $bulan = (int) ($params['bulan'] ?? date('n'));
        $periode_ids = $params['periode_ids'] ?? [];
        $limit = (int) ($params['limit'] ?? 10);
        if ($limit <= 0 || $limit > 50)
            $limit = 10;

        if (!is_array($periode_ids) || count($periode_ids) === 0) {
            return [];
        }

        $this->db->select('pe.nama_pegawai AS nama, p.nilai AS nilai', false);
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);
        $this->db->where('pe.tahun', $tahun);
        $this->db->where('p.bulan', $bulan);
        $this->db->where('p.status', 1);
        $this->db->where('p.nilai IS NOT NULL', null, false);
        $this->db->where_in('p.periode_id', $periode_ids);
        $this->db->order_by('p.nilai', 'desc');
        $this->db->limit($limit);
        $rows = $this->db->get()->result();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'nama' => $r->nama ?? '-',
                'nilai' => (float) $r->nilai
            ];
        }
        return $out;
    }

    public function dashboard_distribusi_nilai_bulan_ini($params)
    {
        $tahun = (int) ($params['tahun'] ?? date('Y'));
        $bulan = (int) ($params['bulan'] ?? date('n'));
        $periode_ids = $params['periode_ids'] ?? [];

        $bins = [
            ['min' => 0, 'max' => 60, 'label' => '< 60'],
            ['min' => 60, 'max' => 76, 'label' => '60-75'],
            ['min' => 76, 'max' => 90, 'label' => '76-89'],
            ['min' => 90, 'max' => 101, 'label' => '>= 90'],
        ];
        $counts = array_fill(0, count($bins), 0);

        if (!is_array($periode_ids) || count($periode_ids) === 0) {
            return ['labels' => array_column($bins, 'label'), 'counts' => $counts];
        }

        $this->db->select('p.nilai AS nilai', false);
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'inner');
        $this->db->where('p.hapus', 0);
        $this->db->where('pe.hapus', 0);
        $this->db->where('pe.tahun', $tahun);
        $this->db->where('p.bulan', $bulan);
        $this->db->where('p.status', 1);
        $this->db->where('p.nilai IS NOT NULL', null, false);
        $this->db->where_in('p.periode_id', $periode_ids);
        $rows = $this->db->get()->result();

        foreach ($rows as $r) {
            $v = (float) $r->nilai;
            foreach ($bins as $i => $b) {
                if ($v >= $b['min'] && $v < $b['max']) {
                    $counts[$i]++;
                    break;
                }
            }
        }

        return [
            'labels' => array_column($bins, 'label'),
            'counts' => $counts
        ];
    }

    /**
     * Hitung jumlah uraian tugas per penilaian:
     * - sudah_dinilai: u.nilai IS NOT NULL
     * - belum_dinilai: u.nilai IS NULL
     */
    public function ambil_stat_uraian_tugas_penilaian($penilaian_ids)
    {
        if (!is_array($penilaian_ids) || count($penilaian_ids) === 0) {
            return [];
        }

        $ids = array_values(array_filter($penilaian_ids, function ($v) {
            return $v !== null && $v !== '';
        }));
        if (count($ids) === 0) {
            return [];
        }

        $this->db->select("
            c.penilaian_id AS penilaian_id,
            SUM(CASE WHEN u.realisasi_kualitas IS NULL THEN 1 ELSE 0 END) AS belum_dinilai,
            SUM(CASE WHEN u.realisasi_kualitas IS NOT NULL THEN 1 ELSE 0 END) AS sudah_dinilai
        ", false);
        $this->db->from('ck_uraian_tugas u');
        $this->db->join('ck_capaian_indikator c', 'u.capaian_id = c.id', 'inner');
        $this->db->join('ck_penilaian p', 'c.penilaian_id = p.id', 'inner');
        $this->db->where_in('c.penilaian_id', $ids);
        $this->db->where('u.hapus', 0);
        $this->db->where('c.hapus', 0);
        $this->db->where('p.hapus', 0);
        $this->db->group_by('c.penilaian_id');

        $rows = $this->db->get()->result();
        $out = [];
        foreach ($rows as $r) {
            $out[$r->penilaian_id] = [
                'sudah_dinilai' => (int) $r->sudah_dinilai,
                'belum_dinilai' => (int) $r->belum_dinilai,
            ];
        }
        return $out;
    }

    private function check_tahun_exists_in_range($pegawai_id, $tahun, $periode_awal, $periode_akhir, $exclude_id = null)
    {
        // Validasi dan konversi format tanggal
        $date_awal = date('Y-m-d', strtotime($periode_awal));
        $date_akhir = date('Y-m-d', strtotime($periode_akhir));

        // Pastikan periode_awal <= periode_akhir
        if (strtotime($date_awal) > strtotime($date_akhir)) {
            return false; // Invalid range
        }

        // Query untuk mengecek overlap
        // Overlap terjadi jika:
        // - periode_awal baru <= periode_akhir lama DAN
        // - periode_akhir baru >= periode_awal lama
        $this->db->from('pk_periode');
        $this->db->where('created_by', $pegawai_id);
        $this->db->where('tahun', $tahun);
        $this->db->where('hapus', 0);

        // Exclude current record if updating
        if ($exclude_id != NULL || $exclude_id != '-1') {
            $this->db->where('id !=', $exclude_id);
        }

        $this->db->where('periode_awal IS NOT NULL');
        $this->db->where('periode_akhir IS NOT NULL');
        $this->db->where('periode_awal <=', $date_akhir);
        $this->db->where('periode_akhir >=', $date_awal);

        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function get_bulan_dalam_periode_penilaian($periode_id, $bulan)
    {
        $this->db->select('*');
        $this->db->from('pk_periode');
        $this->db->where('id', $periode_id);
        $this->db->where('hapus', 0);
        $this->db->where('MONTH(periode_awal) <=', (int) $bulan);
        $this->db->where('MONTH(periode_akhir) >=', (int) $bulan);

        return $this->db->get()->row();
    }

    private function penilaian_belongs_to_pegawai($penilaian_id, $pegawai_id)
    {
        $this->db->select('p.id');
        $this->db->from('ck_penilaian p');
        $this->db->join('pk_periode pe', 'p.periode_id = pe.id', 'left');
        $this->db->where('p.id', $penilaian_id);
        $this->db->where('pe.created_by', $pegawai_id);
        $this->db->where('p.hapus', 0, false);
        $row = $this->db->get()->row();
        return (bool) $row;
    }

    private function get_penilaian_id_safe($periode_id, $bulan)
    {
        $this->db->select('id');
        $this->db->from('ck_penilaian');
        $this->db->where('periode_id', $periode_id);
        $this->db->where('bulan', $bulan);
        $this->db->where('hapus', 0, false);
        $row = $this->db->get()->row();
        return $row ? (int) $row->id : null;
    }
}