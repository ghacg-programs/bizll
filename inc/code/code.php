<?php
/**
 * ZibAut - Authorization stub
 * All authorization checks return true
 */
class ZibAut
{
    // 可在此修改授权有效期：空字符串=永久，具体日期格式 Y-m-d
    private static $expire_date = '';   // 例如改为 '2099-01-01'

    public static function __callStatic($name, $arguments)
    {
        return true;
    }

    public static function is_aut()
    {
        return true;
    }

    public static function is_local()
    {
        return false;
    }

    public static function is_active()
    {
        return true;
    }

    public static function get_aut_url()
    {
        $url = function_exists('home_url') ? home_url() : '127.0.0.1';
        // 去掉协议
        return preg_replace('#^https?://#', '', $url);
    }

    public static function get_aut_time()
    {
        return self::$expire_date;
    }

    public static function is_update()
    {
        return false;
    }

    public static function get_aut_data()
    {
        return array(
            'status'  => 'active',
            'domain'  => self::get_aut_url(),
            'time'    => self::get_aut_time(),
            'version' => defined('THEME_VERSION') ? THEME_VERSION : '8.8.1',
        );
    }
}

function zib_save_options_filter($data)
{
    return $data;
}
add_filter('csf_save_options', 'zib_save_options_filter');