<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php global $aw_theme_options, $merchants, $merchants_length, $uploadDirMerchantsAbsolute, $uploadDirMerchants, $min_price_formated, $max_price_formated;
$deeplinkInSameWindow = $aw_theme_options['tz_price_comparison_table_open_deeplink_in_same_window'];
$shipping_content = "";
?>
<?php if($merchants_length > 0): ?>
    <div class="price">
        <?php if( $aw_theme_options['tz_force_show_price_table'] != 'false' ):                                              
            if($min_price_formated != $max_price_formated): ?>
                <p><?php _e('From', 'framework'); ?> <span><?php echo $min_price_formated ?></span> <?php _e('to', 'framework'); ?> <span><?php echo $max_price_formated ?></span></p>
                <?php else: ?>
                <p><span><?php echo $min_price_formated ?></span></p>
            <?php endif;
        else:
            // Let compare decide automatically
            if($merchants_length === 1): ?>
                <?php if($merchants[0]->shipping != '') {
                        $merchants[0]->shipping = str_replace(',','.',$merchants[0]->shipping);
                        if(is_numeric($merchants[0]->shipping)) {
                            $shipping_content = ' ' . __('Delivery', 'framework') . ' <span>' . aw_the_formated_price($merchants[0]->shipping, true). '</span>';
                        } else {
                            $shipping_content = ' ' . __('Delivery', 'framework') . ' <span>' . wp_kses($merchants[0]->shipping, array()) . '</span>';
                        }                                       
                }
                ?>
               <p><?php _e('Price', 'framework'); ?> <span><?php echo $min_price_formated ?></span><?php echo $shipping_content; ?></p>
               <p>
               <a <?php echo ((isset($deeplinkInSameWindow) && $deeplinkInSameWindow == 'true')) ?  "" : "target='_blank'" ?>  href="<?php echo esc_url($merchants[0]->deeplink); ?>"><span class="merchant-button"><?php echo (isset($aw_theme_options['tz_buy_button_text']) && $aw_theme_options['tz_buy_button_text'] != '') ? stripslashes($aw_theme_options['tz_buy_button_text']) : __('BUY NOW','framework'); ?></span></a>
                
                <?php if($merchants[0]->image != "" && file_exists($uploadDirMerchantsAbsolute.$merchants[0]->image)): ?>
                   
                    
                    <?php if(isset($merchants[0]->url) && trim($merchants[0]->url) != ""): ?>
                        <a href="<?php echo esc_url($merchants[0]->url); ?>" <?php echo (($deeplinkInSameWindow == 'true')) ?  "" : "target='_blank'" ?> rel="nofollow">
                            <img class="merchant-thumb" src="<?php echo home_url().$uploadDirMerchants.$merchants[0]->image; ?>" alt="<?php echo esc_attr(stripslashes($merchants[0]->name))?>" />                    
                        </a>
                    <?php else: ?>
                        <img class="merchant-thumb" src="<?php echo home_url().$uploadDirMerchants.$merchants[0]->image; ?>" alt="<?php echo esc_attr(stripslashes($merchants[0]->name))?>" />     
                    <?php endif; ?>
                   
                <?php else: ?>
                    <?php if(isset($merchants[0]->url) && trim($merchants[0]->url) != ""): ?>
                    <a href="<?php echo esc_url($merchants[0]->url); ?>" rel="nofollow"><?php echo stripslashes($merchants[0]->name) ?></a>
                    <?php else: ?>
                    <?php echo stripslashes($merchants[0]->name) ?>
                    <?php endif; ?>
                <?php endif; ?>
             </p>
            <?php else:
                if($min_price_formated != $max_price_formated): ?>
                    <p><?php _e('From', 'framework'); ?> <span><?php echo $min_price_formated ?></span> <?php _e('to', 'framework'); ?> <span><?php echo $max_price_formated ?></span></p>
                    <?php else: ?>
                    <p><span><?php echo $min_price_formated ?></span></p>
                <?php endif;
            endif; ?>
           
        <?php endif; ?>
    </div>
<?php endif; ?>                                               

