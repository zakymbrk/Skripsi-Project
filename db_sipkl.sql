-- ============================================================
-- DATABASE: db_sipkl
-- Sistem Informasi Praktek Kerja Lapangan (SiPKL)
-- SMK ITIKURIH HIBARNA Ciparay
-- Versi: 3.0 (Final - Single File)
-- Update : 2026-08-14
-- ============================================================
-- Mencakup:
--   - Struktur semua tabel (lengkap)
--   - Data awal / sample data
--   - Foreign key constraints
--   - Index performa
--   - Tabel monitoring: tb_presensi, tb_logbook, tb_penilaian
--   - Kolom geolocation pada tb_dudi
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================
-- Buat & pilih database
-- ============================================================
CREATE DATABASE IF NOT EXISTS `db_sipkl` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_sipkl`;

-- ============================================================
-- 1. tb_group  (Level: 1=Hubin, 2=Pembimbing, 3=Siswa)
-- ============================================================
CREATE TABLE `tb_group` (
  `group_id`   int(11)      NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_group` (`group_id`, `group_name`, `created_at`, `updated_at`) VALUES
(1, 'Hubin',       '2024-01-01 00:00:00', '2024-01-01 00:00:00'),
(2, 'Pembimbing',  '2024-01-01 00:00:00', '2024-01-01 00:00:00'),
(3, 'Siswa',       '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 2. tb_user
-- ============================================================
CREATE TABLE `tb_user` (
  `id`            int(11)                        NOT NULL,
  `user_code`     varchar(30)                    NOT NULL,
  `username`      varchar(255)                   NOT NULL,
  `password`      varchar(255)                   NOT NULL,
  `nama_lengkap`  varchar(255)                   DEFAULT NULL,
  `email`         varchar(255)                   DEFAULT NULL,
  `telepon`       varchar(20)                    DEFAULT NULL,
  `alamat`        text                           DEFAULT NULL,
  `tempat_lahir`  varchar(255)                   DEFAULT NULL,
  `tanggal_lahir` date                           DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan')  DEFAULT NULL,
  `nip_nim`       varchar(50)                    DEFAULT NULL,
  `foto_profil`   varchar(255)                   DEFAULT NULL,
  `level`         int(11)                        NOT NULL COMMENT '1=Hubin, 2=Pembimbing, 3=Siswa',
  `is_active`     tinyint(1)                     DEFAULT 1,
  `created_at`    timestamp                      NOT NULL DEFAULT current_timestamp(),
  `updated_at`    timestamp                      NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email`    (`email`),
  UNIQUE KEY `unique_nip_nim`  (`nip_nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password 'password' = $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO `tb_user` (`id`, `user_code`, `username`, `password`, `nama_lengkap`, `email`, `telepon`, `alamat`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `nip_nim`, `foto_profil`, `level`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin12345678901234567890123456', 'admin',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator',   'admin@example.com',       '081234567890', 'Jl. Contoh No. 123', 'Bandung', '1990-01-01', 'Laki-laki', 'NIP001', NULL, 1, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00'),
(2, 'hubin12345678901234567890123456', 'hubin',  '$2y$10$eibUfa8QpwhE6K2kTN.XM.DfXNS.x.ii1R16JwzK8eRxU1pNeckX6', 'Hubin Staff',     'hubin@example.com',       '081234567891', 'Jl. Sekolah No. 1',  'Bandung', '1985-05-15', 'Laki-laki', 'NIP002', NULL, 1, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00'),
(3, 'guru_12345678901234567890123456', 'guru_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pembimbing Test', 'pembimbing@example.com',  '081234567892', 'Jl. Contoh No. 125', 'Bandung', '2000-12-12', 'Laki-laki', 'NIP003', NULL, 2, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00'),
(4, 'siswa1234567890123456789012345',  'siswa',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siswa Test',      'siswa@example.com',       '081234567893', 'Jl. Contoh No. 124', 'Bandung', '2005-05-15', 'Laki-laki', '1234567890', NULL, 3, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 3. tb_pembimbing
-- ============================================================
CREATE TABLE `tb_pembimbing` (
  `pembimbing_id`      int(11)                       NOT NULL,
  `pembimbing_code`    varchar(30)                   NOT NULL,
  `user_id`            int(11)                       DEFAULT NULL COMMENT 'FK ke tb_user',
  `pembimbing_nama`    varchar(255)                  NOT NULL,
  `pembimbing_nip`     varchar(50)                   DEFAULT NULL,
  `pembimbing_telepon` varchar(20)                   DEFAULT NULL,
  `pembimbing_email`   varchar(255)                  DEFAULT NULL,
  `pembimbing_alamat`  text                          DEFAULT NULL,
  `tempat_lahir`       varchar(255)                  DEFAULT NULL,
  `tanggal_lahir`      date                          DEFAULT NULL,
  `jenis_kelamin`      enum('Laki-laki','Perempuan') DEFAULT NULL,
  `pendidikan_terakhir` varchar(100)                 DEFAULT NULL,
  `jabatan`            varchar(100)                  DEFAULT NULL,
  `jurusan_keahlian`   varchar(100)                  DEFAULT NULL,
  `tahun_masuk`        year                          DEFAULT NULL,
  `status_kepegawaian` varchar(50)                   DEFAULT NULL,
  `tempat_tugas`       varchar(255)                  DEFAULT NULL,
  `is_active`          tinyint(1)                    DEFAULT 1,
  `created_at`         timestamp                     NOT NULL DEFAULT current_timestamp(),
  `updated_at`         timestamp                     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_pembimbing_nip`   (`pembimbing_nip`),
  UNIQUE KEY `unique_pembimbing_email` (`pembimbing_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_pembimbing` (`pembimbing_id`, `pembimbing_code`, `user_id`, `pembimbing_nama`, `pembimbing_nip`, `pembimbing_telepon`, `pembimbing_email`, `pembimbing_alamat`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `pendidikan_terakhir`, `jabatan`, `jurusan_keahlian`, `tahun_masuk`, `status_kepegawaian`, `tempat_tugas`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'pembimbing12345678901234567890', 3, 'Pembimbing Test', 'NIP003', '081234567892', 'pembimbing@example.com', 'Jl. Contoh No. 125', 'Bandung', '2000-12-12', 'Laki-laki', 'S2', 'Guru', 'Teknologi Informasi', 2010, 'PNS', 'Sekolah ABC', 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 4. tb_dudi  (+ kolom geolocation untuk validasi presensi)
-- ============================================================
CREATE TABLE `tb_dudi` (
  `dudi_id`            int(11)                                 NOT NULL,
  `dudi_code`          varchar(30)                             NOT NULL,
  `dudi_nama`          varchar(255)                            NOT NULL,
  `dudi_alamat`        text                                    DEFAULT NULL,
  `dudi_telepon`       varchar(20)                             DEFAULT NULL,
  `dudi_email`         varchar(255)                            DEFAULT NULL,
  `dudi_pic`           varchar(255)                            DEFAULT NULL,
  `dudi_nip_pic`       varchar(50)                             DEFAULT NULL,
  `dudi_instruktur`    varchar(255)                            DEFAULT NULL,
  `dudi_nip_instruktur` varchar(50)                            DEFAULT NULL,
  `latitude`           decimal(10,8)                           DEFAULT NULL  COMMENT 'Koordinat GPS latitude kantor DUDI',
  `longitude`          decimal(11,8)                           DEFAULT NULL  COMMENT 'Koordinat GPS longitude kantor DUDI',
  `radius`             int(11)                                 NOT NULL DEFAULT 100 COMMENT 'Radius toleransi presensi (meter)',
  `status_kerjasama`   enum('mitra','non_mitra','pengajuan')   DEFAULT 'pengajuan',
  `is_mitra`           tinyint(1)                              DEFAULT 0,
  `sumber_data`        enum('sekolah','siswa')                 DEFAULT 'sekolah',
  `created_at`         timestamp                               NOT NULL DEFAULT current_timestamp(),
  `updated_at`         timestamp                               NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_dudi_nama`  (`dudi_nama`),
  UNIQUE KEY `unique_dudi_email` (`dudi_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_dudi` (`dudi_id`, `dudi_code`, `dudi_nama`, `dudi_alamat`, `dudi_telepon`, `dudi_email`, `dudi_pic`, `dudi_nip_pic`, `dudi_instruktur`, `dudi_nip_instruktur`, `latitude`, `longitude`, `radius`, `status_kerjasama`, `is_mitra`, `sumber_data`, `created_at`, `updated_at`) VALUES
(1, 'dudi12345678901234567890123456', 'PT. Contoh Perusahaan', 'Jl. Industri No. 123, Bandung', '02212345678', 'info@contohperusahaan.com', NULL, NULL, NULL, NULL, -6.9175, 107.6191, 100, 'mitra', 1, 'sekolah', '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 5. tb_siswa
-- ============================================================
CREATE TABLE `tb_siswa` (
  `siswa_id`                  int(11)                                                 NOT NULL,
  `siswa_code`                varchar(30)                                             NOT NULL,
  `user_id`                   int(11)                                                 DEFAULT NULL,
  `dudi_id`                   int(11)                                                 DEFAULT NULL,
  `pembimbing_id`             int(11)                                                 DEFAULT NULL,
  `siswa_nama`                varchar(255)                                            NOT NULL,
  `siswa_kelas`               varchar(20)                                             DEFAULT NULL,
  `siswa_jurusan`             varchar(100)                                            DEFAULT NULL,
  `siswa_nis`                 varchar(50)                                             DEFAULT NULL,
  `siswa_nisn`                varchar(50)                                             DEFAULT NULL,
  `siswa_telepon`             varchar(20)                                             DEFAULT NULL,
  `siswa_alamat`              text                                                    DEFAULT NULL,
  `siswa_tempat_lahir`        varchar(255)                                            DEFAULT NULL,
  `siswa_tanggal_lahir`       date                                                    DEFAULT NULL,
  `siswa_jk`                  enum('L','P')                                           DEFAULT NULL,
  `siswa_asal_sekolah`        varchar(255)                                            DEFAULT NULL,
  `status_pengajuan`          enum('draft','menunggu','disetujui','ditolak','selesai') DEFAULT 'draft',
  `tanggal_mulai`             date                                                    DEFAULT NULL,
  `tanggal_selesai`           date                                                    DEFAULT NULL,
  `lama_pelaksanaan`          int(11)                                                 DEFAULT NULL COMMENT 'durasi dalam hari',
  `surat_permohonan`          varchar(255)                                            DEFAULT NULL,
  `surat_balasan`             varchar(255)                                            DEFAULT NULL,
  `periode`                   varchar(20)                                             DEFAULT NULL,
  `other_dudi_nama`           varchar(255)                                            DEFAULT NULL,
  `other_dudi_alamat`         text                                                    DEFAULT NULL,
  `other_dudi_telepon`        varchar(20)                                             DEFAULT NULL,
  `other_dudi_email`          varchar(255)                                            DEFAULT NULL,
  `other_dudi_pic`            varchar(255)                                            DEFAULT NULL,
  `other_dudi_nip_pic`        varchar(50)                                             DEFAULT NULL,
  `other_dudi_instruktur`     varchar(255)                                            DEFAULT NULL,
  `other_dudi_nip_instruktur` varchar(50)                                             DEFAULT NULL,
  `other_dudi_latitude`       decimal(10,8)                                            DEFAULT NULL,
  `other_dudi_longitude`      decimal(11,8)                                            DEFAULT NULL,
  `mentor_assignment_notified` tinyint(1)                                             DEFAULT 0,
  `is_active`                 tinyint(1)                                              DEFAULT 1,
  `created_at`                timestamp                                               NOT NULL DEFAULT current_timestamp(),
  `updated_at`                timestamp                                               NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_siswa_nis`  (`siswa_nis`),
  UNIQUE KEY `unique_siswa_nisn` (`siswa_nisn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_siswa` (`siswa_id`, `siswa_code`, `user_id`, `dudi_id`, `pembimbing_id`, `siswa_nama`, `siswa_kelas`, `siswa_jurusan`, `siswa_nis`, `siswa_nisn`, `siswa_telepon`, `siswa_alamat`, `siswa_tempat_lahir`, `siswa_tanggal_lahir`, `siswa_jk`, `siswa_asal_sekolah`, `status_pengajuan`, `tanggal_mulai`, `tanggal_selesai`, `lama_pelaksanaan`, `surat_permohonan`, `surat_balasan`, `periode`, `other_dudi_nama`, `other_dudi_alamat`, `other_dudi_telepon`, `other_dudi_email`, `other_dudi_pic`, `other_dudi_nip_pic`, `other_dudi_instruktur`, `other_dudi_nip_instruktur`, `other_dudi_latitude`, `other_dudi_longitude`, `mentor_assignment_notified`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'siswa12345678901234567890123456', 4, 1, 1, 'Siswa Test', 'XII RPL 1', 'Rekayasa Perangkat Lunak', '1234567890', '0987654321', '081234567893', 'Jl. Contoh No. 124', 'Bandung', '2005-05-15', 'L', 'SMK ITIKURIH HIBARNA', 'disetujui', '2024-06-01', '2024-07-30', 60, NULL, NULL, '2026/2027', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 6. tb_pengelompokan  (relasi siswa ↔ pembimbing)
-- ============================================================
CREATE TABLE `tb_pengelompokan` (
  `id`             int(11)  NOT NULL,
  `pembimbing_id`  int(11)  NOT NULL,
  `siswa_id`       int(11)  NOT NULL,
  `tanggal_assign` datetime DEFAULT NULL,
  `created_at`     timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at`     timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_pengelompokan` (`id`, `pembimbing_id`, `siswa_id`, `tanggal_assign`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 7. tb_pengumuman
-- ============================================================
CREATE TABLE `tb_pengumuman` (
  `pengumuman_id` int(11)      NOT NULL,
  `judul`         varchar(255) NOT NULL,
  `isi`           text         NOT NULL,
  `gambar`        varchar(255) DEFAULT NULL,
  `created_by`    int(11)      DEFAULT NULL,
  `is_active`     tinyint(1)   DEFAULT 1,
  `created_at`    timestamp    NOT NULL DEFAULT current_timestamp(),
  `updated_at`    timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_pengumuman` (`pengumuman_id`, `judul`, `isi`, `gambar`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Pengumuman PKL', 'Mulai tanggal 1 Juni 2024, pelaksanaan PKL akan dimulai sesuai jadwal yang telah ditentukan.', NULL, 1, 1, '2024-01-01 00:00:00', '2024-01-01 00:00:00');

-- ============================================================
-- 8. tb_presensi  (Presensi + Audit Geolocation — RBV-001 s/d RBV-006)
-- ============================================================
CREATE TABLE `tb_presensi` (
  `presensi_id`       int(11)                                      NOT NULL,
  `siswa_id`          int(11)                                      NOT NULL,
  `tanggal`           date                                         NOT NULL,
  `jam_masuk`         time                                         DEFAULT NULL,
  `foto_masuk`        varchar(255)                                 DEFAULT NULL,
  `keterangan_masuk`  text                                         DEFAULT NULL,
  `jam_pulang`        time                                         DEFAULT NULL,
  `foto_pulang`       varchar(255)                                 DEFAULT NULL,
  `keterangan_pulang` text                                         DEFAULT NULL,
  `status_kehadiran`  enum('hadir','izin','sakit','alpha')         NOT NULL DEFAULT 'hadir',
  `status_validasi`   enum('valid','tidak_valid','menunggu')       NOT NULL DEFAULT 'valid',
  `alasan_penolakan`  varchar(255)                                 DEFAULT NULL,
  `latitude_siswa`    decimal(10,8)                                DEFAULT NULL,
  `longitude_siswa`   decimal(11,8)                                DEFAULT NULL,
  `latitude_dudi`     decimal(10,8)                                DEFAULT NULL,
  `longitude_dudi`    decimal(11,8)                                DEFAULT NULL,
  `jarak`             decimal(10,2)                                DEFAULT NULL COMMENT 'jarak dalam meter',
  `radius`            decimal(10,2)                                DEFAULT NULL COMMENT 'radius toleransi dalam meter',
  `status_lokasi`     enum('valid','tidak_valid','tanpa_geolokasi') NOT NULL DEFAULT 'valid',
  `created_at`        timestamp                                    NOT NULL DEFAULT current_timestamp(),
  `updated_at`        timestamp                                    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_presensi_siswa_tanggal` (`siswa_id`, `tanggal`),
  KEY `idx_presensi_siswa_tanggal` (`siswa_id`, `tanggal`),
  KEY `idx_presensi_tanggal`       (`tanggal`),
  KEY `idx_presensi_status`        (`status_kehadiran`),
  KEY `idx_presensi_validasi`      (`status_validasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 9. tb_logbook  (Logbook Kegiatan Harian — RBV-007 s/d RBV-014)
-- ============================================================
CREATE TABLE `tb_logbook` (
  `logbook_id`           int(11)                                  NOT NULL,
  `siswa_id`             int(11)                                  NOT NULL,
  `tanggal`              date                                     NOT NULL,
  `deskripsi_kegiatan`   text                                     NOT NULL,
  `kendala_pembelajaran` text                                     DEFAULT NULL,
  `foto_kegiatan`        varchar(255)                             NOT NULL,
  `catatan_pembimbing`   text                                     DEFAULT NULL,
  `nilai_pembimbing`     decimal(5,2)                              DEFAULT NULL,
  `status_validasi`      enum('valid','tidak_valid','menunggu')   NOT NULL DEFAULT 'menunggu',
  `alasan_penolakan`     varchar(255)                             DEFAULT NULL,
  `created_at`           timestamp                                NOT NULL DEFAULT current_timestamp(),
  `updated_at`           timestamp                                NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_logbook_siswa_tanggal` (`siswa_id`, `tanggal`),
  KEY `idx_logbook_siswa_tanggal` (`siswa_id`, `tanggal`),
  KEY `idx_logbook_tanggal`       (`tanggal`),
  KEY `idx_logbook_status`        (`status_validasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 10. tb_penilaian  (Rekapitulasi Nilai & Penalti — RBV-016)
-- ============================================================
CREATE TABLE `tb_penilaian` (
  `penilaian_id`              int(11)       NOT NULL,
  `siswa_id`                  int(11)       NOT NULL,
  `pembimbing_id`             int(11)       DEFAULT NULL,
  `total_kehadiran`           int(11)       DEFAULT 0,
  `total_bolos`               int(11)       DEFAULT 0,
  `total_terlambat`           int(11)       DEFAULT 0,
  `total_logbook_valid`       int(11)       DEFAULT 0,
  `total_logbook_tidak_valid` int(11)       DEFAULT 0,
  `total_pengurangan_nilai`   decimal(5,2)  DEFAULT 0.00 COMMENT 'Pengurangan nilai (-5 per logbook tidak valid)',
  `nilai_kehadiran`           decimal(5,2)  DEFAULT 100.00,
  `nilai_logbook`             decimal(5,2)  DEFAULT 100.00,
  `nilai_disiplin`            decimal(5,2)  DEFAULT 100.00,
  `nilai_akhir`               decimal(5,2)  DEFAULT 100.00,
  `catatan_evaluasi`          text          DEFAULT NULL,
  `created_at`                timestamp     NOT NULL DEFAULT current_timestamp(),
  `updated_at`                timestamp     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_penilaian_siswa` (`siswa_id`),
  KEY `idx_penilaian_pembimbing`     (`pembimbing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PRIMARY KEYS
-- ============================================================
ALTER TABLE `tb_group`
  ADD PRIMARY KEY (`group_id`);

ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_code` (`user_code`);

ALTER TABLE `tb_pembimbing`
  ADD PRIMARY KEY (`pembimbing_id`),
  ADD UNIQUE KEY `pembimbing_code` (`pembimbing_code`);

ALTER TABLE `tb_dudi`
  ADD PRIMARY KEY (`dudi_id`),
  ADD UNIQUE KEY `dudi_code` (`dudi_code`);

ALTER TABLE `tb_siswa`
  ADD PRIMARY KEY (`siswa_id`),
  ADD UNIQUE KEY `siswa_code` (`siswa_code`),
  ADD KEY `user_id`       (`user_id`),
  ADD KEY `dudi_id`       (`dudi_id`),
  ADD KEY `pembimbing_id` (`pembimbing_id`);

ALTER TABLE `tb_pengelompokan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembimbing_id` (`pembimbing_id`),
  ADD KEY `siswa_id`      (`siswa_id`);

ALTER TABLE `tb_pengumuman`
  ADD PRIMARY KEY (`pengumuman_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `tb_presensi`
  ADD PRIMARY KEY (`presensi_id`);

ALTER TABLE `tb_logbook`
  ADD PRIMARY KEY (`logbook_id`);

ALTER TABLE `tb_penilaian`
  ADD PRIMARY KEY (`penilaian_id`);

-- ============================================================
-- AUTO_INCREMENT
-- ============================================================
ALTER TABLE `tb_group`       MODIFY `group_id`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `tb_user`        MODIFY `id`             int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `tb_pembimbing`  MODIFY `pembimbing_id`  int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tb_dudi`        MODIFY `dudi_id`        int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tb_siswa`       MODIFY `siswa_id`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tb_pengelompokan` MODIFY `id`           int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tb_pengumuman`  MODIFY `pengumuman_id`  int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `tb_presensi`    MODIFY `presensi_id`    int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_logbook`     MODIFY `logbook_id`     int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_penilaian`   MODIFY `penilaian_id`   int(11) NOT NULL AUTO_INCREMENT;

-- ============================================================
-- FOREIGN KEY CONSTRAINTS
-- ============================================================

-- tb_user  →  tb_group
ALTER TABLE `tb_user`
  ADD CONSTRAINT `fk_user_group`
    FOREIGN KEY (`level`) REFERENCES `tb_group` (`group_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- tb_pembimbing  →  tb_user
ALTER TABLE `tb_pembimbing`
  ADD CONSTRAINT `fk_pembimbing_user`
    FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`id`)
    ON DELETE SET NULL;

-- tb_siswa  →  tb_user / tb_dudi / tb_pembimbing
ALTER TABLE `tb_siswa`
  ADD CONSTRAINT `fk_siswa_user`
    FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_siswa_dudi`
    FOREIGN KEY (`dudi_id`) REFERENCES `tb_dudi` (`dudi_id`)
    ON DELETE SET NULL,
  ADD CONSTRAINT `fk_siswa_pembimbing`
    FOREIGN KEY (`pembimbing_id`) REFERENCES `tb_pembimbing` (`pembimbing_id`)
    ON DELETE SET NULL;

-- tb_pengelompokan  →  tb_pembimbing / tb_siswa
ALTER TABLE `tb_pengelompokan`
  ADD CONSTRAINT `fk_pengelompokan_pembimbing`
    FOREIGN KEY (`pembimbing_id`) REFERENCES `tb_pembimbing` (`pembimbing_id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengelompokan_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `tb_siswa` (`siswa_id`)
    ON DELETE CASCADE;

-- tb_pengumuman  →  tb_user
ALTER TABLE `tb_pengumuman`
  ADD CONSTRAINT `fk_pengumuman_user`
    FOREIGN KEY (`created_by`) REFERENCES `tb_user` (`id`)
    ON DELETE SET NULL;

-- tb_presensi  →  tb_siswa
ALTER TABLE `tb_presensi`
  ADD CONSTRAINT `fk_presensi_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `tb_siswa` (`siswa_id`)
    ON DELETE CASCADE;

-- tb_logbook  →  tb_siswa
ALTER TABLE `tb_logbook`
  ADD CONSTRAINT `fk_logbook_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `tb_siswa` (`siswa_id`)
    ON DELETE CASCADE;

-- tb_penilaian  →  tb_siswa / tb_pembimbing
ALTER TABLE `tb_penilaian`
  ADD CONSTRAINT `fk_penilaian_siswa`
    FOREIGN KEY (`siswa_id`) REFERENCES `tb_siswa` (`siswa_id`)
    ON DELETE CASCADE,
  ADD CONSTRAINT `fk_penilaian_pembimbing`
    FOREIGN KEY (`pembimbing_id`) REFERENCES `tb_pembimbing` (`pembimbing_id`)
    ON DELETE SET NULL;

COMMIT;
