<?php 
require_once 'classes/custom_taxonomies/class_aw_product_brand_taxonomy.php';
require_once 'classes/custom_taxonomies/class_aw_product_bisbrand_taxonomy.php';
require_once 'classes/custom_taxonomies/class_aw_product_category_taxonomy.php';

add_action('init', 'aw_init_taxonomies');
function aw_init_taxonomies(){    
   
    if(class_exists('aw_product_brand_taxonomy')){
       $aw_brand_taxonomy = new aw_product_brand_taxonomy();
      // $aw_brand_taxonomy->aw_register_taxonomy();
    }
   
   if(class_exists('aw_product_bisbrand_taxonomy')){
       $aw_bisrand_taxonomy = new aw_product_bisbrand_taxonomy;
   }
   
   if(class_exists('aw_product_category_taxonomy')){
       $aw_category_taxonomy = new aw_product_category_taxonomy;
   }


}
?>