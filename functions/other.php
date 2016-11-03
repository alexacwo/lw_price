<?php

/* Load Localisation Text Domain*/
load_theme_textdomain ('framework');

// Callback for custom TinyMCE editor stylesheets.
add_editor_style();

// Enable post and comment RSS feed links to head
add_theme_support( 'automatic-feed-links' );

/* Remove WordPress Version / Generator Tag */
remove_action('wp_head', 'wp_generator');

/* Content width */ 
if ( ! isset( $content_width ) ) $content_width = 600;

/**
 * function: aw_mb_ucfirst
 * 
 * Capitalize first letter of string even if it is in UTF-8 encoding.
 * 
 * @param string $str
 * @param string $enc
 * 
 * @return string
 * 
 */
function aw_mb_ucfirst($str, $enc = 'utf-8') {
    if(function_exists('mb_substr')){
        $sub_str = mb_substr($str, 0, 1, $enc);
     } else {
        $sub_str = substr($str, 0, 1);
     }
    if(function_exists('mb_strtoupper')){
        return mb_strtoupper($sub_str, $enc);
    } else {
        return strtoupper($sub_str);
    }
}

/**
 * function: aw_add_custom_upload_mimes
 * 
 * Adds additional mime types in order to be able to upload.
 * 
 * @param array $existing_mimes
 * 
 * @return aray
 * 
 */
function aw_add_custom_upload_mimes ( $existing_mimes = array() ) {
    $existing_mimes['ico'] = 'image/x-icon';
    // or: $existing_mimes['ppt|pot|pps'] = 'application/vnd.ms-powerpoint' to add multiple extensions for the same mime type
    unset( $existing_mimes['exe'] );
    return $existing_mimes;
}
add_filter('upload_mimes', 'aw_add_custom_upload_mimes');

/**
 * function: aw_get_product_merchants
 * 
 * Returns an array of merchants attached to main product
 * 
 * @param int $post_id
 * @return object
 */
function aw_get_product_merchants($post_id = ""){
    global $wpdb;
    if( $post_id == ""){
        $post_id = get_the_id();
    }
    $q = "SELECT pm.*, p.* FROM ".$wpdb->prefix."pc_products_relationships pr, ".$wpdb->prefix."pc_products_merchants pm, ".$wpdb->prefix."pc_products p WHERE pm.slug = p.id_merchant AND p.id_product = pr.id_product AND pr.wp_post_id = '".get_the_id()."' ORDER BY p.price ASC";
    $merchants = $wpdb->get_results($q);
    return $merchants;
}


/**
 * function: aw_excerpt_more
 * 
 * Greturn Excerpt more string
 * 
 * @param string $more
 * 
 * @return string
 * 
 */
function aw_excerpt_more($more) {
    return "...";
}
add_filter('excerpt_more', 'aw_excerpt_more');

function any_ptype_on_tag($request) {
	if ( isset($request['tag']) )
		$request['post_type'] = 'any';
	return $request;
}
add_filter('request', 'any_ptype_on_tag');

/**
 * 
 * function: aw_get_excerpt
 * 
 * Returns a string without html tags and limits the char count to around [or exactyl] $size, does not cut the words in halfs.
 * 
 * @param string $str
 * @param int $size
 * @return string
 */
function aw_get_excerpt($str, $size) {
	$tmp = strip_tags($str);
	while(isset($tmp[$size]) && $tmp[$size] != ' ') {
		$size++;
	}
	
	$str = substr($tmp,0,$size);
	if(strlen($tmp) > $size) $str .= '...';	
	return $str;
}

/**
 * function: aw_get_min_max_price
 * 
 * Gets minimal or maximal price in wole database
 * 
 * @param string $m
 * 
 * @return float|int
 * 
 */
function aw_get_min_max_price($m){
    global $wpdb;
    if ($m == 'min'){		
            $q = "SELECT MIN(price) AS min_price FROM ".$wpdb->prefix."pc_products LIMIT 1";
            $r = $wpdb->get_row($q);		
            return $r->min_price;
    } elseif ($m == 'max'){
            $q = "SELECT MAX(price) AS max_price FROM ".$wpdb->prefix."pc_products LIMIT 1";
            $r = $wpdb->get_row($q);		
            return $r->max_price;
    }
}

function aw_show_listing_options_html(){
    get_template_part('functions/views/frontend/content', 'listing-options');
}
add_action('aw_show_listing_options', 'aw_show_listing_options_html');

/**
 * fnction: aw_show_pagination_html
 * 
 * Displays pagination template part 
 * 
 * @param int $max_num_pages
 * @param int $paged
 * 
 */
function aw_show_pagination_html($max_num_pages, $paged){
    if($max_num_pages > 1):
        get_template_part('functions/views/frontend/content', 'pagination');
    endif;
}
add_action('aw_show_pagination', 'aw_show_pagination_html', 10, 2);

function aw_show_realated_products_html(){
    global $aw_theme_options, $product_id, $related_products, $listOrGrid, $tz_hide_related_products, $tz_number_related_products;
    $related_products = aw_get_related_products($product_id);
    $listOrGrid = aw_get_result_layout_style();
    $tz_number_related_products = $aw_theme_options['tz_number_related_products'];
    
    if( $aw_theme_options['tz_hide_related_products'] == 'false'){
        if( ! is_wp_error($related_products) && count($related_products) > 0 ){
            get_template_part('functions/views/frontend/content', 'related-products');
        }
    }
}
add_action('aw_show_realated_products', 'aw_show_realated_products_html');

/**
 * Function: show_product_reviews_info_html()
 * 
 * Displays 1 Review or X Reviews
 * Can pass post id or will use global post id
 * 
 * @global object $post
 * @param int $post_id
 */
function aw_show_product_reviews_info_html( $post_id = false ){
 
    global $post;

    if($post_id){
        $comment_object = wp_count_comments($post_id); 
    } else {
        if(isset($post->ID))
            $comment_object = wp_count_comments($post->ID);
        else 
            return false;
    }   
    if( isset($comment_object->approved) && $comment_object->approved ){        
        if($comment_object->approved == 1){
            printf(__('%s Review', 'framework'), $comment_object->approved);
        } else {
            printf(__('%s Reviews', 'framework'), $comment_object->approved);
        }
    }
}
add_action('aw_show_product_reviews_info', 'aw_show_product_reviews_info_html', 10, 1);

/**
 * Function: aw_show_header_social_links_html
 * 
 * Shows social icon links if at least one set.
 * 
 * @global type $aw_theme_options
 */
function aw_show_header_social_links_html (){
    global $aw_theme_options;
    $showList = false;
    if( (isset($aw_theme_options['tz_social_twitter']) && trim($aw_theme_options['tz_social_twitter']) != "") ||
        (isset($aw_theme_options['tz_social_facebook']) && trim($aw_theme_options['tz_social_facebook']) != "") ||
        (isset($aw_theme_options['tz_social_rss']) && trim($aw_theme_options['tz_social_rss']) != "") ||
        (isset($aw_theme_options['tz_social_tumblr']) && trim($aw_theme_options['tz_social_tumblr']) != "") || 
        (isset($aw_theme_options['tz_social_pinterest']) && trim($aw_theme_options['tz_social_pinterest']) != "")
     ) $showList = true;

     if($showList):   
        get_template_part('functions/views/frontend/header', 'social-links');
     endif;
}
add_action('aw_show_header_social_links', 'aw_show_header_social_links_html');

/**
 * function aw_show_related_product_content_item_html
 * 
 * Adds related product content loop item template to the loop. Originaly used in single product template
 * 
 * @param object $post
 */
function aw_show_related_product_content_item_html( $post ){
    $is_compare_plus_installed = aw_is_compare_plus_installed(); 
    get_template_part('functions/views/frontend/loop/loop', 'content-related-products-loop-item');
}
add_action('aw_show_related_product_content_item', 'aw_show_related_product_content_item_html', 10, 1);

/**
 * function aw_show_product_archive_content_html
 * 
 * Adds product content template to the loop. Used in all product taxonomies
 * 
 * @param object $post
 */
function aw_show_product_archive_content_html( $post ){
    $is_compare_plus_installed = aw_is_compare_plus_installed(); 
    get_template_part('functions/views/frontend/loop/loop', 'content-archive-products-loop-item');
}
add_action('aw_show_product_archive_content', 'aw_show_product_archive_content_html', 10, 1);

/**
 * function aw_show_search_content_html
 * 
 * Adds search content template to the loop. Used in all search requests
 * 
 * @param object $post
 */
function aw_show_search_content_html( $post ){
    $is_compare_plus_installed = aw_is_compare_plus_installed();    
    get_template_part('functions/views/frontend/loop/loop', 'content-search-loop-item');
}
add_action('aw_show_search_content', 'aw_show_search_content_html', 10, 1);

/**
 * function: aw_show_price_table_html
 * 
 * Shows price table
 * 
 */
function aw_show_price_table_html(){
    get_template_part('functions/views/frontend/content', 'price-table');
}
add_action('aw_show_price_table', 'aw_show_price_table_html');

/**
 * function: aw_show_single_product_price_header_html
 * 
 * Shows price html for single product. Used in single-product.php
 *  * 
 */
function aw_show_single_product_price_header_html(){
    get_template_part('functions/views/frontend/content', 'single-product-price-header');
}
add_action('aw_show_single_product_price_header', 'aw_show_single_product_price_header_html');

/**
 * function: aw_show_single_product_price_header_html
 * 
 * Shows shortlinks html. Used in single-product.php
 *  * 
 */
function aw_show_single_product_shortlinks_html(){
    get_template_part('functions/views/frontend/content', 'single-product-shortlinks');
}
add_action('aw_show_single_product_shortlinks', 'aw_show_single_product_shortlinks_html');

/**
 * function: aw_show_single_product_reviews_html
 * 
 * Shows reviews section's html (comment/review template). Used in single-product.php to show product reviews along with review 
 *  * 
 */
function aw_show_single_product_reviews_html(){
    comments_template('/functions/views/frontend/content-single-product-reviews-template.php');
}
add_action('aw_show_single_product_reviews', 'aw_show_single_product_reviews_html');

/*
 * function: aw_show_compare_header_version_numbers_html
 * 
 * Shows an html comment line with Compare verion number and Compare+ version number if plugin is enabled
 * 
 */
function aw_show_compare_header_version_numbers_html(){
    global $compareplus_version_human, $compare_version_human;
    if( aw_is_compare_plus_installed() ) { ?>
<!-- Compare Responsive Price Comparison Theme <?php echo @$compare_version_human ?> & Compare+ <?php echo @$compareplus_version_human ?> - Built by AWESEM (http://www.awesemthemes.com/) - Powered by WordPress (http://wordpress.org/) -->
	<?php
    } else {
	?>
<!-- Compare Responsive Price Comparison Theme <?php echo @$compare_version_human ?> - Built by AWESEM (http://www.awesemthemes.com/) - Powered by WordPress (http://wordpress.org/) -->
	<?php
    }
}
add_action('aw_show_compare_header_version_numbers',  'aw_show_compare_header_version_numbers_html');

/*
 * function: aw_shape_review
 * 
 * Single review [comment] item
 *  
 */
function aw_shape_review( $comment, $args, $depth ) {
    $comment->comment_content = wp_kses( $comment->comment_content, array( 'strong' => array() ));
    $GLOBALS['comment'] = $comment;
    get_template_part('functions/views/frontend/loop/loop', 'single-review-loop-item');
}

/**
 * function: aw_get_result_layout_style
 * 
 * Returns or echoes string depending on the theme options setting (grid-view or list-view)
 * Original purpose is to output this as a css class
 * 
 * @global array $aw_theme_options
 * @param bool $echo
 * @return string
 */
function aw_get_result_layout_style($echo = false){
    global $aw_theme_options;
    $listOrGrid = "grid-view";
    if( isset($aw_theme_options['tz_result_display_style']) ){
        if($aw_theme_options['tz_result_display_style'] == 'list') {
            $listOrGrid = "list-view";
        }
    }
    if( ! $echo){
        return $listOrGrid;
    }    
    echo $listOrGrid;    
}

/**
 * 
 * function: aw_show_product_description_detailed
 * 
 * Echoes or returns product description. Used in single product page.
 * Adds show [less] [more] options
 * 
 * @global type $wpdb
 * @param object $post
 * @param array $merchants
 * @param int|bool $max_chars
 * @param string $return
 * @return type
 */
function aw_show_product_description_detailed ($post, $merchants = array(), $max_chars = false, $return = false ){
   
    global $wpdb, $aw_theme_options;

    $content = '';
    $tz_hide_shortcodes_in_product_description = $aw_theme_options['tz_hide_shortcodes_in_product_description']; 
    $tz_hide_html_tags_in_product_description = $aw_theme_options['tz_hide_html_tags_in_product_description'];

    // Check product if has global product description ( also stored as post meta )				
    $q = "SELECT product_description FROM ".$wpdb->prefix."pc_products_custom WHERE product_id = '".$post->ID."'";	
    $result = $wpdb->get_results($q);
    if(!empty($result)){
        $content = $result[0]->product_description;
        if($tz_hide_html_tags_in_product_description === 'false') { 
                $content = $result[0]->product_description; 
        } else { 
                $content = strip_tags($content); 
        }
    }
    
    // If no global product description use default one.
    if ($content == ''){
        if( aw_is_compare_plus_installed() && isset($merchants[0]->id_product) ){ // check if C+ table exists
            $q = "SELECT rel.id_feed, rel.id_product FROM ".$wpdb->prefix."pc_products_feeds_relationships rel JOIN ".$wpdb->prefix."pc_feeds fe ON rel.id_feed = fe.id WHERE rel.id_product = '".$merchants[0]->id_product."' AND fe.feed_use_master_description = 1 AND fe.active = 1 LIMIT 1";
            $results_f = $wpdb->get_row($q);
            $results_desc;
            if(!empty($results_f)){		
                    $q = "SELECT slug FROM ".$wpdb->prefix."pc_products_merchants WHERE feed = '".$results_f->id_feed."'";
                    $results_s = $wpdb->get_row($q);

                    $q = "SELECT feed_product_desc FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$results_f->id_product."' AND id_merchant='".$results_s->slug."' LIMIT 1";
                    $results_desc = $wpdb->get_row($q);

            }	
            if (!empty($results_desc->feed_product_desc)){
                    $content = $results_desc->feed_product_desc;
                    if($tz_hide_html_tags_in_product_description === 'false') $content = $content; else $content = strip_tags($content);
            } else {				
                    $content = $post->post_content;
                    if($tz_hide_html_tags_in_product_description === 'false') $content = $content; else $content = strip_tags($content);						
            }
        } else {
            $content = $post->post_content;
            if($tz_hide_html_tags_in_product_description === 'false') $content = $content; else $content = strip_tags($content);	
        }
    }

    $size = $max_chars; // either int or bool (false). False to show all content
    $htmlContent = false;
    if(strlen($content) != strlen(strip_tags($content))) { $htmlContent = true; }
    if($size && strlen($content) > $size) {
            if($htmlContent !== true){
                    while(isset($content[$size]) && $content[$size] != ' ') {
                            $size++;
                    }
                    $display_content  = substr($content,0,$size).'<span class="desc_more" style="display:none;">'.substr($content,$size,strlen($content)).'</span><span class="desc_etc">...</span> <a href="#" id="more_link" onclick="aw_more(); return false;">'.__('More','framework').'</a>';
                    $display_content .= '';						
            } else {

                    $display_content = $content;
            }
    } else {
            $display_content = $content;
    }
    
    if($return){
        return ($tz_hide_shortcodes_in_product_description === 'false') ? do_shortcode($display_content) : $display_content;
    } else {
        echo ($tz_hide_shortcodes_in_product_description === 'false') ? do_shortcode($display_content) : $display_content;
    }
}
add_action ('aw_the_product_description_detailed', 'aw_show_product_description_detailed', 10,  4);

/**
 * 
 * function: aw_show_category_description_detailed
 * 
 * Echoes or returns category description. Used in single category page.
 * Adds show [less] [more] options
 * 
 * @global type $wpdb
 * @param object $term
 * @param array $merchants
 * @param int|bool $max_chars
 * @param string $return
 * @return type
 */
function aw_show_term_description_detailed ($term, $max_chars = false, $return = false ){

    global $wpdb, $aw_theme_options;

    $content = (isset($term->description) ? $term->description : '');
    if(is_tax("product_brand") || is_tax("product_bisbrand"))
    {
        echo $content;
        return;
    }
    
    
    // Get the description from the category
    $content = category_description($category);

    $size = $max_chars; // either int or bool (false). False to show all content
    $htmlContent = false;

    if(strlen($content) != strlen(strip_tags($content))) { 
        $htmlContent = true; 

        // If the only extra HTML content is the opening and closing p tags then we can just remove those tags and treat it like a non HTML string.
        if(preg_match('/^\\<p\\>/', $content) === 1 && preg_match('/\\<\/p\\>$/', $content) === 1 && strlen($content)-strlen(strip_tags($content)) === 7){
            $htmlContent = false;
            $content = strip_tags($content);    
        }
    }
    if($size && strlen($content) > $size) {
            if($htmlContent !== true){
                    while(isset($content[$size]) && $content[$size] != ' ') {
                            $size++;
                    }
                    $display_content  = substr($content,0,$size).'<span class="desc_more" style="display:none;">'.substr($content,$size,strlen($content)).'</span><span class="desc_etc">...</span> <a href="#" id="more_link" onclick="aw_more(); return false;">'.__('More','framework').'</a>';
                    $display_content .= '';                     
            } else {
                    $display_content = $content;
            }
    } else {
            $display_content = $content;
    }
    
    if($return){
        return $display_content;
    } else {
        echo $display_content;
    }
}
add_action ('aw_the_term_description_detailed', 'aw_show_term_description_detailed', 10,  3);

function aw_get_product_info( $wp_post_id ){
    global $wpdb;
    $q = "SELECT MIN(p.price) AS min_price, MAX(p.price) AS max_price, count(p.id_product) AS merchants, p.* 
        FROM 
                ".$wpdb->prefix."pc_products_merchants pm, 
                ".$wpdb->prefix."pc_products p, 
                ".$wpdb->prefix."pc_products_relationships pr 
        WHERE 
                pm.slug = p.id_merchant 
                AND pr.wp_post_id = '".$wp_post_id."' 
                AND p.id_product = pr.id_product 
        GROUP BY id_product 
        ORDER BY price ASC LIMIT 1";
        $product = $wpdb->get_row($q);
        
        return $product;
}

function aw_get_related_products($post_id){
    global $wpdb, $aw_theme_options;
    $q1 = "SELECT tt1.term_taxonomy_id FROM " .  $wpdb->prefix . "posts p1 
                    INNER JOIN " . $wpdb->prefix . "term_relationships tr1 ON (p1.ID = tr1.object_id)
                    INNER JOIN " . $wpdb->prefix . "term_taxonomy tt1 ON (tr1.term_taxonomy_id = tt1.term_taxonomy_id )
           WHERE tr1.object_id = '".$post_id."' AND tt1.taxonomy='product_category'";

    $q2 = "SELECT p2.* FROM " . $wpdb->prefix . "posts p2
                INNER JOIN " . $wpdb->prefix . "term_relationships tr2 ON (p2.ID = tr2.object_id)
        WHERE tr2.term_taxonomy_id IN ($q1) AND tr2.object_id != '".$post_id."'
        GROUP BY p2.ID
        LIMIT " . $aw_theme_options['tz_number_related_products'];

    return $wpdb->get_results($q2);
}

/**
 * function: aw_is_compare_plus_installed
 * 
 * Check if Compare+ is installed and plugin active
 * 
 * @since 2.0
 * 
 * @return bool
 *
 */
function aw_is_compare_plus_installed(){
    if( ! function_exists('get_plugin_data') )
        include( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( ! is_multisite() ) {
        if( is_plugin_active('compare-plus/compare-plus.php') ) {
             return true;
        } else {
            return false;
        }
    } else {
        if( is_plugin_active_for_network('compare-plus/compare-plus.php') ) {
            return true;
        } else {
            return false;
        }        
    }
}

/**
 * 
 * function: show_posts_nav
 * 
 * @global object $wp_query
 * @return bool
 */
function show_posts_nav() {
    global $wp_query;
    return ($wp_query->max_num_pages > 1);
}

/**
 * function: aw_show_message
 * 
 * Shows message box > type can be successful or error
 * 
 * @param string $message
 * @param bool|int $errormsg
 * 
 */
function aw_show_message($message, $errormsg = false) {
	if ($errormsg) {
            echo '<div id="message" class="error">';
	} else {
            echo '<div id="message" class="updated fade">';
	}
	echo "<p><strong>$message</strong></p></div>";
} 

/**
 * function: aw_posts_order_by_filter
 * 
 * Adjusts query in order to filter products by different positions eg. category, brand, price
 * 
 * @param object $query *  
 * 
 */
function aw_custom_product_search_filter($query) {	
    if (! is_admin() && $query->is_main_query() && $query->is_search) {
    	if(isset($_GET['product'])) { // if sidebar ajax search		
			global $wpdb;
			
			// Define vars
			$brand_term_taxonomy_id_array = array(); // Array for brand term_taxonomy_ids
			$category_term_taxonomy_id_array = array(); // Array for category term_taxonomy_ids
			$case = ""; // Stores information if brands&categories, just categories, just brands or non of them are used
			$ks = ""; // Keyword query part 1 
			$ks2 = ""; // Keyword query part 2
			$q  = ""; // Stores main query to get wp post ids > after we get them we can amend main WP query with posts_in 
			
			// Extract get values
			$search['min_price'] = isset($_GET['min']) && is_numeric($_GET['min']) ? esc_sql($_GET['min']) : 0;
			$search['max_price'] = isset($_GET['max']) && is_numeric($_GET['max']) ? esc_sql($_GET['max']) : 999999999999999999999999;
			$search['categories'] = isset($_GET['c']) ? implode(',',$_GET['c']) : null;
			$search['brands'] = isset($_GET['b']) ? implode(',',$_GET['b']) : null;
			$search['keywords'] = isset($_GET['k']) ? implode(',',$_GET['k']) : null;
			
			// Grabs categories, brands selected and keywords entered
			$categories = ($search['categories'] != null) ? explode(',',$search['categories']) : array();
			$brands = ($search['brands'] != null) ? explode(',',$search['brands']) : array();
			$keyword_array = ($search['keywords'] != null) ? explode(',',$search['keywords']) : array();
			
			// Creates Sql part that looks for keywods and/or EAN (PART 1)
			if(!empty($keyword_array)){
			
				foreach ($keyword_array AS $key => $keyword ){
					if(trim($keyword) != ""){
						if(preg_match('/[0-9]{8,}/', $keyword)){ // If EAN search
							// If this is just numbers of at least 8 long then assume that we are serahcing by EAN
							// This is an exact match;
							$ks .= " p.ean = '".esc_sql($keyword)."' AND";
						}
						else
						{ // If normal keyword search > not EAN search
						   $ks .= " pr.product_name LIKE '%".esc_sql($keyword)."%' AND"; // Look in product relationship table > this looks in mapped product names
						   $ks2 .= " p.feed_product_name LIKE '%" . esc_sql($keyword) . "%' AND"; // looks in product table > this searches in original product names from feeds
						}
					}
				}
				if($ks != "")
					$ks = substr($ks, 0, -3); // Remove last AND
					
				if($ks2 != "")
					$ks2 = substr($ks2, 0, -3);	// --"--			
			}
			// Creates Sql part that looks for keywords and/or EAN (PART 2)
			$query_keywords = "";
			if($ks != "" && $ks2 != ""){ // if normal keyword search without EAN			
				$query_keywords = "AND (({$ks}) OR ({$ks2}))";
			} else if ($ks != ""){ // if just EAN search
				$query_keywords = "AND ({$ks})";
			} else if ($ks2 != ""){ // Not possible situation but just in case
				$query_keywords = "AND ({$ks2})";
			}
			
			if(count($categories) != 0 && count($brands) != 0) { // If brands and categories set
                        
			
				// Finds and stores category term_taxonomy_id in array
				// TODO: Change that it grabs also more than 2nd level category term_taxonomy_ids
				$category_term_taxonomy_id_array = array();
				foreach($categories AS $search_category){
					$cat_term = get_term_by( 'slug', $search_category, 'product_category', ARRAY_A);
					
					if(isset($cat_term['term_taxonomy_id'], $cat_term['term_id'])){
						$category_term_taxonomy_id_array[] = $cat_term['term_taxonomy_id'];
						$categorychildren = get_term_children( $cat_term['term_id'], 'product_category' );
						//If Category has got child category
						foreach ( $categorychildren as $categorychild ) {
							$subcategoryterm = get_term_by( 'id', $categorychild, 'product_category',ARRAY_A );
							$category_term_taxonomy_id_array[] = $subcategoryterm['term_taxonomy_id'];
						}						
					}
				}
				// Finds and stores brand term_taxonomy_id in array
				$brand_term_taxonomy_id_array = array();
				foreach($brands AS $search_brand){
					$brand_term = get_term_by( 'slug', $search_brand, 'product_brand', ARRAY_A);
					if(isset($brand_term['term_taxonomy_id'])){
						$brand_term_taxonomy_id_array[] = $brand_term['term_taxonomy_id'];
					}
				}				
				
				// Main query to get products > BOTH CATEGORIES AND BRANDS ARE SET	                    
                                $q = "SELECT DISTINCT pr.wp_post_id FROM " .$wpdb->prefix."pc_products_relationships pr
                                LEFT JOIN ".$wpdb->prefix."pc_products p ON 
                                      (   
                                          p.id_product = pr.id_product                                        
                                      ) 
                                INNER JOIN ".$wpdb->prefix."posts ON (pr.wp_post_id = ".$wpdb->prefix."posts.ID)
                                INNER JOIN ".$wpdb->prefix."term_relationships ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."term_relationships.object_id) 
                                INNER JOIN ".$wpdb->prefix."term_relationships AS tt1 ON (".$wpdb->prefix."posts.ID = tt1.object_id) 
                                WHERE ".$wpdb->prefix."term_relationships.term_taxonomy_id IN (".implode(", ", $category_term_taxonomy_id_array).") 
                                AND tt1.term_taxonomy_id IN (".implode(", ", $brand_term_taxonomy_id_array).")
                                AND ".$wpdb->prefix."posts.post_type IN ('product') 
                                AND (".$wpdb->prefix."posts.post_status = 'publish') {$query_keywords} AND 
                                (
                                    p.price is null OR
                                    (
                                        p.price >= {$search['min_price']} 
                                        AND p.price <= {$search['max_price']}
                                    )
                                )"   ;
                                         
	
			} else if(count($categories) != 0) { // If category selected
                                
                                $filtered_category_arr = array();
                                $category_obj_arr = array();
                                $filtered_category_obj_arr = array();
                                $category_ids_arr = array();
                                
                                // Get all term details
                                foreach($categories AS $search_category_slug){
                                    $term_details = get_term_by( 'slug', $search_category_slug, 'product_category', ARRAY_A);  
                                    if($term_details){
                                        $category_obj_arr[$term_details['term_id']] = $term_details;                                        
                                        $filtered_category_obj_arr[$term_details['term_id']] = $term_details;                                        
                                    }
                                }
                                
                                // If any terms found at all
                                if( ! empty ($category_obj_arr) ){
                                   
                                   // Store all term ids in seperate array
                                   foreach($category_obj_arr AS $category_obj){
                                       $category_ids_arr[] = $category_obj['term_id'];
                                   }
                                   
                                   // Check parent_id against all ids array valueas in order to unset parent ids
                                   foreach($category_obj_arr AS $category_obj){
                                        
                                        $term_parent = (int)$category_obj['parent'];
                                        $term_slug = $category_obj['slug'];
                                        
                                        while($term_parent != 0){
                                            $parent_term_obj = get_term_by( 'id', $term_parent, 'product_category', ARRAY_A );                                                
                                            if(  in_array( $term_parent, $category_ids_arr) ){  
                                                // If parent term in our array 
                                                // Remove it form our array
                                                unset($filtered_category_obj_arr[$term_parent]);
                                                if($parent_term_obj){
                                                    // If we found rent term details
                                                    // Let's set term_parent again to new value to climb up the tree or end this loop
                                                     $term_parent = (int)$parent_term_obj['parent'];                                                     
                                                } else {
                                                    // Something went wrong while finding parent term details and this will end the while loop
                                                    $term_parent = 0;
                                                }
                                            } else {
                                                // Could not find parent in our array so lets end this while loop or try to find upper level term that might be in our array                                                
                                                
                                                if($parent_term_obj){
                                                    // Parent term found
                                                    if(  in_array( (int)$parent_term_obj['term_id'], $category_ids_arr) ){
                                                        // If parent term in our array 
                                                        // Remove it form our array
                                                        unset($filtered_category_obj_arr[$parent_term_obj['term_id']]);
                                                        
                                                        // Let's set term_parent again to new value to climb up the tree or end this loop
                                                        $term_parent = (int)$parent_term_obj['parent'];
                                                        
                                                    } else {
                                                        // Parent term not in our array 
                                                        // Let's set term_parent again to new value to climb up the tree or end this loop
                                                        $term_parent = (int)$parent_term_obj['parent'];                                                        
                                                    }                                                    
                                                } else {
                                                    // Something went wrong while finding parent term details and this will end the while loop
                                                    $term_parent = 0;
                                                }
                                               
                                            }  
                                        }                                        
                                   } 
                                }   
                                
				// Finds and stores category term_taxonomy_id in array
				$category_term_taxonomy_id_array = array();
				foreach($filtered_category_obj_arr AS $category_obj_arr){
					$cat_term = $category_obj_arr;
					if(isset($cat_term['term_taxonomy_id'])){
						$category_term_taxonomy_id_array[] = $cat_term['term_taxonomy_id'];
						$categorychildren = get_term_children( $cat_term['term_id'], 'product_category' );
						foreach ( $categorychildren as $categorychild ) {
							$subcategoryterm = get_term_by( 'id', $categorychild, 'product_category',ARRAY_A );
							$category_term_taxonomy_id_array[] = $subcategoryterm['term_taxonomy_id'];
						}	
					}
				}
				
				// Main query to get products > JUST CATEGORIES SET	                  
                                $q = "SELECT DISTINCT pr.wp_post_id FROM " .$wpdb->prefix."pc_products_relationships pr
                                LEFT JOIN ".$wpdb->prefix."pc_products p ON 
                                      (   
                                          p.id_product = pr.id_product
                                          {$query_keywords}                                             
                                      ) 
                                INNER JOIN ".$wpdb->prefix."posts ON (pr.wp_post_id = ".$wpdb->prefix."posts.ID)
                                INNER JOIN ".$wpdb->prefix."term_relationships ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."term_relationships.object_id) 
                                WHERE ".$wpdb->prefix."term_relationships.term_taxonomy_id IN (".implode(", ", $category_term_taxonomy_id_array).") 
                                AND ".$wpdb->prefix."posts.post_type IN ('product') 
                                AND (".$wpdb->prefix."posts.post_status = 'publish')AND 
                                (
                                    p.price is null OR
                                    (
                                        p.price >= {$search['min_price']} 
                                        AND p.price <= {$search['max_price']}
                                    )
                                )"   ;
                                                        

			} else if(count($brands) != 0) { // if brand selected
				
				// Finds and stores brand term_taxonomy_id in array
				$brand_term_taxonomy_id_array = array();
				foreach($brands AS $search_brand){
					$brand_term = get_term_by( 'slug', $search_brand, 'product_brand', ARRAY_A);
					if(isset($brand_term['term_taxonomy_id'])){
						$brand_term_taxonomy_id_array[] = $brand_term['term_taxonomy_id'];
					}
				}
				
				// Main query to get products > JUST BRANDS ARE SET	                                               
                               $q = "SELECT DISTINCT pr.wp_post_id FROM " .$wpdb->prefix."pc_products_relationships pr
                                LEFT JOIN ".$wpdb->prefix."pc_products p ON 
                                      (   
                                          p.id_product = pr.id_product                                      
                                      ) 
                                INNER JOIN ".$wpdb->prefix."posts ON (pr.wp_post_id = ".$wpdb->prefix."posts.ID)
                                INNER JOIN ".$wpdb->prefix."term_relationships ON (".$wpdb->prefix."posts.ID = ".$wpdb->prefix."term_relationships.object_id) 
                                WHERE 
                                ".$wpdb->prefix."term_relationships.term_taxonomy_id IN (".implode(", ", $brand_term_taxonomy_id_array).")
                                AND ".$wpdb->prefix."posts.post_type IN ('product') 
                                AND (".$wpdb->prefix."posts.post_status = 'publish') {$query_keywords}  AND 
                                (
                                    p.price is null OR
                                    (
                                        p.price >= {$search['min_price']} 
                                        AND p.price <= {$search['max_price']}
                                    )
                                )"   ;
                                                        
			} else { // If no brand or category selected
				                                        
                                $q = "SELECT DISTINCT pr.wp_post_id FROM " .$wpdb->prefix."pc_products_relationships pr
                                LEFT JOIN ".$wpdb->prefix."pc_products p ON 
                                      (   
                                          p.id_product = pr.id_product                                         
                                      ) 
                                INNER JOIN ".$wpdb->prefix."posts ON (pr.wp_post_id = ".$wpdb->prefix."posts.ID)
                                WHERE 
                                ".$wpdb->prefix."posts.post_type IN ('product') 
                                AND (".$wpdb->prefix."posts.post_status = 'publish') {$query_keywords}  
                                AND 
                                (
                                    p.price is null OR
                                    (
                                        p.price >= {$search['min_price']} 
                                        AND p.price <= {$search['max_price']}
                                    )
                                )"   ;
				
			}
			$r = $wpdb->get_results($q);
			// Grabs all post ids from main sql query results
			$post_ids = array();
			foreach($r as $pr_id => $pr_content) {
				$post_ids[] = $pr_content->wp_post_id;
			}
			
			
			if(count($post_ids) == 0) {
				global $noEntries;
				$noEntries = true;
                                $query->set('post__in', array(0)); // Tell WP to not return any products as none were found by the filter
				$query->set('post_type', 'product'); // Not needed as be know that they are products anyway but just in case
				$query->set('s', '');
				$query->set('is_search', true);
                                $query->set('ignore_sticky_posts', true);
                                
			} else { // Change the query with our new post ids
				$query->set('post__in', $post_ids); // Tell WP to find only our products
				$query->set('post_type', 'product'); // Not needed as be know that they are products anyway but just in case
				$query->set('s', '');
				$query->set('is_search', true);
			}
		}
    } 
}
add_filter('pre_get_posts','aw_custom_product_search_filter');

/**
 * function: aw_posts_order_by_filter
 * 
 * Depending on requested [order by] value, function returns database column name to use while ordering
 * 
 * @param string $name Possible min_price, title, date_added
 * 
 * @TODO what is $default_products_order_by ?
 * 
 * @return string
 * 
 */
function aw_get_product_order_by_database_field_from_friendly_name($name){
    global $wpdb;	
    //Possible order by value array
    $friendly_array = array(
        "min_price"=>"min_price",
        "title"=>$wpdb->posts.".post_title",
        "date_added"=>$wpdb->posts.".post_date"
    );
    $name = trim(strtolower($name));
    if(empty($friendly_array[$name])){
        // Return the default
        return $friendly_array[$default_products_order_by];
    }
    else
    {
        //Return requested order
        return $friendly_array[$name];	
    }
}

/**
 * function: aw_posts_order_by_filter
 * 
 * Function grabs requested product [order] and [order by] values > if not requested, then use theme's default values stored in theme options
 * 
 * @return array
 * 
 */
function aw_get_product_order_options(){		
    $order = isset($_REQUEST["order"]) ? $_REQUEST["order"] : get_option('tz_products_order');
    $order_by = isset($_REQUEST["order_by"]) ? $_REQUEST["order_by"] : get_option('tz_products_order_by');
    $order = esc_sql(trim(strtolower($order)));
    $order_by = esc_sql(trim(strtolower($order_by)));
    if($order!= "asc" && $order != "desc"){ // If variables are not in theme options DB for some reason
            $order = "asc";
    }
    return array("order"=>$order, "order_by"=>$order_by);
}

/**
 * function: aw_posts_order_by_filter
 * 
 * Joins wp_query with additional tables so we can order by other custom columns.  
 * Joins pc_products_relationships and pc_products to posts.
 * 
 * @param string $join
 * 
 * @return string
 * 
 */
function aw_posts_join_filter($join) {
    global $wpdb, $wp_query;
    if($wp_query->is_search){		
            if(isset($_REQUEST['product'])){
                    // If ajax sidebar search
                    $join.= " INNER JOIN " . $wpdb->prefix . "pc_products_relationships pr1 ON (" . $wpdb->posts . ".ID = pr1.wp_post_id)";
                    $join.= " LEFT JOIN " . $wpdb->prefix . "pc_products prod1 ON (pr1.id_product = prod1.id_product)";
            } else {
                    // If WP generic search
                    $join.= " LEFT JOIN " . $wpdb->prefix . "pc_products_relationships pr1 ON (" . $wpdb->posts . ".ID = pr1.wp_post_id)";
                    $join.= " LEFT JOIN " . $wpdb->prefix . "pc_products prod1 ON (pr1.id_product = prod1.id_product)";
            }
    } else if ($wp_query->is_tax && isset($wp_query->query_vars['taxonomy']) && ($wp_query->query_vars['taxonomy'] == 'product_category' || $wp_query->query_vars['taxonomy'] == 'product_brand' || $wp_query->query_vars['taxonomy'] == 'product_bisbrand')) {
            // If in taxonomy pages
            $join.= " INNER JOIN " . $wpdb->prefix . "pc_products_relationships pr1 ON (" . $wpdb->posts . ".ID = pr1.wp_post_id)";
            $join.= " LEFT JOIN " . $wpdb->prefix . "pc_products prod1 ON (pr1.id_product = prod1.id_product)";
    }
    return $join;
}
add_filter('posts_join', 'aw_posts_join_filter');

/**
 * function: aw_posts_order_by_filter
 * 
 * Adds addition order by elements to main query
 * 
 * @param string $orderby_statement
 * 
 * @return string
 * 
 */
function aw_posts_order_by_filter($orderby_statement) {
    global $wpdb, $wp_query;
    if($wp_query->is_search){	
            // If search
            // Check if there is a order variable being sent through
            $product_order_options = aw_get_product_order_options();
            if(!empty($product_order_options)){
                    $order_by = $product_order_options["order_by"];
                    $order = $product_order_options["order"];					
                    if (!empty($orderby_statement)) {
                            // Order by values given, use default values if they are not valid
                            $order_by = aw_get_product_order_by_database_field_from_friendly_name($order_by);
                            $orderby_statement = $order_by . " " . $order;
                    }		
            }
    } else if ($wp_query->is_tax && isset($wp_query->query_vars['taxonomy']) && ($wp_query->query_vars['taxonomy'] == 'product_category' || $wp_query->query_vars['taxonomy'] == 'product_brand' || $wp_query->query_vars['taxonomy'] == 'product_bisbrand')) {
            // If in taxonomy pages
            // Check if there is a order variable being sent through
            $product_order_options = aw_get_product_order_options();
            if(!empty($product_order_options)){
                    $order_by = $product_order_options["order_by"];
                    $order = $product_order_options["order"];					
                    if (!empty($orderby_statement)) {
                            // Order by values given, use default values if they are not valid
                            $order_by = aw_get_product_order_by_database_field_from_friendly_name($order_by);
                            $orderby_statement = $order_by . " " . $order;
                    }		
            }
    }
    return $orderby_statement;
}
add_filter('posts_orderby', 'aw_posts_order_by_filter');

/**
 * function: wp_posts_fields_filter
 * 
 * Adds addition select fields to main query
 * 
 * @param string $fields
 * 
 * @return string
 * 
 */
function wp_posts_fields_filter($fields) {
    global $wpdb, $wp_query;
	if($wp_query->is_search){	
		// If search
		if (!empty($fields)) {
			$fields.= ",MIN(prod1.price) as min_price ";
		}	
	} else if ($wp_query->is_tax && isset($wp_query->query_vars['taxonomy']) && ($wp_query->query_vars['taxonomy'] == 'product_category' || $wp_query->query_vars['taxonomy'] == 'product_brand' || $wp_query->query_vars['taxonomy'] == 'product_bisbrand')) {
		// If in taxonomy page
		if (!empty($fields)) {
				$fields.= ",MIN(prod1.price) as min_price ";
		}
	}
    return $fields;
}
add_filter('posts_fields', 'wp_posts_fields_filter');

/**
 * function: aw_posts_where_filter
 * 
 * Adds addition group by elements to main query
 * 
 * @param string $groupby_statement
 * 
 * @return string
 * 
 */
function aw_posts_group_by_filter($groupby_statement) {
    global $wpdb, $wp_query;
    if($wp_query->is_search){
            // If search			
                    if(empty($groupby_statement)){
                            $groupby_statement.= ' '.$wpdb->posts.'.ID';
                    } 
    } else if ($wp_query->is_tax && isset($wp_query->query_vars['taxonomy']) && ($wp_query->query_vars['taxonomy'] == 'product_category' || $wp_query->query_vars['taxonomy'] == 'product_brand' || $wp_query->query_vars['taxonomy'] == 'product_bisbrand')) {
            if(empty($groupby_statement)){
                            $groupby_statement.= ' '.$wpdb->posts.'.ID';
            }    
    }
    return $groupby_statement;
}
add_filter('posts_groupby', 'aw_posts_group_by_filter');

/**
 * function: aw_posts_where_filter
 * 
 * Adds addition where clauses if searching for ean
 * 
 * @param string $where
 * 
 * @return string
 * 
 */
function aw_posts_where_filter($where ){
    global $wpdb, $wp_query;
    if($wp_query->is_search){
        if(!isset($_REQUEST['product'])){
	    // If not sidebar ajax search
            // Determine whether the list of keywords contains any EAN's
            $has_ean = false;
            foreach($wp_query->query_vars['search_terms'] as $term){
                if(preg_match('/[0-9]{8,}/', $term)){
                    // This is an ean
                    $has_ean = true;
                    break;
                }
            }
            if($has_ean){
                // If this isn't a product search then we can do this, there is no need if it is.
                // Reset the $where clause and rebuild it, this has to be done to stop wordpress from auto adding the ean as normal keywords
                $where = '';
                foreach($wp_query->query_vars['search_terms'] as $term){
                    if(preg_match('/[0-9]{8,}/', $term)){
                        // This is an ean
                        $where.= " AND (prod1.ean=".  esc_sql($term).")";
                    }
                    else
                    {
                        // This is not an ean
                        $where.= " AND ((".$wpdb->prefix."posts.post_title LIKE '%".esc_sql($term)."%') 
                            OR (".$wpdb->prefix."posts.post_content LIKE '%".  esc_sql($term)."%'))";
                    }
                }
               
                $aw_post_types = aw_get_public_post_types();
                $aw_post_type_string = "";
                if( !empty($aw_post_types) ){
                    foreach ($aw_post_types AS $aw_slug => $value){
                        $aw_post_type_string .= "'" . $aw_slug . "',";
                    }
                    
                }
                
                $where.= "AND ".$wpdb->prefix."posts.post_type IN (" . trim($aw_post_type_string, ',') . ") 
                AND (".$wpdb->prefix."posts.post_status = 'publish' OR ".$wpdb->prefix."posts.post_author = 1 AND ".$wpdb->prefix."posts.post_status = 'private')";
            }
        }
    }
    return $where;
}
add_filter('posts_where', 'aw_posts_where_filter');

/**
 * function: aw_get_public_post_types
 * 
 * Gets all public post types in an object
 * 
 * @return array
 * 
 */
function aw_get_public_post_types(){
    $args = array(
        'public'   => true,
        '_builtin' => true
    );
    $output = 'objects'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types( $args, $output, $operator );
    return $post_types;
}

/**
 * function: aw_tinymce_global_description_js
 * 
 * Adds Product Global Description > TinyMCE's javascript on edit/add pages
 * 
 */
function aw_tinymce_global_description_js(){
        
    global $wp_version;
    
    if ( version_compare($wp_version, '3.9', '>=') ) { // WP 3.9 and on uses TinyMCE 4 ?>
        <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var settings = { menubar : false, selector :  '#global_description'};
            try {
                tinymce.init( settings );
            } catch(e) {}
        });
        </script>
   <?php  } else { // Older WP version than 3.9 uses TinyMCE 3 ?>
            <?php if($post->post_type == 'product'): ?>
                    <script type="text/javascript">
                        jQuery(document).ready( tinymce_excerpt );
                         function tinymce_excerpt() {
                             jQuery("#global_description").addClass("mceEditor");
                             var tinymceConfigs = [{
                             theme : "advanced",
                                     mode : "none",
                                     height:"200",
                                     width:"100%",
                                     theme_advanced_layout_manager : "SimpleLayout",
                                     theme_advanced_toolbar_location : "top",
                                     theme_advanced_toolbar_align : "left",
                                     theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,code",        
                                     theme_advanced_buttons2 : "",
                                     forced_root_block : "",
                                     theme_advanced_buttons3 : "",
                                     convert_urls: false }];

                             tinyMCE.settings =  tinymceConfigs[0];
                             tinyMCE.execCommand("mceAddControl", false, "global_description");
                         }
                    </script>
             <?php endif;   
    }
}
add_action( 'admin_footer', 'aw_tinymce_global_description_js');

/**
 * function: aw_tinymce_css
 * 
 * Adds Product Global Description > TinyMCE's css on edit/add pages
 * 
 */
function aw_tinymce_css(){ // Applies only if WP version is older than 3.9 ?>
            
    <?php if(get_post_type() == 'product'): ?>
    <style type='text/css'>
    #global_description_ifr {background-color: #FFFFFF;} 
    </style>
    <?php endif; ?>
<?php }
add_action( 'admin_head-post.php', 'aw_tinymce_css');
add_action( 'admin_head-post-new.php', 'aw_tinymce_css');

/**
 * function: aw_the_formated_price
 * 
 * Formats the price passed and outputs together with currency symbol,
 * used for displaying price like 23.4$ etc.
 * 
 * @param string|float|int $price
 * 
 */
function aw_the_formated_price($price, $return = false){
    $product_price = number_format($price,compare_get_currency('decimals'),'.','');
    // Format the product price
    $price_exploded = explode('.', $product_price);
    $price_format_exploded =  explode(',', compare_get_currency('format'));
    $format_i = sizeof($price_format_exploded)-1;
    if(strlen($price_exploded[0]) < intval($price_format_exploded[$format_i])){
    }
    else{
        // Loop backwards through the exploded format.
        $format_sum = 0;
        for($i = sizeof($price_format_exploded)-1; $i >= 0; $i--){
            $format_sum += intval($price_format_exploded[$i]);
            if($format_sum >= strlen($price_exploded[0])){
                // Add the commas in and stop
                $pos = strlen($price_exploded[0])-1;
                for($k = sizeof($price_format_exploded)-1; $k >= $i; $k--){
                    // Loop backwards through the format.
                    $pos -= intval($price_format_exploded[$k]);
                    if($pos > -1){
                    $price_exploded[0] = substr_replace($price_exploded[0], ',', $pos+1, 0);
                    }
                    else{
                        break;
                    }
                }
                $product_price = implode($price_exploded, '.');
                break;
            }
        }
    }
    if(compare_get_currency('position') == "left"){
        if( $return ){
            return str_replace(array("\r\n", "\r", "\n", " "), "", compare_get_currency('symbol')).$product_price;
        } else {
            echo str_replace(array("\r\n", "\r", "\n", " "), "", compare_get_currency('symbol')).$product_price;
        }
    } else {
        if( $return ){
            return $product_price.str_replace(array("\r\n", "\r", "\n", " "), "", compare_get_currency('symbol'));
        } else {
            echo $product_price.str_replace(array("\r\n", "\r", "\n", " "), "", compare_get_currency('symbol'));
        }
    }    
}

/**
 * Fuction: compare_get_currency
 * 
 * Displays either currency symbol, symbol position or currency abbreviation.
 * 
 * @param string $part Tells the the function what to return currency, symbol or position
 * 
 * @return string 
 * 
 */ 
function compare_get_currency($part){
	$currency_left_right = "";	
	$currency = "";	
	$currency_symbol = "";
	$currency 			= get_option('tz_currency');	
	$currencies			= trim(get_option('tz_currencies'));	
	if($currencies != ""){
		$currency_options 	= explode("\n", $currencies	);	
		if(isset($currency_options[$currency])) {			
			$currency 			= $currency_options[$currency];
			$currencyExplode 		= explode(",", $currency); 

			$currency_left_right 	= (isset($currencyExplode[0])) ? trim(preg_replace('/\s+/', '', $currencyExplode[0])) : "";
			$currency_code 				= (isset($currencyExplode[1])) ? trim(preg_replace('/\s+/', '', $currencyExplode[1])) : "";
			$currency_symbol 		= (isset($currencyExplode[2])) ? trim(preg_replace('/\s+/', '', $currencyExplode[2])) : "";
            // Assume 2 decimal places if it is not supplied		
		    $number_of_decimal_places = (isset($currencyExplode[3])) ? trim(preg_replace('/\s+/', '', $currencyExplode[3])) : "2";
            // Get the format if it is defined.
            $format_matches = array();
            preg_match('/\\[([0-9,]+)\\]/', $currency, $format_matches);
            // Default to commas every 3 characters
            $format = isset($format_matches[1]) ? $format_matches[1] : "3,3,3,3,3";
        }	
	}
	if($part == "currency"){
		return $currency_code; 
	} else if($part == "symbol") {
		return $currency_symbol;
	} else if($part == "position"){
		return $currency_left_right;
	} else if($part == "decimals"){
        return $number_of_decimal_places;
    } else if($part == "format"){
        return $format;
    } else {
		return "Function compare_get_currency not used properly.";
	}
}

/**
 * Function : compare_slugify
 * 
 * Returns an alphanumerical string with - as separator
 * 
 * @param string $text
 * 
 * @return string 
 * 
 */
function compare_slugify($text) {
	// Need WPLANG if need special chars, e.g. in French é
	if(WPLANG != ''){	
		setlocale(LC_CTYPE, WPLANG.'.utf8');
	}else {
		setlocale(LC_CTYPE, 'fr_FR.utf8');
	}
	
	$text = compare_cyrillic_to_latin($text); 
	
    // transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    // trim
    $text = trim($text, '-'); 
    // lowercase
    $text = strtolower($text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
   	if(strlen($text) > 200) {
    	$text = substr($text, 0, 200);
    	while (substr($text, -1) == "-") {
    		$text = substr($text, 0, -1);
    	} 
    }
    
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

/**
 * Fuction: compare_cyrillic_to_latin
 * 
 * Returns a string where cyrillic chars are rplaced with latin
 * 
 * @param string $text
 * 
 * @return string 
 * 
 */
function compare_cyrillic_to_latin($str)
{
    $tr = array(
    "А"=>"a", "Б"=>"b", "В"=>"v", "Г"=>"g", "Д"=>"d",
    "Е"=>"e", "Ё"=>"yo", "Ж"=>"zh", "З"=>"z", "И"=>"i", 
    "Й"=>"j", "К"=>"k", "Л"=>"l", "М"=>"m", "Н"=>"n", 
    "О"=>"o", "П"=>"p", "Р"=>"r", "С"=>"s", "Т"=>"t", 
    "У"=>"u", "Ф"=>"f", "Х"=>"kh", "Ц"=>"ts", "Ч"=>"ch", 
    "Ш"=>"sh", "Щ"=>"sch", "Ъ"=>"", "Ы"=>"y", "Ь"=>"", 
    "Э"=>"e", "Ю"=>"yu", "Я"=>"ya", "а"=>"a", "б"=>"b", 
    "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "ё"=>"yo", 
    "ж"=>"zh", "з"=>"z", "и"=>"i", "й"=>"j", "к"=>"k", 
    "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", 
    "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f", 
    "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", 
    "ъ"=>"", "ы"=>"y", "ь"=>"", "э"=>"e", "ю"=>"yu", 
    "я"=>"ya", " "=>"-", "."=>"", ","=>"", "/"=>"-", 
);
    return strtr($str,$tr);
}
/**
 * Function : compare_remove_taxonomy
 * 
 * Removes all taxonomy terms
 * 
 * @param string|array $taxonomy taxonomy slug
 * 
 */
function compare_remove_taxonomy_terms($taxonomy) {
	$terms = get_terms($taxonomy);
	if(!empty($terms))
	foreach ($terms as $term) {
		wp_delete_term( $term->term_id, $taxonomy );
	}
}
/**
 * Function : showPermalinkWarning
 * 
 * Shows warning message to ensure that user enable custom permalink structure
 * 
 */
function showPermalinkWarning()
{
	$current_screen = get_current_screen();
	if ('options-permalink' == $current_screen->base) {
		if (isset($_POST['submit'])) {
			if(isset($_POST['permalink_structure']) && trim($_POST['permalink_structure']) == ''){
				 aw_show_message(__("Compare price comparison theme requires custom permalinks structure.", "framework") . " <a href='options-permalink.php' style='text-decoration:underline'>" . __("Enable permalinks here.", "framework") . "</a>", true);
			}
		} else {		
			if ( get_option('permalink_structure') == '' ) {
			 aw_show_message(__("Compare price comparison theme requires custom permalinks structure.", "framework") . " <a href='options-permalink.php' style='text-decoration:underline'>" . __("Enable permalinks here.", "framework") . "</a>", true);
			} 		
		}
	} else {
		if ( get_option('permalink_structure') == '' ) {
			 aw_show_message(__("Compare price comparison theme requires custom permalinks structure.", "framework") . " <a href='options-permalink.php' style='text-decoration:underline'>" . __("Enable permalinks here.", "framework") . "</a>", true);
		}
	}
}
add_action('admin_notices', 'showPermalinkWarning');


/**
 * 
 * function validate_url
 * 
 * Validates url passed in
 * 
 * @param string $url
 * @return bool
 * 
 */
function validate_url($url) {
    
	// SCHEME
	$urlregex = "^(https?|ftp)\:\/\/";
	
	// USER AND PASS (optional)
	$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";
	
	// HOSTNAME OR IP
	$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*"; // http://x = allowed (ex. http://localhost, http://routerlogin)
	//$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+"; // http://x.x = minimum
	//$urlregex .= "([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}"; // http://x.xx(x) = minimum
	//use only one of the above
	
	// PORT (optional)
	$urlregex .= "(\:[0-9]{2,5})?";
	// PATH (optional)
	$urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
	// GET Query (optional)
	$urlregex .= "(\?[a-z0-9+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?";
	// ANCHOR (optional)
	$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
	
	// check
	return eregi($urlregex, $url);
}

/**
 * function: checkortransfromutf8
 * 
 * Tries to convert the string passed into utf-8 encoding
 * 
 * @param string $str
 * @return string
 */
function checkortransfromutf8($str) {
	$enc = mb_detect_encoding($str);
    if ($enc && ($enc != 'utf-8' && $enc != 'UTF-8' && $enc != 'utf8' && $enc != 'UTF8')) {
        return iconv($enc, 'UTF-8', $str);
    } else {
        return $str;
    }
}

/**
 * 
 * function: splitLine
 * 
 * Create a 2D array from a CSV string
 *
 * @param string $data CSV string
 * @param string $delimiter Field delimiter
 * @param string $enclosure Field enclosure
 * @param string $newline Line seperator
 * @return array 2D array representing CSV values
 * 
 */
function splitLine($data, $delimiter = ',', $enclosure = '"', $newline = "\n"){
    $pos = $last_pos = -1;
    $end = strlen($data);
    $row = 0;
    $quote_open = false;
    $trim_quote = false;
    $return = array();

    // Create a continuous loop
    for ($i = -1;; ++$i){
        ++$pos;
        // Get the positions
        $comma_pos = strpos($data, $delimiter, $pos);
        $quote_pos = strpos($data, $enclosure, $pos);
        $newline_pos = strpos($data, $newline, $pos);

        // Which one comes first?
        $pos = min(($comma_pos === false) ? $end : $comma_pos, ($quote_pos === false) ? $end : $quote_pos, ($newline_pos === false) ? $end : $newline_pos);

        // Cache it
        $char = (isset($data[$pos])) ? $data[$pos] : null;
        $done = ($pos == $end);

        // It it a special character?
        if ($done || $char == $delimiter || $char == $newline){

            // Ignore it as we're still in a quote
            if ($quote_open && !$done){
                continue;
            }

            $length = $pos - ++$last_pos;

            // Is the last thing a quote?
            if ($trim_quote){
                // Well then get rid of it
                --$length;
            }

            // Get all the contents of this column
            $return[$row][] = ($length > 0) ? str_replace($enclosure . $enclosure, $enclosure, substr($data, $last_pos, $length)) : '';

            // And we're done
            if ($done){
                break;
            }

            // Save the last position
            $last_pos = $pos;

            // Next row?
            if ($char == $newline){
                ++$row;
            }

            $trim_quote = false;
        }
        // Our quote?
        else if ($char == $enclosure){

            // Toggle it
            if ($quote_open == false){
                // It's an opening quote
                $quote_open = true;
                $trim_quote = false;

                // Trim this opening quote?
                if ($last_pos + 1 == $pos){
                    ++$last_pos;
                }

            }
            else {
                // It's a closing quote
                $quote_open = false;

                // Trim the last quote?
                $trim_quote = true;
            }

        }

    }

    return $return;
}

?>