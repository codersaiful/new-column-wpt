<?php
namespace WOO_PRODUCT_TABLE_PRO\Admin\License; //WOO_PRODUCT_TABLE_PRO

class Init {

    use \WOO_PRODUCT_TABLE_PRO\Admin\License\Singleton;

    public function init() {

        define( 'WPT_EDD_STORE_URL', 'https://codeastrology.com/' );
        define( 'WPT_EDD_PRODUCT_ID', 6553 );
        define( 'WPT_EDD_PLUGIN_NAME', __( 'Woo Product Table Pro', 'wpt_pro' ) );
        define( 'WPT_EDD_AUTHOR_NAME', 'codeastrology' );
        define( 'WPT_EDD_LICENCE_HELP_URL', 'https://wooproducttable.com/docs/doc/license/where-is-my-license-key/' );

        //Plugin Base Details
        define( 'WPT_EDD_BASE_FILE', WPTP_BASE_DIR . 'woo-product-table-pro.php' ); //It should be plugin based File path link

        define( 'WPT_EDD_CURRENT_VERSION', WPT_PRO_DEV_VERSION ); //It should be plugin based File path link

        //Menu/link
        define( 'WPT_EDD_PARENT_MENU', 'edit.php?post_type=wpt_product_table' ); //There will be parent menu slug if already available.
        define( 'WPT_EDD_LICENSE_PAGE', 'wproducttable-license' );
        define( 'WPT_EDD_LICENSE_PAGE_TITLE', __( 'License', 'wpt_pro' ) );
        

        //Key Status
        define( 'WPT_EDD_LICENSE_KEY', 'wpt_pro_license_key' );
        define( 'WPT_EDD_LICENSE_STATUS', 'wpt_pro_license_status' );

        //Permission
        define( 'WPT_EDD_PERMISSION', 'manage_wpt_product_table' ); //manage_options

        define( 'WPT_EDD_LICENSE_PAGE_LINK', admin_url( WPT_EDD_PARENT_MENU . '&page=' . WPT_EDD_LICENSE_PAGE) );

        // if ( current_user_can( WPT_EDD_PERMISSION ) ) {

            //add submenu for license
            add_action( "admin_menu", [$this, "add_submenu_for_license"], 99 );

            //handle license notice
            $this->manage_license_notice();

            //fire up edd update module
            Updater\Init::instance()->init();
        // }
        return 'licence_init';
    }

    /**
     * Add admin submenu page for license
     */
    public function add_submenu_for_license() {
        add_submenu_page(
            WPT_EDD_PARENT_MENU,
            WPT_EDD_LICENSE_PAGE_TITLE . ' ' . WPT_EDD_PLUGIN_NAME,
            WPT_EDD_LICENSE_PAGE_TITLE,
            WPT_EDD_PERMISSION,
            WPT_EDD_LICENSE_PAGE,
            [$this, 'license_page_template']
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function license_page_template() {
        $file_path = plugin_dir_path( __FILE__ ) . "/template/license-view.php";

        if ( file_exists( $file_path ) ) {
            include_once $file_path;
        }

    }
    
    public function manage_license_notice() {

        // Register license module
        $license = new \WOO_PRODUCT_TABLE_PRO\Admin\License\License();

        if ( $license->status() != 'valid' ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_activate_license' ] );
        }

    }

    public function admin_notice_activate_license(){
        $link_label = __( 'Activate License', 'wpt_pro' );
        $link = WPT_EDD_LICENSE_PAGE_LINK;
		$message = esc_html__( 'Please activate ', 'wpt_pro' ) . '<strong>' . esc_html__( WPT_EDD_PLUGIN_NAME ) . '</strong>' . esc_html__( ' license to get automatic updates.', 'wpt_pro' ) . '</strong>';
        printf( '<div class="error error-warning is-dismissible"><p>%1$s <a href="%2$s">%3$s</a></p></div>', $message, $link, $link_label );

    }

}
