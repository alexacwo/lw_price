<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<!-- review begin -->
<div class="row review">
    <div class="row row-head">
        <div class="author">
                <h5><?php printf( __( 'Review by %s', 'framework' ),  get_comment_author_link() ); ?></h5>
                <?php if ( $comment->comment_approved == '0' ) : ?>
                    <em><?php _e( 'Your comment is awaiting moderation.', 'framework' ); ?></em>
                    <br />
                <?php endif; ?>
                <p><?php  printf( __( 'reviewed on %1$s at %2$s', 'framework' ), get_comment_date(), get_comment_time() ); ?> <?php edit_comment_link( __( '(Edit)', 'framework' ), ' ' );?></p>
        </div>
    </div>
    <div class="row review-content">        
        <p><?php comment_text(); ?></p>
    </div>            
</div> 
<!-- review end -->