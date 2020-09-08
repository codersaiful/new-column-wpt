<?php

//var_dump(get_post_meta(get_the_ID(),'imei_number',true));
$attachment_id = get_post_meta(get_the_ID(),'pdf',true);

$url = wp_get_attachment_url($attachment_id);
$url = $url ? $url : false;

if($url){
    ?>
        

<audio controls>
  <source src="<?php echo esc_attr( $url ); ?>" type="audio/mpeg">
  Your browser does not support the audio tag.
</audio>
        <?php
    
}
$img_att_id = get_post_meta(get_the_ID(),'image',true);
$url = wp_get_attachment_url($img_att_id);
$url = $url ? $url : false;
var_dump($settings);
if($url){
    ?>
        
<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_attr( $url ); ?>"></a>
        <?php
}