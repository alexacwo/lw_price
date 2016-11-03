<?php 
get_header();
global $aw_theme_options, $wpdb, $uploadDirMerchantsAbsolute, $uploadDirMerchants, $post;
$content = '';
$tz_hide_shortcodes_in_product_description = $aw_theme_options['tz_hide_shortcodes_in_product_description']; 
$tz_hide_html_tags_in_product_description = $aw_theme_options['tz_hide_html_tags_in_product_description'];
$tz_hide_product_comments = $aw_theme_options['tz_hide_product_comments'];
$tz_force_show_price_table = $aw_theme_options['tz_force_show_price_table'];  
$listOrGrid = aw_get_result_layout_style();
?>
<div class="nine columns single-product push_three">
        <script type="text/javascript">
        function aw_more() {
                jQuery('.desc_more').toggle();
                jQuery('.desc_etc').toggle();
                jQuery('#more_link').html((jQuery('#more_link').html() == '<?php _e('More','framework') ?>' ? '<?php _e('Less','framework') ?>' : '<?php _e('More','framework') ?>'));
        }
        </script>

        <?php if ( have_posts() ) : ?>

            <?php while ( have_posts() ) : the_post(); ?>
            <?php
            $product_id = $post->ID;
            $product = aw_get_product_info($post->ID); 
            $min_price_formated = aw_the_formated_price($product->min_price, true);            
            $max_price_formated = aw_the_formated_price($product->max_price, true);
            $merchants = aw_get_product_merchants($post->ID);
            $merchants_length = count($merchants);
            ?>
                    <article>

                        <section>
                            <h1><?php the_title(); ?></h1>
                            <div class="row info-header">

                                <?php do_action('aw_show_single_product_price_header'); ?>

                                <div class="share">
                                </div>
                            </div>                                
                            
                            <?php if($merchants_length > 1): ?>

                            <?php do_action('aw_show_single_product_shortlinks'); ?>	

                            <?php endif; ?>	        	

                        </section>

                        <section id="overview" class="row product-overview post-content">

                                <?php
                                //Featured Image
                                if( isset($product->feed_product_image ) && $product->feed_product_image != ''){ ?>
                                    <img src="<?php echo esc_url($product->feed_product_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                <?php } elseif (function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
                                     the_post_thumbnail('slider');
                                } else {
                                ?>
                                <img src="<?php get_template_directory_uri()."/img/no-photo.png"; ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />    
                                <?php }  ?>
                                <p>
                                    <?php do_action('aw_the_product_description_detailed', $post, $merchants, 500); ?>
                                </p>    

                        </section>
                        
                        <section>
                        <?php  
                        $posttags = get_the_tags();
                        if ( $posttags !== false && count($posttags) != 0 ) :
                                $fulltag = array();
                                foreach($posttags as $tag) :
                                        $taglink = get_tag_link($tag->term_id);
                                        $tagname = $tag->name;
                                $fulltag[] = '<a href="'.$taglink.'">'.$tagname.'</a>';
                                endforeach;
                                echo '<p>'.__('Tags:', 'framework').' '.implode(', ',$fulltag).'</p>';
                        endif;
                        ?>
                        </section>

                        <?php if($merchants_length > 0):

                            if( $tz_force_show_price_table != 'false' ):                                              

                                do_action('aw_show_price_table');

                            else:
                                // Let compare decide automatically
                                if($merchants_length > 1):    

                                    do_action('aw_show_price_table'); 

                                endif;

                            endif; 
                        else:
                            if($aw_theme_options['tz_enable_adsense'] == 'true'):
                                ?>
                                    <div id="afshcontainer"></div>
                                    <script type="text/javascript" charset="utf-8">
                                    var pageOptions = {
                                    "pubId" : "<?php echo $aw_theme_options['tz_adsense_client_id']; ?>",
                                    "query" : "<?php echo get_the_title(); ?>",
                                    "adsafe" : "high",
                                    "adtest" : "off",
                                    "hl" : "en"
                                    };

                                    var afshBlock = {
                                    "container" : "afshcontainer",
                                    "width" : jQuery('#afshcontainer').width(),
                                    "height" : 400,
                                    "promoted" : false
                                    };

                                    _googCsa("plas", pageOptions, afshBlock);
                                    </script>
                                <?php
                            endif;
                        endif; ?>


                        <?php if( $tz_hide_product_comments == 'false' ):

                                if( comments_open( $post->ID ) ):

                                    do_action('aw_show_single_product_reviews');

                                endif;

                        endif; ?> 

                </article>
            <?php endwhile; ?>
        
            <?php
            
            do_action('aw_show_realated_products');
            
            ?>
       
        <?php endif; // end have_posts() check ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>