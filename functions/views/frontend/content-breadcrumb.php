<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php

/**
 * Includable breadcrumb
 */

$current_term = get_term_by('slug',get_query_var('term'), get_query_var('taxonomy'));

?>

<span class="you-are-here"><?php _e('You are here','framework'); ?>: </span> <a href="<?php  echo home_url(); ?>/"><?php _e('Home','framework'); ?></a> > 
	<?php
        
        
        
	if($current_term && $current_term->parent != 0) {
                //Array to hold array of parents
                $parent_array;
                $array_term = $current_term;
                //While the current term has a parent
                while(get_term($array_term->parent,$array_term->taxonomy)->name != "")
                {
                    //Add the parent to the array
                    $parent_array[] = get_term($array_term->parent,$array_term->taxonomy);
                    //Move up the ladder of parents
                    $array_term = get_term($array_term->parent,$array_term->taxonomy);
                }

                //Display parents in reverse order
                if(is_array($parent_array))
                {
                    $parent_array = array_reverse($parent_array);
                    foreach($parent_array as $item)
                    {
                    ?>
                    <a href="<?php echo get_term_link($item->slug,$item->taxonomy) ?>"><?php echo $item->name ?></a> >
                    <?php
                    }
                }
	}
	?>
	
	<?php if(is_tax()): // BRANDS ?>
		<?php 
			if(is_tax('product_brand')):
			$category = get_term_by('slug',$wp_query->query_vars['product_category'],'product_category');
			if($category->parent != 0) {
                            do {
					$category_parent = get_term($category->parent,$category->taxonomy);
					?>
					<a href="<?php echo get_term_link($category_parent->slug,$category_parent->taxonomy) ?>"><?php echo $category_parent->name ?></a> >
					<?php
				} while($category_parent->parent != 0);	
			}
		?>
		<a href="<?php echo get_term_link($category->slug,$category->taxonomy) ?>"><?php echo $category->name ?></a> > 
		<?php endif; ?>
                <?php echo $current_term->name ?>
	<?php endif; ?>

	<?php 
	// Blog
	if( is_home() || (  get_post_type() == 'post' && is_single() ) || (  get_post_type() == 'post' && is_category() ) || (  get_post_type() == 'post' && is_archive() ) ) {
                               echo  __('Blog');
                                if( get_post_type() == 'post' && is_single() || get_post_type() == 'post' && is_category() || get_post_type() == 'post' && is_archive() ) {
                                            echo ' > ';
                                }
			}

	?>

	
	<?php if(is_single()): // SINGLE POST ?>
		<?php if(get_post_type() == 'product'): // PRODUCT PAGE ?>
			<?php
			$category = get_term_by('slug',$wp_query->query_vars['product_category'],'product_category');
                        $category_parent = $category;
			if($category->parent != 0) {
				// Get an array of the parents of this category because it is at least a second level category
                            $sub_category_array = array();
                                do {
                                    $category_parent = get_term($category_parent->parent,$category_parent->taxonomy);
                                    $sub_category_array[] = $category_parent;
                                } while($category_parent->parent != 0);
                                // Reverse the array because it will be the wrong way around.
                                $sub_category_array = array_reverse($sub_category_array);
                                // Loop through each of the categories
				foreach($sub_category_array as $sub_category) {
					?>
					<a href="<?php echo get_term_link($sub_category->slug,$sub_category->taxonomy) ?>"><?php echo $sub_category->name ?></a> >
					<?php
				}
			}
			
			if(!$category) {
				$categories = wp_get_post_terms(get_the_ID(),'product_category');
				if(count($categories) > 0)
					$category = $categories[0];
				if($category):
                                        $category_parent = $category;
					if($category->parent != 0) {
					     // Get an array of the parents of this category because it is at least a second level category
                                            $sub_category_array = array();
                                            do {
                                                $category_parent = get_term($category_parent->parent,$category_parent->taxonomy);
                                                $sub_category_array[] = $category_parent;
                                            } while($category_parent->parent != 0);
                                            // Reverse the array because it will be the wrong way around.
                                            $sub_category_array = array_reverse($sub_category_array);
                                            // Loop through each of the categories
                                            foreach($sub_category_array as $sub_category) {
                                                    ?>
                                                    <a href="<?php echo get_term_link($sub_category->slug,$sub_category->taxonomy) ?>"><?php echo $sub_category->name ?></a> >
                                                    <?php
                                            }
				}
				?>
				<a href="<?php echo get_term_link($category->slug,$category->taxonomy) ?>"><?php echo $category->name  ?></a> > 
				<?php
				endif;
			} else {
				?>
				<a href="<?php echo get_term_link($category->slug,$category->taxonomy) ?>"><?php echo $category->name ?></a> > 
				<?php
			}
			
			$brand = get_term_by('slug',$wp_query->query_vars['product_brand'],'product_brand');
			if(!$brand) {
				$brands = wp_get_post_terms(get_the_ID(),'product_brand');
				if(count($brands) > 0)
					$brand = $brands[0];
				if($brand):
				?>
				<a href="<?php echo get_term_link($brand->slug,$brand->taxonomy) ?>"><?php echo $brand->name ?></a> >	
				<?php
				endif;
			} else {
				?>
				<a href="<?php echo get_term_link($brand->slug,$brand->taxonomy) ?>"><?php echo $brand->name ?></a> >	
				<?php
			}
			?>
			<?php endif; ?>
		<?php $title = get_the_title(); echo ((strlen($title) > 40) ? substr($title,0,40).'...' : $title); ?>
	<?php endif; ?>

	<?php if(is_page()): // PAGE ?>
		<?php the_title() ?>
	<?php endif; ?>	
	
	<?php if(is_search()): // SEARCH ?>
		<?php _e('Search:','framework'); ?> <?php echo sanitize_text_field(get_search_query()) ?>
	<?php endif; ?>
	
	<?php if(is_404()): // 404 ?>
		<?php _e('404 Not Found','framework'); ?>
	<?php endif; ?>
	
	<?php /* If this is a category archive */ if (is_category()): ?>
		<?php printf(__('All posts in %s', 'framework'), single_cat_title('',false)); ?>
	<?php endif; ?>
	
	<?php /* If this is a tag archive */ if( is_tag() ): ?>
		<?php printf(__('All posts tagged %s', 'framework'), single_tag_title('',false)); ?>
 	<?php endif; ?>
 	
 	<?php /* If this is a daily archive */ if (is_day()): ?>
		<?php _e('Archive for', 'framework') ?> <?php the_time('F jS, Y'); ?>
 	<?php endif; ?>
 	
 	<?php /* If this is a monthly archive */ if (is_month()): ?>
		<?php _e('Archive for', 'framework') ?> <?php the_time('F, Y'); ?>
 	<?php endif; ?>
 	
 	<?php /* If this is a yearly archive */ if (is_year()): ?>
		<?php _e('Archive for', 'framework') ?> <?php the_time('Y'); ?>
	<?php endif; ?>
	
	<?php /* If this is an author archive */ if (is_author()): ?>
		<?php		
		global $author;
		$userdata = get_userdata($author);
		?>
		<?php _e('All posts by', 'framework') ?> <?php echo $userdata->display_name; ?>
 	<?php endif; ?>
		
 	<?php /* If this is a paged archive */ if (isset($_GET['paged']) && !empty($_GET['paged'])): ?>
		<?php _e('Blog Archives', 'framework') ?>
 	<?php endif; ?>
