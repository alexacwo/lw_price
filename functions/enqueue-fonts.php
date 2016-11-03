<?php

/* -- Enqueue Google Fonts -- */
function aw_enqueue_fonts() {
    $protocol = is_ssl() ? 'https' : 'http';
    wp_enqueue_style( 'google-fonts', "$protocol://fonts.googleapis.com/css?family=Signika:400,300,600" );
}
add_action( 'wp_enqueue_scripts', 'aw_enqueue_fonts' );

?>
