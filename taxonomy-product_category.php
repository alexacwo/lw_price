<?php

global $wpdb, $wp_query, $post, $aw_theme_options;

$total_number_of_items = $wp_query->found_posts;
$number_of_items = $wp_query->post_count;
$max_num_pages = $wp_query->max_num_pages;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$term = get_term_by('slug', get_query_var('term'), 'product_category');
$listOrGrid = aw_get_result_layout_style();
get_header();
?>

<div class="nine columns push_three product-listing">
    <script type="text/javascript">
        function aw_more() {
                jQuery('.desc_more').toggle();
                jQuery('.desc_etc').toggle();
                jQuery('#more_link').html((jQuery('#more_link').html() == '<?php _e('More','framework') ?>' ? '<?php _e('Less','framework') ?>' : '<?php _e('More','framework') ?>'));
        }
        </script>
    
    <?php if(have_posts()):  ?>
    
        <div class="listing-options">
            <?php do_action('aw_show_listing_options'); ?>
        </div>
        
        <div class="product-listing-container <?php echo $listOrGrid; ?>">
        <?php
        $i = 1; //  1,2,3 iteration in order to set properly responsive design
        $itotal = $number_of_items; // total products in page
        $it = 1; // total iterated
        while(have_posts()): the_post(); 
            do_action('aw_show_product_archive_content', $post);
            if($i == 3) $i = 1; else $i++;
            $it++;
        endwhile;
        ?>
        <?php do_action('aw_show_pagination', $max_num_pages, $paged ); ?>

        </div>
    
    <?php endif; ?>
    
</div>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>