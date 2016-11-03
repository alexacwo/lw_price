<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php global $aw_theme_options; ?>
<ul class="header-social">
<?php if( (isset($aw_theme_options['tz_social_twitter']) && trim($aw_theme_options['tz_social_twitter']) != "") ): ?>
<li><a target="_blank" href="<?php echo esc_url($aw_theme_options['tz_social_twitter']); ?>"><i class="icon-twitter"></i></a></li>
<?php endif; ?>
<?php if( (isset($aw_theme_options['tz_social_facebook']) && trim($aw_theme_options['tz_social_facebook']) != "") ): ?>
<li><a target="_blank" href="<?php echo esc_url($aw_theme_options['tz_social_facebook']); ?>"><i class="icon-facebook"></i></a></li>
<?php endif; ?>
<?php if( (isset($aw_theme_options['tz_social_rss']) && trim($aw_theme_options['tz_social_rss']) != "") ): ?>
<li><a target="_blank" href="<?php echo esc_url($aw_theme_options['tz_social_rss']); ?>"><i class="icon-rss"></i></a></li>
<?php endif; ?>
<?php if( (isset($aw_theme_options['tz_social_tumblr']) && trim($aw_theme_options['tz_social_tumblr']) != "") ): ?>
<li><a target="_blank" href="<?php echo esc_url($aw_theme_options['tz_social_tumblr']); ?>"><i class="icon-tumblr"></i></a></li>
<?php endif; ?>
<?php if( (isset($aw_theme_options['tz_social_pinterest']) && trim($aw_theme_options['tz_social_pinterest']) != "") ): ?>
<li><a target="_blank" href="<?php echo esc_url($aw_theme_options['tz_social_pinterest']); ?>"><i class="icon-pinterest"></i></a></li>
<?php endif; ?>
</ul>