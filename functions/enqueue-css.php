<?php

/* -- Enqueue CSS -- */
function enqueue_css() {

	wp_register_style( 'main-stylesheet', get_template_directory_uri() . '/css/gumby.css', array(), '', 'all' );
	wp_enqueue_style( 'main-stylesheet' );
        wp_register_style( 'custom-stylesheet', get_template_directory_uri() . '/css/custom.css', array(), '', 'all' );
	wp_enqueue_style( 'custom-stylesheet' );

}

add_action( 'wp_enqueue_scripts', 'enqueue_css' );

function enqueue_admin_css(){
    
     wp_register_style('theme-options', get_template_directory_uri() . '/css/theme-options.css', array(), '', 'all' );
     wp_enqueue_style('theme-options');
    
}

add_action( 'admin_enqueue_scripts', 'enqueue_admin_css' );


?>
