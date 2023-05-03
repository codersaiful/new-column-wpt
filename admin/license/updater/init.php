<?php
namespace WOO_PRODUCT_TABLE_PRO\Admin\License\Updater;

defined( 'ABSPATH' ) || exit;

class Init {
    
    use \WOO_PRODUCT_TABLE_PRO\Admin\License\Singleton;

    public function init() {

        $license_key = explode( '-', trim( get_option( WPT_EDD_LICENSE_KEY ) ) );
        $license_key = !isset( $license_key[0] ) ? '' : $license_key[0];


        $plugin_dir_and_filename = WPT_EDD_BASE_FILE;

        $active_plugins = get_option( 'active_plugins' );

        
        foreach ( $active_plugins as $active_plugin ) {

            if ( false !== strpos( $active_plugin, 'init.php' ) ) {
                $plugin_dir_and_filename = $active_plugin;
                break;
            }

        }

        if ( !isset( $plugin_dir_and_filename ) || empty( $plugin_dir_and_filename ) ) {
            throw ( 'Plugin not found! Check the name of your plugin file in the if check above' );
        }

        new Edd_Warper(
            WPT_EDD_STORE_URL,
            $plugin_dir_and_filename,
            [
                'version' => WPT_EDD_CURRENT_VERSION,
                'license' => $license_key,
                'item_id' => WPT_EDD_PRODUCT_ID,
                'author'  => WPT_EDD_AUTHOR_NAME,
                'url'     => home_url(),
            ]
        );
    }

}
