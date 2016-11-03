<?php
class aw_theme_installation {
    
    function __construct(){
       $this->aw_add_actions();
       $this->aw_add_filters();
    }
   
    function aw_add_actions(){
        add_action('admin_init', array( $this, 'aw_check_compare_version' )); 
    }
      
    
    function aw_add_filters(){
        
    }
    
    /**
     * 
     * function: aw_check_compare_version
     * 
     * Compares current file version against the one in Db ad depending on result decides wether to run inctall poccess
     * 
     * @global type $compare_version
     * @global type $wpdb
     * 
     */
    function aw_check_compare_version(){
        global $compare_version, $wpdb;
        if(get_option('compare_version') < $compare_version) 
            $this->aw_compare_do_install();
            flush_rewrite_rules();
    }
    
    /**
     * 
     * protected function: aw_compare_do_install
     * 
     * Installs the theme, creates tables and ensures that all settings ar up to date.
     * 
     * @global type $wpdb
     * @global type $compare_version
     * @global type $compare_version_human
     * @global type $default_currency_labels
     * @global type $default_currency_options
     * @global type $default_products_order
     * @global type $default_products_order_by
     * 
     */
    protected function aw_compare_do_install() {
        global $wpdb, 
	$compare_version,
	$compare_version_human,
	$default_currency_labels,
	$default_currency_options,
	$default_products_order,
	$default_products_order_by;
        
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
        // products_relationships
	$sql = $this->aw_create_mysql_query_string("pc_products_relationships", $wpdb->prefix);
	dbDelta($sql);
        
	// products_merchants
	$sql = $this->aw_create_mysql_query_string("pc_products_merchants", $wpdb->prefix);
	dbDelta($sql);
        
	// products
	$sql = $this->aw_create_mysql_query_string("pc_products", $wpdb->prefix);
	dbDelta($sql);
        
	//Remove duplicates, bugfix introduced in Compare theme v1.3.1
	$q = "SELECT count(id_product) AS num_products, id_product, id_merchant, max(last_update) AS last_updated FROM ".$wpdb->prefix."pc_products GROUP BY id_product, id_merchant HAVING  num_products > 1";
	$products = $wpdb->get_results($q);
	foreach ($products AS $product ) {
		$q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$product->id_product."' AND id_merchant = '".$product->id_merchant."' AND last_update != '".$product->last_updated ."'";	
		$response = $wpdb->query($q);
		if($response === 0) {
			$limit = $product->num_products - 1;
			$q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$product->id_product."' AND id_merchant = '".$product->id_merchant."' LIMIT ".$limit ;
			$response = $wpdb->query($q);
		}
	}
	
	// options
	$sql = $this->aw_create_mysql_query_string("pc_options", $wpdb->prefix);
	dbDelta($sql);
        
	// cutom fields per product 
	$sql = $this->aw_create_mysql_query_string("pc_products_custom", $wpdb->prefix);
	$response = dbDelta($sql);
	
	// open comments to all products > controlled also in theme options
	$wpdb->query("UPDATE ".$wpdb->prefix."posts SET comment_status = 'open' WHERE post_type = 'product'");
	
	// alter collation and charset if used older version of Compare
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products_relationships` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products_merchants` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"; 
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_options` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"; 
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products_custom` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$wpdb->query($sql);
	
	// alter engine if used older version of Compare
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products_relationships` ENGINE=MyISAM";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products_merchants` ENGINE=MyISAM";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products` ENGINE=MyISAM"; 
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_options` ENGINE=MyISAM"; 
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products_custom` ENGINE=MyISAM";
	$wpdb->query($sql);
	
	// alter primary key if used older version of Compare
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products` DROP PRIMARY KEY";
	$wpdb->query($sql);
	
	$sql = "ALTER TABLE `" . $wpdb->prefix . "pc_products` ADD PRIMARY KEY  (`id_product`,`id_merchant`(30),`feed_product_name`(100))";
	$wpdb->query($sql);
	
	
	$sql = "SELECT option_name FROM `" . $wpdb->prefix . "options` WHERE option_name = 'tz_hide_product_comments'";
	$option_tz_hide_product_comments = $wpdb->get_row($sql);
	if(( isset($option_tz_hide_product_comments->option_name) && $option_tz_hide_product_comments->option_name == 'tz_hide_product_comments' ) || $compare_version == 15){ // do not overwrite if option exists
		update_option('tz_hide_product_comments', 'true');
	}	
	
	update_option('compare_version', $compare_version);
	
	update_option('compare_version_human', $compare_version_human);
	
	// Product relationships out of date
	$q = "DELETE pr FROM ".$wpdb->prefix."pc_products_relationships pr 
			LEFT OUTER JOIN  ".$wpdb->prefix."pc_products p ON (pr.id_product = p.id_product) 
			WHERE p.id_product IS NULL";
	$wpdb->query($q);
	
	// Delete zombies
	$q = "SELECT p.ID FROM ".$wpdb->prefix."posts p 
	LEFT OUTER JOIN ".$wpdb->prefix."pc_products_relationships pr ON (p.ID = pr.wp_post_id) 
	WHERE pr.wp_post_id IS NULL AND post_type = 'product'";
	$zombies = $wpdb->get_results($q);
	foreach($zombies as $zombie) {
		wp_delete_post($zombie->ID,true);
	}
	
	//taxonomy-term count update (objects per taxonomy+term id)
	$q = "SELECT count(p.ID) AS count, tr.term_taxonomy_id  FROM ".$wpdb->prefix."posts p 
			INNER JOIN ".$wpdb->prefix."term_relationships tr ON (p.ID = tr.object_id) 
			WHERE p.post_status = 'publish' AND p.post_type = 'product' 
			GROUP BY tr.term_taxonomy_id";
	$object_count = $wpdb->get_results($q);
	foreach ($object_count AS $object){
		$q = "UPDATE ".$wpdb->prefix."term_taxonomy SET count = ".$object->count." WHERE term_taxonomy_id = ".$object->term_taxonomy_id;
		$wpdb->query($q);
	}
        
	//taxonomy-term count update - those where no object in post table
	$q = "UPDATE ".$wpdb->prefix."term_taxonomy tt LEFT OUTER JOIN ".$wpdb->prefix."term_relationships tr ON (tr.term_taxonomy_id = tt.term_taxonomy_id ) SET tt.count = 0 WHERE tr.term_taxonomy_id IS NULL AND tt.count > 0";
	$wpdb->query($q);
	
	
	// Add default products order and order by if not set already
	$products_order = trim(get_option('tz_products_order'));
	
	$products_order_by = trim(get_option('tz_products_order_by'));
	
	if($products_order == ''){ // If option not in db	
		update_option('tz_products_order', $default_products_order); // Set the default ordering	
	}
	
	if($products_order_by  == ''){ // If option not in db	
		update_option('tz_products_order_by', $default_products_order_by); // Set the default ordering	
	}
	
	
	//$default_currency_labels 	= array('GBP', 'USD', 'EUR', 'INR'); // Now in defaults.php
	//$default_currency_options = "left, ".$default_currency_labels[0].", &pound;\nright, ".$default_currency_labels[1].", $\nleft, ".$default_currency_labels[2].", &euro;\nleft, ".$default_currency_labels[3].", &#8377;"; // Now in functions.php
	$currency_old = trim(get_option('tz_currency'));
	
	$currencies_old = trim(get_option('tz_currencies'));
	
	$updated_tz_currency = false;
	
	if($currency_old == ''){            
		//replacing old theme version where tz_currency does not exist OR fresh Compare install (no previous versions to replace)
            
		if($currencies_old == ''){	
			//print_r("TC1");
			//fresh install OR replacing older theme where tz_currencies is not introduced yet
			update_option('tz_currency', '0'); // sets to GBP because  $default_currency_options[0] == {GBP currency details}
			update_option('tz_currencies', $default_currency_options);
			
		} else {
			//print_r("TC2");
			// replace older version of Compare theme
			$currency_options = explode("\n", $currencies_old);			
			
			foreach ($currency_options AS $key => $currency_option) {			
				$currencyExplode = explode(",", $currency_option); 				
				if(isset($currencyExplode[1]) && trim($currencyExplode[1]) == 'GBP') {					
					update_option('tz_currency', $key);
					$updated_tz_currency = true;
					break;					
				}			
			} // EO foreach
			
			if(!$updated_tz_currency) {
				//if GBP not set in currencies options
				update_option('tz_currency', 999999999); // so it will most likely not show any currency unles user has got 999999999 or more currencies :)				
			}
			
		}
	} else {
		if(is_numeric($currency_old)){
			//print_r("TC6 or TC4");
			//is version where option tz_currencies already exists
			//do nothing		
		} else {			
			if($currencies_old != ""){
				//print_r("TC5");
				// moste likely this situation is impossible, but just in case
				$currency_options 	= explode("\n", $currencies_old);
				
				foreach ($currency_options AS $key => $currency_option) {
				
					$currencyExplode = explode(",", $currency_option); 
					
					if(isset($currencyExplode[1]) && trim($currencyExplode[1]) == trim($currency_old)) {
						
						update_option('tz_currency', $key);
						
						$updated_tz_currency = true;
						
						break;
						
					}
				
				} // EO foreach
				
				if(!$updated_tz_currency) {
				//if currency not found in array
					update_option('tz_currency', 999999999); // so it will most likely not show any currency unles user has got 1000000000 currencies :)
				
				}
			
			} else {
				//print_r("TC3");
				// replacing older theme			
				
				foreach ($default_currency_labels AS $key => $currency_option) {				
					
					if($currency_option == trim($currency_old)) {
						
						update_option('tz_currency', $key);	
						
						$updated_tz_currency = true;
						
						break;
						
					}
				
				} // EO foreach
				
				if(!$updated_tz_currency) {
				//if currency not found in array
					update_option('tz_currency', 999999999); // so it will most likely not show any currency unles user has got 999999999 or more currencies :)
				
				}
				
				update_option('tz_currencies', $default_currency_options);
			
			}
			
		}	
	}
    }
    
    /**
     * protected function: aw_create_mysql_query_string
     * 
     * Creates query strings for installation process and returns them back
     *  
     * @param string $table_name
     * @param string $table_prefix
     * @return string
     */
    protected function aw_create_mysql_query_string($table_name, $table_prefix){
	$query = "";
	switch($table_name){
		case "pc_products_relationships": 
			$table_name = $table_prefix."pc_products_relationships";
                        $query = "CREATE TABLE `".$table_name."` (
                            `id_product` bigint(20) NOT NULL auto_increment,
                            `wp_post_id` bigint(20) NOT NULL default '0',
                            `product_ean` varchar(255) NOT NULL default '',
                            `product_name` varchar(255) NOT NULL default '',
                            `last_update` int(11) NOT NULL default '0',
                            `display_order` int(11) NOT NULL default '9999999',
                            `base_feed_import` int(1) NOT NULL default '0',
                            PRIMARY KEY  (`id_product`),
                            KEY `wp_post_id` (`wp_post_id`)
                            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
			break;
			
		case "pc_products_merchants":
			$table_name = $table_prefix."pc_products_merchants";
			$query = "CREATE TABLE `".$table_name."` (
				`slug` varchar(255) NOT NULL default '',
				`name` varchar(255) NOT NULL default '',
				`url` varchar(255) NOT NULL default '',
				`image` varchar(255) NOT NULL default '',
				`feed` int(11) NOT NULL default '0',
				UNIQUE KEY `slug` (`slug`)
			) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
			break;
		
		case "pc_products":
			$table_name = $table_prefix."pc_products";
			$query = "CREATE TABLE `".$table_name."` (
				`id_product` bigint(20) NOT NULL default '0',
				`id_merchant` varchar(255) NOT NULL default '',
				`feed_product_name` varchar(255) NOT NULL default '',
				`feed_product_desc` text,
				`feed_product_image` varchar(255) NOT NULL default '',
				`price` double NOT NULL default '0',
				`deeplink` text,
				`shipping` varchar(255) NOT NULL default '',
				`voucher` varchar(255) NOT NULL default '',
  				`ean` varchar(255) NOT NULL default '',
  				`price_was` varchar(255) NOT NULL default '',
				`last_updated_feed_level` varchar(255) NOT NULL default '',
				`mature_content_rating` varchar(255) NOT NULL default '',
				`stock` varchar(255) NOT NULL default '',
				`gender` varchar(255) NOT NULL default '',
				`color` varchar(255) NOT NULL default '',
				`size` varchar(255) NOT NULL default '',
				`last_update` int(11) NOT NULL default '0',
				`updated` varchar(1) NOT NULL default '1',
				PRIMARY KEY  (`id_product`,`id_merchant`(30),`feed_product_name`(100)),
				KEY `id_product` (`id_product`)
			) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
			break;
			
		case "pc_options":
			$table_name = $table_prefix."pc_options";
			$query = "CREATE TABLE `".$table_name."` (
					  `opt` varchar(50) NOT NULL default '',
					  `value` varchar(255) NOT NULL default ''
				) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
			break;
			
		case "pc_products_custom":
			$table_name = $table_prefix."pc_products_custom";
			$query = "CREATE TABLE `".$table_name."` (
				`product_id` int(11) NOT NULL default '0',
				`product_description` text,
				`product_name` varchar(255) DEFAULT NULL,
                                `insertion_date`  int(11) NOT NULL default '0',
				PRIMARY KEY  (`product_id`)
			) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
			break;
	}
	
	return $query;
    }
    
}
?>
