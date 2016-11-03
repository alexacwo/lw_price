<?php

if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	//set_post_thumbnail_size( 50, 50, true );
	add_image_size( 'slider-thumbnail', 130, 130, false );
	add_image_size( 'slider', 300, 300, false );
	add_image_size( 'blog-post', 879, 200, true );
}

?>