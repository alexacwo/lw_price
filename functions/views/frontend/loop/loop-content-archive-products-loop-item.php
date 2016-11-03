<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php  global $wpdb, $post, $i, $it, $itotal, $is_compare_plus_installed, $aw_theme_options;
$product = aw_get_product_info( $post->ID ); 
$content = '';
?>
<!-- Product <?php echo $itotal; ?> begin -->
<div class="product">
    <div class="product-photo">
        <a href="<?php echo get_permalink(); ?>">
            <?php
            //echo 'test' . get_the_id();
            $image_meta = get_post_meta(get_the_id(),'image_meta',true);
            //Is there image meta for this
            if($image_meta != "")
            {?>
                <img src="<?php echo esc_url($image_meta); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
            <?php 
            }
            //Featured Image
            elseif (function_exists('has_post_thumbnail') && has_post_thumbnail() ) 
            {
                 the_post_thumbnail('slider');
            }
            //Image from retailer
            elseif( isset($product->feed_product_image ) && $product->feed_product_image != '')
            { ?>
                <img src="<?php echo esc_url($product->feed_product_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
            <?php 
            }
            //No image image
            else
            {
            ?>
                <img src="<?php get_template_directory_uri()."/img/no-photo.png"; ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />    
            <?php 
            }
            ?>
        </a>
    </div>

    <div class="product-desc">
      <h2><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>
      <p>
          <?php 
                // Check if has global product description ( also stored as post meta )				
                $q = "SELECT product_description FROM ".$wpdb->prefix."pc_products_custom WHERE product_id = ".$post->ID;	
                $result = $wpdb->get_results($q);

                if (!empty($result)) $content = $result[0]->product_description;

                // If no global product description use default one.
                $display_content = "";
                if ($content == ''){

                        if($is_compare_plus_installed){ // check if C+ installed
                                $q = "SELECT rel.id_feed, rel.id_product FROM ".$wpdb->prefix."pc_products_feeds_relationships rel JOIN ".$wpdb->prefix."pc_feeds fe ON rel.id_feed = fe.id WHERE rel.id_product = '".$product->id_product."' AND fe.feed_use_master_description = 1 AND fe.active = 1 LIMIT 1";
                                $results_f = $wpdb->get_row($q);

                                if(!empty($results_f)){	
                                        $q = "SELECT slug FROM ".$wpdb->prefix."pc_products_merchants WHERE feed = ".$results_f->id_feed;
                                        $results_s = $wpdb->get_row($q);

                                        $q = "SELECT feed_product_desc FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$results_f->id_product."' AND id_merchant = '".$results_s->slug."' LIMIT 1";
                                        $results_desc = $wpdb->get_row($q);

                                }	
                                if (!empty($results_desc->feed_product_desc)){
                                        $content = $results_desc->feed_product_desc;
                                        $size = 360;
                                        $content = strip_tags($content);
                                        if(strlen($content) > $size) {
                                                while(isset($content[$size]) && $content[$size] != ' ') {
                                                        $size++;
                                                }
                                                $display_content  = substr($content,0,$size);
                                                $display_content .= '';
                                        } else {
                                                $display_content = $content;
                                        }
                                        $results_desc->feed_product_desc = "";
                                } else {				
                                                $content = get_the_excerpt();	
                                                $display_content = $content;
                                }
                        } else {

                                $content = get_the_excerpt();	
                                $display_content = $content;

                        }
            } else {					
                    $size = 360;
                    $content = strip_tags($content);
                    if(strlen($content) > $size) {
                            while(isset($content[$size]) && $content[$size] != ' ') {
                                    $size++;
                            }
                            $display_content  = substr($content,0,$size);
                            $display_content .= '';
                    } else {
                            $display_content = $content;
                    }
            }
            ?>
            <?php echo strip_shortcodes($display_content); ?><a class="view-more-link" href="<?php echo get_permalink(); ?>"><?php _e('View more', 'framework'); ?></a>
      </p>
      <div class="reviews">
        <p><?php do_action('aw_show_product_reviews_info'); ?></p>        
      </div>
    </div>

    <div class="product-view">
      <div>
        <p class="price">
            <?php if($product->merchants > 1): ?>
                <?php _e('from','framework'); echo " "; ?>
            <?php endif; ?>
            <span>
            <?php 
            if($aw_theme_options['tz_enable_adsense'] == 'true' && $product->merchants == 0){
                echo '';
            }
            else{
                aw_the_formated_price($product->min_price);
            } ?>
            </span>
        </p>
        <?php
         if($product->merchants == 0) {
                if($aw_theme_options['tz_enable_adsense'] == 'true'){
                    $merchants = '';
                }
                else{
                    $merchants = __('No retailer','framework');
                }
        } elseif($product->merchants == 1) {
                $merchants = __('1 retailer','framework');
        } else {
                $merchants = $product->merchants.' '.__('retailers','framework');
        }
        ?>
        <a href="<?php echo get_permalink($post->ID); ?>" class="retailers"><?php echo $merchants; ?></a>
      </div>
      <div class="medium primary btn metro rounded">
        <a href="<?php echo get_permalink($post->ID); ?>"><?php _e('Compare Prices', 'framework'); ?></a>
      </div>
    </div>
</div>
<!-- Product <?php echo $itotal; ?> end -->

<div class="product-<?php echo $i; ?><?php echo ( ($i == 2 && $it != $itotal)) ? " clearfix-2" : "";  echo ( $i == 3 || $it == $itotal ) ? " clearfix-3" : ""; ?>"></div>