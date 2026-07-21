<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApiHelper
{
    protected $CI;
    protected $url_api;

    public function __construct()
    {
        // Ambil instance CI dan URL API dari config
        $this->CI =& get_instance();
        $this->url_api = rtrim($this->CI->config->item('sso_server'), '/') . '/';
    }

    /**
     * Kirim request PATCH ke endpoint API di server SSO
     * @param string $endpoint - Contoh: 'api/update_partial'
     * @param array $payload - Data yang ingin dikirim
     * @param array $headers - Header tambahan jika ada
     * @return array ['status_code' => ..., 'response' => ...]
     */

    public function get($endpoint, $params = [], $headers = [])
    {
        $full_url = $this->url_api . ltrim($endpoint, '/');

        // Tambahkan query string jika ada parameter
        if (!empty($params)) {
            $full_url .= '?' . http_build_query($params);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/json'
        ], $headers));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status_code' => $http_code,
            'response' => json_decode($response, true)
        ];
    }

    public function get2($endpoint, $params = [], $headers = [])
    {
        // Tambahkan query string jika ada parameter
        if (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/json'
        ], $headers));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status_code' => $http_code,
            'response' => json_decode($response, true)
        ];
    }

    public function post($endpoint, $payload = [], $headers = [])
    {
        $full_url = $this->url_api . ltrim($endpoint, '/');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/json'
        ], $headers));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status_code' => $http_code,
            'response' => json_decode($response, true)
        ];
    }

    public function patch($endpoint, $payload = [], $headers = [])
    {
        $full_url = $this->url_api . ltrim($endpoint, '/');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); // method PATCH
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/json'
        ], $headers));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status_code' => $http_code,
            'response' => json_decode($response, true)
        ];
    }
}
