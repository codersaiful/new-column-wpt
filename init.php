<?php
/**
 * Plugin Name: A A A EDD License Test 
 * Plugin URI: https://wooproducttable.com/
 * Description: A A A EDD License Test. A A A EDD License Test. WooCommerce all products display as a table in one page by shortcode. Fully responsive and mobile friendly. Easily customizable - color,background,title,text color etc.
 * Author: Saiful
 * Author URI: https://codeastrology.com/
 * Tags: woocommerce product,woocommerce product table, product table
 * 
 * Version: 1.0.0
 * Requires at least:    4.0.0
 * Tested up to:         6.2
 * WC requires at least: 3.0.0
 * WC tested up to: 	 7.4.0
 * 
 * Text Domain: wpt_pro
 * Domain Path: /languages/
 */

/**
* Include Autoloader
* @since 8.0.2.4
* @author Saiful Islam <codersaiful@gmail.com>
*/
define( 'AAA_EDD_SAMPLE_ITEM__FILE__', __FILE__ );
define( 'AAA_EDD_SAMPLE_VERSION', '1.0.0' );
// include_once 'autoloader.php';

add_action( 'plugins_loaded', 'aaa_test_lincne_func' );
function aaa_test_lincne_func(){
    include 'autoloader.php';
    $aaa = new Test_AAAA\License\Init();
}
