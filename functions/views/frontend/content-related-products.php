<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php global $tz_number_related_products, $related_products, $listOrGrid, $it, $i, $itotal; ?>
<h2 class="header-line"><?php _e('Related Products', 'framework'); ?></h2>
<div class="product-listing-container related-products <?php echo $listOrGrid; ?>">
    <?php
    $i = 1; //  1,2,3 iteration in order to set properly responsive design
    $itotal = count($related_products); // total products in page
    $it = 1; // total iterated
    foreach ($related_products AS $post):
        setup_postdata($post);	
        do_action('aw_show_related_product_content_item', $post);
        if($i == 3) $i = 1; else $i++;
        $it++;
    endforeach;
    wp_reset_postdata();
    ?>
</div>