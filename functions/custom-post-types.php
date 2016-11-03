<?php 
require_once 'classes/custom_post_types/class_aw_products_post_type.php';
require_once 'classes/custom_post_types/class_aw_slides_post_type.php';

add_action('init', 'aw_init_post_types');
function aw_init_post_types(){    
   
    if(class_exists('aw_products_post_type')){
       $products_post_type = new aw_products_post_type;
   }
   
   if(class_exists('aw_slides_post_type')){
       $slides_post_type = new aw_slides_post_type;
   }

}
?>