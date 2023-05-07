<?php

/**
* For further details please visit http://docs.easydigitaldownloads.com/article/383-automatic-upgrades-for-wordpress-plugins
 */

 define( 'AAA_EDD_SAMPLE_STORE_URL', 'https://staging19.codeastrology.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
 define( 'AAA_EDD_SAMPLE_ITEM_ID', 12858 ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
 define( 'AAA_EDD_SAMPLE_ITEM_NAME', 'A A A EDD License Test' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
 define( 'AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE', 'aaa-sample-edd-license' );
 define( 'AAA_EDD_SAMPLE_PLUGIN_LICENSE_DATA', 'aaa_license_license_data' );
 
if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php';
}

/**
 * Initialize the updater. Hooked into `init` to work with the
 * wp_version_check cron job, which allows auto-updates.
 */
function aaaa_sl_sample_plugin_updater() {

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'aaa_license_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater(
		AAA_EDD_SAMPLE_STORE_URL,
		AAA_EDD_SAMPLE_ITEM__FILE__,//__FILE__,
		array(
			'version' => '1.0.0',                    // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => AAA_EDD_SAMPLE_ITEM_ID,       // ID of the product
			'author'  => 'Author Name', // author of this plugin
			'beta'    => true,
		)
	);

	// var_dump($edd_updater);
}
add_action( 'init', 'aaaa_sl_sample_plugin_updater' );


/************************************
* the code below is just a standard
* options page. Substitute with
* your own.
*************************************/

/**
 * Adds the plugin license page to the admin menu.
 *
 * @return void
 */
function aaa_license_license_menu() {
	add_plugins_page(
		__( 'Plugin License' ),
		__( 'Plugin License' ),
		'manage_options',
		AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE,
		'aaa_license_license_page'
	);
}
add_action( 'admin_menu', 'aaa_license_license_menu' );

function aaa_license_license_page() {
	add_settings_section(
		'aaa_license_license',
		__( 'Plugin License' ),
		'aaa_license_license_key_settings_section',
		AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE
	);
	add_settings_field(
		'aaa_license_license_key',
		'<label for="aaa_license_license_key">' . __( 'License Key' ) . '</label>',
		'aaa_license_license_key_settings_field',
		AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE,
		'aaa_license_license',
	);
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Plugin License Options' ); ?></h2>
		<form method="post" action="options.php">

			<?php
			do_settings_sections( AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE );
			settings_fields( 'aaa_license_license' );
			submit_button();
			?>

		</form>
	<?php
}

/**
 * Adds content to the settings section.
 *
 * @return void
 */
function aaa_license_license_key_settings_section() {
	esc_html_e( 'This is where you enter your license key.' );
}

/**
 * Outputs the license key settings field.
 *
 * @return void
 */
function aaa_license_license_key_settings_field() {
	$license = get_option( 'aaa_license_license_key' );
	$status  = get_option( 'aaa_license_license_status' );
	$license_data  = get_option( AAA_EDD_SAMPLE_PLUGIN_LICENSE_DATA );
	// $license_data_headers  = get_option( AAA_EDD_SAMPLE_PLUGIN_LICENSE_DATA . '_headers' );
	var_dump($license,$status,$license_data);
	
	?>
	<p class="description"><?php esc_html_e( 'Enter your license key.' ); ?></p>
	<?php
	printf(
		'<input type="text" class="regular-text" id="aaa_license_license_key" name="aaa_license_license_key" value="%s" />',
		esc_attr( $license )
	);
	$button = array(
		'name'  => 'aaa_edd_license_deactivate',
		'label' => __( 'Deactivate License' ),
	);
	if ( 'valid' !== $status ) {
		$button = array(
			'name'  => 'aaa_edd_license_activate',
			'label' => __( 'Activate License' ),
		);
	}
	wp_nonce_field( 'aaa_license_nonce', 'aaa_license_nonce' );
	?>
	<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>"/>
	<?php
}

/**
 * Registers the license key setting in the options table.
 *
 * @return void
 */
function aaa_license_register_option() {
	register_setting( 'aaa_license_license', 'aaa_license_license_key', 'aaa_edd_sanitize_license' );
}
add_action( 'admin_init', 'aaa_license_register_option' );

/**
 * Sanitizes the license key.
 *
 * @param string  $new The license key.
 * @return string
 */
function aaa_edd_sanitize_license( $new ) {
	$old = get_option( 'aaa_license_license_key' );
	if ( $old && $old !== $new ) {
		delete_option( 'aaa_license_license_status' ); // new license has been entered, so must reactivate
	}

	return sanitize_text_field( $new );
}

/**
 * Activates the license key.
 *
 * @return void
 */
function aaa_license_activate_license() {

	// listen for our activate button to be clicked
	if ( ! isset( $_POST['aaa_edd_license_activate'] ) ) {
		return;
	}

	// run a quick security check
	if ( ! check_admin_referer( 'aaa_license_nonce', 'aaa_license_nonce' ) ) {
		return; // get out if we didn't click the Activate button
	}

	// retrieve the license from the database
	$license = trim( get_option( 'aaa_license_license_key' ) );
	if ( ! $license ) {
		$license = ! empty( $_POST['aaa_license_license_key'] ) ? sanitize_text_field( $_POST['aaa_license_license_key'] ) : '';
	}
	if ( ! $license ) {
		return;
	}

	// data to send in our API request
	$api_params = array(
		'edd_action'  => 'activate_license',
		'license'     => $license,
		'item_id'     => AAA_EDD_SAMPLE_ITEM_ID,
		'item_name'   => rawurlencode( AAA_EDD_SAMPLE_ITEM_NAME ), // the name of our product in EDD
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		AAA_EDD_SAMPLE_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	
		// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' );
		}
	} else {

		
	

		$license_data_headers = wp_remote_retrieve_headers($response);
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		var_dump($license_data);
		// var_dump($response);

		if ( false === $license_data->success ) {

			switch ( $license_data->error ) {

				case 'expired':
					$message = sprintf(
						/* translators: the license key expiration date */
						__( 'Your license key expired on %s.', 'aaaaa' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled':
				case 'revoked':
					$message = __( 'Your license key has been disabled.', 'aaaaa' );
					break;

				case 'missing':
					$message = __( 'Invalid license.', 'aaaaa' );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'aaaaa' );
					break;

				case 'item_name_mismatch':
					/* translators: the plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'aaaaa' ), AAA_EDD_SAMPLE_ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'aaaaa' );
					break;

				default:
					$message = __( 'An error occurred, please try again.', 'aaaaa' );
					break;
			}
		}
	}

		// Check if anything passed on a message constituting a failure
	if ( ! empty( $message ) ) {
		$redirect = add_query_arg(
			array(
				'page'          => AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE,
				'sl_activation' => 'false',
				'message'       => rawurlencode( $message ),
			),
			admin_url( 'plugins.php' )
		);

		wp_safe_redirect( $redirect );
		exit();
	}

	// $license_data->license will be either "valid" or "invalid"
	if ( 'valid' === $license_data->license ) {
		update_option( 'aaa_license_license_key', $license );
	}
	update_option( 'aaa_license_license_status', $license_data->license );
	update_option( AAA_EDD_SAMPLE_PLUGIN_LICENSE_DATA, $license_data );
	// update_option( AAA_EDD_SAMPLE_PLUGIN_LICENSE_DATA . '_headers', $license_data_headers );
	wp_safe_redirect( admin_url( 'plugins.php?page=' . AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE ) );
	exit();
}
add_action( 'admin_init', 'aaa_license_activate_license' );

/**
 * Deactivates the license key.
 * This will decrease the site count.
 *
 * @return void
 */
function aaa_license_deactivate_license() {
	// var_dump('sample-edd',$_POST);
	// listen for our activate button to be clicked
	if ( isset( $_POST['aaa_edd_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'aaa_license_nonce', 'aaa_license_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = trim( get_option( 'aaa_license_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $license,
			'item_id'     => AAA_EDD_SAMPLE_ITEM_ID,
			'item_name'   => rawurlencode( AAA_EDD_SAMPLE_ITEM_NAME ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			AAA_EDD_SAMPLE_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);
		// var_dump($response);
		
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$redirect = add_query_arg(
				array(
					'page'          => AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE,
					'sl_activation' => 'false',
					'message'       => rawurlencode( $message ),
				),
				admin_url( 'plugins.php' )
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license ) {
			delete_option( 'aaa_license_license_status' );
		}

		wp_safe_redirect( admin_url( 'plugins.php?page=' . AAA_EDD_SAMPLE_PLUGIN_LICENSE_PAGE ) );
		exit();

	}
}
add_action( 'admin_init', 'aaa_license_deactivate_license' );

/**
 * Checks if a license key is still valid.
 * The updater does this for you, so this is only needed if you want
 * to do somemthing custom.
 *
 * @return void
 */
function aaa_license_check_license() {

	$license = trim( get_option( 'aaa_license_license_key' ) );

	// var_dump($license);
	$api_params = array(
		'edd_action'  => 'check_license',
		'license'     => $license,
		'item_id'     => AAA_EDD_SAMPLE_ITEM_ID,
		'item_name'   => rawurlencode( AAA_EDD_SAMPLE_ITEM_NAME ),
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		AAA_EDD_SAMPLE_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( 'valid' === $license_data->license ) {
		echo 'valid';
		exit;
		// this license is still valid
	} else {
		echo 'invalid';
		exit;
		// this license is no longer valid
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function aaa_license_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch ( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'aaa_license_admin_notices' );
