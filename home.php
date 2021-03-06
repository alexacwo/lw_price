<?php get_header(); ?>

	<div class="nine columns blog-column push_three">

	    <?php if ( have_posts() ) : ?>
	    
	        <?php while ( have_posts() ) : the_post(); ?>
	        	<article>
		        	<p class="meta-tags">
		        		<time datetime="<?php the_time('Y-m-d'); ?>"><?php the_time(get_option('date_format')); ?></time>
		        		&middot;
		        		<?php _e('Written by','framework'); echo ' '; the_author_posts_link(); echo ' '; ?>
						&middot;
						<?php comments_popup_link(__('No comments', 'framework'), __('1 comment', 'framework'), __('% comments', 'framework')); ?>
		        	</p>
		        	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php
						//Featured Image
			        	if (function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
			        		echo '<a href="'.get_permalink().'">';
			        		the_post_thumbnail('blog-post'); 
			        		echo '</a>';
			        	}
			        ?>
					
					<div class="post-content">
				        <?php
			            	//More Tag
			            	global $more;
							$more = 0;
			            	$is_more = @strpos($post->post_content, '<!--more-->');
							$content = (empty($post->post_content));						
							echo ($is_more) ? the_content('...') : the_excerpt();
							if(!$content) echo '<a class="read-more" href="'.get_permalink().'">'.__('Read more', 'framework').'</a>';
						?>
					</div>

	            </article>
	        <?php endwhile; ?>

	        <?php
				global $wp_query;
				$big = 999999999;
				$pagination = paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, get_query_var('paged') ),
					'total' => $wp_query->max_num_pages
				) );

				if(!empty($pagination)) {
					echo '<div class="paginate">'.$pagination.'</div>';
				}
				
			?>
	        
	    <?php endif; // end have_posts() check ?>

    </div>

    <?php get_sidebar(); ?>

<?php get_footer(); ?>