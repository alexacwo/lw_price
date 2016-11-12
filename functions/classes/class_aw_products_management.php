<?php
class aw_products_merchants_management {
    
    function __construct(){
       $this->aw_add_actions();
       $this->aw_add_filters();
    }
    
    function aw_create_menu_items(){
        $icon_url = get_stylesheet_directory_uri().'/img/admin/admin-products.png';
        $position = 30;
        add_menu_page( 'Compare', 'Compare', 'edit_posts', 'compare', array( $this, 'c_compare_home' ), $icon_url, $position );
        add_submenu_page( 'compare', __('Feed management','framework'), __('Manage Feed','framework'), 'edit_posts', 'products_feed', array( $this, 'c_feed_management' ) );
        add_submenu_page( 'compare', __('Product management','framework'), __('Manage Products','framework'), 'edit_posts', 'products_management', array( $this, 'c_products_management' ) );
        add_submenu_page( 'compare', __('Retailer management','framework'), __('Manage Retailers','framework'), 'edit_posts', 'merchants_management', array( $this, 'c_merchants_management' ) );        
    }
   
    function aw_add_actions(){
        add_action('admin_menu', array( $this, 'aw_create_menu_items' )); 
    }
      
    
    function aw_add_filters(){
        
    }
    
    function c_compare_home() {
        include( AW_ROOT_PATH . '/functions/views/admin/home.php');
    }



    function c_feed_management() {
       include( AW_ROOT_PATH . '/functions/views/admin/products_feed.php');
    }



    function c_products_management() {
       include( AW_ROOT_PATH . '/functions/views/admin/products_management.php');
    }



    function c_merchants_management() {
        include( AW_ROOT_PATH . '/functions/views/admin/merchants_management.php');
    }

    
}
?>
