<?php


// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Defining constant
 */
if( !defined( 'WPTP_PLUGIN_BASE_FOLDER' ) ){
    define( 'WPTP_PLUGIN_BASE_FOLDER', plugin_basename( dirname( __FILE__ ) ) );
}
if( !defined( 'WPT_PRO_DEV_VERSION' ) ){
  define( 'WPT_PRO_DEV_VERSION', '8.2.1.0' );
}

if( ! defined( 'WPTP_CAPABILITY' ) ){
    $wpt_capability = apply_filters( 'wpt_menu_capability', 'manage_wpt_product_table' );
    define( 'WPTP_CAPABILITY', $wpt_capability );
}

if( !defined( 'WPTP_PLUGIN' ) ){
    define( 'WPTP_PLUGIN', '/woo-product-table/woo-product-table.php' );
}

if( !defined( 'WPTP_PLUGIN_BASE_FILE' ) ){
    define( 'WPTP_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );
}

if( !defined( 'WPTP_BASE_URL' ) ){
    define( "WPTP_BASE_URL", plugins_url() . '/'. plugin_basename( dirname( __FILE__ ) ) . '/' );
}

if( !defined( 'WPTP_DIR_BASE' ) ){
    define( "WPTP_DIR_BASE", dirname( __FILE__ ) . '/' );
}
if( !defined( 'WPTP_BASE_DIR' ) ){
    define( "WPTP_BASE_DIR", str_replace( '\\', '/', WPTP_DIR_BASE ) );
}

if( !defined( 'WPTP_PLUGIN_FOLDER_NAME' ) ){
    define( "WPTP_PLUGIN_FOLDER_NAME",plugin_basename( dirname( __FILE__ ) ) ); //aDDED TO NEW VERSION
}

if( !defined( 'WPTP_PLUGIN_FILE_NAME' ) ){
    define( "WPTP_PLUGIN_FILE_NAME", __FILE__ ); //aDDED TO NEW VERSION
}


/**
 * Default Configuration for WOO Product Table Pro
 * 
 * @since 1.0.0 -5
 */
$shortCodeText = 'Product_Table';
/**
* Including Plugin file for security
* Include_once
* 
* @since 1.0.0
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

WOO_Product_Table::getInstance();


/**
 * Main Manager Class for WOO Product Table Plugin.
 * All Important file included here.
 * Set Path and Constant also set WOO_Product_Table Class
 * Already set $_instance, So no need again call
 */
class WOO_Product_Table{
    
    /**
     * Static Property
     * Used for Maintenance of Admin Notice for Require Plugin
     * With Our Plogin Woo Product Table Pro and Woo Product Table
     *
     * @var Array
     */
    public static $own = array(
        'plugin'  => 'woo-product-table/woo-product-table.php',
        'plugin_slug'  => 'woo-product-table',
        'type'  => 'error',
        'message' => 'Install To working',
        'btn_text' => 'Install Now',
        'name' => 'Woo Product Table',
        'perpose' => 'install', //install,upgrade,activation
    );

    public static $direct;

    /**
     * To set Default Value for Woo Product Table, So that, we can set Default Value in Plugin Start and 
     * can get Any were
     *
     * @var Array 
     */
    public static $default = array();
    
    /*
     * List of Path
     * 
     * @since 1.0.0
     * @var array
     */
    protected $paths = array();
    
    /**
     * Set like Constant static array
     * Get this by getPath() method
     * Set this by setConstant() method
     *  
     * @var type array
     */
    private static $constant = array();
    
    /**
     * Property for Shortcode Storing
     *
     * @var String 
     */
    public static $shortCode;
    
    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '5.6';
    
    /**
     * To check Minimum Plugin Version for Free version
     * or Main Plugin/Module Version
     * for our Pro Version
     * 
     * @last_modified_date: September 5, 2021
     */
    const MINIMUM_WPTP_VERSION = '2.9.8';
    
    
    /**
     * Only for Admin Section, Collumn Array
     * 
     * @since 1.7
     * @var Array
     */
    public static $columns_array = array();

    
    /**
     * Only for Admin Section, Disable Collumn Array
     * 
     * @since 1.7
     * @var Array
     */
    public static $colums_disable_array = array();

    /**
     * Set Array for Style Form Section Options
     *
     * @var type 
     */
    public static $style_form_options = array();
    
    /**
    * Core singleton class
    * @var self - pattern realization
    */
   private static $_instance;
   
   /**
    * Set Plugin Mode as 1 for Giving Data to UPdate Options
    *
    * @var type Int
    */
   protected static $mode = 1;
   
    /**
    * Get the instane of WOO_Product_Table
    *
    * @return self
    */
   public static function getInstance() {
        if ( ! ( self::$_instance instanceof self ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
   }
   
   
   public function __construct() {
       
       /**
        * Getting All Install plugin Details Here
        * To check required plugin Availability, Version etc.
        * @since 6.1.0.15
        */
       $installed_plugins = get_plugins();
       
       //Condition and check php verion and WooCommerce activation
       if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }
        
        $plugin     = 'woo-product-table/woo-product-table.php';
        $link_text  = '<strong><a href="' . esc_url( 'https://wordpress.org/plugins/woo-product-table/' ) . '" target="_blank">' . esc_html__( 'Woo Product Table – Free WooCommerce Table view solution', 'wpt_pro' ) . '</a></strong>';
        //Check Installation of WOO Product Table Free Version
        if( !isset( $installed_plugins[$plugin] ) ) {
            $message = sprintf(
                   esc_html__( '"%1$s" requires "%2$s" to be Installed and Activated.', 'wpt_pro' ),
                   '<strong>' . esc_html__( 'Woo Product Table Pro', 'wpt_pro' ) . '</strong>',
                   $link_text
            );
            self::$own['message']       = $message;//'You to activate your Plugin';
            add_action( 'admin_notices', [ $this, 'admin_notice' ] );
            return;
        }
        
        //Check Activation of Woo Product Table Free Version
        //var_dump($installed_plugins[$plugin]);
        if( isset( $installed_plugins[$plugin] ) && !is_plugin_active( $plugin ) ) {
            self::$own['perpose']       = 'activation';
            self::$own['plugin']        = $plugin;
            self::$own['btn_text']      = 'Activate Now';
            self::$own['type']          = 'error';
            $message = sprintf(
                   /* translators: 1: Plugin name 2: WooPrdouct Table */
                   esc_html__( '"%1$s" requires "%2$s" to be activated.', 'wpt_pro' ),
                   '<strong>' . esc_html__( 'Woo Product Table Pro', 'wpt_pro' ) . '</strong>',
                   '<strong><a href="' . esc_url( 'https://wordpress.org/plugins/woo-product-table/' ) . '" target="_blank">' . esc_html__( 'Woo Product Table – Free WooCommerce Table view solution', 'wpt_pro' ) . '</a></strong>'
           );

            
            
            self::$own['message']       = $message;//'You to activate your Plugin';
            add_action( 'admin_notices', [ $this, 'admin_notice' ] );
            return;
        }
        
        /**
         * Need to check Installed Version of WPT Plugin (Free Version)
         * @since 6.1.0.12
         */
        $WPTP_Version = isset( $installed_plugins[$plugin]['Version'] ) ? $installed_plugins[$plugin]['Version'] : '';
        // Check for required PHP version
        if ( version_compare( $WPTP_Version, self::MINIMUM_WPTP_VERSION, '<' ) ) {
            self::$own['perpose']       = 'upgrade';
            self::$own['plugin']        = $plugin;
            self::$own['btn_text']      = __( 'Upgrade Now', 'wpt' );
            self::$own['type']          = 'error';
            $message = sprintf(
                   /* translators: 1: Plugin name 2: WooPrdouct Table */
                   esc_html__( '"%1$s" requires "%2$s" to be upgraded to latest version.', 'wpt_pro' ),
                   '<strong>' . esc_html__( 'Woo Product Table Pro', 'wpt_pro' ) . '</strong>',
                   '<strong><a href="' . esc_url( 'https://wordpress.org/plugins/woo-product-table/' ) . '" target="_blank">' . esc_html__( 'Woo Product Table – Free WooCommerce Table view solution', 'wpt_pro' ) . '</a></strong>'
           );

            
            self::$own['message']       = $message;//'You to Upgrade your Plugin';
            add_action( 'admin_notices', [ $this, 'admin_notice' ] );
            return;
        }
        
        
        
        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
                return;
        }

       $dir = dirname( __FILE__ ); //dirname( __FILE__ )

       /**
        * Include Autoloader
        * @since 8.0.2.4
        * @author Saiful Islam <codersaiful@gmail.com>
        */
       include_once $dir . '/autoloader.php';

       /**
        * See $path_args for Set Path and set Constant
        * 
        * @since 1.0.0
        */
       $path_args = array(
           'PLUGIN_BASE_FOLDER'     =>  plugin_basename( $dir ),
           'PLUGIN_BASE_FILE'       =>  plugin_basename( __FILE__ ),
           'BASE_URL'               =>  plugins_url() . '/'. plugin_basename( $dir ) . '/', //using plugins_url() instead of WP_PLUGIN_URL
           'BASE_DIR'               =>  str_replace( '\\', '/', $dir . '/' ),
       );
       /**
        * Set Path Full with Constant as Array
        * 
        * @since 1.0.0
        */
       $this->setPath($path_args);

       /**
        * Set Constant
        * 
        * @since 1.0.0
        */
       $this->setConstant($path_args);
       
       //Function file should at the Top of all file loading
       include_once $this->path('BASE_DIR','includes/functions.php');
       //Load File
       if( is_admin() ){
            include_once $this->path('BASE_DIR','admin/functions.php'); //Added at V7.0.0 @date 
            
            include_once $this->path('BASE_DIR','admin/menu_plugin_setting_link.php');
            include_once $this->path('BASE_DIR','admin/admin-enqueue.php');
            
            include_once $this->path('BASE_DIR','admin/action-hook.php');
            
            include 'License-test/edd-sample-plugin.php';
            //Licence Updated -> eta invato hole nicher line comment korte hobe.
            self::$direct = WOO_PRODUCT_TABLE_PRO\Admin\License\Init::instance()->init();
            self::$direct = 1;
       }
       
       

    include_once $this->path('BASE_DIR','includes/enqueue.php');

    /**
     * Custdom CSS/Design Class included
     * which is available in Product Table Design Tab
     * 
     * @since 8.0.8.1
     */
    include_once $this->path('BASE_DIR','classes/inline-css-generator.php');
    
    
    //Action Hook Remove from Main module and add new here for Admin Section
    include_once $this->path('BASE_DIR','includes/action-hook.php');


    // update_option('wpt_pro_oop_enble', true);
    // $wpt_pro_oop = get_option('wpt_pro_oop_enble');
    // if($wpt_pro_oop){
    //     $pro = new WOO_PRODUCT_TABLE_PRO\Inc\Feature_Loader();
    // }
    if(class_exists('WOO_PRODUCT_TABLE\Core\Base')){
        new WOO_PRODUCT_TABLE_PRO\Inc\Feature_Loader();
    }
    
    /**
     * Text-domain load in init hook.
     * It's important
     * 
     * @since 8.1.9.0
     * @author Saiful Islam <codersaiful@gmail.com>
     */
    add_action( 'plugin_loaded', [ $this, 'load_textdomain' ] );
   }
    public function load_textdomain() {
        load_plugin_textdomain( 'wpt_pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
    }
   
    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
        }

        $plugin         = isset( self::$own['plugin'] ) ? self::$own['plugin'] : '';
        $type           = isset( self::$own['type'] ) ? self::$own['type'] : false;
        $plugin_slug    = isset( self::$own['plugin_slug'] ) ? self::$own['plugin_slug'] : '';
        $message        = isset( self::$own['message'] ) ? self::$own['message'] : '';
        $btn_text       = isset( self::$own['btn_text'] ) ? self::$own['btn_text'] : '';
        $name           = isset( self::$own['name'] ) ? self::$own['name'] : false; //Mainly providing OUr pLugin Name
        $perpose        = isset( self::$own['perpose'] ) ? self::$own['perpose'] : 'install';
        if( $perpose == 'activation' ){
            $url = $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
        }elseif( $perpose == 'upgrade' ){
            $url = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $plugin, 'upgrade-plugin_' . $plugin );
        }elseif( $perpose == 'install' ){
            //IF PERPOSE install or Upgrade Actually || $perpose == install only supported Here
            $url = wp_nonce_url( self_admin_url( 'update.php?action=' . $perpose . '-plugin&plugin=' . $plugin_slug ), $perpose . '-plugin_' . $plugin_slug ); //$install_url = 
        }else{
            $url = false;
        }
        
        
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = '<p>' . $message . '</p>';
        if( $url ){
            $style = isset( $type ) && $type == 'error' ? 'style="background: #ff584c;border-color: #E91E63;color:white;"' : 'style="background: #ffb900;border-color: #c37400;"';
            $message .= '<p>' . sprintf( '<a href="%s" class="button-primary" %s>%s</a>', $url,$style, $btn_text ) . '</p>';
        }
        printf( '<div class="notice notice-' . $type . ' is-dismissible"><p>%1$s</p></div>', $message );

    }
    
    /**
     * Admin notice
     *
     * Warning when the site doesn't have WooCommerce installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin() {

           if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

           $message = sprintf(
                   esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'wpt_pro' ),
                   '<strong>' . esc_html__( 'Woo Product Table Pro', 'wpt_pro' ) . '</strong>',
                   '<strong><a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '" target="_blank">' . esc_html__( 'WooCommerce', 'wpt_pro' ) . '</a></strong>'
           );

           printf( '<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $message );

    }



    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version() {

           if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

           $message = sprintf(
                   /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                   esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'wpt_pro' ),
                   '<strong>' . esc_html__( 'Woo Product Table', 'wpt_pro' ) . '</strong>',
                   '<strong>' . esc_html__( 'PHP', 'wpt_pro' ) . '</strong>',
                    self::MINIMUM_PHP_VERSION
           );

           printf( '<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $message );

    }
    
   /**
    * Getting Device Object Info by Using Mobile_Detect Class
    * 
    * @param type $userAgent
    * @return \Mobile_Detect Object
    */
   public static function detectDevice( $userAgent = null ) {
       return new Mobile_Detect( null, $userAgent );
   }
   
   /**
    * Getting Device Info Here
    * 
    * @return string Getting Device Name, Such: Mobile, Table or Desktop
    */
   public static function getDevice() {
       $device = 'Desktop';
       $detect = self::detectDevice();
       
       if($detect->isTablet()){
           $device = "Tablet";
       }elseif($detect->isMobile()){
           $device = "Mobile";
       }
       
       return $device;
   }
   
   /**
    * Set Path
    * 
    * @param type $path_array
    * 
    * @since 1.0.0
    */
   public function setPath( $path_array ) {
       $this->paths = $path_array;
   }
   
   private function setConstant( $contanst_array ) {
       self::$constant = $this->paths;
   }
   /**
    * Set Path as like Constant Will Return Full Path
    * Name should like Constant and full Capitalize
    * 
    * @param type $name
    * @return string
    */
   public function path( $name, $_complete_full_file_path = false ) {
       $path = $this->paths[$name] . $_complete_full_file_path;
       return $path;
   }
   
   /**
    * To Get Full path to Anywhere based on Constant
    * 
    * @param type $constant_name
    * @return type String
    */
   public static function getPath( $constant_name = false ) {
       $path = self::$constant[$constant_name];
       return $path;
   }
   /**
    * Update Options when Installing
    * This method has update at Version 3.6
    * 
    * @since 1.0.0
    * @updated since 3.6_29.10.2018 d/m/y
    */
   public static function install() {
      //Nothing to do now
   }
   
    /**
     * Plugin Uninsall Activation Hook 
     * Static Method
     * 
     * @since 1.0.0
     */
   public static function uninstall() {
       //Nothing for now
   }
   
    /**
     * Getting full Plugin data. We have used __FILE__ for the main plugin file.
     * 
     * @since V 1.5
     * @return Array Returnning Array of full Plugin's data for This Woo Product Table plugin
     */
    public static function getPluginData(){
       return get_plugin_data( __FILE__ );
    }
   
    /**
     * Getting Version by this Function/Method
     * 
     * @return type static String
     */
    public static function getVersion() {
        $data = self::getPluginData();
        return $data['Version'];
    }
   
    /**
     * Getting Version by this Function/Method
     * 
     * @return type static String
     */
    public static function getName() {
        $data = self::getPluginData();
        return $data['Name'];
    }

    /**
     * Getting Plugin Default Data
     * 
     * @param type $indexKey
     * @return variable
     */
    public static function getDefault( $indexKey = false ){
        $default = self::$default;
        if( $indexKey && isset( $default[$indexKey] ) ){
            return $default[$indexKey];
        }
        return $default;
    }

}

/**
* Plugin Install and Uninstall
*/
//register_activation_hook(__FILE__, array( 'WOO_Product_Table','install' ) );
//register_deactivation_hook( __FILE__, array( 'WOO_Product_Table','uninstall' ) );
