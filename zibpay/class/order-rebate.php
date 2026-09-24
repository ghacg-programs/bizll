<?php

if (!class_exists('ZibPayRebate')) {
    class ZibPayRebate
    {
        public function __construct() {}

        public static function is_ok($data)
        {
            return true;
        }

        public function err()
        {
            return false;
        }

        public static function init()
        {
            return true;
        }

        public static function save_referrer()
        {
            return true;
        }

        public static function register_save_referrer($user_id)
        {
            return true;
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
