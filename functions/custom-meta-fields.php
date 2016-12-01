<?php 
/* ADD META BOXES */
add_action( 'add_meta_boxes', 'aw_add_meta_box' );  
function aw_add_meta_box() {  
    add_meta_box( 'url_meta', 'Slide URL', 'aw_display_silde_url_meta', 'slider', 'normal', 'high' ); 
}

function aw_display_silde_url_meta(){   
    global $post;  
    $values = get_post_custom( $post->ID );  
    $slide_url = isset( $values['url_meta'][0] ) ? esc_attr( $values['url_meta'][0] ) : ''; 
    wp_nonce_field( 'aw_silde_url_meta_check_nonce', 'aw_silde_url_meta_nonce' ); 
    ?> 
    
    <p> 
        <input type="text" id="url_meta" name="url_meta" style="width: 98%" value="<?php echo esc_attr($slide_url); ?>" />         
    </p>
    
<?php      
}

/* SAVE META BOXES */
add_action( 'save_post', 'aw_save_meta_box' );  
function aw_save_meta_box( $post_id )  
{  
	// Bail if we're doing an auto save  
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
	
	$post_type = get_post_type( $post_id );
	
        if($post_type == 'slider'){	
            
            // Check if our nonce is set.
            if ( ! isset( $_POST['aw_silde_url_meta_nonce'] ) )
              return $post_id;

            
            $nonce = $_POST['aw_silde_url_meta_nonce'];
            
            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $nonce, 'aw_silde_url_meta_check_nonce' ) )
                return $post_id;

            
            // if our current user can't edit this post, bail  
            if( !current_user_can( 'edit_post', $post_id ) ) return $post_id;  

            $url = isset( $_POST['url_meta'] ) ?  sanitize_text_field($_POST['url_meta']) : '';  
            update_post_meta( $post_id, 'url_meta', $url );

        }      
}  

/* SCHEDULED'2'PUBLISHED POSTS META BOX ISSUE FIX */
add_action('future_to_publish', 'aw_remove_save_meta_box');
function aw_remove_save_meta_box($post){
    remove_action('save_post', 'aw_save_meta_box');
}

?>