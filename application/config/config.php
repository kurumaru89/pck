<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

$ssl =
    (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );

$protocol = $ssl ? "https://" : "http://";

$config['base_url'] = $protocol . $_SERVER['SERVER_NAME'] . '/';
$config['sso_server'] = $protocol . 'sso.ms-bandaaceh.local/'; # Domain SSO Server
$config['id_app'] = '10'; # ID Aplikasi Client

$config['index_page'] = '';
$config['uri_protocol']	= 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_+\-@\=()';

$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

$config['allow_get_array'] = TRUE;
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = 'M4hk4m4h@Bn4';

$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'sso_session';
$config['sess_samesite'] = 'Lax';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

$config['cookie_prefix']	= '';
$config['cookie_domain']	= '.ms-bandaaceh.local';
$config['cookie_path']		= '/';
$config['cookie_secure']	= $ssl ? TRUE : FALSE;
$config['cookie_httponly'] 	= TRUE;
$config['cookie_samesite'] 	= 'Lax';

$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;

$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 1800;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';

$config['jwt_key'] = 'Brisingr@3234#saPPhire3234';
$config['jwt_issuer'] = 'sso.local';
$config['jwt_expire_time'] = 3600; // 1 jam

$config['api_key'] = 'M4hk4m4hBn4@2025';
$config['api_izincuti'] = 'seudati.ms-bandaaceh.local/api';