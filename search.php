<?php
global $wpdb, $post, $wp_query, $aw_theme_options;
$total_number_of_items = $wp_query->found_posts;
$number_of_items = $wp_query->post_count;
$max_num_pages = $wp_query->max_num_pages;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$listOrGrid = aw_get_result_layout_style();
if(!isset($_GET['ajaxsearch'])){ // IF NOT AJAX SEARCH
    get_header();
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
                do_action('aw_show_search_content', $post);
                if($i == 3) $i = 1; else $i++;
                $it++;
            endwhile;
            ?>
            <?php do_action('aw_show_pagination', $max_num_pages, $paged ); ?>
        </div>
    <?php else: ?>
    
    <?php _e("No results.",'framework'); ?>
    
    <?php endif; ?>			
</div>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>
    
<?php } else { // IF AJAX SEARCH ?>

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
            do_action('aw_show_search_content', $post);
            if($i == 3) $i = 1; else $i++;
            $it++;
        endwhile;
        ?>
        <?php do_action('aw_show_pagination', $max_num_pages, $paged ); ?> 

    </div>
    
    <?php else: ?>
    
    <?php _e("No results.",'framework'); ?>
    
    <?php endif; ?>
    
<?php } ?>