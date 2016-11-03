<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php 
global $merchants_length, $aw_theme_options, $post; 
?>
<div class="row row-shortlinks">
    <ul>
        <li class="skiplink"><a class="btn-label" href="#" gumby-goto="#overview" gumby-update gumby-offset="-10"><?php _e('Overview', 'framework'); ?></a></li>
        
        <?php if( $aw_theme_options['tz_force_show_price_table'] != 'false' || $merchants_length > 1 ): ?>
        <li class="skiplink"><a class="btn-label" href="#" gumby-goto="#retailers" gumby-update gumby-offset="-10"><?php _e('Retailers', 'framework'); ?></a></li>
        <?php endif; ?>
        
        <?php if( $aw_theme_options['tz_hide_product_comments'] == 'false' ): ?>
            <?php if( comments_open( $post->ID ) ): ?>
                <li class="skiplink"><a class="btn-label" href="#" gumby-goto="<?php echo ( get_comments_number() ) ? "#reviews" : "#write-review"; ?>" gumby-update gumby-offset="-10"><?php _e('Product Reviews', 'framework'); ?></a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
</div>                                 

