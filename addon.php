<?php
/**
 * Plugin Name: A Test for Addons of WPT
 * Plugin Author: Saiful
 * 
 * Version: 1.0.0
 */

add_filter( 'wpto_default_column_arr', 'aaaa_new_text_column' );

function aaaa_new_text_column($sss){
    $sss['text_col'] = 'Text';
    return $sss;
}

add_filter( 'wpto_template_loc_item_text_col', 'habizabi_func' );
function habizabi_func($file){
    $my_file = __DIR__ . '/text_collls.php';
    //var_dump(is_file($my_file));
    return $my_file;
}

add_action( 'wpto_column_setting_form_text_col',function($column_settings){
    $text_col = $column_settings['text_col'];
    $auido = isset( $text_col['audio_switch'] ) ? 'checked' : '';
    
    ?>
<label>Audio Swithc</label>
<input type="checkbox" name="column_settings[text_col][audio_switch]" <?php echo $auido; ?>>

<?php
} );