<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
				printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'framework' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h3>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
                                        'walker' => new aw_Walker_Comment_Gumby(),
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 74,                                   
				) );
			?>
		</ol><!-- .comment-list -->

		<?php
			// Are there comments to navigate through?
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
		<nav class="navigation comment-navigation" role="navigation">
			<h1 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'framework' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'framework' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'framework' ) ); ?></div>
		</nav><!-- .comment-navigation -->
		<?php endif; // Check for comment navigation ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php _e( 'Comments are closed.' , 'framework' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>
        <?php
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' ); ?>
        <?php $comment_args = array(      
                                        
                                                            
                                        'fields' => apply_filters( 'comment_form_default_fields', array(    
                                            
                                        'author' => '<div id="write-comment" class="row">' .
                                            
                                                    '</div>' . 
                                            
                                                    '<div class="row row-write-comment-head">' .
                                            
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

                                                    '<label for="email">' . __( 'Email', 'framework' ) . '</label>' .
                                            
                                                    ( $req ? '<span>*</span>' : '' ) .

                                                    '<input id="email" name="email" type="email" class="input email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . ' />',

                                                    '</li>' . 
            
                                                    '</ul>' .
            
                                                    '</div>',

                                        'url'    => '<div class="four columns">' .
            
                                                    '<ul>' .
            
                                                    '<li class="field">' .

                                                    '<label for="url">' . __( 'Website', 'framework' ) . '</label>' .                                                                                              

                                                    '<input id="url" name="url" type="text" class="input text" value="' . esc_attr(  $commenter['comment_author_url'] ) . '"' . $aria_req . ' />',

                                                    '</li>' . 
            
                                                    '</ul>' .
                                             
                                                    '</div>' .    
                                            
                                                    '</div>',                                
                                        
                                            
                                            ) ),
            
                                            'comment_field' => '<div class="row">' .
                                                    
                                                    '<div class="twelve columns">' .
            
                                                    '<ul>' .
            
                                                    '<li class="field">' .

                                                    '<label for="comment">' . __( 'Comment', 'framework' ) . '</label>' .

                                                    '<textarea id="comment" name="comment" class="input textarea" rows="3" aria-required="true"></textarea>' .

                                                    '</li>' . 
            
                                                    '</ul>' .
            
                                                    '</div>' .
            
                                                    '</div>',

                                        );

        comment_form($comment_args); ?>

</div><!-- #comments -->