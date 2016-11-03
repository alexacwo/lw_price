<?php
//Shortcodes in widgets
add_filter('widget_text', 'do_shortcode');

// Button
function button($atts, $content = null) {
	extract(shortcode_atts(array(
		"url" => ''
	), $atts));
	return '<div class="medium primary btn metro rounded"><a href="'.$url.'">'.$content.'</a></div>';
}
add_shortcode('button', 'button');

// Highlight
function highlight($atts, $content = null) {
	return '<span class="highlight">'.do_shortcode($content).'</span>';
}
add_shortcode('highlight', 'highlight');

// Drop cap
function dropCap($atts, $content = null) {
	return '<span class="dropcap">'.$content.'</span>';
}
add_shortcode('dropcap', 'dropCap');

// Toggle
function toggle($atts, $content = null) {
	extract(shortcode_atts(array(
		"title" => ''
	), $atts));
	return '<div><a href="#" class="toggle-button"><span class="toggle-icon"></span>'.$title.'</a><div class="toggle-content">'.do_shortcode($content).'</div></div>';
}
add_shortcode('toggle', 'toggle');

// Add right pullquote shortcode
function quoteRight($atts, $content = null) {
	return '<span class="quote_right">'.$content.'</span>';
}
add_shortcode('quote_right', 'quoteRight');

// Add left pullquote shortcode
function quoteLeft($atts, $content = null) {
	return '<span class="quote_left">'.$content.'</span>';
}
add_shortcode('quote_left', 'quoteLeft');

// Youtube
function youTube($atts, $content = null) {
   	extract(shortcode_atts(array(
		'id'  => '',
		'width'  => '590',
		'height' => '355'
	), $atts));
	return '<div class="youtube video mb30"><iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$id.'"></iframe ></div>';
}
add_shortcode('youtube', 'youTube');

// Tooltip
function tooltip($atts, $content = null) {

	global $wpdb;

	extract(shortcode_atts(array(
		"id" => 0
	), $atts));
	
	$q = "SELECT MIN(p.price) AS min_price, count(p.id_product) AS merchants, pr.product_name AS post_product_name, pr.wp_post_id, p.* FROM ".$wpdb->prefix."pc_products p, ".$wpdb->prefix."pc_products_relationships pr WHERE pr.id_product = '".esc_sql($id)."' AND p.id_product = pr.id_product GROUP BY id_product ORDER BY price ASC LIMIT 1";

        $r = $wpdb->get_row($q);
	
	if(!$r) {
		return $content;
	}
	$output = '<span class="relative">';	
	$output .= '<a rel="nofollow" id="p_'.$id.'" class="tooltip-link" >'.$content.'</a>';	
        $output .= '<span class="tooltip">';
    	$output .= '<span class="centered">';
    		$output .= '<span class="IECenter"></span>';
    		$output .= '<span class="col1">';
                        if(isset($r->feed_product_image) && $r->feed_product_image != ""){
                           $output .= '<a href="'.esc_url(get_permalink($r->wp_post_id)).'"><img class="tooltip-image" src="'.esc_url($r->feed_product_image).'" alt="'.esc_attr($r->post_product_name).'" title="'.esc_attr($r->post_product_name).'" /></a>'; 
                        } else {
                           $output .= '<a href="'.esc_url(get_permalink($r->wp_post_id)).'"><img class="tooltip-image" src="'.get_template_directory_uri() . '/img/no-photo.png' . '" alt="'.esc_attr($r->post_product_name).'" title="'.esc_attr($r->post_product_name).'" /></a>'; 
                        } 			
				if($r->merchants == 0) {
					$merchants = __('No merchant','framework');
				} elseif($r->merchants == 1) {
					$merchants = __('1 merchant','framework');
				} else {
					$merchants = $r->merchants.' '.__('merchants','framework');
				}
    			$output .= '<span class="merchants">'.$merchants.'</span>';
    			$output .= '<span class="product-price"><span>'.__('From','framework').':</span>';
			$output .= aw_the_formated_price($r->min_price, true);
				$output .= '</span>';
    		$output .= '</span>';
    		$output .= '<span class="col2">';
    			$output .= '<span class="title"><a href="'.esc_url(get_permalink($r->wp_post_id)).'">'.$r->post_product_name.'</a></span>';
    		$output .= '</span>';
    		$output .= '<a href="'.esc_url(get_permalink($r->wp_post_id)).'" class="button">'.((isset($tz_buy_button_text) && $tz_buy_button_text != '') ? stripslashes($tz_buy_button_text) : __('BUY NOW','framework')).'</a>';
    	$output .= '</span>';
    $output .= '</span>';
    
    $output .= '</span>';
    
    $output .= '<script type="text/javascript">jQuery(document).ready(function() { jQuery("#p_'.$id.'").tooltip({ effect: "fade", position: "top right" }); });</script>';
    
     
    return $output;
}
add_shortcode('tooltip', 'tooltip');

// Price Table
function price_table($atts, $content = null) {

	global $wpdb, $merchants, $merchants_length;

	extract(shortcode_atts(array(
		"id" => 0
	), $atts));
	
  
        
	$q = "SELECT pm.*, p.* FROM ".$wpdb->prefix."pc_products_relationships pr, ".$wpdb->prefix."pc_products_merchants pm, ".$wpdb->prefix."pc_products p WHERE pm.slug = p.id_merchant AND p.id_product = pr.id_product AND pr.id_product = '".$id."' ORDER BY p.price ASC";
	$merchants = $wpdb->get_results($q);
	$merchants_length = count($merchants);
     
	if(!$merchants) {
		return;
	}
	
	ob_start();
	do_action('aw_show_price_table'); 
	$output = ob_get_clean();
	
	return $output;
}
add_shortcode('price-table', 'price_table');
?>