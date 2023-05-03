<?php
$license = \WOO_PRODUCT_TABLE_PRO\Admin\License\License::instance();
$user_details = $license->get_user_info();
$customer_name = '';
if(is_object($user_details)){
    $customer_name = $user_details->customer_name;
}
$key = get_option( WPT_EDD_LICENSE_KEY );
$key_status = get_option( WPT_EDD_LICENSE_STATUS );

?>

<div class="license-module-parent">
    <div class="settings-section">
        <div class="license-parent">
            <?php 
            $message = isset( $_GET['barta'] ) & ! empty( $_GET['barta'] ) ? $_GET['barta'] : false;
            $message_type = isset( $_GET['type'] ) & ! empty( $_GET['type'] ) ? $_GET['type'] : 'warning';
            if( ! empty( $message ) ){?>
                <div id="codeastrology-license-message-area" class="notice notice-<?php echo esc_attr( $message_type ); ?>">
                    <p><?php echo wp_kses_post( $message ); ?></p>
                </div>
                
                <?php } ?>
            <div class="container">

            </div>
            <?php

            if( $license->status() !== 'valid' ){
                ?>
                <div class="container">
                    <form action="" method="POST" class="admin-form" id="admin-license-form">
                        <div class="tab_wraper">
                            <div class="admin-card attr-tab-content admin-card-shadow">
                                <div class="attr-card-body">
                                    <p class="wpt-license-heading">
                                        <?php echo sprintf( __("Enter your license key here for %s", "wpt_pro"), '<b>' . WPT_EDD_PLUGIN_NAME . '</b>' );?>
                                    </p>
                                    
                                    <div>
                                        <label class="admin-option-text-license-key" for="admin-option-text-license-key" >
                                            <?php echo esc_html__(" License Key", "wpt_pro");?>
                                        </label>
                                    </div>
                                    <div class="admin-input-text  license-input-box">
                                        <input type="text" class="attr-form-control" 
                                        id="admin-option-text-license-key" 
                                        aria-describedby="admin-option-text-help-license-key" 
                                        placeholder="Please insert your license key here" 
                                        name="<?php echo esc_attr(WPT_EDD_LICENSE_PAGE); ?>_pro_license_key" value="" >
                                    </div>
                                    <div class="attr-input-group-btn license-input-box">
                                        <input type="hidden" name="type" value="activate" />
                                        <button class="btn-license-activate attr-btn-primary admin-license-form-submit" type="submit" > <?php echo esc_html__("Activate", "wpt_pro");?></button>
                                    </div>
                                    <div class="license-result-box">
                                    </div>
                                    <div class="license-key-doc">
                                        <p class="license-key"><strong>Tips: </strong><a href="<?php echo esc_url( WPT_EDD_LICENCE_HELP_URL ); ?>" target="_black"><?php echo esc_html__( 'Where is my license key? Click Here', 'wpt_pro' ); ?></a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php
            } else {
                ?>
                <div class="license-form-result">
                    <p class="attr-alert attr-alert-success">
                        <?php 
                        
                        echo sprintf( esc_html__("Congratulations %s%s%s! Your product is activated for '%s'.", 'wpt_pro'), '<b>',$customer_name,'</b>', parse_url(home_url(), PHP_URL_HOST) );
                        ?>
                    </p>
                    <form action="" method="POST" class="admin-form" id="admin-license-form">
                        <input type="hidden" name="edd_action_type" value="revoke" />
                        <input type="submit" name="<?php echo esc_attr( WPT_EDD_LICENSE_PAGE ); ?>-revoke-license" value="<?php echo esc_html__('Revoke License', 'wpt_pro');?>"/>
                    </form>
                    <?php if(is_object( $user_details )){ ?>
                    <div class="user-info-edd">
                        <h3 class="sec-title">Your Details</h3>
                        <div class="user-details">

                            <div class="edd-single-user-ifno item_name">
                                <p class="field-name">Product</p>
                                <p class="field-value"><?php echo esc_html( $user_details->item_name ); ?></p>
                            </div>
                            
                            <div class="edd-single-user-ifno customer_name">
                                <p class="field-name">Name</p>
                                <p class="field-value"><?php echo esc_html( $user_details->customer_name ); ?></p>
                            </div>
                            <div class="edd-single-user-ifno customer_email">
                                <p class="field-name">Email</p>
                                <p class="field-value"><?php echo esc_html( $user_details->customer_email ); ?></p>
                            </div>
                            <div class="edd-single-user-ifno license">
                                <p class="field-name">license</p>
                                <p class="field-value"><?php echo esc_html( $user_details->license ); ?></p>
                            </div>
                            <div class="edd-single-user-ifno license_limit">
                                <p class="field-name">license_limit</p>
                                <p class="field-value"><?php echo esc_html( $user_details->license_limit ); ?></p>
                            </div>
                            <div class="edd-single-user-ifno site_count">
                                <p class="field-name">site_count</p>
                                <p class="field-value"><?php echo esc_html( $user_details->site_count ); ?></p>
                            </div>
                            <div class="edd-single-user-ifno activations_left">
                                <p class="field-name">activations_left</p>
                                <p class="field-value"><?php echo esc_html( $user_details->activations_left ); ?></p>
                            </div>
                            <div class="edd-single-user-ifno expires">
                                <p class="field-name">expires</p>
                                <p class="field-value">
                                    <?php 
                                    try{
                                        $dateObj = new DateTime($user_details->expires);
                                        $formattedDate = $dateObj->format('j F, Y');
                                        echo esc_html( $formattedDate ); 
                                    }catch( Exception $eee ){
                                        echo esc_html( $user_details->expires ); 
                                    }
                                    ?>
                                </p>
                            </div>
                            
                            <div class="edd-single-user-ifno my-account">
                                <p class="field-name">Login</p>
                                <p class="field-value">
                                    <?php
                                    $payment_id = $user_details->payment_id ?? '';
                                    // var_dump($payment_id);
                                    ?>
                                    <a href="https://codeastrology.com/my-account/?target_tab=purches_history&action=manage_licenses&payment_id=<?= $payment_id ?>&utm=User Details Page" target="_blank">
                                        My Account (Check license/Upgrade/Manage Site)  
                                    </a>
                                </p>
                            </div>

                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php
            }
            ?>
        </div>

        <?php
        if(function_exists('wpt_social_links')){
            echo '<br><br><br><br>';
            wpt_social_links();
            echo '<br><br>';
        }
        ?>
    </div>
    <div class="wpt-plugin-recommend-area wpt-plugin-recommend-tab-page">
        <?php do_action( 'wpt_plugin_recommend_here' ); ?>
    </div>
</div>

