<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php 
global $aw_theme_options, $number_of_items, $total_number_of_items, $term, $brand_website_meta;
?>
<article>
<?php if(is_search()): ?>
    <h1><?php _e( 'Search results', 'framework' ) ?></h1>                
<?php else: ?>
    <h1><?php echo (isset($term->name) ? $term->name : ''); ?></h1>
    <p><?php do_action('aw_the_term_description_detailed', $term, 500); ?></p>
<?php if($brand_website_meta != ''): ?>
    <p><a href="<?php echo $brand_website_meta ?>"><?php _e("Visit brand's website",'framework'); ?></a></p>
<?php endif; ?>
<?php endif; ?>
</article>  

<div class="listing-options">           		

    <p class="listing-results"><?php echo sprintf( __( 'Showing products %s of %s', 'framework' ), "<strong>" . $number_of_items . "</strong>", "<strong>" . $total_number_of_items . "</strong>"); ?></p>

    <div class="listing-params">
        
       <fieldset id="order_by_placeholder"></fieldset>

       <fieldset>
         <legend><?php _e('View', 'framework'); ?></legend>
         <ul id="list-toggle" class="list-toggle">
           <li class="<?php echo (isset($aw_theme_options['tz_result_display_style']) && $aw_theme_options['tz_result_display_style'] == 'list' ? "active" : ""); ?>" >
             <a href="#" id="list-layout-switch" class="btn-form list-layout-switch">
               <i class="icon-list"></i>
             </a>
           </li>
           <li class="<?php echo ( (isset($aw_theme_options['tz_result_display_style']) && $aw_theme_options['tz_result_display_style'] == 'grid') || ! isset($aw_theme_options['tz_result_display_style']) ? "active" : ""); ?>">
             <a href="#" id="grid-layout-switch" class="btn-form grid-layout-switch">
               <i class="icon-layout"></i>
             </a>
           </li>
         </ul>
       </fieldset>
     </div>
    <script type="text/javascript">
        function aw_more() {
                jQuery('.desc_more').toggle();
                jQuery('.desc_etc').toggle();
                jQuery('#more_link').html((jQuery('#more_link').html() == '<?php _e('More','framework') ?>' ? '<?php _e('Less','framework') ?>' : '<?php _e('More','framework') ?>'));
        }
        </script>    
</div>                     

