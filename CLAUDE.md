# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Framework**: CodeIgniter 3 (PHP 5.6+), autoloaded via `system/`
- **Database**: MySQL/MariaDB (`pck` database) via `application/config/database.php`
- **Auth**: SSO-based — external server at `sso.ms-bandaaceh.local`. Auth flow in `MY_Controller.__construct()` checks `sso_token` cookie and validates via SSO API.
- **Views**: Plain PHP templates in `application/views/`
- **No PHP test suite** — this is a runtime application, not a library

## Key Files

| Path | Purpose |
|---|---|
| `application/core/MY_Controller.php` | Base controller — SSO auth check, session init, role/pegawai data loading |
| `application/models/Model.php` | Single model — all DB queries + SSO API calls |
| `application/libraries/ApiHelper.php` | HTTP client for SSO server (`get`, `post`, `patch`, `get2`) |
| `application/controllers/HalamanUtama.php` | Dashboard + page routing |
| `application/controllers/HalamanCapaianKinerja.php` | PK/PCK period management, indikator, uraian tugas |
| `application/controllers/HalamanLaporan.php` | Report generation |
| `application/controllers/HalamanPresensi.php` | Attendance |
| `application/controllers/HalamanMagang.php` | Internship |
| `application/config/config.php` | Base URL, SSO server URL, JWT/encryption keys, app ID |
| `application/config/routes.php` | All route definitions |

## Architecture Notes

- All controllers extend `MY_Controller` — every page is guarded by SSO auth
- `Model.php` drives all data operations: local MySQL queries via `CI_Model` and SSO API calls via `ApiHelper`
- SSO API endpoints (`apiclient/get_data_seleksi`, `apiclient/simpan_data`) are used to read/write remote SSO DB tables (`v_users`, `v_pegawai`, `ref_jabatan`, `peran`, etc.)
- Session stores: `userid`, `pegawai_id`, `jab_id`, `peran`, `role`, `status_plh`, `status_plt`, `ketua` (jab_id == 1), `struktural`
- Routes use standard CodeIgniter routing (not REST). Route names match their controller methods.
- `pck.sql` contains the full MySQL schema for this application