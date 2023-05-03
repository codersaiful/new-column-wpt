<?php
namespace WOO_PRODUCT_TABLE_PRO\Admin\License;

defined( 'ABSPATH' ) || exit;

trait Singleton{

    private static $instance;

    public static function instance(){
        if (!self::$instance) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
}
