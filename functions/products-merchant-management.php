<?php 
require_once 'classes/class_aw_products_management.php';

add_action('init', 'aw_init_products_merchants_management');
function aw_init_products_merchants_management(){    
   
    if(class_exists('aw_products_merchants_management')){
       $products_merchants_management = new aw_products_merchants_management;
   }

}
?>