<?php

class ZibAut
{
    public static function get_aut_url()
    {
        return function_exists('home_url') ? home_url() : '127.0.0.1';
    }

    public static function get_home_url()
    {
        return function_exists('home_url') ? home_url() : '127.0.0.1';
    }

    public static function replace_url($url)
    {
        return $url;
    }

    public static function top_host($url)
    {
        $host = is_string($url) ? parse_url($url, PHP_URL_HOST) : '';
        return $host ?: '';
    }

    public static function is_local()
    {
        return false;
    }

    public static function is_aut()
    {
        return true;
    }

    public static function aut_required()
    {
        return array();
    }

    public static function is_ok($data = null)
    {
        return true;
    }

    public static function is_singok($data = null)
    {
        return true;
    }

    public static function get_aut_time()
    {
        return '';
    }

    public static function get_zat_end_time()
    {
        return '';
    }

    public static function save_zat_end_time($result_data = null)
    {
        return true;
    }

    public static function timing_aut()
    {
        return true;
    }

    public static function save_aut_code($aut_code = '')
    {
        return true;
    }

    public static function get_aut_code()
    {
        return '';
    }

    public static function get_aut_code_sign($aut_code = '')
    {
        return is_string($aut_code) ? md5($aut_code) : '';
    }

    public static function curl_aut($aut_code = null)
    {
        return array('error' => 0, 'data' => array());
    }

    public static function curl_aut_data($aut_code = '')
    {
        return array('error' => 0, 'data' => array());
    }

    public static function http_request($url = '', $data = array())
    {
        return false;
    }

    public static function update_ok($result = null)
    {
        return true;
    }

    public static function delete()
    {
        return true;
    }

    public static function get_theme_version()
    {
        return defined('THEME_VERSION') ? THEME_VERSION : '';
    }

    public static function get_download_url()
    {
        return '';
    }

    public static function curl_update($skip_flag = false)
    {
        return array('error' => 0, 'data' => array());
    }

    public static function update_save_data($result_obj = null, $skip_flag = false)
    {
        return true;
    }

    public static function timing_update()
    {
        return true;
    }

    public static function is_update($result = 'null')
    {
        return false;
    }

    public static function skip_update()
    {
        return true;
    }

    public static function noaut_update()
    {
        return true;
    }

    public static function noaut_notice()
    {
        return '';
    }

    public static function admin_js()
    {
        return '';
    }

    public static function footer_html()
    {
        return '';
    }

    public static function csf_save($data = array())
    {
        return $data;
    }

    public static function __callStatic($name, $arguments)
    {
        return true;
    }
}

function zib_save_options_filter($data)
{
    return $data;
}
add_filter('csf_save_options', 'zib_save_options_filter');
