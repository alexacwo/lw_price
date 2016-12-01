<?php
class aw_product_bisbrand_taxonomy {
    
    function __construct(){
       $this->aw_register_taxonomy();
       $this->aw_add_taxonomy_actions();
       $this->aw_add_taxonomy_filters();
    }
    
    public function aw_register_taxonomy(){        
      // Labels
	$labels = array(
		'name' => 'Product brands',
		'singular_name' => 'Product brand',
		'search_items' => 'Search a brand',
		'all_items' => 'All brands',
		'parent_item' => 'Parent brand',
		'parent_item_colon' => 'Parent brand:',
		'edit_item' => 'Edit brand', 
		'update_item' => 'Update brand',
		'add_new_item' => 'Add new brand',
		'new_item_name' => 'New brand',
		'menu_name' => 'Brands'
	);
	
	$slug = (get_option('tz_custom_taxonomies_bisbrand_slug') != '') ? get_option('tz_custom_taxonomies_bisbrand_slug') : 'brands';
	
	// Arguments
	$args =  array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => false,
		'query_var' => true,
		'rewrite' => array('slug' => $slug)
	);
	// Register taxonomy
	register_taxonomy('product_bisbrand',array('product'),$args);
    }
    
    function aw_add_taxonomy_actions(){        
         add_action('pre_get_posts', array( $this, 'aw_modify_bisbrand_query' ));
    }
    
    function aw_add_taxonomy_filters(){  
  
    }
    
     /*
     * function: aw_modify_bisbrand_query
     * 
     * Modifies main WP query if product bisbrand taxonomy page:
     *  1. Enables pagination
     * 
     * @param object $query
     * 
     */
    function aw_modify_bisbrand_query($query){
        if ( ! is_admin() && $query->is_main_query() && is_tax('product_bisbrand') ){
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query->set('paged', $paged);
        }
    }
    
}
?>