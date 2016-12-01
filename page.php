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
		        	<h1><?php the_title(); ?></h1>
					<?php
						//Featured Image
			        	if (function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
			        		echo '<a href="'.get_permalink().'">';
			        		the_post_thumbnail('blog-post'); 
			        		echo '</a>';
			        	}
			        ?>
					
					<div class="post-content">
				        <?php the_content(); ?>
					</div>

	            </article>
            
                <?php comments_template(); ?>
            
	        <?php endwhile; ?>
     
	    <?php endif; // end have_posts() check ?>

    </div>

    <?php get_sidebar(); ?>

<?php get_footer(); ?>