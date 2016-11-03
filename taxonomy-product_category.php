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

	<table border="1">
		<tr ng-repeat="x in records | filter: {title: title_filter, screen_width: screen_width_filter} ">
			<td>{{x.title}}</td>
			<td>{{x.screen_width}}</td>
			<td>{{x.ram}}</td> 
		</tr>
	</table>
	
	<input type="checkbox" ng-model='title_filter' ng-true-value="'Putin'" ng-false-value="'Trump'" /> RAM
	<input type="checkbox" ng-model='screen_width_filter'  value="1200"/> Screen Width

</div>


<script>
	var app = angular.module("myApp", []);
	 
	app.controller("myCtrl", function($scope) {
		$scope.screen_width = {1200: true, 1400: true};
		
		$scope.records = [
			{
				"title" : "Alfreds Futterkiste",
				"screen_width" : 1200,
				"ram" : 'yes'
			},
			{
				"title" : "Putin",
				"screen_width" : 1400,
				"ram" : 'yes'
			},
			{
				"title" : "Trump",
				"screen_width" : 1400,
				"ram" : 'no'
			},
			{
				"title" : "Trump",
				"screen_width" : 1200,
				"ram" : true
			}
			<?php
				$j = 0;
				/*$k = count($products);
				foreach($products as $product) {
					$j++;
					echo "'" . $product->post_title . "'";
					if ($j != $k) echo ",";
				}*/
			?>
			
		]
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