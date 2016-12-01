<?php
/*
 * Plugin Name: AWESEM Homepage SEO Text Widget
 * Plugin URI: http://www.awesem.com
 * Description: A widget that displays a text for SEO purposes on homepage
 * Version: 1.0
 * Author: AWESEM
 * Author URI: http://www.awesem.com
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_seo_widgets' );

/*
 * Register widget.
 */
function tz_seo_widgets() {
	register_widget( 'TZ_Seo_Widget' );
}

/*
 * Widget class.
 */
class tz_seo_widget extends WP_Widget {

	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'tz_seo_widget', 'description' => __('A widget that displays a text for SEO purposes on homepage.', 'framework') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'tz_seo_widget' );

		/* Create the widget. */
		parent::__construct( 'tz_seo_widget', __('COMPARE: Custom Homepage SEO Text Widget', 'framework'), $widget_ops, $control_ops );
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$seo_title = apply_filters('widget_title', $instance['seo_title'] );
		$seo_text = $instance['seo_text'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;
		
		echo '<div class="row row-category tz_seo_widget_container">';

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $seo_title )
			echo '<h1 class="widget-title">' . $seo_title . '</h1>';

		/* Display Widget */
		?>
                <p>
			<?php echo do_shortcode($seo_text); ?>
                </p>
		<?php
		
		echo '<div class="clear"></div></div>';

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['seo_title'] = strip_tags( $new_instance['seo_title'] );
		
		/* Stripslashes for html inputs */
		$instance['seo_text'] = stripslashes( $new_instance['seo_text']);
		
		/* No need to strip tags for.. */

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

		/* Set up some default widget settings. */
		$defaults = array('seo_title' => 'SEO H1 Heading', 'seo_text' => 'Lorem ipsum dolor sit amet, erant quodsi pro ex. Ei mel placerat similique. Nostrum philosophia at duo, oratio discere reprehendunt ne pro. Vix movet mundi probatus in. Nullam noster an per, mundi consequuntur ut pri, dicat soluta eum eu. Usu tale iracundia no, ex vim possim placerat recusabo.

Mea alia etiam possit ex, te movet nihil dolorem quo. Homero legendos cu duo. Ius enim unum simul et. Eum ut legimus copiosae scripserit, ex unum dicam prodesset usu.

Ne audire contentiones sea, pri adhuc vocibus ex. Ut qui movet cotidieque, justo dicant ex quo. Mei quem malis aeterno no. Id partem utamur sed, qui atomorum salutatus ea.

Vel eripuit commune consequat no, te homero facilisi vis. Audire molestie adipisci eam ei. Veri putent probatus in has, duo cu utinam admodum vituperatoribus. Adipisci ocurreret eu eam. At sit eirmod delenit necessitatibus.

Pri liber discere efficiendi ea. Gubergren percipitur eos an, est eu case adversarium delicatissimi, quis perpetua iracundia in duo. Eos primis tritani in. Dolore aperiri nec te, at duo posse nostrud legimus. Ut has detracto deseruisse intellegebat, ea mea novum platonem eloquentiam. Qui ne eius meis admodum, mei assum summo cu.');

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('SEO Title:', 'framework') ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'seo_title' ); ?>" name="<?php echo $this->get_field_name( 'seo_title' ); ?>" value="<?php echo $instance['seo_title']; ?>" />
		</p>

		<!-- SEO Text: Textarea -->
		<p>
			<label for="<?php echo $this->get_field_id( 'seo_text' ); ?>"><?php _e('SEO Text:', 'framework') ?></label>
			<textarea style="height:200px;" class="widefat" id="<?php echo $this->get_field_id( 'seo_text' ); ?>" name="<?php echo $this->get_field_name( 'seo_text' ); ?>"><?php echo stripslashes(htmlspecialchars(( $instance['seo_text'] ), ENT_QUOTES)); ?></textarea>
		</p>
				
	<?php
	}
}
?>