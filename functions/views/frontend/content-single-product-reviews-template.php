<section>  

<?php
/**
 * The template for displaying Reviews [aka in this context as Comments].
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to shape_comment() which is
 * located in the inc/template-tags.php file.
 *
 */
?>
 
<?php
    /*
     * If the current post is protected by a password and
     * the visitor has not yet entered the password we will
     * return early without loading the comments.
     */
    if ( post_password_required() )
        return;
?>
        
        <?php if ( have_comments() ) : ?>         
        
        <div id="reviews" class="row">
            <h2 class="header-line"><?php _e('Product Reviews', 'framework'); ?></h2>
        </div>
        
        <?php
           global $allowedtags;
           wp_list_comments( array( 'walker' => new aw_Walker_Comment_Gumby(), 'callback' => 'aw_shape_review' ) );
        ?>
       
 
        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through? If so, show navigation ?>
        <nav role="navigation" id="review-nav-below" class="site-navigation review-navigation">
            <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older reviews', 'framework' ) ); ?></div>
            <div class="nav-next"><?php next_comments_link( __( 'Newer Reviews &rarr;', 'framework' ) ); ?></div>
        </nav><!-- #review-nav-below .site-navigation .review-navigation -->
        <?php endif; // check for review navigation ?>
 
    <?php endif; // have_comments() ?>
        
        <?php
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' ); ?>
        <?php $comment_args = array(
                                        'title_reply'=> '',
            
                                        'label_submit' => __('Submit Review', 'framework'),

                                        'fields' => apply_filters( 'comment_form_default_fields', array( 
                                            
                                        'author' => '<div id="write-review" class="row">' .
                                                    
                                                    '<h2 class="header-line">' . __('Write A Review', 'framework') . '</h2>' .
                                            
                                                    '</div>' . 
                                            
                                                    '<div class="row row-write-review-head">' .
                                            
                                                    '<div class="four columns">' .
            
                                                    '<ul>' .
            
                                                    '<li class="field">' .

                                                    '<label for="author">' . __( 'Name', 'framework' ) . '</label>' .
                                            
                                                    ( $req ? '<span>*</span>' : '' ) .

                                                    '<input id="author" name="author" type="text" class="input text" value="' . esc_attr(  $commenter['comment_author'] ) . '"' . $aria_req . ' />',

                                                    '</li>' . 
            
                                                    '</ul>' .           
                                            
                                                    '</div>',   
                              
                                        'email'  => '<div class="four columns">' .
            
                                                    '<ul>' .
            
                                                    '<li class="field">' .

                                                    '<label for="email">' . __( 'Email address', 'framework' ) . '</label>' .
                                            
                                                    ( $req ? '<span>*</span>' : '' ) .

                                                    '<input id="email" name="email" type="email" class="input email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . ' />',

                                                    '</li>' . 
            
                                                    '</ul>' .
            
                                                    '</div>' .
                                            
                                                    '</div>',

                                        'url'    => '' ) ),

                                        'comment_field' => '<div class="row">' .
                                                    
                                                    '<div class="twelve columns">' .
            
                                                    '<ul>' .
            
                                                    '<li class="field">' .

                                                    '<label for="comment">' . __( 'Review', 'framework' ) . '</label>' .

                                                    '<textarea id="comment" name="comment" class="input textarea" rows="3" aria-required="true"></textarea>' .

                                                    '</li>' . 
            
                                                    '</ul>' .
            
                                                    '</div>' .
            
                                                    '</div>',
            
                                        'logged_in_as' =>  '<div id="write-review" class="row">' .
                                                    
                                                    '<h2 class="header-line">' . __('Write A Review', 'framework') . '</h2>' .
                                            
                                                    '</div>' . 
            
                                                    '<p class="logged-in-as">' . 
            
                                                    sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'framework' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . 
            
                                                    '</p>',

                                       
                                        'comment_notes_after' => '',
                                        'comment_notes_before' => '',

                                        );

comment_form($comment_args); ?>
        
</section>