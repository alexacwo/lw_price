<?php
/**
 * Template Name: Brands Page
 */
get_header();
$brands = get_terms('product_bisbrand');
$letters = array();
$current_letter = null;
$endLetterSection = false;
foreach($brands as $brand) {
        $char = aw_mb_ucfirst($brand->name); 
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
                        foreach($brands AS $index => $brand):
                            $char = aw_mb_ucfirst($brand->name);
                            if($char != $current_letter) {   
                                    $current_letter = $char; ?> 
                                    <h2 id="<?php echo esc_attr($current_letter); ?>"><?php echo $current_letter; ?></h2>
                                    <?php
                                    $i = 1;
                            }
                            $term_link = get_term_link($brand->slug,'product_bisbrand');
                            ?>
                            <?php if($i == 1): ?>
                            <div class="row row-homepage-featured homepage-categories">
                            <?php endif; ?>
                            <div class="four columns">
                                    <a href="<?php echo esc_url($term_link); ?>">
                                    <?php
                                    //Get brand not bisbrand for image
                                    $brandbrand= get_term_by('slug', $brand->slug, 'product_brand');
                                    //echo $brandbrand->term_id;
                                    $image_url = aw_product_brand_taxonomy::aw_get_brand_image_url($brandbrand->term_id);
                                    if($image_url != ''):
                                    ?> 
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($brand->name); ?>" />
                                    <?php else: ?>
                                    <img src="<?php echo get_template_directory_uri() ?>/img/no-photo.png" alt="<?php echo esc_attr($brand->name); ?>" />
                                    <?php endif; ?>                                  
                                    <h3><?php echo $brand->name; ?></h3>
                                    </a>  
                            </div>
                            <?php 
                            if( isset($brands[$index+1]) ){
                                $char = aw_mb_ucfirst($brands[$index+1]->name);
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