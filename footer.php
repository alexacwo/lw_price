<?php global $aw_theme_options; ?>	

        </div>
		</div>
	<!-- .master-row -->
	<footer class="master-footer">
                
                <?php if(isset($aw_theme_options['tz_header_leaderboard']) && trim($aw_theme_options['tz_header_leaderboard']) != ""): ?>
		<div class="row leaderboard">
			<div class="twelve columns">
				<?php echo stripslashes($aw_theme_options['tz_header_leaderboard']); ?>
			</div>
		</div>
                <?php endif; ?>

		<div class="row footer-widgets">

			<div class="three columns">
				<?php if ( dynamic_sidebar('footer_1') ) : else : endif; ?>
			</div>

			<div class="three columns">
				<?php if ( dynamic_sidebar('footer_2') ) : else : endif; ?>
			</div>

			<div class="three columns">
				<?php if ( dynamic_sidebar('footer_3') ) : else : endif; ?>
			</div>

			<div class="three columns">
				<?php if ( dynamic_sidebar('footer_4') ) : else : endif; ?>
			</div>

		</div>

		<div class="row footer-bottom">
			
			<div class="twelve columns">
                            <p>&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>. <?php _e('Powered by', 'framework') ?> <a href="http://wordpress.org/">WordPress</a>. <a href="http://www.wppricecomparison.com">Compare by WP Price Comparison</a>.</p>
			</div>

		</div>
		
	</footer>

	<?php wp_footer(); ?>       
        
        <?php if($aw_theme_options['tz_g_analytics'] != ""): /* if google analytics is set in theme options then show code */ ?>
	<!-- Google Analytics -->
        <?php echo stripslashes($aw_theme_options['tz_g_analytics']); ?>
        
        <?php endif; ?>
        
  </body>
</html>