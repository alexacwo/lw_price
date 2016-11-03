<?php

/**
 * Template Name: Home Page
 */

?>
<?php get_header(); ?>

<?php if ( dynamic_sidebar('homepage') ) : else : endif; ?>

<?php get_footer(); ?>