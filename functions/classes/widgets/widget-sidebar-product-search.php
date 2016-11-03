<?php
/*
 * Plugin Name: Product search
 * Plugin URI: http://www.awesem.com
 * Description: A widget that allows the integration of a sidebar search module
 * Version: 1.1
 * Author: AWESEM
 * Author URI: http://www.awesem.com
 * 
 */

/*
 * Add function to widgets_init that'll load our widget.
 */
add_action( 'widgets_init', 'tz_product_search_widget' );

/*
 * Register widget.
 */
function tz_product_search_widget() {
	register_widget( 'TZ_product_search_widget' );
}

/*
 * Widget class.
 */
class TZ_product_search_widget extends WP_Widget {
	/* ---------------------------- */
	/* -------- Widget setup -------- */
	/* ---------------------------- */
	function __construct() {	
		
		/* Widget settings */
		$widget_ops = array( 'classname' => 'tz_product_search_widget tz_SPC_widget', 'description' => __('A widget that allows the integration of a sidebar search module.', 'framework') );
		
		/* Widget control settings */
		$control_ops = array( 'width' => 125, 'id_base' => 'tz_product_search_widget' );

		/* Create the widget */
		parent::__construct('tz_product_search_widget', __('COMPARE: Sidebar Product Search', 'framework'), $widget_ops, $control_ops );
		
	}

	/* ---------------------------- */
	/* ------- Display Widget -------- */
	/* ---------------------------- */
	function widget( $args, $instance ) {
		
		global $wp_query;
		extract( $args );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$minPrice = $instance['minPrice'];
		$maxPrice = $instance['maxPrice'];
                $hide_filter_btn_input = ($instance['hide_filter_btn'] == "1"  ? "display:none;" : "");
                $hide_keyword_input = ($instance['hide_keywords'] == "1" ? "display:none;" : "");
                $hide_category_input = ($instance['hide_category'] == "1" ? "display:none;" : "");
                $hide_brand_input = ($instance['hide_brand'] == "1" ? "display:none;" : "");
                $hide_price_slider_input = ( $instance['hide_price_slider'] == "1" ? "display:none;" : "");
                
                if($maxPrice == 0){
                    $maxPrice = ceil(aw_get_min_max_price('max'));
                }
                if($minPrice == 0){
                    $minPrice = floor(aw_get_min_max_price('min'));
                }
		$step = $instance['step'];
                if($maxPrice == 0 && $minPrice == 0 ){
                    $minPrice = 0;
                    $maxPrice = 10;
                } elseif ($maxPrice == $minPrice && $minPrice != 0 && $maxPrice != 0){
                    $maxPrice = $maxPrice + 10;
                }

                $ajaxImgHtml = '<p style="width:100%; text-align:center; margin-top:20px;"><img src="' . get_template_directory_uri() . '/img/ajax-loader.gif" / ></p>';
                $wrapHtml = '<span class="wrap" />';
                $searchPlaceholder = __('Search...','framework'); ?>
                <script type='text/javascript'>
                    var ajax_mode = false;			
		    jQuery(document).ready(function($) {
	
                       // Fix for the ajax loading problem on mobiles.
                       window.ajaxSearchCheckBoxesAlreadyLoaded = (function(){
                        var checkBoxInitialValues = {};
                        jQuery('.compare_attribute[type=checkbox]').each(function(i,o){
                                checkBoxInitialValues[$(o).attr('name')] = $(o).is(':checked');
                            });
                        return checkBoxInitialValues;
                       })();
                        /*Hide sidebar elements by default*/
                        $('#compare-sidebar-search-categories').slideToggle( "fast" );
                        $('#compare-sidebar-search-brand').slideToggle( "fast" );
        
                        /*Hide and show sidebar elements*/
                        $('#compare-sidebar-search-category-header').click(function(){
                            $('#compare-sidebar-search-category-header').toggleClass('open');
                            $('#compare-sidebar-search-categories').slideToggle( "fast" );
                        });
                        
                        $('#compare-sidebar-search-brand-header').click(function(){
                            $('#compare-sidebar-search-brand-header').toggleClass('open');
                            $('#compare-sidebar-search-brand').slideToggle( "fast" );
                        });
        
                        $('.compare_products_order_by_select_box').live('change', function(event){
                            var live_order_html = $(this).html();
                            var live_order_value = $(this).val();				
                            $('.main_compare_products_order_by_select_box option').removeAttr('selected');
                            $('.main_compare_products_order_by_select_box option[value=\''+live_order_value+'\']').attr('selected', '');
                            jQuery( ".ajax-filter-btn" ).trigger( "click" );
                        })
                        var order_by_object  = jQuery('#compare_products_order_by_select_box<?php echo $this->number; ?>').html();
                        jQuery('#order_by_placeholder').html('<legend><?php _e('Sort By', 'framework'); ?></legend><ul><li class="field"><div class="picker"><select class=\'compare_products_order_by_select_box\'>'+order_by_object+'</select></div></li></ul>');


                        // Keyword enter event
                        $('.filterTextField<?php echo $this->number; ?>').keydown(function(e) {
                                if(e.keyCode == 13 || e.which == 13){
                                    <?php echo "jQuery.filterProductsCompare" . $this->number . "();"; ?>
                                }
                        });
                        
                        jQuery('#<?php echo $this->id; ?> .priceSlider').slider({ 
                            from: <?php echo $minPrice; ?>, 
                            to: <?php echo $maxPrice; ?>, 
                            step: <?php echo $step; ?>, 
                            smooth: true, 
                            round: 0, 
                            dimension: '&nbsp;<?php echo compare_get_currency('symbol'); ?>', 
                            skin: 'plastic',
                                callback: function( value ){
                                        jQuery.filterProductsCompare<?php echo $this->number; ?>();
                                }
                        });
                        
                        // Hack so that the pagination links work as ajax request
			jQuery('.ajax-search-navigation-links a').live('click', function(e){
				e.preventDefault();
				// scroll body to 0px on click
				$('body,html').animate({
					scrollTop: 0
				}, 800);	
				var link = jQuery(this).attr('href');
				var product_order_oject = jQuery('.main_compare_products_order_by_select_box').html();
				var order_value = jQuery('.main_compare_products_order_by_select_box').val();
				$ajaxImgHtml = '<p style="width:100%; text-align:center; margin-top:20px;"><img src="<?php echo  get_template_directory_uri(); ?>/img/ajax-loader.gif" / ></p>';
				jQuery('.master-row div:first').html($ajaxImgHtml);												
				jQuery('.master-row div:first').load(link,function() {
					if(jQuery('#order_by_placeholder').html() != null){
						jQuery('#order_by_placeholder').html('<legend><?php _e('Sort By', 'framework'); ?></legend><ul><li class="field"><div class="picker"><select class=\'compare_products_order_by_select_box\'>'+order_by_object+'</select></div></li></ul>');
						//Below IE9 hook
						$('#order_by_placeholder select option').removeAttr('selected');
						$('#order_by_placeholder select option[value=\''+order_value +'\']').attr('selected', '');	
					}					
				})
			});
                        
				
                        
                        /* Gumby hack > elements' on events*/
                        $('.category_menu_child').on('gumby.onChange', function(e) {
                            if(jQuery(e.currentTarget).find('input').attr('type') === 'checkbox'){
                                 // Some code to make sure that we only search if the checkbox has actually been changed and that it isn't just a fake trigger from gumby
                                if(typeof window.ajaxSearchCheckBoxesAlreadyLoaded === 'undefined'){
                                        jQuery.filterProductsCompare<?php echo $this->number; ?>();
                                    }
                                    else{
                                    var name = jQuery(e.currentTarget).find('input').attr('name');
                                    if(jQuery(e.currentTarget).find('input').is(':checked') !== window.ajaxSearchCheckBoxesAlreadyLoaded[name]){
                                        jQuery.filterProductsCompare<?php echo $this->number; ?>();
                                    }
                                    else{
                                        delete window.ajaxSearchCheckBoxesAlreadyLoaded;
                                    }
                                } 
                            }
                            else{
                                jQuery.filterProductsCompare<?php echo $this->number; ?>();
                            }
                        })                        
                        $('.ajax-filter-btn').on(Gumby.click, function(e) {                           
                            jQuery.filterProductsCompare<?php echo $this->number; ?>();
                        })
                        /* EO Gumby hack */
                        
                        jQuery('#<?php echo $this->id; ?> .compare_attribute').live('change', jQuery.filterProductsCompare<?php echo $this->number; ?> = function() {
                            var order_by_object  = jQuery('#compare_products_order_by_select_box<?php echo $this->number; ?>').html();
                            var order_value = jQuery('#compare_products_order_by_select_box<?php echo $this->number; ?>');	
                            order_value = order_value.length>0 ? order_value.val().split(','): '<?php echo get_option('tz_products_order'); ?>,<?php echo get_option('tz_products_order_by'); ?>';
                            var order = order_value[1];
                            var orderBy = order_value[0];
                            
                            jQuery('body').removeClass('single-product');
                            jQuery('.master-row div:first').removeClass('single-product').addClass('product-listing');
                            jQuery('.master-row div:first').html('<?php echo $ajaxImgHtml; ?>');
                            var brands = new Array();
                            var categories = new Array();
                            var keywords_compare = new Array();
                            var minPrice = <?php echo $minPrice; ?>;
                            var maxPrice = <?php echo $maxPrice; ?>;
                            jQuery('#<?php echo $this->id; ?> .compare_attribute_group').each(function() {
                                
                                var isEmptyPlaceholder = 0;
                                if(jQuery(this).attr('type') != 'slider'){ // If not slider
                                    if(jQuery(this).attr('name') == 'k'){ // If keyword input
                                            if(jQuery.trim(jQuery(this).attr('value')) != ''){
                                                    if(!jQuery.support.placeholder) { 
                                                            if(jQuery.trim(jQuery(this).attr('value')) ==  '<?php echo $searchPlaceholder; ?>' ){
                                                                    jQuery(this).val('');
                                                                    isEmptyPlaceholder = 1;
                                                            }								
                                                    }
                                                    keyword_field_compare = jQuery.trim(jQuery(this).attr('value'));
                                                    keywords_compare = keyword_field_compare.split(' ');
                                            }
                                    }
                                    jQuery(this).find('li label input.compare_attribute').each(function(i, inputelement) { // If categories or brands
                                    
                                        if($(inputelement).attr('checked')) {						
                                            
                                            if($(inputelement).attr('name').indexOf('b[') != -1){					  		
                                                    brands.push($(inputelement).attr('value'));

                                            } 
                                            if($(inputelement).attr('name').indexOf('c[') != -1){					  		
                                                    categories.push($(inputelement).attr('value'));

                                            }
                                        }
                                    });
                                } else {
                                    minmaxarr = jQuery(this).attr('value').split(';');
                                    minPrice = minmaxarr[0];
                                    maxPrice = minmaxarr[1];
                                }

                                if(!jQuery.support.placeholder) { 
                                    if(isEmptyPlaceholder == 1){
                                        jQuery(this).val('<?php echo $searchPlaceholder; ?>');
                                    }								
                                } 	
                            })
			    		 
						
						
                            jQuery.ajax({
                                    url: '<?php echo home_url(); ?>/',							  
                                    cache: false,
                                    data: { 
                                          ajaxsearch: '1',
                                          s: 'compare', 
                                          k: keywords_compare,
                                          product: '',
                                          c: categories, 
                                          b : brands,
                                          min: minPrice,
                                          max: maxPrice,
                                          order: order,
                                          order_by: orderBy
                                          },
                                    success: function(data) {
                                    ajax_mode = true;
                                    $('body,html').animate({
					scrollTop: 0
                                    }, 800);
                                    jQuery('.master-row div:first').html(data);                                  
                                    if(jQuery('#order_by_placeholder').html() != null){           
                                            order_by_object  = jQuery('#compare_products_order_by_select_box<?php echo $this->number; ?>').html();
                                            jQuery('#order_by_placeholder').html('<legend><?php _e('Sort By', 'framework'); ?></legend><ul><li class="field"><div class="picker"><select class=\'compare_products_order_by_select_box\'>'+order_by_object+'</select></div></li></ul>');

                                            //Below IE9 hook
                                            $('#order_by_placeholder select option').removeAttr('selected');
                                            $('#order_by_placeholder select option[value=\''+order_value +'\']').attr('selected', '');	

                                            jQuery('#order_by_placeholder select option').each (function(){

                                            })
                                        }
                                    }
                            });	 
                            if(!jQuery.support.placeholder) { 
                            /*if(isEmptyPlaceholder == 1){
                                            jQuery(this).val('<?php echo $searchPlaceholder; ?>');
                            }	*/							
                            } 			
                    }); 
                });
            </script>
		
		<?php
                // If at least one component visible
                if($instance['hide_filter_btn'] != "1" || $instance['hide_keywords'] != "1" || $instance['hide_category'] != "1" || $instance['hide_brand'] != "1" || $instance['hide_price_slider'] != "1"){
                    /* Before widget (defined by themes). */
                    echo $before_widget;
                }

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		?>
		<form <?php if($instance['hide_filter_btn'] == "1" && $instance['hide_keywords'] == "1" && $instance['hide_category'] == "1" && $instance['hide_brand'] == "1" && $instance['hide_price_slider'] == "1") echo " style='display:none;'"; ?>>
		
		<div class="product_order_select_wrapper" style="display:none;">
		<?php $product_order_options = aw_get_product_order_options();
		$order_by = $product_order_options["order_by"];
		//$order_by = get_product_order_by_database_field_from_friendly_name($order_by);
		$order = $product_order_options["order"];
		$options_array = array(
			array(
				'text'=>'Minimum price ascending',
				'value'=>'min_price,asc',
				'selected'=>(($order=='asc' && $order_by=='min_price')?'selected':'')
			),
			array(
				'text'=>'Minimum price descending',
				'value'=>'min_price,desc',
				'selected'=>(($order=='desc' && $order_by=='min_price')?'selected':'')
			),
			array(
				'text'=>'Product name ascending',
				'value'=>'title,asc',
				'selected'=>(($order=='asc' && $order_by=='title')?'selected':'')
			),
			array(
				'text'=>'Product name descending',
				'value'=>'title,desc',
				'selected'=>(($order=='desc' && $order_by=='title')?'selected':'')
			),
					array(
				'text'=>'Date added ascending',
				'value'=>'date_added,asc',
				'selected'=>(($order=='asc' && $order_by=='date_added')?'selected':'')
			),
			array(
				'text'=>'Date added descending',
				'value'=>'date_added,desc',
				'selected' =>(($order=='desc' && $order_by=='date_added')?'selected':'')
			)
                );
		$select_box = '<select class="main_compare_products_order_by_select_box compare_products_order_by_select_box" id="compare_products_order_by_select_box'. $this->number .'">';
		foreach($options_array as $option){
			$select_box.='<option value="'.$option['value'].'" '.$option['selected'].'>'.$option['text'].'</option>';
		}
		$select_box.= '</select>';
		echo $select_box;	
		
		?>
		</div><span class="clear"></span>
		<div style="<?php echo $hide_keyword_input; ?>" >			
                        <ul>
                            <li class="field">
                                    <input placeholder="Search for..."  class="xxwide input compare_attribute_group filterTextFieldStyling filterTextField<?php echo $this->number; ?>" <?php echo (isset($_GET['s']) && trim($_GET['s']) != "") ? "value='".sanitize_text_field(stripslashes(trim($_GET['s'])))."'" : ""; ?> type="search" name="k"  />
                            </li>
                        </ul>
                </div>
               
				<?php 
					$this_category = "";
					if(isset($wp_query->query_vars['taxonomy']) && $wp_query->query_vars['taxonomy'] == 'product_category'){
					$this_category = $wp_query->query_vars['term'];
					}
				
				?>
                <div style="<?php echo $hide_category_input; ?>">
                    <h4 id="compare-sidebar-search-category-header" class="widget-title"><?php _e('Categories','framework'); ?>
                        <div class="arrow"></div>
                    </h4>
                    <ul id="compare-sidebar-search-categories" class="field row compare_attribute_group">
                    <?php
                    $this->theListTree( 'product_category', $categories, $children, $parent = 0, $level = 0, $i = 0, $this_category);               
                    ?>			
                    </ul>
                </div>
                <?php    
		$brands = get_terms('product_brand');
                $children = $this->_get_term_hierarchy( 'product_brand' );
		$this_brand = "";
                if(isset($wp_query->query_vars['taxonomy']) && $wp_query->query_vars['taxonomy'] == 'product_bisbrand'){
                    $this_brand = $wp_query->query_vars['term'];
                }
				
                if(isset($wp_query->query_vars['taxonomy']) && $wp_query->query_vars['taxonomy'] == 'product_brand'){
                    $this_brand = $wp_query->query_vars['term'];
                }
				
		
		?>
                <div style="<?php echo $hide_brand_input; ?>">
                    <h4 id="compare-sidebar-search-brand-header" class="widget-title"><?php _e('Brands','framework'); ?>
                        <div class="arrow"></div>
                    </h4>
                    <ul id="compare-sidebar-search-brand" class="field row compare_attribute_group">
                    <?php               
                    $this->theListTree( 'product_brand', $brands, $children, $parent = 0, $level = 0, $i = 0, $this_brand);               
                    ?>			
                    </ul>
                </div>
			
		<div style="<?php echo $hide_price_slider_input; ?>">
                        <h4 class="widget-title"><?php _e('Price range:', 'framework'); ?></h4>
			<input class="priceSlider compare_attribute_group compare_attribute" type="slider" name="price" value="<?php  echo $minPrice. ';' . $maxPrice; ?>" />		
		</div>
		
		<?php
		echo '<input type="hidden" type="text" name="s" value="compare"  />';
		echo '<input type="hidden" name="product" value="" />';		
		?>
			
		<div style="<?php echo $hide_filter_btn_input; ?>">
                        <div class="medium primary btn metro rounded ajax-filter-btn">
                            <a class="button" class="wrap" ><?php echo __('Filter','framework'); ?></a>
                        </div>
		</div>
		
		</form>
		<?php
                // If at least one component visible
                if($instance['hide_filter_btn'] != "1" || $instance['hide_keywords'] != "1" || $instance['hide_category'] != "1" || $instance['hide_brand'] != "1" || $instance['hide_price_slider'] != "1"){
                    /* After widget (defined by themes). */
                    echo $after_widget;
                }
	
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		/* No need to strip tags */
		if (is_numeric ($new_instance['minPrice'])) $instance['minPrice'] = floor($new_instance['minPrice']);
		if (is_numeric ($new_instance['maxPrice'])) $instance['maxPrice'] = ceil($new_instance['maxPrice']);
		if (is_numeric ($new_instance['step'])) $instance['step'] = $new_instance['step'];
		
		$instance['hide_keywords'] = $new_instance['hide_keywords'];
	
		$instance['hide_category'] = $new_instance['hide_category'];
		
		$instance['hide_brand'] = $new_instance['hide_brand'];
		
		$instance['hide_price_slider'] = $new_instance['hide_price_slider'];
		
		$instance['hide_filter_btn'] = $new_instance['hide_filter_btn'];
	
		return $instance;
	}
	
	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	
	function form( $instance ) {
		//print_r($instance);
		/* Get stuff for price range slider */ 
		$max_price = 0;
		$min_price = 0;
		
		/* Set up some default widget settings. */
		$defaults = array(
                    'title' => '',
                    'minPrice' => $min_price,
                    'maxPrice' => $max_price,
                    'step' => '1',		
                    'hide_keywords' => '',		
                    'hide_category' => '',		
                    'hide_brand' => '',		
                    'hide_price_slider' => '0',		
                    'hide_filter_btn' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<!-- Widget Title: Text Input -->
		<script type="text/javascript">
		function resetSidebarSearchWidget(event){
			event.preventDefault();
			jQuery(".sidebar_search_widget_settings .title").val('');
			jQuery(".sidebar_search_widget_settings .minprice").val('0');
			jQuery(".sidebar_search_widget_settings .maxprice").val('0');
			jQuery(".sidebar_search_widget_settings .step").val('1');
			jQuery(".sidebar_search_widget_settings .hide_keywords").removeAttr("checked");
			jQuery(".sidebar_search_widget_settings .hide_category").removeAttr("checked");
			jQuery(".sidebar_search_widget_settings .hide_brand").removeAttr("checked");
			jQuery(".sidebar_search_widget_settings .hide_price_slider").removeAttr("checked");
			jQuery(".sidebar_search_widget_settings .hide_filter_btn").removeAttr("checked");			
			return;
		}
		</script>

		<div class="sidebar_search_widget_settings">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'framework') ?></label>
				<input class="widefat title" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
			
			<!-- Widget Min Price: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'minPrice' ); ?>"><?php _e('Min price:', 'framework') ?></label>
							<br>
							<label>Set this as 0 to get the minimum from the database, specify a limit if there is a slowdown, you can use the the link below to get the current minimum.</label>
				<input class="widefat minprice" id="<?php echo $this->get_field_id( 'minPrice' ); ?>" name="<?php echo $this->get_field_name( 'minPrice' ); ?>" value="<?php echo $instance['minPrice']; ?>" />
				<a href="#" onClick="jQuery(this).prev().val('<?php echo floor(aw_get_min_max_price('min')); ?>')">Get current minimum</a>
			</p>
			
			<!-- Widget Max Price: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'maxPrice' ); ?>"><?php _e('Max price:', 'framework') ?></label>
							<br>
							<label>Set this as 0 to get the maximum from the database, specify a limit if there is a slowdown, you can use the the link below to get the current maximum.</label>
				<input class="widefat maxprice" id="<?php echo $this->get_field_id( 'maxPrice' ); ?>" name="<?php echo $this->get_field_name( 'maxPrice' ); ?>" value="<?php echo $instance['maxPrice']; ?>" />
				<a href="#" onClick="jQuery(this).prev().val('<?php echo ceil(aw_get_min_max_price('max')); ?>')">Get current maximum</a>
			</p>
			
			<!-- Widget Step: Text Input -->
			<p>
				<label for="<?php echo $this->get_field_id( 'step' ); ?>"><?php _e('Step:', 'framework') ?></label>
				<input class="widefat step" id="<?php echo $this->get_field_id( 'step' ); ?>" name="<?php echo $this->get_field_name( 'step' ); ?>" value="<?php echo $instance['step']; ?>" />	
			</p>
			
			<p>
				<label><?php _e('Hide widget sections:', 'framework') ?></label><br />				
				<input type="checkbox" class="hide_keywords" id="<?php echo $this->get_field_id( 'hide_keywords' ); ?>" name="<?php echo $this->get_field_name( 'hide_keywords' ); ?>" <?php echo ($instance['hide_keywords'] == "1" ? "checked='checked'" : ""); ?> value="1" /><span> <?php _e('Search box', 'framework') ?></span><br />	
				<input type="checkbox" class="hide_brand" id="<?php echo $this->get_field_id( 'hide_brand' ); ?>" name="<?php echo $this->get_field_name( 'hide_brand' ); ?>" <?php echo ($instance['hide_brand'] == "1" ? "checked='checked'" : ""); ?> value="1" /><span> <?php _e('Brands select box ', 'framework') ?></span><br />	
				<input type="checkbox" class="hide_category" id="<?php echo $this->get_field_id( 'hide_category' ); ?>" name="<?php echo $this->get_field_name( 'hide_category' ); ?>" <?php echo ($instance['hide_category'] == "1" ? "checked='checked'" : ""); ?> value="1" /><span> <?php _e('Categories select box', 'framework') ?></span><br />	
				<input type="checkbox" class="hide_price_slider" id="<?php echo $this->get_field_id( 'hide_price_slider' ); ?>" name="<?php echo $this->get_field_name( 'hide_price_slider' ); ?>" <?php echo ($instance['hide_price_slider'] == "1" ? "checked='checked'" : ""); ?> value="1" /><span> <?php _e('Price Slider', 'framework') ?></span><br />	
				<input type="checkbox" class="hide_price_slider" id="<?php echo $this->get_field_id( 'hide_filter_btn' ); ?>" name="<?php echo $this->get_field_name( 'hide_filter_btn' ); ?>" <?php echo ($instance['hide_filter_btn'] == "1" ? "checked='checked'" : ""); ?> value="1" /><span> <?php _e('Filter Button', 'framework') ?></span><br />	
			</p>
			
			<a href="#" onClick="resetSidebarSearchWidget(event);">Reset</a>
		</div>
	<?php
	}
        
        function single_row_category( $term, $level, $i, $currentCategory ){
            
            echo '<li class="field category_menu_child' . ( $level != 0 ? ' sub_item_filter submenu-' . $level : '' ) . '">
                    <label class="checkbox' . ($currentCategory == $term->slug ? ' checked' : '') . '" for="c_'.$i.'">
                    <input class="compare_attribute" type="checkbox" name="c['.$i.']" title="c_'.$i.'" value="'.$term->slug.'"' . ($currentCategory == $term->slug ? ' checked="checked"' : '') . ' />
                    <span></span> ' . $term->name . '
                    </label>
                </li>';      
                     
        }
        
        function single_row_brand( $term, $level, $i, $currentBrand ){
            
            echo '<li class="field category_menu_child' . ( $level != 0 ? ' sub_item_filter submenu-' . $level : '' ) . '">
                    <label class="checkbox' . ($currentBrand == $term->slug ? ' checked' : '') . '" for="b_'.$i.'">
                    <input class="compare_attribute" type="checkbox" name="b['.$i.']" title="b_'.$i.'" value="'.$term->slug.'"' . ($currentBrand == $term->slug ? ' checked="checked"' : '') . ' />
                    <span></span> ' . $term->name . '
                    </label>
                </li>';      
                     
        }
        
        function theListTree( $taxonomy = 'product_category', $terms, &$children, $parent = 0, $level = 0, $i = 0, $currentItem ) {

		foreach ( $terms as $key => $term ) {
                        
                    
			if ( $term->parent != $parent  )
                            continue;
			
                        echo "\t";    
                        if($taxonomy == 'product_category'){
                            $this->single_row_category( $term, $level, $i, $currentItem );
                        } else if ($taxonomy == 'product_brand'){
                            
                            $this->single_row_brand( $term, $level, $i, $currentItem );
                        }
                        $i++;
			
			unset( $terms[$key] );

			if ( isset( $children[$term->term_id] ) )
				$this->theListTree( $taxonomy, $terms, $children, $term->term_id, $level + 1, $i, $currentItem );
		}
	}
        
        function _get_term_hierarchy($taxonomy) {
            
            if ( !is_taxonomy_hierarchical($taxonomy) )
                    return array();
            $children = get_option("{$taxonomy}_children");

            if ( is_array($children) )
                    return $children;
            $children = array();
            $terms = get_terms($taxonomy, array('get' => 'all', 'orderby' => 'id', 'fields' => 'id=>parent'));
            foreach ( $terms as $term_id => $parent ) {
                    if ( $parent > 0 )
                            $children[$parent][] = $term_id;
            }
            update_option("{$taxonomy}_children", $children);

            return $children;
        
        }
        
        
	
}
?>
