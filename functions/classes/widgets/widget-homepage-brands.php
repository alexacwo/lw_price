<?php
/*
 * Plugin Name: Homepage brands
 * Plugin URI: http://www.awesem.com
 * Description: A widget that displays product brands on homepage
 * Version: 1.0
 * Author: AWESEM
 * Author URI: http://www.awesem.com
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_homepage_brands' );

/*
 * Register widget.
 */
function tz_homepage_brands() {
	register_widget( 'TZ_Homepage_Brands' );
}

/*
 * Widget class.
 */
class TZ_Homepage_Brands extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'tz_homepage_brands', 'description' => __('A widget that displays product brands on homepage.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'tz_homepage_brands' );

		/* Create the widget. */
		parent::__construct( 'tz_homepage_brands', __('COMPARE: Homepage Brands', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		
                extract( $args );
		
		/* Our variables from the widget settings. */
		$title = $instance['title'];	
		?>

		<!-- Brands BEGIN -->
                <div class="row row-category">
                         <?php echo $before_title . $title . $after_title;  ?>
                </div>
		
		
		
		<?php
		$brands = get_terms('product_bisbrand');
		if(count($brands) != 0):
		?>
                    <div class="row row-homepage-brands">
			<ul>
				<?php
				$brands = get_terms('product_bisbrand');
				shuffle($brands);			
				$i = 1;
				foreach($brands as $brand):
				?>
				<li>
					<?php
                                        //Get brand not bisbrand for image
                                        $brandbrand= get_term_by('slug', $brand->slug, 'product_brand');
					$image_url =  aw_product_brand_taxonomy::aw_get_brand_image_url($brandbrand->term_id);
					if($image_url != ''):
					?>
					<a href="<?php echo get_term_link($brand->slug,'product_bisbrand') ?>">
					<img src="<?php echo $image_url ?>" alt="<?php echo esc_attr($brand->name); ?>" />
					</a>
					<?php else: ?>
					<a href="<?php echo get_term_link($brand->slug,'product_bisbrand') ?>">
					<img src="<?php echo get_template_directory_uri() ."/img/no-photo.png" ?>" alt="<?php echo esc_attr($brand->name); ?>" />
					</a>
					<?php endif; ?>
				</li>
				<?php if($i == 6) break; $i++; endforeach; ?> 
			</ul>
                    </div>
		<?php endif; ?>		
			
		<?php
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
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
			'title' => 'Brands'
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