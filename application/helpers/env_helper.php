<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('load_env')) {
    /**
     * Muat file .env dari root project dan simpan ke $_ENV.
     * Dipanggil sekali di index.php sebelum CI berjalan.
     */
    function load_env() {
        $file = dirname(BASEPATH) . '/.env';
        if (!file_exists($file)) {
            log_message('debug', 'env: file .env tidak ditemukan di ' . $file);
            return;
        }
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (!strpos($line, '=')) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Hapus tanda kutip opsional
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'") && substr($value, -1) === $value[0]) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

if (!function_exists('env')) {
    /**
     * Ambil nilai dari $_ENV dengan fallback.
     *
     * @param string $key     Kunci di file .env
     * @param mixed  $default Nilai cadangan bila key tidak ada
     * @return mixed
     */
    function env($key, $default = null) {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }
}
