<?php
class aw_slides_post_type {
    
    function __construct(){
       $this->aw_register_post_type();
       $this->aw_add_post_type_actions();
       $this->aw_add_post_type_filters();
    }
    
    public function aw_register_post_type(){        
        register_post_type('slider', array(
            'label' => 'Slider',
            'description' => '',
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'slider', 'with_front' => true),
            'query_var' => true,
            'exclude_from_search' => true,
            'menu_position' => '5',
            'supports' => array('title','excerpt','thumbnail','page-attributes'),
            'labels' => array (
              'name' => 'Slider',
              'singular_name' => 'Slide',
              'menu_name' => 'Slider',
              'add_new' => 'Add Slide',
              'add_new_item' => 'Add New Slide',
              'edit' => 'Edit',
              'edit_item' => 'Edit Slide',
              'new_item' => 'New Slide',
              'view' => 'View Slide',
              'view_item' => 'View Slide',
              'search_items' => 'Search Slider',
              'not_found' => 'No Slider Found',
              'not_found_in_trash' => 'No Slider Found in Trash',
              'parent' => 'Parent Slide',
            )
        )); 
    }
    
    function aw_add_post_type_actions(){
      // do nothing
    }
    
    function aw_add_post_type_filters(){
      // do nothing
    }  
       
}
?>