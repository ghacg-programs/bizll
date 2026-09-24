<?php

if (!class_exists('ZibPayIncome')) {
    class ZibPayIncome
    {
        public function __construct() {}

        public function err()
        {
            return false;
        }

        public static function aut_md5($md5 = '', $data = array())
        {
            return $md5;
        }

        public static function replace_url($url)
        {
            return $url;
        }

        public static function get_theme_version()
        {
            return defined('THEME_VERSION') ? THEME_VERSION : '';
        }

        public static function autdata($cut_code = '')
        {
            return array('error' => 0, 'data' => array());
        }

        public static function payment_order($pay_order)
        {
            return $pay_order;
        }

        public static function __callStatic($name, $arguments)
        {
            return true;
        }

        public function __call($name, $arguments)
        {
            return true;
        }
    }
}
