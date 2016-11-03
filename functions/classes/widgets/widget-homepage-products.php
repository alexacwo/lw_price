<?php
/*
 * Plugin Name: Homepage Products Widget
 * Plugin URI: http://www.awesem.com
 * Description: A widget that displays products on homepage
 * Version: 1.0
 * Author: AWESEM
 * Author URI: http://www.awesem.com
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_homepage_products' );

/*
 * Register widget.
 */
function tz_homepage_products() {
	register_widget( 'TZ_Homepage_Products' );
}

/*
 * Widget class.
 */
class TZ_Homepage_Products extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'tz_homepage_products', 'description' => __('A widget that displays products on homepage.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'tz_homepage_products' );

		/* Create the widget. */
		parent::__construct( 'tz_homepage_products', __('COMPARE: Homepage Products Widget', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------ Display Widget ------ */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );
                $title = $instance['title'];
		/* Display Widget */
		
		
		/* Before widget (defined by themes). */
		echo $before_widget; ?>

                <!-- Products BEGIN -->
                <?php if ($title != ""): ?>
                <div class="row row-category">                        
                        <?php echo $before_title . $title . $after_title;  ?>
                </div>
                <?php endif; ?>
		
                
                <?php		
                global $wpdb;
                $q = "SELECT wp_post_id AS post_id, MIN(p.price) AS min_price, count(p.id_product) AS merchants, p.* FROM ".$wpdb->prefix."pc_products p, ".$wpdb->prefix."pc_products_relationships pr WHERE p.id_product = pr.id_product GROUP BY id_product ORDER BY RAND() LIMIT 4";
                $products = $wpdb->get_results($q);
		if(count($products) != 0 && ! is_wp_error($products) ): $itotal = 1; $i = 1; ?>
                    <?php foreach($products as $product): ?>
                            <?php $permalink = get_permalink( $product->post_id );  ?>
                            <?php if($i == 1): ?>                
                            <div class="row row-homepage-featured homepage-products">                              
                            <?php endif; ?>
                                    <div class="three columns">
                                            <a href="<?php echo esc_url($permalink); ?>">
                                                <?php                                                    
                                                $image_url = $product->feed_product_image;
                                                if($image_url != ''):
                                                ?>
                                                    <img class="product-carousel-image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->feed_product_name ); ?>" />
                                                <?php elseif (function_exists('has_post_thumbnail') && has_post_thumbnail( $product->post_id  ) ) :
                                                    echo get_the_post_thumbnail( $product->post_id,  'slider', array( 'class' => 'product-carousel-image' ) );
                                                else: ?>
                                                    <img class="product-carousel-image" src="<?php echo get_template_directory_uri() ."/img/no-photo.png" ?>" alt="<?php echo esc_attr( $product->feed_product_name ); ?>"  />
                                                <?php endif; ?>
                                                <h4><?php echo $product->feed_product_name; ?></h4>
                                                <?php if( isset($product->merchants) &&  (int) $product->merchants > 1): ?>
                                                <p class="from"><?php _e('from', 'framework'); ?>
                                                <?php endif; ?>
                                                <span>
                                                <?php
                                                aw_the_formated_price($product->min_price);
                                                ?>
                                                </span>
                                                </p>
                                                <?php if( isset($product->merchants) &&  (int) $product->merchants > 1): ?>
                                                <p class="retailers"><?php echo sprintf( __( ' (Available from %s retailers)', 'framework' ), $product->merchants); ?></p>
                                                <?php endif; ?>
                                            </a>                                 
                                    </div>
                            <?php if($i == 4): $i = 1; $itotal++; ?>                
                                </div>                            
                            <?php elseif (count($products) <= $itotal): $itotal++; $i++; ?>
                                </div>
                            <?php else: ?>                          
                            <?php $itotal++; $i++; ?>
                            <?php endif; ?>     
                   <?php endforeach; ?>
               <?php endif;
               
               /* After widget (defined by themes). */
               echo $after_widget;
                    
        }

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;	
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	
	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	 
	function form( $instance ) {
		$defaults = array(			
			'title' => 'Products'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>    
		<!-- Widget Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Widget Title:', 'framework') ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo $instance['title']; ?>" />
		</p>
	<?php }
}
?>