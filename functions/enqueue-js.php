<?php

/* -- Enqueue JS -- */
function aw_enqueue_js() {  

	if (!is_admin()) {
            
            // Non-admin js
            
            wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr-2.6.2.min.js', array(), '2.6.2', false);
            wp_enqueue_script('modernizr');

            wp_enqueue_script('jquery');

            if (is_singular()) {
                    wp_enqueue_script('comment-reply');
            }

            wp_register_script('gumby', get_template_directory_uri() . '/js/gumby.min.js', array('jquery'), '', false);
            wp_enqueue_script('gumby');

            wp_register_script('plugins', get_template_directory_uri() . '/js/plugins.js', array('jquery'), '', false);
            wp_enqueue_script('plugins');

            wp_register_script('main', get_template_directory_uri() . '/js/main.js', array('jquery'), '', false);
            wp_enqueue_script('main');

	} 

} 

add_action( 'wp_enqueue_scripts', 'aw_enqueue_js' );


/* -- Enqueue Admin JS -- */
function aw_enqueue_admin_js() {  
	
    // // Admin js
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tabs');

    wp_register_script('jscolor', get_template_directory_uri().'/js/libs/colorpicker/jquery.modcoder.excolor.js');
    wp_enqueue_script('jscolor');

    wp_register_script('tabs', get_template_directory_uri().'/js/libs/tabs.js');
    wp_enqueue_script('tabs');

} 

add_action( 'admin_enqueue_scripts', 'aw_enqueue_admin_js' );
?>
