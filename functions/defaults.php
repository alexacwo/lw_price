<?php
// Set theme defaults
// 22 => 1.3.4
// 23 => 2.0
// 24 => 2.2
// 27 => 2.5
$compare_version = 27;
$compare_version_human = "v2.5";
$themename = "Compare";
$shortname = "tz";
$default_currency_labels 	= array('GBP', 'USD', 'EUR', 'INR'); // Default currency labels. Used when installing theme or resetting theme options.
$default_currency_options = "left, ".$default_currency_labels[0].", &pound;\nright, ".$default_currency_labels[1].", $\nleft, ".$default_currency_labels[2].", &euro;\nleft, ".$default_currency_labels[3].", &#8377;"; // Default currency options. Used when installing theme or resetting.
$currency_options = explode("\n", get_option($shortname.'_currencies')); // Real currency options in database.
$default_products_order_by = 'date_added'; 
$default_products_order = 'asc';		
if(get_current_blog_id() == 1){
        $uploadDirMerchantsAbsolute = ABSPATH.'wp-content/uploads/compare/merchants/';
        $uploadDirMerchants = '/wp-content/uploads/compare/merchants/';
} else {
        // For WP multisite installation
        $current_blog_id = get_current_blog_id();
        $uploadDirMerchantsAbsolute = ABSPATH.'wp-content/uploads/compare/merchants/'.$current_blog_id.'/';
        $uploadDirMerchants = '/wp-content/uploads/compare/merchants/'.$current_blog_id.'/';
}
?>