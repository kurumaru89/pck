/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - pck
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pck` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

/*Table structure for table `ck_capaian_indikator` */

CREATE TABLE `ck_capaian_indikator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penilaian_id` int(11) NOT NULL,
  `indikator_id` int(11) NOT NULL,
  `capaian` decimal(5,2) NOT NULL DEFAULT 0.00,
  `hapus` tinyint(1) DEFAULT 0,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fk_penilaian` (`penilaian_id`) USING BTREE,
  CONSTRAINT `ck_capaian_indikator_ibfk_1` FOREIGN KEY (`penilaian_id`) REFERENCES `ck_penilaian` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

/*Table structure for table `ck_penilaian` */

CREATE TABLE `ck_penilaian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periode_id` int(11) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `nilai` decimal(5,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 0,
  `jabatan` varchar(255) DEFAULT NULL,
  `pangkat` varchar(255) DEFAULT NULL,
  `hapus` tinyint(1) DEFAULT 0,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fk_periode` (`periode_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

/*Table structure for table `ck_uraian_tugas` */

CREATE TABLE `ck_uraian_tugas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `capaian_id` int(11) NOT NULL COMMENT 'ID PCK',
  `uraian_tugas` varchar(255) NOT NULL,
  `target_kuantitas` int(11) NOT NULL,
  `target_kualitas` int(11) NOT NULL DEFAULT 100,
  `satuan` varchar(50) NOT NULL COMMENT '1=kegiatan, 2=dokumen',
  `realisasi_kuantitas` int(11) DEFAULT NULL,
  `realisasi_kualitas` int(11) DEFAULT NULL,
  `nilai` decimal(5,2) DEFAULT 0.00,
  `tautan` text DEFAULT NULL,
  `hapus` tinyint(1) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_pck_id` (`capaian_id`) USING BTREE,
  KEY `idx_created_by` (`created_by`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='Bukti kerja untuk RHK';

/*Table structure for table `peran` */

CREATE TABLE `peran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `role` enum('operator') NOT NULL,
  `hapus` enum('0','1') NOT NULL DEFAULT '0',
  `created_by` text NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` text DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Table structure for table `pk_indikator_kinerja` */

CREATE TABLE `pk_indikator_kinerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sasaran_id` int(11) NOT NULL COMMENT 'ID pegawai pemilik RHK',
  `nama_indikator` varchar(255) NOT NULL COMMENT 'Nama Rencana Hasil Kerja',
  `target_mutu` int(11) NOT NULL DEFAULT 100,
  `target_kuantitas` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `bulan_penyelesaian` text DEFAULT NULL,
  `anggaran` bigint(50) DEFAULT NULL,
  `hapus` tinyint(1) DEFAULT 0 COMMENT 'Soft delete',
  `created_by` int(11) NOT NULL COMMENT 'User yang membuat RHK',
  `created_on` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_created_at` (`created_on`) USING BTREE,
  KEY `idx_rhk_id` (`sasaran_id`) USING BTREE,
  KEY `fk_iki_pegawai` (`created_by`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='Indikator Kinerja Individu, Detail dari Rencana Hasil Kerja (RHK)';

/*Table structure for table `pk_periode` */

CREATE TABLE `pk_periode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(255) NOT NULL COMMENT 'Nama periode penilaian',
  `nip` varchar(20) NOT NULL,
  `nama_pegawai` varchar(255) NOT NULL COMMENT 'Nama Pegawai Saat Membuat Periode',
  `id_jabatan_pegawai` int(11) DEFAULT NULL COMMENT 'Id Jabatan Pegawai',
  `jabatan_pegawai` varchar(255) NOT NULL COMMENT 'Jabatan Pegawai Saat Periode Ini',
  `tahun` year(4) NOT NULL COMMENT 'Tahun penilaian',
  `periode_awal` date NOT NULL COMMENT 'Periode Awal Penilaian',
  `periode_akhir` date NOT NULL COMMENT 'Periode Akhir Penilaian',
  `jabatan_id_penilai` int(11) NOT NULL COMMENT 'ID Jabatan Penilai',
  `validator_nama` varchar(255) DEFAULT NULL COMMENT 'ID Pegawai Validator',
  `validator_nip` varchar(20) DEFAULT NULL COMMENT 'Jabatan Pegawai Validator',
  `status` tinyint(1) DEFAULT 0 COMMENT '0=Draft, 1=Pengajuan, 2=Valid',
  `hapus` tinyint(1) DEFAULT 0 COMMENT 'Soft delete',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_created_at` (`created_on`) USING BTREE,
  KEY `idx_tahun_bulan` (`tahun`) USING BTREE,
  KEY `fk_periode_pegawai` (`created_by`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='Periode penilaian kinerja';

/*Table structure for table `pk_sasaran_kinerja` */

CREATE TABLE `pk_sasaran_kinerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periode_id` int(11) DEFAULT NULL COMMENT 'ID periode penilaian (untuk filtering)',
  `jabatan_id` int(11) DEFAULT NULL COMMENT 'ID jabatan pemilik RHK',
  `nama_sasaran` varchar(255) DEFAULT NULL COMMENT 'Nama Perjanjian Kinerja',
  `hapus` tinyint(1) DEFAULT 0 COMMENT 'Soft delete',
  `created_by` int(11) NOT NULL COMMENT 'User yang membuat RHK',
  `created_on` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_periode_id` (`periode_id`) USING BTREE,
  KEY `idx_created_at` (`created_on`) USING BTREE,
  KEY `idx_jabatan_id` (`jabatan_id`) USING BTREE,
  KEY `fk_rhk_pegawai` (`created_by`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT COMMENT='Rencana Hasil Kerja, Tabel Detail dari Periode';

/*Table structure for table `v_capaian_indikator` */

DROP TABLE IF EXISTS `v_capaian_indikator`;

/*!50001 CREATE TABLE  `v_capaian_indikator`(
 `id` int(11) ,
 `penilaian_id` int(11) ,
 `indikator_id` int(11) ,
 `nama_indikator` varchar(255) ,
 `capaian` decimal(5,2) ,
 `hapus` tinyint(1) ,
 `created_on` datetime ,
 `created_by` int(11) ,
 `modified_on` datetime ,
 `modified_by` int(11) 
)*/;

/*Table structure for table `v_sasaran_kinerja` */

DROP TABLE IF EXISTS `v_sasaran_kinerja`;

/*!50001 CREATE TABLE  `v_sasaran_kinerja`(
 `id` int(11) ,
 `periode_id` int(11) ,
 `tahun` year(4) ,
 `jabatan_id` int(11) ,
 `nama_sasaran` varchar(255) ,
 `hapus` tinyint(1) ,
 `created_by` int(11) ,
 `created_on` datetime ,
 `modified_by` int(11) ,
 `modified_on` datetime 
)*/;

/*Table structure for table `v_uraian_tugas` */

DROP TABLE IF EXISTS `v_uraian_tugas`;

/*!50001 CREATE TABLE  `v_uraian_tugas`(
 `penilaian_id` int(11) ,
 `bulan` tinyint(2) ,
 `tahun` year(4) ,
 `indikator_id` int(11) ,
 `nama_indikator` varchar(255) ,
 `periode_id` int(11) ,
 `jabatan_penilai` int(11) ,
 `nama_pegawai` varchar(255) ,
 `jabatan_pegawai` varchar(255) ,
 `id` int(11) ,
 `capaian_id` int(11) ,
 `uraian_tugas` varchar(255) ,
 `target_kuantitas` int(11) ,
 `target_kualitas` int(11) ,
 `satuan` varchar(50) ,
 `realisasi_kuantitas` int(11) ,
 `realisasi_kualitas` int(11) ,
 `nilai` decimal(5,2) ,
 `tautan` text ,
 `hapus` tinyint(1) ,
 `created_by` int(11) ,
 `created_on` datetime ,
 `modified_by` int(11) ,
 `modified_on` datetime 
)*/;

/*View structure for view v_capaian_indikator */

/*!50001 DROP TABLE IF EXISTS `v_capaian_indikator` */;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_capaian_indikator` AS (select `c`.`id` AS `id`,`c`.`penilaian_id` AS `penilaian_id`,`c`.`indikator_id` AS `indikator_id`,`i`.`nama_indikator` AS `nama_indikator`,`c`.`capaian` AS `capaian`,`c`.`hapus` AS `hapus`,`c`.`created_on` AS `created_on`,`c`.`created_by` AS `created_by`,`c`.`modified_on` AS `modified_on`,`c`.`modified_by` AS `modified_by` from (`ck_capaian_indikator` `c` left join `pk_indikator_kinerja` `i` on(`c`.`indikator_id` = `i`.`id`))) */;

/*View structure for view v_sasaran_kinerja */

/*!50001 DROP TABLE IF EXISTS `v_sasaran_kinerja` */;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sasaran_kinerja` AS (select `s`.`id` AS `id`,`s`.`periode_id` AS `periode_id`,`p`.`tahun` AS `tahun`,`s`.`jabatan_id` AS `jabatan_id`,`s`.`nama_sasaran` AS `nama_sasaran`,`s`.`hapus` AS `hapus`,`s`.`created_by` AS `created_by`,`s`.`created_on` AS `created_on`,`s`.`modified_by` AS `modified_by`,`s`.`modified_on` AS `modified_on` from (`pk_sasaran_kinerja` `s` left join `pk_periode` `p` on(`p`.`id` = `s`.`periode_id`))) */;

/*View structure for view v_uraian_tugas */

/*!50001 DROP TABLE IF EXISTS `v_uraian_tugas` */;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_uraian_tugas` AS (select `c`.`penilaian_id` AS `penilaian_id`,`p`.`bulan` AS `bulan`,`pe`.`tahun` AS `tahun`,`c`.`indikator_id` AS `indikator_id`,`i`.`nama_indikator` AS `nama_indikator`,`p`.`periode_id` AS `periode_id`,`pe`.`jabatan_id_penilai` AS `jabatan_penilai`,`pe`.`nama_pegawai` AS `nama_pegawai`,`pe`.`jabatan_pegawai` AS `jabatan_pegawai`,`u`.`id` AS `id`,`u`.`capaian_id` AS `capaian_id`,`u`.`uraian_tugas` AS `uraian_tugas`,`u`.`target_kuantitas` AS `target_kuantitas`,`u`.`target_kualitas` AS `target_kualitas`,`u`.`satuan` AS `satuan`,`u`.`realisasi_kuantitas` AS `realisasi_kuantitas`,`u`.`realisasi_kualitas` AS `realisasi_kualitas`,`u`.`nilai` AS `nilai`,`u`.`tautan` AS `tautan`,`u`.`hapus` AS `hapus`,`u`.`created_by` AS `created_by`,`u`.`created_on` AS `created_on`,`u`.`modified_by` AS `modified_by`,`u`.`modified_on` AS `modified_on` from ((((`ck_uraian_tugas` `u` left join `ck_capaian_indikator` `c` on(`u`.`capaian_id` = `c`.`id`)) left join `pk_indikator_kinerja` `i` on(`c`.`indikator_id` = `i`.`id`)) left join `ck_penilaian` `p` on(`c`.`penilaian_id` = `p`.`id`)) left join `pk_periode` `pe` on(`p`.`periode_id` = `pe`.`id`))) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
