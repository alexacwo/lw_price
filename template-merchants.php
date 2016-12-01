<?php
/**
 * Template Name: Retailers Page
 */
get_header();

if (aw_is_compare_plus_installed()){	
        $q = "SELECT * FROM ".$wpdb->prefix."pc_products_merchants WHERE feed NOT IN (SELECT id FROM ".$wpdb->prefix."pc_feeds WHERE feed_relationships != '0') ORDER BY name ASC";
} else{
        $q = "SELECT * FROM ".$wpdb->prefix."pc_products_merchants ORDER BY name ASC";
}

$merchants = $wpdb->get_results($q);
$letters = array();
$current_letter = null;
$char = "";
$current_blog_id = get_current_blog_id();
if($current_blog_id == 1){
	$uploadDir = ABSPATH.'wp-content/uploads/compare/merchants/';
} else {
	// For WP multisite installation
	$uploadDir = ABSPATH.'wp-content/uploads/compare/merchants/'.$current_blog_id.'/';
}
foreach($merchants as $merchant) {
        $char = aw_mb_ucfirst($merchant->name);
        $firstletter = $char;
        if(!in_array($firstletter,$letters)) {
                $letters[] = $firstletter;
        }
}
?>
<div class="nine columns single-product push_three">

<?php if ( have_posts() ) : ?>	    
    <?php while ( have_posts() ) : the_post(); ?>
                <article>
                        <h1><?php the_title(); ?></h1>	        	

                        <section id="overview" class="row product-overview post-content">

                            <?php
                            //Featured Image
                            if (function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
                                    the_post_thumbnail('slider');
                            }
                            ?>

                            <?php the_content(); ?>

                        </section>

                        <nav class="nav-brands">
                            <ul>
                                <?php
                                foreach($letters as $letter) {
                                        echo '<li class="skiplink"><a gumby-goto="#' . esc_attr($letter) . '" gumby-update gumby-offset="-10" href="#">'.$letter.'</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>

                        <?php                        
                        foreach($merchants AS $index => $merchant):
                            $char = aw_mb_ucfirst($merchant->name);
                            if($char != $current_letter) {   
                                    $current_letter = $char; ?> 
                                    <h2 id="<?php echo esc_attr($current_letter); ?>"><?php echo $current_letter; ?></h2>
                                    <?php
                                    $i = 1;
                            } 
                            ?>
                            <?php if($i == 1): ?>
                            <div class="row row-homepage-featured homepage-categories">
                            <?php endif; ?>
                            <div class="four columns">
                                    <?php if($merchant->url != ''): ?>
                                    <a target="_blank" href="<?php echo esc_url($merchant->url); ?>">
                                    <?php endif; ?>
                                    <?php
                                    
                                    if($merchant->image != "" && file_exists($uploadDir.$merchant->image)){
                                        if($current_blog_id == 1):
                                                $image_url = home_url().'/wp-content/uploads/compare/merchants/'.$merchant->image;
                                        else:
                                                $image_url = home_url().'/wp-content/uploads/compare/merchants/'.$current_blog_id.'/'.$merchant->image;                                                
                                        endif;                                        
                                    } else {
                                        $image_url = get_template_directory_uri() . "/img/no-photo.png";
                                    }
                                    ?>
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo esc_attr($merchant->name); ?>" />                                 
                                    <h3><?php echo $merchant->name; ?></h3>
                                    <?php if($merchant->url != ''): ?>
                                    </a>
                                    <?php endif; ?>
                            </div>
                            <?php 
                            if( isset($merchants[$index+1]) ){
                                $char = aw_mb_ucfirst($merchants[$index+1]->name);
                                if($char != $current_letter) $endLetterSection = true;                                                
                            } else {
                                $endLetterSection = true;
                            }                                            
                            ?>   
                            <?php 
                            $i++; 
                            if($i == 4 || $endLetterSection ): $endLetterSection = false; $i=1; ?>
                            </div>
                            <?php endif; ?>                                            
                        <?php endforeach; ?>					

                </article>
    <?php endwhile; ?>     
<?php endif; // end have_posts() check ?>
    
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>