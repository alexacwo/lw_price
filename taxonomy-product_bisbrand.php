<?php
get_header();
global $wpdb, $wp_query, $post, $aw_theme_options;

$total_number_of_items = $wp_query->found_posts;
$number_of_items = $wp_query->post_count;
$max_num_pages = $wp_query->max_num_pages;
$term = get_term_by('slug', get_query_var('term'), 'product_brand');
$taxonomy_extra = get_option('taxonomy_'.$term->term_id);
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$brand_website_meta = (isset($taxonomy_extra['website'])) ? $taxonomy_extra['website'] : '';
$listOrGrid = aw_get_result_layout_style();
?>

<div class="nine columns push_three product-listing">
        
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