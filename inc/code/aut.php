<?php
/**
 * ZibCodeAut - Authorization check stub
 */
class ZibCodeAut
{
    public static function __callStatic($name, $arguments)
    {
        return true;
    }

    public static function check()
    {
        return true;
    }

    public static function is_aut()
    {
        return true;
    }

    public static function get_status()
    {
        return 'active';
    }

    public static function get_aut_time()
    {
        // 返回空字符串表示永久授权，或返回具体日期如 '2099-01-01'
        return '';
    }
}