<?php
	global $wpdb, $wp_query, $post, $aw_theme_options;
?> 

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<script data-require="angular-ui-bootstrap@0.3.0" data-semver="0.3.0" src="http://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.3.0.min.js"></script>
<div ng-app="myApp" ng-controller="myCtrl">
 

        <?php
			$post_count = 0;
			$scope_products = '';
			$scope_filterSelectedOptions = '';
			
			while(have_posts())
			{ 
				the_post() ;
				
				$image = get_post_meta($post->ID, 'image_meta')[0];
				
				$product_categories = wp_get_post_terms($post->ID, 'product_category');
					
				$brands = array();
				$term_brands = wp_get_post_terms($post->ID,'product_brand');
				foreach($term_brands as $term) {
					$brands[] = $term->name;
				}
				$product_brand = (count($brands) != 0) ? $brands[0] : '';
			
				$params = $wpdb->get_results( 
					"
					SELECT param_name, param_value 
					FROM ".$wpdb->prefix."pc_products_params
					WHERE wp_post_id = " . $post->ID
				);
				
				$retailer_details = $wpdb->get_row( 
					"
					SELECT COUNT(p.id_merchant) AS count_retailers, MIN(p.price) AS lowest_price
					FROM ".$wpdb->prefix."pc_products_relationships pr
					LEFT JOIN ".$wpdb->prefix."pc_products p
					ON pr.id_product = p.id_product
					WHERE pr.wp_post_id = " . $post->ID
				);
				
				$scope_products .= '
				{ 	name: "' . str_replace('"',"''", $post->post_title) . '",
										image: "' . $image . '",
										main_category: "' . $product_categories[1]->name . '",
										category: "' . $product_categories[0]->name . '",
										lowest_price: "' . $retailer_details->lowest_price . '",
										count_retailers: "' . $retailer_details->count_retailers . '",
										parameters: { ';
											$count_params = count($params);
											foreach ( $params as $param ) { 
												$scope_products .= $param->param_name . ' : "' . $param->param_value . '"';
												if (--$count_params !== 0) $scope_products .= ',';
												//Get parameter names (as they are all the same for each product in main category) from the first product
												if ($post_count == 0) {
													$scope_filterSelectedOptions.='' . $param->param_name . ' : []';
													if ($count_params !== 0) $scope_filterSelectedOptions.= ',';
												}
											}
											
					$scope_products .= ', brands: "' . $product_brand . '"';
					$scope_products .= '}},
					';
				
				$post_count++;
			}
		?>
		
<script>
 	
var app = angular.module("myApp", ['ui.bootstrap']);

app.filter('removeUnderscores', function() {
    return function(input) {
      return input.replace("_", " ");
    }
});

app.filter('replaceChars', function() {
    return function(input) {
		formattedInput = input.replace(/"/g, "");
		formattedInput = formattedInput.replace(/'/g, "");
		formattedInput = formattedInput.replace(/\./g, "");
		formattedInput = formattedInput.replace(/,/g, "");
		formattedInput = formattedInput.replace(/ /g, "-");
		return formattedInput;
    }
});

app.filter('startFrom', function() {
    return function(input, start) {
        if(input) {
            start = +start; //parse to int
            return input.slice(start);
        }
        return [];
    }
});

app.controller("myCtrl", ['$scope', '$window', '$timeout', function($scope, $window, $timeout) { 

    $scope.currentPage = 1; //current page
    $scope.maxSize = 4; //pagination max size
    $scope.entryLimit = 20; //max rows for data table
	
	$scope.filterSelectedOptions = {<?php echo $scope_filterSelectedOptions; ?>, brands: []};	
	$scope.products = [<?php echo $scope_products; ?>];
	$scope.filterUniqueParameterOptions = {<?php echo $scope_filterSelectedOptions; ?>, brands: []};
	$scope.filterUniqueProductBrands = [];
		 	
	$scope.toggleSelection = function toggleSelection(parameterName, option) {
		
		$timeout(function() { 
		
			var idx = $scope.filterSelectedOptions[parameterName].indexOf(option);
			// is currently selected
			if (idx > -1) {
				$scope.filterSelectedOptions[parameterName].splice(idx, 1);
			}
			// is newly selected
			else {
				$scope.filterSelectedOptions[parameterName].push(option);
			}
			
			// refresh the number of pages in pagination
			$scope.noOfPages = Math.ceil($scope.filteredProducts.length/$scope.entryLimit);
		}, 500);
	};
	
	$scope.filterProducts = function(product)
	{
		var keepGoing = true; 
		
		angular.forEach($scope.filterSelectedOptions, function(array, parameterName) {
			if (keepGoing) {
				var parameterValue = product.parameters[parameterName];
				var parameterIsInSelectedList = $scope.filterSelectedOptions[parameterName].indexOf(parameterValue);
				
				if (parameterIsInSelectedList == -1)  {
					keepGoing = false;
				}
				if (Object.keys($scope.filterSelectedOptions[parameterName]).length === 0) {					
					keepGoing = true;
				} 				
			}
		});
		
		priceValues = $scope.priceValues.split(";");
		productLowestPrice = parseInt(product.lowest_price);
		filterMinPrice = parseInt(priceValues[0]);
		filterMaxPrice = parseInt(priceValues[1]);
		if (productLowestPrice < filterMinPrice || productLowestPrice > filterMaxPrice) keepGoing = false;
		return keepGoing;		
	}; 
	
	var unique = {};
	var distinct = [];
	for( var i in $scope.filterUniqueParameterOptions ){	
		if( "undefined" === typeof(unique[i])){
			unique[i] = [];
		}
		for( var j in $scope.products ){
			//console.log('parameter: ' + i + ', value: ' + $scope.products[j].parameters[i] + ', unique: ' + unique[i][$scope.products[j].parameters[i]]);
			if( "undefined" === typeof(unique[i][$scope.products[j].parameters[i]])){
				if ($scope.products[j].parameters[i] != "") $scope.filterUniqueParameterOptions[i].push($scope.products[j].parameters[i]);
				unique[i][$scope.products[j].parameters[i]] = 0;
			}
		}
	}
	
    $scope.noOfPages = Math.ceil($scope.products.length/$scope.entryLimit);
		
	$scope.countFilteredProducts = function(){
		$length = ( "undefined" === typeof($scope.filteredProducts)) ? 'all' : $scope.filteredProducts.length;
		return $length;
    }
	
	$scope.priceValues = '10;15000';
	jQuery('.priceSlider').slider({ 
		from: 10, 
		to: 15000, 
		step: 10, 
		smooth: true, 
		round: 0, 
		dimension: '&nbsp;$', 
		skin: 'plastic',
		callback: function( value ){
			$scope.priceValues = value;
			$scope.$apply();
		}
	}); 
					
}]);
</script>
	
<?php 
 
$total_number_of_items = $wp_query->found_posts;
$number_of_items = $wp_query->post_count;
$max_num_pages = $wp_query->max_num_pages;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$term = get_term_by('slug', get_query_var('term'), 'product_category');
$listOrGrid = aw_get_result_layout_style();
get_header();
?>	
<div class="nine columns push_three product-listing 444">
    <script type="text/javascript">
        function aw_more() {
                jQuery('.desc_more').toggle();
                jQuery('.desc_etc').toggle();
                jQuery('#more_link').html((jQuery('#more_link').html() == '<?php _e('More','framework') ?>' ? '<?php _e('Less','framework') ?>' : '<?php _e('More','framework') ?>'));
        }
        </script>
    
    <?php if(have_posts() && 1==1 ):  ?>
    
        <div class="listing-options">
			<?php 
			global $aw_theme_options, $number_of_items, $total_number_of_items, $term, $brand_website_meta;
			?>
			<article>
			<?php if(is_search()): ?>
			<h1><?php _e( 'Search results', 'framework' ) ?></h1>                
			<?php else: ?>
			<h1><?php echo (isset($term->name) ? $term->name : ''); ?></h1>
			<p><?php do_action('aw_the_term_description_detailed', $term, 500); ?></p>
			<?php if($brand_website_meta != ''): ?>
			<p><a href="<?php echo $brand_website_meta ?>"><?php _e("Visit brand's website",'framework'); ?></a></p>
			<?php endif; ?>
			<?php endif; ?>
			</article>  

			<div class="listing-options">           		

			<p class="listing-results">
				Showing <strong>{{countFilteredProducts()}}</strong> product<span ng-if="countFilteredProducts() > 1">s</span>
			</p>

			<div class="listing-params">

			<fieldset id="order_by_placeholder"></fieldset>

			<fieldset>
			<legend><?php _e('View', 'framework'); ?></legend>
			<ul id="list-toggle" class="list-toggle">
			<li class="<?php echo (isset($aw_theme_options['tz_result_display_style']) && $aw_theme_options['tz_result_display_style'] == 'list' ? "active" : ""); ?>" >
			 <a href="#" id="list-layout-switch" class="btn-form list-layout-switch">
			   <i class="icon-list"></i>
			 </a>
			</li>
			<li class="<?php echo ( (isset($aw_theme_options['tz_result_display_style']) && $aw_theme_options['tz_result_display_style'] == 'grid') || ! isset($aw_theme_options['tz_result_display_style']) ? "active" : ""); ?>">
			 <a href="#" id="grid-layout-switch" class="btn-form grid-layout-switch">
			   <i class="icon-layout"></i>
			 </a>
			</li>
			</ul>
			</fieldset>
			</div>
			<script type="text/javascript">
			function aw_more() {
				jQuery('.desc_more').toggle();
				jQuery('.desc_etc').toggle();
				jQuery('#more_link').html((jQuery('#more_link').html() == '<?php _e('More','framework') ?>' ? '<?php _e('Less','framework') ?>' : '<?php _e('More','framework') ?>'));
			}
			</script>    
			</div>    
        </div>
        
			 <pagination data-boundary-links="true" data-num-pages="noOfPages" data-current-page="currentPage" max-size="maxSize" class="pagination-small" data-previous-text="&laquo;" data-next-text="&raquo;"></pagination>
				
        <div class="product-listing-container <?php echo $listOrGrid; ?>">
        
			<div class="product" ng-repeat="product in filteredProducts = (products | filter:filterProducts) | startFrom:(currentPage-1)*entryLimit | limitTo:entryLimit">
				<div class="product-photo">
					<a href="<?php echo get_home_url(); ?>/{{product.main_category | lowercase | replaceChars}}/{{product.category | lowercase | replaceChars}}/{{product.name | lowercase | replaceChars}}">
						<img ng-src="{{product.image}}" alt="<?php echo esc_attr(get_the_title()); ?>" />  
					</a>
				</div>

				<div class="product-desc">
					<h2>
						<a href="<?php echo get_home_url(); ?>/{{product.main_category | lowercase | replaceChars}}/{{product.category | lowercase | replaceChars}}/{{product.name | lowercase | replaceChars}}">
							{{product.name}}
						</a>
					</h2>
				</div>

				<div class="product-view">
					<div>
						<p class="price">
							<span>
								<span ng-if="product.count_retailers > 1">from </span>${{product.lowest_price}}
							</span>
						</p>
						<a href="<?php echo get_home_url(); ?>/{{product.main_category | lowercase | replaceChars}}/{{product.category | lowercase | replaceChars}}/{{product.name | lowercase | replaceChars}}" class="retailers">
							{{product.count_retailers}} merchant<span ng-if="product.count_retailers > 1">s</span>
						</a>
					</div>
					<div class="medium primary btn metro rounded">
						<a href="<?php echo get_home_url(); ?>/{{product.main_category | lowercase | replaceChars}}/{{product.category | lowercase | replaceChars}}/{{product.name | lowercase | replaceChars}}">
							<?php _e('Compare Prices', 'framework'); ?>
						</a>
					</div>
				</div>
			</div>
	
        </div>
			
    
    <?php endif; ?>
   
</div>
  
	
<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>