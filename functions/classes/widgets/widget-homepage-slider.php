<?php
/*
 * Widget Name: Homepage Slider Widget
 * Widget URI: http://www.awesem.com
 * Description: A widget that displays a slider on homepage
 * Version: 1.0
 * Author: AWESEM
 * Author URI: http://www.awesem.com
 */


/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_slider_widgets' );

/*
 * Register widget.
 */
function tz_slider_widgets() {
	register_widget( 'TZ_Slider_Widget' );
}


/*
 * Widget class.
 */
class tz_slider_widget extends WP_Widget {

	/* ---------------------------- */
	/* ------- Widget setup ------- */
	/* ---------------------------- */

	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'tz_slider_widget', 'description' => __('A widget that displays a slider on homepage.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'tz_slider_widget' );

		/* Create the widget. */
		parent::__construct( 'tz_slider_widget', __('COMPARE: Homepage Slider Widget', 'framework'), $widget_ops, $control_ops );

	}



	/* --------------------------- */
	/* ------ Display Widget ----- */
	/* --------------------------- */	

	function widget( $args, $instance ) {

		extract( $args );
		$delayBetweenImages = $instance['delayBetweenImages'];		
		$navigation = $instance['navigation'];
		$pagination = $instance['pagination'];
		$hoverPause = $instance['hoverPause'];
               

		/* Before widget (defined by themes). */
		echo $before_widget;		

                $slides = new WP_Query(array('post_type' => 'slider', 'posts_per_page' => 3, 'order' => 'ASC', 'orderby' => 'menu_order'));     
                if($slides->have_posts()):  $i=0; ?>
                    <div class="row homepage-slider">
                            <div id="sequence">

                                    <img class="sequence-prev" src="<?php bloginfo('template_directory') ?>/img/sequence/bt-prev.png" alt="Previous Frame" />
                                    <img class="sequence-next" src="<?php bloginfo('template_directory') ?>/img/sequence/bt-next.png" alt="Next Frame" />

                                    <ul class="sequence-canvas">

                                            <?php while($slides->have_posts()): $slides->the_post(); ?>
                                                    <?php $url_meta = get_post_meta(get_the_ID(),'url_meta', TRUE); ?>
                                                    <li <?php if($i==0) { echo 'class="animate-in"'; } ?> >

                                                            <h2 class="title">
                                                            <?php if($url_meta != ''): ?>
                                                                <a href="<?php echo $url_meta; ?>">
                                                            <?php endif; ?>
                                                                    <?php the_title() ?>
                                                             <?php if($url_meta != ''): ?>
                                                                    </a>
                                                             <?php endif; ?>                                                           
                                                            </h2>
                                                            <?php
                                                                    $excerpt = get_the_excerpt();
                                                                    if(!empty($excerpt)) { echo '<h3 class="subtitle">'.$excerpt.'</h3>'; } ?>
                                                                    
                                                                    <?php if($url_meta != ''): ?>
                                                                    <a href="<?php echo $url_meta; ?>">
                                                                    <?php endif; ?>
                                                                        
                                                                        <?php the_post_thumbnail('slider'); ?>
                                                                    
                                                                     <?php if($url_meta != ''): ?>
                                                                    </a>
                                                                    <?php endif; ?>
                                                             ?>
                                                    </li>

                                            <?php $i++; endwhile; ?>
                                    </ul>

                                    <ul class="sequence-pagination">
                                            <?php while($slides->have_posts()): $slides->the_post(); ?>
                                                    <li><?php the_post_thumbnail('slider-thumbnail'); ?></li>
                                            <?php endwhile; wp_reset_query(); ?>
                                    </ul>

                            </div>
                    </div>
                    
            <script type="text/javascript">
            jQuery(document).ready(function() {                

            //sequence.js
                var options = {
                    cycle: true,
                    autoPlay: true,
<?php if ($delayBetweenImages != ''){ ?>
                    autoPlayDelay: <?php echo $delayBetweenImages; ?>,
<?php } ?>
<?php if ($navigation != ''){ ?>
                    nextButton: true,
                    prevButton: true,
<?php } ?>
<?php if ($pagination != ''){ ?>
                    pagination: <?php echo $pagination; ?>,
<?php } ?>
<?php if ($hoverPause != ''){ ?>
                    pauseOnHover: <?php echo $hoverPause; ?>,
<?php } ?>
                    preloader: true,
                    preloadTheseFrames: [1]
                };
            var mySequence = jQuery("#sequence").sequence(options).data("sequence");
            });
            </script>

            <?php endif; ?>

            
            <?php
            /* After widget (defined by themes). */
            echo $after_widget;

	}
	
	/* -- Update widget -- */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;		
		$instance['delayBetweenImages'] = strip_tags( $new_instance['delayBetweenImages'] );
		$instance['navigation'] = strip_tags( $new_instance['navigation'] );
		$instance['pagination'] = strip_tags( $new_instance['pagination'] );
		$instance['hoverPause'] = strip_tags( $new_instance['hoverPause'] );
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array(			
			'delayBetweenImages' => '3000',
			'navigation' => 'true',
			'pagination' => 'true',
			'hoverPause' => 'true'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
    
		<!-- Delay between Images -->
		<p>
			<label for="<?php echo $this->get_field_id( 'delayBetweenImages' ); ?>"><?php _e('Delay between Images (ms):', 'framework') ?></label>
			<input id="<?php echo $this->get_field_id( 'delayBetweenImages' ); ?>" name="<?php echo $this->get_field_name( 'delayBetweenImages' ); ?>" class="widefat" value="<?php echo $instance['delayBetweenImages']; ?>" />
		</p>
        
		<!-- Navigation -->
		<p>
			<label for="<?php echo $this->get_field_id( 'navigation' ); ?>"><?php _e('Navigation:', 'framework') ?></label>
			<select id="<?php echo $this->get_field_id( 'navigation' ); ?>" name="<?php echo $this->get_field_name( 'navigation' ); ?>" class="widefat">
				<option <?php if ( 'true' == $instance['navigation'] ) echo 'selected="selected"'; ?>>true</option>
				<option <?php if ( 'false' == $instance['navigation'] ) echo 'selected="selected"'; ?>>false</option>
			</select>
		</p>
                <!-- Pagination -->
		<p>
			<label for="<?php echo $this->get_field_id( 'pagination' ); ?>"><?php _e('Pagination:', 'framework') ?></label>
			<select id="<?php echo $this->get_field_id( 'pagination' ); ?>" name="<?php echo $this->get_field_name( 'pagination' ); ?>" class="widefat">
				<option <?php if ( 'true' == $instance['pagination'] ) echo 'selected="selected"'; ?>>true</option>
				<option <?php if ( 'false' == $instance['pagination'] ) echo 'selected="selected"'; ?>>false</option>
			</select>
		</p>
		
		<!-- Hover Pause -->
		<p>
			<label for="<?php echo $this->get_field_id( 'hoverPause' ); ?>"><?php _e('Pause on Hover:', 'framework') ?></label>
			<select id="<?php echo $this->get_field_id( 'hoverPause' ); ?>" name="<?php echo $this->get_field_name( 'hoverPause' ); ?>" class="widefat">
				<option <?php if ( 'true' == $instance['hoverPause'] ) echo 'selected="selected"'; ?>>true</option>
				<option <?php if ( 'false' == $instance['hoverPause'] ) echo 'selected="selected"'; ?>>false</option>
			</select>
		</p>
		
	<?php
	}
}

?>