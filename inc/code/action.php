<?php
// v9.0 stub: 原版 ZibCodeAut 重复定义 + hook 注册
if (!class_exists('ZibCodeAut')) {
    class ZibCodeAut
    {
        public static function __callStatic($name, $arguments)
        {
            return true;
        }
    }
}
