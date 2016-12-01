<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php global $max_num_pages, $paged; ?>
<!-- Pagination -->
<div class="paginate<?php echo (isset($_GET['ajaxsearch']) ? " ajax-search-navigation-links" : "" );?>">
<?php
        $big = 99999999;
        echo paginate_links( array(
                'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'  => '?paged=%#%',
                'current' => $paged,
                'total'   => $max_num_pages,
                'prev_text'    =>  '&laquo; ' . __('Prev', 'framework'),
                'next_text'    => __('Next', 'framework') . ' &raquo;'
        ) );
?>
</div>
<!-- END Pagination -->