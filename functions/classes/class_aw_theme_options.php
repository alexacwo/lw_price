<?php
class aw_theme_options {
    
    function __construct(){
       $this->aw_add_actions();
       $this->aw_add_filters();
    }
   
    function aw_add_actions(){
        add_action('admin_menu', array( $this, 'add_theme_option_menu_item' )); 
        add_action('admin_init', array( $this, 'aw_process_theme_options' ));
        add_action('init', array( $this, 'aw_get_theme_options' ));
    }
      
    
    function aw_add_filters(){
        
    }
    
    function aw_process_theme_options() {
        
        global $themename, 
        $shortname, 
        $wpdb, 
        $default_currency_labels, 
        $default_currency_options,
        $default_products_order_by, 
        $default_products_order;
        
        $options = $this->aw_get_compare_theme_option_tabs();

        if ( isset($_GET['page']) && $_GET['page'] == 'theme-options.php' ) {

                $request_action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
          
                if ( 'save' == $request_action ) {
                    
                    $url = $_REQUEST['tz_selectedtab'];

                    if ($url == ''){
                        $url = 'themes.php?page=theme-options.php&saved=true&option_tab=1';
                    } else {                             
                        $t = substr($url, -1);
                        $url = 'themes.php?page=theme-options.php&saved=true&option_tab='.$t;
                    }

                   /* foreach ($options as $value) {
                        if( ! isset( $value['id'] ) || ! isset( $_REQUEST[$value['id']] ) ) continue;
                        if ( ($value['id'] != 'tz_logo_url') && ($value['id'] != 'tz_pattern_url') && ($value['id'] != 'tz_apple_touch_icon_url') && ($value['id'] != 'tz_favicon_url') && ($value['id'] != 'tz_thumbnail') && ($value['id'] != 'tz_thumbnail_wide') && ($value['id'] != 'tz_thumbnail_slider') ){
                            update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
                        }
                    } */
                    
                    foreach ($options as $value) {
                        if( ! isset( $value['id'] ) ) continue;
                        if ( ($value['id'] != 'tz_logo_url') && ($value['id'] != 'tz_pattern_url') && ($value['id'] != 'tz_apple_touch_icon_url') && ($value['id'] != 'tz_favicon_url') && ($value['id'] != 'tz_thumbnail') && ($value['id'] != 'tz_thumbnail_wide') && ($value['id'] != 'tz_thumbnail_slider') ){
                            if( isset( $_REQUEST[ $value['id'] ] ) ) { 
                                update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
                            } else { 
                                delete_option( $value['id'] );                                            
                            } 
                        }
                    }


                    // If files has been uploaded, move them to the /uploads dir and update the option value
                    if (  (isset($_FILES['tz_logo_url'])) && ($_FILES['tz_logo_url']['error'] == UPLOAD_ERR_OK)  ) {
                        $overrides = array('test_form' => false); 
                        $file = wp_handle_upload($_FILES['tz_logo_url'], $overrides);
                        $urlimage = $file['url'];
                        update_option('tz_logo_url', $urlimage);
                    }

                    if (  (isset($_FILES['tz_pattern_url'])) && ($_FILES['tz_pattern_url']['error'] == UPLOAD_ERR_OK)  ) {
                        $overrides = array('test_form' => false);	       
                        $file = wp_handle_upload($_FILES['tz_pattern_url'], $overrides);

                        $urlimage = $file['url'];
                        update_option('tz_pattern_url', $urlimage);
                    }

                    if (  (isset($_FILES['tz_favicon_url'])) && ($_FILES['tz_favicon_url']['error'] == UPLOAD_ERR_OK)  ) {
                        $overrides = array('test_form' => false); 
                        $file = wp_handle_upload($_FILES['tz_favicon_url'], $overrides);
                        $urlimage = $file['url'];

                        update_option('tz_favicon_url', $urlimage);
                    }
                    
                    if (  (isset($_FILES['tz_apple_touch_icon_url'])) && ($_FILES['tz_apple_touch_icon_url']['error'] == UPLOAD_ERR_OK)  ) {
                        $overrides = array('test_form' => false); 
                        $file = wp_handle_upload($_FILES['tz_apple_touch_icon_url'], $overrides);
                        $urlimage = $file['url'];

                        update_option('tz_apple_touch_icon_url', $urlimage);
                    }

                    if (  (isset($_FILES['tz_post_thumbnail_wide'])) && ($_FILES['tz_post_thumbnail_wide']['error'] == UPLOAD_ERR_OK)  ) {
                        $overrides = array('test_form' => false); 
                        $file = wp_handle_upload($_FILES['tz_post_thumbnail_wide'], $overrides);
                        $urlimage = $file['url'];
                        update_option('tz_post_thumbnail_wide', $urlimage);
                    }

                    if (  (isset($_FILES['tz_slide_thumbnail'])) && ($_FILES['tz_slide_thumbnail']['error'] == UPLOAD_ERR_OK)  ) {
                        $overrides = array('test_form' => false); 
                        $file = wp_handle_upload($_FILES['tz_slide_thumbnail'], $overrides);
                        $urlimage = $file['url'];
                        update_option('tz_slide_thumbnail', $urlimage);
                    }

                    if(isset($_REQUEST['tz_custom_products_slug'])) {
                        update_option('tz_custom_products_slug', compare_slugify($_REQUEST['tz_custom_products_slug']));
                    }

                    if(isset($_REQUEST['tz_custom_taxonomies_slug'])) {
                        update_option('tz_custom_taxonomies_slug', compare_slugify($_REQUEST['tz_custom_taxonomies_slug']));
                    }

                    if(isset($_REQUEST['tz_custom_taxonomies_bisbrand_slug'])) {
                        update_option('tz_custom_taxonomies_bisbrand_slug', compare_slugify($_REQUEST['tz_custom_taxonomies_bisbrand_slug']));
                    }

                    global $wp_rewrite;
                    $wp_rewrite->flush_rules();

                    header("Location: ".$url);
                    die;

                } else if( 'reset' == $request_action ) {
                    
                    $confirmButtonText = __('I confirm I want to reset', 'framework');
                    $confirmInfo1Text = __('I know this action cannot be reversed and that the blog layout might change.', 'framework');
                    $confirmInfo2Text = __('I have backed up my database and want to continue.', 'framework');
                    $confirmMessage = '<form method="post" action="">
                    <p class="submit">
                    <span style="display: block;float: left;font-weight: bold;margin-right: 15px;font-size: 1.12em;">' . $confirmInfo1Text . '
                    </br> ' . $confirmInfo2Text . '</span>
                            <input name="resetFactoryDefaultsConfirm" type="submit" class="button" value="' . $confirmButtonText . '" />
                            <input type="hidden" name="action" value="resetConfirm" />

                    </p>
                    </form>';

                    echo '<div id="confirmreset" class="error"><p><strong>'.$confirmMessage.'</strong></p></div>';

                } else if( 'resetConfirm' == $request_action ) {
                   
                    // Reset theme setings
                    $this->aw_reset_theme_settings($options);
                    
                    $this->aw_flush_rules();
                    
                    header("Location: themes.php?page=theme-options.php&reset=true");
                    die;

                } else if( 'resetFactoryDefaults' == $request_action ) {
                    $confirmButtonText = __('I confirm I want to reset', 'framework');
                    $confirmInfo1Text = __('I know this action cannot be reversed and that I might loose data that I have manually added to the blog.', 'framework');
                    $confirmInfo2Text = __('I have backed up my database and want to continue.', 'framework');
                    $confirmMessage = '<form method="post" action="">
                    <p class="submit">
                    <span style="display: block;float: left;font-weight: bold;margin-right: 15px;font-size: 1.12em;">' . $confirmInfo1Text . '
                    </br> ' . $confirmInfo2Text . '</span>
                            <input name="resetFactoryDefaultsConfirm" type="submit" class="button" value="' . $confirmButtonText . '" />
                            <input type="hidden" name="action" value="resetFactoryDefaultsConfirm" />

                    </p>
                    </form>';

                    echo '<div id="confirmreset" class="error"><p><strong>'.$confirmMessage.'</strong></p></div>';
                    
                } 
                else if('clearOrphanedProductDescriptions' == $request_action ) {   
                    $prefix = $wpdb->prefix;

                    //Get all the product descriptions which ar orphaned in the correct order
                    //Grouped in chunks by the product name with the the latest insert first
                    $q= "SELECT * 
                    FROM  ".$prefix."pc_products_custom
                    WHERE product_id NOT 
                    IN (
                        SELECT ID
                        FROM wp_posts
                    )
                    ORDER BY product_name asc, insertion_date desc
                    ";
                    $orphaned_custom_products = $wpdb->get_results($q, ARRAY_A);
                    
                    $current_orphaned_product_name = "";
                    //Loop through and delete all Orphaned products
                    foreach($orphaned_custom_products AS $orphaned_custom_product){
                        //If the orphaned product name currently set is the same as the one from the database then delete this in the database
                        //Unless the time inserted is 0, all these need to be left
                        if($current_orphaned_product_name == $orphaned_custom_product['product_name'])
                        {
                            $q= "DELETE FROM ".$prefix."pc_products_custom
                                WHERE product_name =  '" . $orphaned_custom_product['product_name'] . "'
                                AND product_description = '" . $orphaned_custom_product['product_description']. "'
                                AND insertion_date = '"  . $orphaned_custom_product['insertion_date']. "'
                                AND product_id =  '"  . $orphaned_custom_product['product_id']. "'
                                AND insertion_date > 0";
                                $wpdb->get_results($q);
                        }
                        
                        $current_orphaned_product_name = $orphaned_custom_product['product_name'];
                    }
                    $confirmMessage = __('Orphaned custom product data has been deleted', 'framework');
                    echo '<div id="confirmreset" class="updated settings-error" style="display: block;"><p><strong>'.$confirmMessage.'</strong></p></div>';

                }
                else if( 'resetFactoryDefaultsConfirm' == $request_action ) {                    
                  
                    $prefix = $wpdb->prefix;
                    
                    //Comments
                    $q= "SELECT com.comment_ID FROM ".$prefix."comments com INNER JOIN "
                    .$prefix."pc_products_relationships pr on pr.wp_post_id = com.comment_post_ID";
                    $comments = $wpdb->get_results($q, ARRAY_A);

                    foreach($comments AS $comment){
                        wp_delete_comment( $comment['comment_ID'], TRUE );
                    }
        
                    
                    //Posts meta
                    $q= "SELECT p_meta.post_id, p_meta.meta_key FROM ".$prefix."postmeta p_meta INNER JOIN ".$prefix."pc_products_relationships pr ON
                    pr.wp_post_id = p_meta.post_id";
                    $postmetas = $wpdb->get_results($q, ARRAY_A);

                    foreach($postmetas AS $postmeta){
                        delete_post_meta($postmeta['post_id'], $postmeta['meta_key']); 
                    }

                    //Posts
                    $q= "SELECT ID FROM ".$prefix."posts INNER JOIN ".$prefix."pc_products_relationships ON ".
                    $prefix."pc_products_relationships.wp_post_id = ".$prefix."posts.ID";
                    $product_posts = $wpdb->get_results($q, ARRAY_A);

                    foreach($product_posts AS $product_post){
                        wp_delete_post( $product_post['ID'], TRUE );
                    }

                    //Slides
                    $q = "SELECT ID FROM ".$prefix."posts WHERE post_type = 'slider'";
                    $slides = $wpdb->get_results($q, ARRAY_A);

                    foreach($slides AS $slide){
                            wp_delete_post( $slide['ID'], TRUE );
                    }

                    //Terms
                    if(function_exists('compare_remove_taxonomy_terms')){
                        compare_remove_taxonomy_terms('product_category');
                        compare_remove_taxonomy_terms('product_brand');
                        compare_remove_taxonomy_terms('product_bisbrand');
                    }		

                    //Terms Taxonomy         
                    $q = "DELETE FROM ".$prefix."term_taxonomy WHERE taxonomy = 'product_category' OR taxonomy = 'product_brand' OR taxonomy = 'product_bisbrand';";
                    $wpdb->get_results($q);
                    
                    //Products relationships
                    $q = "DELETE FROM ".$prefix."pc_products_relationships;";
                    $wpdb->get_results($q);

                    //Products merchants
                    $q = "DELETE FROM ".$prefix."pc_products_merchants;";
                    $wpdb->get_results($q);
                    
                    //products
                    $q = "DELETE FROM ".$prefix."pc_products;";
                    $wpdb->get_results($q);

                    //Options
                    $q = "DELETE FROM ".$prefix."pc_options;";
                    $wpdb->get_results($q);
                    
                    //Products Options
                    $q = "DELETE FROM ".$prefix."pc_products_custom;";
                    $wpdb->get_results($q);

                    //pc_category_mapping
                    $q = "DELETE FROM ".$prefix."pc_category_mapping;";
                    $wpdb->get_results($q);
                    
                    //pc_brand_mapping
                    $q = "DELETE FROM ".$prefix."pc_brand_mapping;";
                    $wpdb->get_results($q);

                    //pc_exclusions
                    $q = "DELETE FROM ".$prefix."pc_exclusions;";
                    $wpdb->get_results($q);

                    //pc_feeds
                    if(aw_is_compare_plus_installed()){
                        $q = "DELETE FROM ".$prefix."pc_feeds;";
                        $wpdb->get_results($q);
                    }

                    //pc_parsers
                    $q = "DELETE FROM ".$prefix."pc_parsers;";
                    $wpdb->get_results($q);
                    
                    //pc_products_feeds_relationships
                    if(aw_is_compare_plus_installed()){                        
                        $q = "DELETE FROM ".$prefix."pc_products_feeds_relationships;";
                        $wpdb->get_results($q);
                    }

                    //pc_products_raw
                    if(aw_is_compare_plus_installed()){ 
                        $q = "DELETE FROM ".$prefix."pc_products_raw;";
                        $wpdb->get_results($q);
                    }

                    //pc_product_mapping
                    if(aw_is_compare_plus_installed()){ 
                        $q = "DELETE FROM ".$prefix."pc_product_mapping;";
                        $wpdb->get_results($q);
                    }
                    
                    //pc_product_mapping
                    if(aw_is_compare_plus_installed()){ 
                        $q = "DELETE FROM ".$prefix."pc_brand_mapping;";
                        $wpdb->get_results($q);
                    }

                    //pc_product_original_retailer
                    if(aw_is_compare_plus_installed()){ 
                        $q = "DELETE FROM ".$prefix."pc_product_original_retailer;";
                        $wpdb->get_results($q);
                    }

                    //TD API options
                    delete_option( "tz_tradedoubler_token" );
                    delete_option( "tz_tradedoubler_currency" );
                    delete_option( "tz_tradedoubler_language" );

                    //Recreate the options                  
                    $wpdb->query("INSERT IGNORE INTO ".$wpdb->prefix."pc_options VALUES('force_import_categories', '0')");
                    $wpdb->query("INSERT IGNORE INTO ".$wpdb->prefix."pc_options VALUES('force_import_products', '0')");
                    $wpdb->query("INSERT IGNORE INTO ".$wpdb->prefix."pc_options VALUES('cron_during_night', '1')");
                    $wpdb->query("INSERT IGNORE INTO ".$wpdb->prefix."pc_options VALUES('cron_during_night_start', '0')");
                    $wpdb->query("INSERT IGNORE INTO ".$wpdb->prefix."pc_options VALUES('cron_during_night_end', '7')");
                    $wpdb->query("INSERT IGNORE INTO ".$wpdb->prefix."pc_options VALUES('map_on_name', '1')");
                    
                    /*Add back in the default parsers*/
                    if(aw_is_compare_plus_installed()){ 
                        $table_name = $wpdb->prefix."pc_parsers";
                        $td_xml = 0;
                        $td_csv = 0;
                        $q = "SELECT featured_network FROM ".$table_name." WHERE featured_network != 0 GROUP BY featured_network;";
                        $results = $wpdb->get_results($q);
                        foreach($results AS $result){
                            if($result->featured_network == '1') $td_xml = 1;
                            if($result->featured_network == '2') $td_csv = 1;
                        }
                        if($td_xml === 0) {
                            $sql = "INSERT IGNORE INTO ".$table_name." (`id`, `network_name`, `parser_format`, `feed_root`, `product_ean`, `product_name`, `product_category`, `product_brand`, `product_description`, `product_image`, `product_price`, `product_price_was`, `product_last_updated_feed_level`, `product_mature_content_rating`, `product_stock`, `product_gender`, `product_color`, `product_size`, `product_shipping`, `product_deeplink`, `featured_network`) VALUES
                                    ('', 'TradeDoubler XML parser', 'xml', 'product', 'ean', 'name', 'merchantCategoryName', 'brand', 'description', 'imageUrl', 'price', 'previousPrice', '', '', '', '', '', '', 'shippingCost', 'productUrl', '1');";
                            $wpdb->query($sql);		
                        }
                        if($td_csv === 0) {
                            $sql = "INSERT IGNORE  INTO ".$table_name." (`id`, `network_name`, `parser_format`, `feed_root`, `product_ean`, `product_name`, `product_category`, `product_brand`, `product_description`, `product_image`, `product_price`, `product_price_was`, `product_last_updated_feed_level`, `product_mature_content_rating`, `product_stock`, `product_gender`, `product_color`, `product_size`, `product_shipping`, `product_deeplink`, `featured_network`) VALUES
                                    ('', 'TradeDoubler CSV parser', 'csv', '', 'ean', 'name', 'merchantCategoryName', 'brand', 'description', 'imageUrl', 'price', 'previousPrice', '', '', '', '', '', '', 'shippingCost', 'productUrl', '2');";
                            $wpdb->query($sql);		
                        }
                    }
                    /*END: Add back in the default parsers*/

                    /* Reset theme settings */
                    $this->aw_reset_theme_settings($options);

                    /* EO Reset theme settings */

                    $this->aw_flush_rules();

                    $confirmMessage = __('Settings have been reset to factory settings', 'framework');
                    echo '<div id="confirmreset" class="updated settings-error" style="display: block;"><p><strong>'.$confirmMessage.'</strong></p></div>';
                }
                else if( 'installDemoSettings' == $request_action ) { 
                    //Add a home page
                    
                    //Add a blog page
                    
                    //Change the reading settings

                    //Add a top menu
                    
                    //Add a middle menu
                    
                    //Add a couple of slides
                    
                    //Add a couple of cagegogores
                    
                    //Add a couple of brands
                    
                    //Add a couple of products
                    
                    //Set the read
                    
                }
        }
        
    }
    
    /**
     * 
     * method: aw_get_theme_options
     * 
     * Used to initialize theme options. 
     *  
     * @return array
     * 
     */
     function aw_get_theme_options(){        
        global $aw_theme_options;        
        $options = $this->aw_get_compare_theme_option_tabs();
        foreach ($options as $value) {
            if(!isset($value['id'])) continue;
            if (get_option( $value['id'] ) === FALSE) {
               $aw_theme_options[$value['id']] = (isset($value['std']) ? $value['std'] : null);             
            } else { 
               $aw_theme_options[$value['id']] = get_option( $value['id'] );         
            }    
        }
         
    }
    
    function add_theme_option_menu_item(){
        global $themename;
        add_theme_page($themename." Options", "Theme Options", 'edit_pages', 'theme-options.php', array ( $this, 'aw_mytheme_admin' ) );
    }
    
    
    function aw_mytheme_admin(){
        global $themename, $shortname;
        $options = $this->aw_get_compare_theme_option_tabs();
        include( AW_ROOT_PATH . '/functions/views/admin/theme-options.php');
    }
    
    protected function aw_reset_theme_settings($options = array()){
        
        global $default_currency_options,
        $default_products_order_by, 
        $default_products_order;
        
        if( empty($options) ){
            $options  = $this->aw_get_compare_theme_option_tabs();
        }
        
        foreach ($options as $value) {
            if(isset($value['id'])){
                    delete_option( $value['id'] );
            }
        }
        
        update_option('tz_currency', '0'); // sets to GBP because  $default_currency_options[0] == {GBP currency details}
        update_option('tz_currencies', $default_currency_options);
        update_option('tz_products_order_by', $default_products_order_by);
        update_option('tz_products_order', $default_products_order);
        
    }
    
    protected function aw_flush_rules(){
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
    
    /**
     *  
     * protected method: aw_get_compare_theme_option_tabs
     * 
     * Sets an array with theme options's prototype including including theme option field descriptiors
     * 
     * @since v1.3.3
     * 
     * @global sting $shortname
     * @global array $currency_options
     * @global array $default_currency_options
     * @return array
     * 
     */
    protected function aw_get_compare_theme_option_tabs(){
        
	global $shortname, $currency_options, $default_currency_options;
	
        $arr =  array (		
			array(	"name" => __("selected", 'framework'),
			"id" => $shortname."_selectedtab",
			"std" => "",
			"type" => "hidden"),

			array(	"type" => "opentab"),
			
			array(	"type" => "open"),
			
			array(	"name" => __('Logo and Favicon Settings', 'framework'),
			"id" => $shortname."_logo_settings",
			"type" => "title"),
			
			array(	"name" => __("Upload Logo", 'framework'),
			"desc" => '',
			"id" => $shortname."_logo_url",
			"std" => "",
			"type" => "file",
                        "subtype" => "image"),
			
			array(	"name" => __("Enable plain text logo",'framework'),
			"desc" => __("Check this box to use a plain text logo rather than an image. Info will be taken from your WordPress settings.", 'framework'),
			"id" => $shortname."_plain_logo",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Upload Favicon", 'framework'),
			"desc" => '',
			"id" => $shortname."_favicon_url",
			"std" => get_template_directory_uri()."/favicon.ico",
			"type" => "file",
                        "subtype" => "image"),
            
                        array(	"name" => __("Upload Apple Touch Icon (.png)", 'framework'),
			"desc" => '',
			"id" => $shortname."_apple_touch_icon_url",
			"std" => get_template_directory_uri()."/apple-touch-icon.png",
			"type" => "file",
                        "subtype" => "image"),		
			
			array(	"type" => "close"),
			
			array(	"type" => "open"),
			
			array(	"name" => __("Analytics Settings", 'framework'),
			"id" => $shortname."_analytics",
			"type" => "title"),
			
			array(	"name" => __("Google Analytics Code", 'framework'),
			"desc" => __("Enter your full Google Analytics code (or any other site tracking code) here. It will be inserted before the closing head tag.", 'framework'),
			"id" => $shortname."_g_analytics",
			"std" => "",
			"type" => "textarea"),
			
			array(	"type" => "close"),

            array(  "type" => "open"),
            
            array(  "name" => __("AdSense Settings", 'framework'),
            "id" => $shortname."_adsense",
            "type" => "title"),
            
            array(  "name" => __("Enable ad sense for products with no retailers",'framework'),
            "desc" => __("Check this box to use google ad sense on products with no retailers.", 'framework'),
            "id" => $shortname."_enable_adsense",
            "std" => "false",
            "type" => "checkbox"),

            array(  "name" => __("Google AdSense Client ID", 'framework'),
            "desc" => __("Enter your ad sense client id.", 'framework'),
            "id" => $shortname."_g_adsense_client_id",
            "std" => "",
            "type" => "text"),
            
            array(  "type" => "close"),
			
			array(	"type" => "open"),
			
			array(	"name" => __('Footer advertising area', 'framework'),
			"id" => $shortname."_header",
			"type" => "title"),
			
			array(	"name" => __("Leaderboard", 'framework'),
			"desc" => __("Enter your leaderboard ad code.", 'framework'),
			"id" => $shortname."_header_leaderboard",
			"std" => "",
			"type" => "textarea"),
				
			array(	"type" => "close"),
				
			array(	"type" => "closetab"),
			
			array(	"type" => "opentab"),
			
			array(	"type" => "open"),
			
			array(	"name" => __('Price Comparison', 'framework'),
			"id" => $shortname."_compare_opt",
			"type" => "title"),
			
			array(	"name" => __("Currency Options", 'framework'),
			"desc" => __("Paste each currency you wish to use in this text area. Please use a new line for each currency and use the example format ([Left|Right], Code, Symbol, Decimal Places, [Comma Format]): Left, USD, $, 2, [3,3,3,3]", 'framework'),
			"id" => $shortname."_currencies",
			"std" => $default_currency_options,
			"type" => "textarea"),
				
			array(	"name" => __("Display Currency",'framework'),
			"id" => $shortname."_currency",
			"std" => "GBP",
			"type" => "select",
			"options" => $currency_options),
			
			array(	"name" => __("Buy button text", 'framework'),
			"desc" => __("Text used on the button in the price comparison table", 'framework'),
			"id" => $shortname."_buy_button_text",
			"std" => "BUY NOW",
			"type" => "text"),
			
			array(	"name" => __("Date format", 'framework'),
			"desc" => 'Used for the last update date displayed in the price comparison table. <a target="_blank" href="http://codex.wordpress.org/Formatting_Date_and_Time">'.__("Documentation on date and time formatting", 'framework').'</a>',
			"id" => $shortname."_date_format",
			"std" => "d-m-Y H:i",
			"type" => "text"),
			
			array(	"type" => "close"),
				
			array(	"type" => "closetab"),
			
			array(	"type" => "opentab"),
			
			array(	"type" => "open"),
			
			array(	"name" => __('Main menu', 'framework'),
			"id" => $shortname."_colour1_settings",
			"type" => "title"),
			
			array(	"name" => __("Menu elements", 'framework'),
			"id" => $shortname."_colorset1_c1",
			"std" => "#3b6ab1",
			"type" => "text"),
            
                        array(	"name" => __("Submenu elements",'framework'),
			"id" => $shortname."_colorset1_c2",
			"std" => "#204885",
			"type" => "text"),
			
			array(	"type" => "close"),
			
			array(	"type" => "open"),
			
			array(	"name" => __('Gradient #1 Settings', 'framework'),
			"id" => $shortname."_colour2_settings",
			"type" => "title"),
			
			array(	"name" => __("Color #1", 'framework'),
			"id" => $shortname."_colorset2_c1",
			"std" => "#497ac3",
			"type" => "text"),
			
			array(	"name" => __("Color #2",'framework'),
			"id" => $shortname."_colorset2_c2",
			"std" => "#bcd5fa",
			"type" => "text"),
					
			array(	"type" => "close"),
            
            		array(	"type" => "open"),
			
			array(	"name" => __('Gradient #2 Settings', 'framework'),
			"id" => $shortname."_colour3_settings",
			"type" => "title"),
			
			array(	"name" => __("Color #1", 'framework'),
			"id" => $shortname."_colorset3_c1",
			"std" => "#ff8c21",
			"type" => "text"),
            
                        array(	"name" => __("Color #2", 'framework'),
			"id" => $shortname."_colorset3_c2",
			"std" => "#ff993b",
			"type" => "text"),
							
			array(	"type" => "close"),
            
                        array(	"type" => "open"),
			
			array(	"name" => __('Footer', 'framework'),
			"id" => $shortname."_colour4_settings",
			"type" => "title"),
			
			array(	"name" => __("Footer Background", 'framework'),
			"id" => $shortname."_colorset4_c1",
			"std" => "#23272a",
			"type" => "text"),
            
                        array(	"name" => __("Footer Header", 'framework'),
			"id" => $shortname."_colorset4_c2",
			"std" => "#83929b",
			"type" => "text"),
            
                        array(	"name" => __("Footer Text", 'framework'),
			"id" => $shortname."_colorset4_c3",
			"std" => "#c8ced2",
			"type" => "text"),
							
			array(	"type" => "close"),
            
                        array(	"type" => "open"),
			
			array(	"name" => __('Background', 'framework'),
			"id" => $shortname."_colour4_settings",
			"type" => "title"),
			
			array(	"name" => __("Background", 'framework'),
			"id" => $shortname."_colorset5_c1",
			"std" => "#e9eef2",
			"type" => "text"),            
							
			array(	"type" => "close"),
			
			array(	"type" => "closetab"),
					
			array(	"type" => "opentab"),
            
                        array(	"type" => "open"),
				
			array("id" => $shortname."_result_display",
                        "name" => "Result layout",
			"type" => "title"),

			array(	"name" => __("Default result layout style", 'framework'),			
			"desc" => __('Choose between grid or list .','framework'),			
			"id" => $shortname."_result_display_style",			
			"std" => "grid",			
			"type" => "select",			
			"options" => array("grid" => __("Grid Layout",'framework'), "list" => __("List Layout",'framework'))
                        ),			
				
			array(	"type" => "close"),
			
			array(	"type" => "open"),			
			array(	"name" => __("Product detail page", 'framework'),
			"id" => $shortname."_product_detail_page",
			"type" => "title"),
            
                        array(	"name" => __("Check this box to force show comparison table on product page even if there is just one retailer", 'framework'),
			"id" => $shortname."_force_show_price_table",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Check this box to open product deeplinks in the same window or leave it unchecked to open deeplink in new window", 'framework'),
			"id" => $shortname."_price_comparison_table_open_deeplink_in_same_window",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Check this box to disallow shortcodes in product description", 'framework'),
			"id" => $shortname."_hide_shortcodes_in_product_description",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Check this box to disallow html tags in product description", 'framework'),
			"id" => $shortname."_hide_html_tags_in_product_description",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Check this box to disallow reviews for products", 'framework'),
			"id" => $shortname."_hide_product_comments",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Check this box to disallow related products", 'framework'),
			"id" => $shortname."_hide_related_products",
			"std" => "false",
			"type" => "checkbox"),
			
			array(	"name" => __("Number of related products to display", 'framework'),
			"id" => $shortname."_number_related_products",
			"std" => "3",
			"type" => "text",
			"class" => "number_related_products"),	
				
			array(	"type" => "close"),
			
			array(	"type" => "open"),
			
			array(	"name" => __("Products", 'framework'),
			"id" => $shortname."_posts_and_products",
			"type" => "title"),
			
			array(	"name" => __("Categories URL slug", 'framework'),
			"desc" => __('Categories URL slug MUST BE different from product URL slug.','framework').' <a href="options-permalink.php" target="_blank">'.__('Visit this page after any change','framework').'</a>',
			"id" => $shortname."_custom_taxonomies_slug",
			"std" => "products",
			"type" => "text"),
			
			array(	"name" => __("Brands URL slug", 'framework'),
			"desc" => __('Brands URL slug MUST BE different from product URL slug.','framework').' <a href="options-permalink.php" target="_blank">'.__('Visit this page after any change','framework').'</a>',
			"id" => $shortname."_custom_taxonomies_bisbrand_slug",
			"std" => "brands",
			"type" => "text"),
			
			array(	"name" => __("Product URL slug", 'framework'),
			"desc" => __('Product URL slug MUST BE different from categories and brands URL slug.','framework').' <a href="options-permalink.php" target="_blank">'.__('Visit this page after any change','framework').'</a>',
			"id" => $shortname."_custom_products_slug",
			"std" => "product",
			"type" => "text"),
			
			array(	"name" => __("Allow short product URLs",'framework'),
			"desc" => __('Allow Compare to have short URLs instead of the /%category%/%brand%/%product_name%/ pattern','framework').' <a href="options-permalink.php" target="_blank">'.__('Visit this page after any change','framework').'</a>',
			"id" => $shortname."_products_short_url",
			"std" => 0,
			"type" => "select",
			"options" => array("1" => __("YES",'framework'), "0" => __("NO",'framework'))),
			
			array(	"name" => __("Products per comparison table page", 'framework'),
			"desc" => '',
			"id" => $shortname."_products_per_page",
			"std" => "20",
			"type" => "text"),
			
                        array(	"name" => __("Default product order by",'framework'),
			"desc" => __('Which the field products should be sorted by, by default, users may select their own sorting afterwards.','framework'),			
			"id" => $shortname."_products_order_by",			
			"std" => "date_added",			
			"type" => "select",			
			"options" => array("min_price" => __("Minimum Price",'framework'), "title" => __("Title",'framework'), "date_added" =>__("Date added","framework"))),
			
            
                        array(	"name" => __("Default product order",'framework'),			
			"desc" => __('Ascending or Descending.','framework'),			
			"id" => $shortname."_products_order",			
			"std" => "asc",			
			"type" => "select",			
			"options" => array("asc" => __("Ascending",'framework'), "desc" => __("Descending",'framework'))),			
			
                        array(	"type" => "close"),
				
			array(	"type" => "closetab"),
            
                        array(	"type" => "opentab"),
			
			array(	"type" => "open"),
			
			array(	"name" => __('Social Network URLs', 'framework'),
			"id" => $shortname."_social_opt",
			"type" => "title"),
			
			array(	"name" => __("Twitter", 'framework'),
			"desc" => __("Please enter your Twitter public profile URL", 'framework'),
			"id" => $shortname."_social_twitter",
			"std" => 'https://twitter.com/awesemthemes',
			"type" => "text"),
				
			array(	"name" => __("Facebook", 'framework'),
			"desc" => __("Please enter your Facebook public profile URL", 'framework'),
			"id" => $shortname."_social_facebook",
			"std" => 'https://www.facebook.com/awesemltd',
			"type" => "text"),
            
                        array(	"name" => __("RSS", 'framework'),
			"desc" => __("Please enter your RSS feed URL", 'framework'),
			"id" => $shortname."_social_rss",
			"std" => '',
			"type" => "text"),
            
                        array(	"name" => __("Tumblr", 'framework'),
			"desc" => __("Please enter your Tumblr public profile URL", 'framework'),
			"id" => $shortname."_social_tumblr",
			"std" => '',
			"type" => "text"),
            
                        array(	"name" => __("Pinterest", 'framework'),
			"desc" => __("Please enter your Pinterest public profile URL", 'framework'),
			"id" => $shortname."_social_pinterest",
			"std" => '',
			"type" => "text"),
			
			array(	"type" => "close"),
				
			array(	"type" => "closetab"),
			
	);
	
	return $arr;	
    }
    
}
?>
