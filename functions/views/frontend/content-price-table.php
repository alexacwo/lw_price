<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php global $aw_theme_options, $merchants, $merchants_length, $uploadDirMerchantsAbsolute, $uploadDirMerchants;
$deeplinkInSameWindow = $aw_theme_options['tz_price_comparison_table_open_deeplink_in_same_window'];
$unicalPriceTableId = mt_rand();
$current_blog_id = get_current_blog_id();
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
            jQuery('.paginator-<?php echo $unicalPriceTableId; ?>').smartpaginator({ totalrecords: <?php echo $merchants_length; ?>, recordsperpage: <?php if (isset($aw_theme_options['tz_products_per_page']) && is_numeric($aw_theme_options['tz_products_per_page'])){ if($merchants_length >= $aw_theme_options['tz_products_per_page']) { echo $aw_theme_options['tz_products_per_page']; } else { echo $merchants_length; } } else {  echo "10"; }; ?>, datacontainer: 'price-comparison-table-<?php echo $unicalPriceTableId; ?>', dataelement: 'tr', initval: 0, next: 'Next', prev: 'Prev', first: 'First', last: 'Last', theme: 'black' });

    });
</script>
<section id="retailers" class="row retailers-table">
    <div class="twelve columns">
        <h2 class="header-line"><?php echo sprintf( __( '%s Retailers', 'framework' ), $merchants_length); ?></h2>
        <table class="retailer-table" id="price-comparison-table-<?php echo $unicalPriceTableId; ?>">
                <thead>
                        <tr>
                                <th class="image-col">&nbsp;</th>
                                <th class="merchant-col"><?php _e('Retailer', 'framework')?></th>
                                <th class="product-col"><?php _e('Details', 'framework')?></th>                                
                                <th class="delivery-col"><i class="icon-flight"></i><span class="desc"><?php _e('Delivery', 'framework')?></span></th>
                                <th class="price-col thead-price sort-numeric"><i class="icon-tag"></i><span class="desc"><?php _e('Price', 'framework')?></span></th>
                        </tr>
                </thead>

                <tbody>
                        <?php foreach($merchants as $merchant): ?>
                    	
                                            
                        <!-- retailer start -->
                        <tr>
                                <td class="image-col">
				<?php if($merchant->feed_product_image != ''): ?>
				<img src="<?php echo esc_url($merchant->feed_product_image); ?>" alt="<?php echo esc_attr($merchant->feed_product_name); ?>" />
				<?php endif; ?>
                                </td>                                
                                <td class="merchant-col">                                

                                        <?php if($merchant->image != "" && file_exists($uploadDirMerchantsAbsolute.$merchant->image)): ?>
                                                <?php if(isset($merchant->url) && trim($merchant->url) != ""): ?>
                                                    <a href="<?php echo esc_url($merchant->url); ?>" <?php echo (($deeplinkInSameWindow == 'true')) ?  "" : "target='_blank'" ?> rel="nofollow">
                                                        <img src="<?php echo home_url().$uploadDirMerchants.$merchant->image; ?>" alt="<?php echo esc_attr(stripslashes($merchant->name))?>" />                    
                                                    </a>
                                                <?php else: ?>
                                                    <img src="<?php echo home_url().$uploadDirMerchants.$merchant->image; ?>" alt="<?php echo esc_attr(stripslashes($merchant->name))?>" />     
                                                <?php endif; ?>
                                        <?php else: ?>
                                                <?php if(isset($merchant->url) && trim($merchant->url) != ""): ?>
                                                <a href="<?php echo esc_url($merchant->url); ?>" rel="nofollow"><?php echo stripslashes($merchant->name) ?></a>
                                                <?php else: ?>
                                                <?php echo stripslashes($merchant->name) ?>
                                                <?php endif; ?>
                                        <?php endif; ?>
                                            
                              
                                        <p class="last-update"><?php _e('Last update:','framework'); echo ' '. date( ($aw_theme_options['tz_date_format'] != '' ? $aw_theme_options['tz_date_format'] : 'd-m-Y H:i' ) , $merchant->last_update); ?><p>

                                </td>                              
                                <td class="product-col"><p class="product-details"><?php echo stripslashes($merchant->feed_product_name); ?></p>
                                    <p><?php echo aw_get_excerpt($merchant->feed_product_desc, 100); ?></p>
                                    <p><?php if($merchant->voucher != ''): ?>
                                    <?php _e('VOUCHER CODE:','framework'); ?> <?php echo $merchant->voucher; ?><?php endif; ?>
                                    </p>
                                </td>
                                <td class="delivery-col"><p>
                                <?php
                                if($merchant->shipping != '') {
                                        $merchant->shipping = str_replace(',','.',$merchant->shipping);
                                        if(is_numeric($merchant->shipping)) {
                                               aw_the_formated_price($merchant->shipping);
                                        } else {
                                            echo '<div class="ttip" data-tooltip="'.esc_attr($merchant->shipping).'"><i class="icon-info-circled"></i></div>';
                                        }
                                        echo '<br />';
                                }
				?>       
                                </p></td>
                                <td class="price-col"><p><?php echo aw_the_formated_price($merchant->price); ?></p><div class="small primary btn metro rounded"><a <?php echo ((isset($deeplinkInSameWindow) && $deeplinkInSameWindow == 'true')) ?  "" : "target='_blank'" ?>  href="<?php echo esc_url($merchant->deeplink); ?>"><?php echo (isset($aw_theme_options['tz_buy_button_text']) && $aw_theme_options['tz_buy_button_text'] != '') ? stripslashes($aw_theme_options['tz_buy_button_text']) : __('BUY NOW','framework'); ?></a></div></td>
                        </tr>
                        <!-- retailer end -->
                    
                        <?php endforeach; ?>                       

                </tbody>
        </table>
    
        <div class="paginator-<?php echo $unicalPriceTableId; ?>" style="margin: auto;" />
    </div>
</section>
