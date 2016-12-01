<?php
/*
 * Plugin Name: Homepage Categories Widget
 * Plugin URI: http://www.awesem.com
 * Description: A widget that displays product categories on homepage
 * Version: 1.0
 * Author: AWESEM
 * Author URI: http://www.awesem.com
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_homepage_categories' );

/*
 * Register widget.
 */
function tz_homepage_categories() {
	register_widget( 'TZ_Homepage_Categories' );
}

/*
 * Widget class.
 */
class TZ_Homepage_Categories extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'tz_homepage_categories', 'description' => __('A widget that displays product categories on homepage.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'tz_homepage_categories' );

		/* Create the widget. */
		parent::__construct( 'tz_homepage_categories', __('COMPARE: Homepage Categories Widget', 'framework'), $widget_ops, $control_ops );
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

                <!-- Popular Categories BEGIN -->
                <?php if ($title != ""): ?>
                <div class="row row-category">                        
                        <?php echo $before_title . $title . $after_title;  ?>
                </div>
                <?php endif; ?>
		
                
                <?php
		$categories = get_terms('product_category', array('parent' => 0, 'hide_empty' => false));
		if(count($categories) != 0): $itotal = 1; $i = 1; ?>
                    <?php foreach($categories as $category): ?>                        
                            <?php if($i == 1): ?>                
                            <div class="row row-homepage-featured homepage-categories">                              
                            <?php endif; ?>
                                    <div class="three columns">
                                            <a href="<?php echo get_term_link($category->slug,'product_category') ?>">
                                                    <?php
                                                    $taxonomy_extra = get_option('taxonomy_'.$category->term_id);     
                                                    if(isset($taxonomy_extra['icon']) && $taxonomy_extra['icon'] != ''):
                                                    ?>
                                                    <img src="<?php echo esc_url( $taxonomy_extra['icon'] ); ?>" alt="<?php echo esc_attr($category->name) ?>" />  
                                                    <?php endif; ?>                                                    
                                                    <h3><?php echo $category->name ?></h3>
                                            </a>                                        
                                            <p>
                                            <?php
                                            $subcategories = get_terms('product_category',array('parent' => $category->term_id));
                                            $subcategories_links = array();
                                            foreach($subcategories as $subcategory) {
                                                    $subcategories_links[] = '<a href="'.get_term_link($subcategory->slug,'product_category').'">'.$subcategory->name.'</a>';
                                            } 
                                            echo implode(', ',$subcategories_links);
                                            ?>
                                            </p>                                            
                                    </div>
                            <?php if($i == 4): $i = 1; $itotal++; ?>                
                                </div>                            
                            <?php elseif (count($categories) <= $itotal): $itotal++; $i++; ?>
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
			'title' => 'Product Categories'
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