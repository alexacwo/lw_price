<?php get_header(); ?>

	<div class="nine columns blog-column push_three">

	    <?php if ( have_posts() ) : ?>
	    
	        <?php while ( have_posts() ) : the_post(); ?>
	        	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		        	<p class="meta-tags">
		        		<time datetime="<?php the_time('Y-m-d'); ?>"><?php the_time(get_option('date_format')); ?></time>
		        		&middot;
		        		<?php _e('Written by','framework'); echo ' '; the_author_posts_link(); echo ' '; ?>
						&middot;
						<?php comments_popup_link(__('No comments', 'framework'), __('1 comment', 'framework'), __('% comments', 'framework')); ?>                                      
                                        <?php 
                                            if (is_singular()) :
                                                    $posttags = get_the_tags();
                                                    if ( $posttags !== false && count($posttags) != 0) :
                                                            $fulltag = array();
                                                            foreach($posttags as $tag) :
                                                                    $taglink = get_tag_link($tag->term_id);
                                                                    $tagname = $tag->name;
                                                            $fulltag[] = '<a href="'.$taglink.'">'.$tagname.'</a>';
                                                            endforeach; ?>
                                                            <p class="meta-tags">
                                                            <?php echo __('Tags:', 'framework').' '.implode(', ',$fulltag);; ?>
                                                            </p>                                                                                                                     
                                                    <?php endif;
                                            endif;
                                         ?>                     
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
				        <?php the_content(); ?>
                                        <?php wp_link_pages('before=<p>&after=</p>&next_or_number=number&pagelink=Page %'); ?>
					</div>
                                
                                        

	            </article>

                    <?php comments_template(); ?>

	        <?php endwhile; ?>
     
	    <?php endif; // end have_posts() check ?>

	    <?php wp_link_pages(); 
	    // Next and Previous Post
	    if ( (get_adjacent_post(false, '', true)) || (get_adjacent_post(false, '', false)) ): ?>
			
			<!-- BEGIN .navigation -->
			<div class="paginate">
				<div class="alignleft buttons"><?php previous_post_link('%link','&laquo; %title',true); ?></div>
				<div class="alignright buttons"><?php next_post_link('%link','%title &raquo;',true); ?></div>
				<div class="clear"></div>
			</div>
			<!-- END .navigation -->
		
		<?php endif; ?>

    </div>

    <?php get_sidebar(); ?>

<?php get_footer(); ?>