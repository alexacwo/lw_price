<?php

global $wpdb, $wp_query, $post, $aw_theme_options;


// determine the topmost parent of a term
function get_product_topmost_parent_cat($term_id){

	$current_cat = get_term_by('id', $term_id, 'product_category');						
	$parent = $current_cat->parent;
	
	return $parent == 0 ? $current_cat->name : get_product_topmost_parent_cat($parent);
}

//Current taxonomy id
$term_id = get_queried_object_id();
//Topmost parent id
$topmost_parent_cat_id = get_product_topmost_parent_cat($term_id);

////////////////////////////////////////////////////////////
/////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/////////// TERM_ID нужен для вывода продуктов данной категории
/////////// TOPMOST PARENT CAT ID нужен для сортировки, т.к. к этой родительской категории максимального уровня будут привязаны характеристики для сортировки
/////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
/////////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
////////////////////////////////////////////////////////////

var_dump($term_id);
var_dump($topmost_parent_cat_id);

echo "<br><br>dddddddddddddddddddddddddddddddddddddddd<br><br>";
					
$args = array(
    'post_type' => 'product',
	'product_category' => $term_id
);
$products = get_posts( $args );
?> 

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>

<div ng-app="myApp" ng-controller="myCtrl">
	Products:
	<table>
		<tr ng-repeat="product in products | filter: filterProducts">
			<td style="border:1px solid;">
				<br>Name: {{product.name}}
				<br>Parameters: {{product.parameters}}
			</td>
		</tr>
	</table>

	{{filterSelectedOptions}}
	<br><br>{{filterParameterOptions}}
	<div ng-repeat="(parameterName, parameterOptions) in filterParameterOptions">
		<h2>{{parameterName}}</h2>
		<div ng-repeat="option in parameterOptions">
		  <input
			type="checkbox"
			name="selectedOptions[]"
			value="{{option}}"
			ng-click="toggleSelection(parameterName, option)"
		  > {{option}}
		</div>
	</div>
</div>

        <?php
			$post_count = 0;
			$scope_products = '';
			$scope_filterSelectedOptions = '';
			while(have_posts())
			{
				the_post();
				$params = $wpdb->get_results( 
					"
					SELECT param_name, param_value 
					FROM ".$wpdb->prefix."pc_products_params
					WHERE wp_post_id = " . $post->ID
				);
				
				$scope_products .= '{ name: "' . $post->post_title . '",
									parameters: { ';
						$count_params = count($params);
						foreach ( $params as $param ) { 
							$scope_products .= $param->param_name . ' : "' . $param->param_value . '"';
							if (--$count_params !== 0) $scope_products .= ',';
							//Get parameter names (as they are equal for each product in the category) from the first product
							if ($post_count == 0) {
								$scope_filterSelectedOptions.='' . $param->param_name . ' : []';
								if ($count_params !== 0) $scope_filterSelectedOptions.= ',';
							}
						}
				//if (--$post_desc_count != 0)
					$scope_products .= '}},';
				
				$post_count++;
			}
		?>
		
<script>
var app = angular.module("myApp", []);

app.controller("myCtrl", function($scope) { 

	//$scope.filterSelectedOptions = {<?php echo $scope_filterSelectedOptions; ?>};
	$scope.filterSelectedOptions = {"green_compliance":[],"operating_system":[],"hdmi":[]};
	$scope.filterParameterOptions = {"green_compliance":["yes","no"],"operating_system":["ios","windows"],"hdmi":["wer","de","ee"]};
	$scope.products = [<?php echo $scope_products; ?>];
	//$scope.filterParameterOptions = {<?php echo $scope_filterSelectedOptions; ?>};
	
	$scope.toggleSelection = function toggleSelection(parameterName, option) {
		var idx = $scope.filterSelectedOptions[parameterName].indexOf(option);

		// is currently selected
		if (idx > -1) {
			$scope.filterSelectedOptions[parameterName].splice(idx, 1);
		}

		// is newly selected
		else {
			$scope.filterSelectedOptions[parameterName].push(option);
		}
	};
	

	
	$scope.filterProducts = function(product)
	{
		var result = true;
		var keepGoing = true; 
		
		for (var parameterName in $scope.filterSelectedOptions){
			if (Object.keys($scope.filterSelectedOptions[parameterName]).length === 0) {
					return true;
				}
			var parameterValue = product.parameters[parameterName];
			var parameterIsInSelectedList = $scope.filterSelectedOptions[parameterName].indexOf(parameterValue);
			
			if (parameterIsInSelectedList == -1)  {
				return false;
			}	 
		}
		
		return true;
	}; 
	
		 
	/*var unique = {};
	var distinct = [];
	for( var i in $scope.filterParameterOptions ){	
		for( var j in $scope.products ){		
			if( typeof(unique[$scope.products[j].parameters[i]]) == "undefined"){
				$scope.filterParameterOptions[i].push($scope.products[j].parameters[i]);
				unique[$scope.products[j].parameters[i]] = 1;
			}
		}
	}*/
});
</script>
		

<?php
					echo "<br><br>aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa<br><br>";
/* Дальше - выводим список продуктов с ангуларовской пагинацией и фильтруем */

 

$total_number_of_items = $wp_query->found_posts;
$number_of_items = $wp_query->post_count;
$max_num_pages = $wp_query->max_num_pages;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$term = get_term_by('slug', get_query_var('term'), 'product_category');
$listOrGrid = aw_get_result_layout_style();
get_header();
?>

<div class="nine columns push_three product-listing 2222222222222222">
    <script type="text/javascript">
        function aw_more() {
                jQuery('.desc_more').toggle();
                jQuery('.desc_etc').toggle();
                jQuery('#more_link').html((jQuery('#more_link').html() == '<?php _e('More','framework') ?>' ? '<?php _e('Less','framework') ?>' : '<?php _e('More','framework') ?>'));
        }
        </script>
    
    <?php if(have_posts() && 1==1 ):  ?>
    
        <div class="listing-options">
            <?php do_action('aw_show_listing_options'); ?>
        </div>
        
        <div class="product-listing-container <?php echo $listOrGrid; ?>">
        <?php
        $i = 1; //  1,2,3 iteration in order to set properly responsive design
        $itotal = $number_of_items; // total products in page
        $it = 1; // total iterated
        while(have_posts()): the_post();  
            do_action('aw_show_product_archive_content', $post);
            if($i == 3) $i = 1; else $i++;
            $it++;
        endwhile;
        ?>
        <?php do_action('aw_show_pagination', $max_num_pages, $paged ); ?>

        </div>
    
    <?php endif; ?>
    
</div>
	
<?php get_sidebar(); ?>

<?php get_footer(); ?>