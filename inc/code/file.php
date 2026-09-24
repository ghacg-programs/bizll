<?php
/**
 * ZibDB 静态代理类
 * 将 ZibDB::method() 静态调用代理到 zib_db 实例
 * v9.0: zib_db 已在 db-class.php 中完整实现
 */
class ZibDB
{
    public static function name($name)
    {
        $instance = new zib_db();
        return $instance->name($name);
    }

    public static function table($table, $alias = null)
    {
        $instance = new zib_db();
        return $instance->table($table, $alias);
    }

    public static function __callStatic($method, $args)
    {
        $instance = new zib_db();
        return $instance->$method(...$args);
    }
}
