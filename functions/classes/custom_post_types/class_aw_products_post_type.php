<?php
class aw_products_post_type {
    
    function __construct(){
       $this->aw_register_post_type();
       $this->aw_add_post_type_actions();
       $this->aw_add_post_type_filters();
    }
        
	public function get_taxonomy_parents($id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array()) {    
		$chain = '';   
		//$parent = &get_term($id, $taxonomy);
		$parent = get_term($id, $taxonomy); 
		if (is_wp_error($parent)) {
			return $parent;
		}

		 
		if ($nicename)    
			$name = $parent -> slug;        
		else    
			$name = $parent -> name;

		if ($parent -> parent && ($parent -> parent != $parent -> term_id) && !in_array($parent -> parent, $visited)) {    
			$visited[] = $parent -> parent;    
			$chain .= $this->get_taxonomy_parents($parent -> parent, $taxonomy, $link, $separator, $nicename, $visited);
		}

		if (!$link) $chain .= $separator . $name ;    

		return $chain; 
	}
	
    public function aw_register_post_type(){        
        // Labels
	$labels = array(
		'name' => __('Products','framework'),
		'singular_name' => __('Product','framework'),
		'add_new' => __('Add new','framework'),
		'add_new_item' => __('Add new product','framework'),
		'edit_item' => __('Edit','framework'),
		'new_item' => __('New product','framework'),
		'view_item' => __('View product','framework'),
		'search_items' => __('Search product','framework'),
		'not_found' =>  __('No product found','framework'),
		'not_found_in_trash' => __('No product found in trash','framework'), 
		'parent_item_colon' => '',
		'menu_name' => __('Products','framework')
	);
	
	$short_url = (get_option('tz_products_short_url') != '') ? get_option('tz_products_short_url') : 0;
	$slug_first_part = ((get_option('tz_custom_products_slug') != '') ? get_option('tz_custom_products_slug') : 'product');
	if($short_url == 1) {
		$slug = $slug_first_part;
	} else {
		$slug = $slug_first_part."/%product_category_main%/%product_category%";
	}
	
	$slug = /*$slug_first_part.*/"/%product_category_main%/%product_category%";
	
	// Arguments
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'menu_icon' => get_stylesheet_directory_uri().'/img/admin/admin-products.png',
		'rewrite' => true,
		'capability_type' => 'post',
		'rewrite' => array(
			'slug' => $slug
		), // Permalinks format
		'has_archive' => true, 
		'hierarchical' => true,
		'menu_position' => null,
		'taxonomies' => array('post_tag'),
		'supports' => array('title','editor','author','thumbnail','excerpt', 'comments', 'tags')
	);
	// Register post type
	register_post_type('product',$args);
    }
    
    function aw_add_post_type_actions(){
        add_action('admin_menu', array($this, 'aw_remove_useless_custom_fields'));
        add_action('admin_init', array($this, 'aw_create_product_metas'));        
        add_action('save_post', array($this, 'aw_save_product_metas'));     
        add_action('publish_product', array($this, 'aw_save_to_cplus_database'));
        add_action('delete_post', array($this, 'aw_delete_from_cplus_database'));
    }
    
    function aw_add_post_type_filters(){
        add_filter('post_type_link', array($this, 'aw_filter_post_type_link'), 10, 2);
        add_filter('term_link',  array($this, 'aw_filter_term_link'), 10, 2);				
		add_filter( 'request', array($this, 'se77513_display_query_vars'), 1 );


    }
	    
		function se77513_display_query_vars( $query_vars ) {
   /* echo '<pre>' . print_r( $query_vars, true ) . '</pre>';

	die();*/
    return $query_vars;
}
    function aw_filter_post_type_link($link, $post) {
        if ($post->post_type != 'product')
            return $link;
        /*if ($cats = get_the_terms($post->ID, 'product_brand'))
            $link = str_replace('%product_brand%', array_pop($cats)->slug, $link);*/
        if ($cats = get_the_terms($post->ID, 'product_category'))
			//$link = str_replace('%product_category%', array_pop($cats)->slug, $link);	   
			 $current_term = array_shift($cats);
			$parent_term =  array_pop($cats); 
			$link = str_replace('%product_category_main%', $parent_term->slug, $link);	   
			$link = str_replace('%product_category%', $current_term->slug, $link);	   
			//$link = str_replace('%product_category_main%', $this->get_taxonomy_parents($current_term, 'product_category', false, '/', true) , $link);
			
			//var_dump($link);
			//die();
        return $link;
    }
    
    function aw_filter_term_link($link, $category) {
        if ($cats = @get_the_terms($post->ID, 'product_brand'))
        $link = str_replace('%product_brand%', array_pop($cats)->slug, $link);
        if ($cats = @get_the_terms($post->ID, 'product_category'))
            $link = str_replace('%product_category%', array_pop($cats)->slug, $link);
            if( strpos($link,'%product_category%') !== false AND $category->taxonomy == "product_brand")  {
                    $bisbrand = get_term_by('slug', $category->slug, 'product_bisbrand');
                     if ( ! $bisbrand == false ){
                            $link_temp = get_term_link( intval($bisbrand->term_id) , 'product_bisbrand' );
                            if( ! is_wp_error( $link_temp ) AND $link_temp != "" ){
                                    $link = $link_temp;
                            }
                     }
            }
        return $link;
    }
    
    function aw_remove_useless_custom_fields() {
	remove_meta_box('postexcerpt','product','normal'); 
	remove_meta_box('authordiv','product','normal');
	remove_meta_box('commentsdiv','product','normal'); 
	remove_meta_box('postimagediv','product','side');
    }
    
  
    function aw_create_product_metas() {
  	add_meta_box('image_meta',__('Product image'),array($this, 'aw_image_meta'),'product');
	add_meta_box('description_meta',__('Product description'),array($this, 'aw_description_meta'),'product');
    } 
       
    
    function aw_description_meta() {
        global $wpdb, $post;
        $description_meta = "";
        if($post !== null) {
                $custom = get_post_custom($post->ID);     
                if(isset($custom['description_meta'][0])){
                        $description_meta = $custom['description_meta'][0];	
                }
        }
        
        /* Add nonce field */
        wp_nonce_field('aw_decription_meta_nonce_check', 'aw_description_meta_nonce');
        
        echo '<textarea id="global_description" name="description_meta">'.htmlspecialchars($description_meta).'</textarea><br />';
        echo '<p class="description">Add product description. This will substitute the original product description even after product update.</p>';
        if($description_meta == '' && get_the_title() != ''){		
                $q = 'SELECT DISTINCT product_description FROM '.$wpdb->prefix.'pc_products_custom WHERE product_name LIKE  "%'. addslashes(htmlspecialchars_decode($post->post_title, ENT_QUOTES)) .'%" AND product_description != ""';		
                $results = $wpdb->get_results($q);		
                if (isset ($results[0]->product_description) && $results[0]->product_description != ''){
                    echo '<br /><p class="description"><b>Suggestions:</b></p>';
                    echo '<p class="description">';
                    echo "<ol>";
                    foreach ($results AS $id => $result){
                            echo "<li style='font-size: 12px; font-style: italic; font-family: sans-serif; color: #666;'>";
                            echo $result->product_description;
                            echo "</li>";
                    }
                    echo "</ol>";
                    echo '</p>';
                }
        }
    }
    
    function aw_image_meta() {
  	global $post;	
	$image_meta = "";	
	if($post !== null){	
            $custom = get_post_custom($post->ID);		
            if(isset($custom['image_meta'][0])){			
                    $image_meta = $custom['image_meta'][0];			
            }		
	}
        
        /* Add nonce field */
        wp_nonce_field('aw_image_meta_nonce_check', 'aw_image_meta_nonce');
        
  	echo '<input type="text" name="image_meta" value="'.$image_meta.'" style="width:99%;" /><br />';
  	echo '<p class="description">Use full URL (with http://)</p>';
    }
    
    function aw_save_product_metas($postID){
      
            global $post, $wpdb;
            if( ! empty( $_POST ) ){
           
                    
                    // Avoid autosave
                    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                            return $postID;
                
                    if( isset($post->post_type) && $post->post_type == 'product'){
                                   
                        if ( check_admin_referer( 'aw_image_meta_nonce_check', 'aw_image_meta_nonce' ) ) {
                            
                            update_post_meta($post->ID, 'image_meta', $_POST['image_meta']);
                        }
                        if ( check_admin_referer( 'aw_decription_meta_nonce_check', 'aw_description_meta_nonce' ) ) {
                    
                            update_post_meta($post->ID, 'description_meta', $_POST['description_meta']);
                        }
                        // Terms brands
                        $brands = wp_get_post_terms($post->ID,'product_brand');
                        // Select all old relationships - The aim is to delete relationships and then add again them.
                        $sql = "SELECT * FROM ".$wpdb->prefix."term_relationships tr 
                                        INNER JOIN  ".$wpdb->prefix."term_taxonomy tt 
                                        ON (tr.term_taxonomy_id = tt.term_taxonomy_id) 
                                        WHERE object_id = '".$post->ID."' AND taxonomy = 'product_bisbrand'"; 
                        $relationships_todelete = $wpdb->get_results($sql);
                        if(!empty($relationships_todelete)){
                                // Loop thru all old relationships
                                foreach ($relationships_todelete AS $relationship_todelete){
                                        //Delete all old relationships 
                                        $sql = "DELETE FROM ".$wpdb->prefix."term_relationships WHERE object_id = '".$post->ID."' AND term_taxonomy_id = '".$relationship_todelete->term_taxonomy_id."'"; 
                                        $wpdb->query($sql);
                                        // Recalculate count
                                        $sql = "UPDATE ".$wpdb->prefix."term_taxonomy tt
                                        SET count =
                                        (SELECT count(p.ID) FROM  ".$wpdb->prefix."term_relationships tr
                                        LEFT JOIN ".$wpdb->prefix."posts p
                                        ON (p.ID = tr.object_id AND p.post_type = 'product' AND p.post_status = 'publish')
                                        WHERE tr.term_taxonomy_id = tt.term_taxonomy_id)
                                        WHERE tt.taxonomy = 'product_bisbrand'
                                        ";
                                        $wpdb->query($sql);	
                                }
                        }
                        // Add new relationships
                        foreach($brands as $brand) {
                                if(!($bisbrand = term_exists($brand->name, 'product_bisbrand'))) {
                                        $bisbrand = wp_insert_term($brand->name, 'product_bisbrand',array(
                                                'slug' => $brand->slug,
                                                'description' => $brand->description,
                                                'parent' => $brand->parent
                                        ));
                                }
                                wp_set_post_terms($post->ID, intval($bisbrand['term_id']), 'product_bisbrand',true);
                        }
                    }
            }
    }
    
    function aw_save_to_cplus_database() {
	global $wpdb, $post;	
	if(!empty($post)){
		
		// Avoid autosave
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post->ID;
                
                
			
		// Terms brands
		$brands = wp_get_post_terms($post->ID,'product_brand');
		
		// Select all old relationships - The aim is to delete relationships and then add again them.
		$sql = "SELECT * FROM ".$wpdb->prefix."term_relationships tr 
				INNER JOIN  ".$wpdb->prefix."term_taxonomy tt 
				ON (tr.term_taxonomy_id = tt.term_taxonomy_id) 
				WHERE object_id = '".$post->ID."' AND taxonomy = 'product_bisbrand'"; 
		
		$relationships_todelete = $wpdb->get_results($sql);
		
		
		if(!empty($relationships_todelete)){
			// Loop thru all old relationships
			foreach ($relationships_todelete AS $relationship_todelete){
				
				//Delete all old relationships 
				$sql = "DELETE FROM ".$wpdb->prefix."term_relationships WHERE object_id = '".$post->ID."' AND term_taxonomy_id = '".$relationship_todelete->term_taxonomy_id."'"; 
				$wpdb->query($sql);
				
				// Recalculate count
				$sql = "UPDATE ".$wpdb->prefix."term_taxonomy tt
				SET count =
				(SELECT count(p.ID) FROM  ".$wpdb->prefix."term_relationships tr
				LEFT JOIN ".$wpdb->prefix."posts p
				ON (p.ID = tr.object_id AND p.post_type = 'product' AND p.post_status = 'publish')
				WHERE tr.term_taxonomy_id = tt.term_taxonomy_id)
				WHERE tt.taxonomy = 'product_bisbrand'
				";
				$wpdb->query($sql);	
			}
		}
		// Add new relationships
		foreach($brands as $brand) {
			if(!($bisbrand = term_exists($brand->name, 'product_bisbrand'))) {
				$bisbrand = wp_insert_term($brand->name, 'product_bisbrand',array(
					'slug' => $brand->slug,
					'description' => $brand->description,
					'parent' => $brand->parent
				));
			}
			wp_set_post_terms($post->ID, intval($bisbrand['term_id']), 'product_bisbrand',true);
		}
		// Save post
		$q = "SELECT * FROM ".$wpdb->prefix."pc_products_relationships WHERE wp_post_id = '".$post->ID."'";
		$r = $wpdb->get_row($q);
		if(count($r) == 0) {
			$q = "INSERT INTO ".$wpdb->prefix."pc_products_relationships SET product_name = '".$_POST['post_title']."', wp_post_id = '".$post->ID."'";
			$wpdb->query($q);
		} else {
			$q = "UPDATE ".$wpdb->prefix."pc_products_relationships SET product_name = '".$_POST['post_title']."' WHERE wp_post_id = '".$post->ID."'";  
			$wpdb->query($q); 		
		}
		
		$q = "INSERT INTO ".$wpdb->prefix."pc_products_custom (product_id, product_description, product_name,insertion_date ) VALUES ('" . $post->ID . "', '" . $_POST['description_meta'] . "', '" . $_POST['post_title'] . "','".time()."') 
		ON DUPLICATE KEY UPDATE product_description = '" . $_POST['description_meta'] . "', product_name = '" . $_POST['post_title'] . "'";
		$wpdb->query($q);
	}
    }
    
    function aw_delete_from_cplus_database($post_id) {
	global $wpdb;
        $post = get_post($post_id);
        if($post->post_type == 'product') {
            $q = "SELECT id_product FROM ".$wpdb->prefix."pc_products_relationships WHERE wp_post_id = '".$post_id."' LIMIT 1";
            $id_product = $wpdb->get_var($q);
            $q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE wp_post_id = '".$post_id."'";
            $wpdb->query($q);
            $q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$id_product."'";
            $wpdb->query($q);
            if(aw_is_compare_plus_installed()){
                    $q = "DELETE FROM ".$wpdb->prefix."pc_products_feeds_relationships WHERE id_product = '".$id_product."'";
                    $wpdb->query($q);	
                    $q = "DELETE FROM ".$wpdb->prefix."pc_product_original_retailer WHERE id_product = '".$id_product."'";
                    $wpdb->query($q);
            }
        }
    }
    
    
    
}
?>