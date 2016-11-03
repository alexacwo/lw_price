<?php
class aw_product_brand_taxonomy {
    
    protected $uploadDirBrands;
    protected $current_blog_id;
    
    function __construct($firstInit = true){
       if($firstInit){
        $this->aw_init_global_properties();
        $this->aw_set_upload_path();        
        $this->aw_register_taxonomy();
        $this->aw_add_taxonomy_actions();
        $this->aw_add_taxonomy_filters();
       } else {
           $this->aw_init_global_properties();
           $this->aw_set_upload_path();                      
       }
    }
    
    public function aw_register_taxonomy(){        
       // Labels
	$labels = array(
		'name' => __('Product brands','framework'),
		'singular_name' => __('Product brand','framework'),
		'search_items' => __('Search a brand','framework'),
		'all_items' => __('All brands','framework'),
		'parent_item' => __('Parent brand','framework'),
		'parent_item_colon' => __('Parent brand:','framework'),
		'edit_item' => __('Edit brand','framework'), 
		'update_item' => __('Update brand','framework'),
		'add_new_item' => __('Add new brand','framework'),
		'new_item_name' => __('New brand','framework'),
		'menu_name' => __('Brands','framework')
	);	
	$slug = (get_option('tz_custom_taxonomies_slug') != '') ? get_option('tz_custom_taxonomies_slug') : 'products';

	// Arguments
	$args =  array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'query_var' => true,
		'rewrite' => array('slug' => $slug.'/%product_category%')
	);
	// Register taxonomy
	register_taxonomy('product_brand',array('product'),$args);
    }
    
    function aw_init_global_properties(){
        $this->current_blog_id = get_current_blog_id();
    }
    
    function aw_set_upload_path(){
        if($this->current_blog_id == 1){
            $this->uploadDirBrands = ABSPATH.'wp-content/uploads/compare/brands/';
        } else {
            // For WP multisite installation
            $this->uploadDirBrands = ABSPATH.'wp-content/uploads/compare/brands/'.$this->current_blog_id.'/';
        }
        if(!is_dir($this->uploadDirBrands)) {
                wp_mkdir_p($this->uploadDirBrands);
        }
    }
    
    function aw_add_taxonomy_actions(){
        add_action('pre_get_posts', array( $this, 'aw_modify_brand_query' ));
        add_action('product_brand_add_form_fields', array( $this, 'aw_extra_product_brand_fields_add' ));
        add_action('product_brand_edit_form_fields', array( $this, 'aw_extra_product_brand_fields_edit' ));
        add_action('created_product_brand', array( $this, 'aw_save_extra_product_brand' ));
        add_action('edited_product_brand', array( $this, 'aw_save_extra_product_brand' ));
        add_action('delete_product_brand', array( $this, 'aw_remove_product_bisbrand' ));
        add_action('product_brand_edit_form_fields', array( $this, 'aw_extra_product_brand_fields' ));
        add_action('product_brand_edit_form', array( $this, 'aw_product_brand_edit_form' ));
    }
    
    function aw_add_taxonomy_filters(){  
        add_filter('manage_edit-product_brand_columns', array( $this, 'aw_custom_add_product_brand_column' ) );
        add_filter('manage_product_brand_custom_column',array( $this, 'aw_custom_add_product_brand_column_rows' ), 10, 3);
    }
    
    function aw_extra_product_brand_fields_add() {       
	echo '<div class="form-field">';
	echo '	<label for="term_meta[website]">'.__('Brand website','framework').'</label>';
	echo '	<input type="text" name="term_meta[website]" id="term_meta[website]" size="40" value="">';
	echo '	<p>'.__('Use full URL (with http://)','framework').'</p>';
	echo '</div>';
    }
    
    function aw_extra_product_brand_fields_edit($tag) {
	$tag_id = $tag->term_id;
	$term_meta = get_option("taxonomy_".$tag_id);
	echo '<tr class="form-field">';
	echo '	<th scope="row" valign="top"><label for="term_meta[website]">'.__('Brand website','framework').'</label></th>';
	echo '	<td>';
	echo '		<input type="text" name="term_meta[website]" id="term_meta[website]" size="3" style="width:60%;" value="'.($term_meta['website'] ? $term_meta['website'] : '').'"><br />';
	echo '		<span class="description">'.__('Use full URL (with http://)','framework').'</span>';
	echo '	</td>';
	echo '</tr>';
    }
    //at this point the brand may already have been updated, so it can't work to update it
    //it will be looking for the new brand name, can't find it so it just always creates a new one
    //Solution is either to force this to happen before the brand updates....
    //Or find a product attached to the brand term, find a bisbrand related to this product and then update the bisbrand name...
        //if there are no products related to the brand term then just create a new one
    //We really need some way of linking the brand and the bisbrand togethe this  would be the only way to resolve this efficienyl....can't use the slug as it's possible to change this.
    
    //So we could on install create a new brand table containing
    //ID, brand_slug, bisbrand_slug
    //On install link the two things togther using the following scenarios
        //A product exists using the names
        //In create here add the a link to this table
        //In update here use the link table to update the appropriate bisbrand
    //Or look at how how term_groups work, this could be away to go forward rather than a new table....
    function aw_save_extra_product_brand($term_id) { 
        // Create or update bisbrand
        $brand = get_term_by('id',$term_id,'product_brand');    
	if(!($bisbrand = term_exists($brand->name, 'product_bisbrand'))) {
		$bisbrand = wp_insert_term($brand->name, 'product_bisbrand',array(
			'slug' => $brand->slug,
			'description' => $brand->description,
			'parent' => $brand->parent
		));
	} else {
		wp_update_term($bisbrand->term_id,'product_bisbrand',array(
			'slug' => $brand->slug,
			'name' => $brand->name,
			'description' => $brand->description,
			'parent' => $brand->parent
		));
	}
	// Update custom fields
        if (isset($_POST['term_meta'])) {
      
            $term_meta_brand = get_option("taxonomy_".$term_id);
            $cat_keys = array_keys($_POST['term_meta']);
                foreach($cat_keys as $key){
                if (isset($_POST['term_meta'][$key])){
                    $term_meta_brand[$key] = $_POST['term_meta'][$key];
                }
            }
            if(isset($_POST['term_meta']['aw_show_homepage']) && $_POST['term_meta']['aw_show_homepage'] == 'on'){
                $term_meta_brand['aw_show_homepage'] = $_POST['term_meta']['aw_show_homepage'];
            } else {
                $term_meta_brand['aw_show_homepage'] = 'off';
            }
            update_option("taxonomy_".$term_id,$term_meta_brand);
            update_option("taxonomy_".$bisbrand->term_id,$term_meta_brand);
        }
        
        $allowed_formats = array('png','jpeg','jpg','gif','bmp','PNG','JPEG','JPG','GIF','BMP');

        if ( isset($_FILES['term_meta_image']['tmp_name']) && is_uploaded_file($_FILES['term_meta_image']['tmp_name'])) {

            // Remove old file if any     
            $dir =  $this->uploadDirBrands.$term_id;
            $png = (glob( $dir. '.png') === false)? array() : glob( $dir. '.png');
                    $gif = (glob( $dir. '.gif') === false)? array() : glob( $dir. '.gif');
                    $jpg = (glob( $dir. '.jpg') === false)? array() : glob( $dir. '.jpg');
                    $jpeg = (glob( $dir. '.jpeg') === false)? array() : glob( $dir. '.jpeg');
                    $bmp = (glob( $dir. '.bmp') === false)? array() : glob( $dir. '.bmp');
                    $png_c = (glob( $dir. '.PNG') === false)? array() : glob( $dir. '.PNG');
                    $gif_c = (glob( $dir. '.GIF') === false)? array() : glob( $dir. '.GIF');
                    $jpg_c = (glob( $dir. '.JPG') === false)? array() : glob( $dir. '.JPG');
                    $jpeg_c = (glob( $dir. '.JPEG') === false)? array() : glob( $dir. '.JPEG');
                    $bmp_c = (glob( $dir. '.BMP') === false)? array() : glob( $dir. '.BMP');
            $files = array_merge( $png, $gif,  $jpg, $jpeg, $bmp,  $png_c,  $gif_c,  $jpg_c,  $jpeg_c,  $bmp_c); 
            if(count($files) != 0) {
                            $oldfilename = str_replace( $this->uploadDirBrands,'',$files[0]);
                            unlink( $this->uploadDirBrands.$oldfilename);
                    }

            $uploadedFileName = $_FILES['term_meta_image']['name'];
            $extension_three_symbols = substr($uploadedFileName,-3);
            $extension_four_symbols  = substr($uploadedFileName,-4);
            if(in_array($extension_three_symbols,$allowed_formats)) {
                $extension = $extension_three_symbols;
                $uploadedFileName = $term_id.'.'.$extension;
                $fileMoved = move_uploaded_file($_FILES['term_meta_image']['tmp_name'], $this->uploadDirBrands.$uploadedFileName);
            } else if (in_array($extension_four_symbols,$allowed_formats)){
                $extension = $extension_four_symbols;
                $uploadedFileName = $term_id.'.'.$extension;
                $fileMoved = move_uploaded_file($_FILES['term_meta_image']['tmp_name'], $this->uploadDirBrands.$uploadedFileName);
            }
	}
        
        //Delete any orphaned brand mappings -- delete any brand mappings where there is no brand to map to it
        global $wpdb;
        $q = "UPDATE ".$wpdb->prefix."pc_brand_mapping SET brand_slug_to_match_to = '' WHERE brand_slug_to_match_to NOT IN 
             (SELECT t.slug FROM ".$wpdb->prefix."terms t 
                         INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON tt.term_id = t.term_id 
                         WHERE tt.taxonomy IN ('product_brand','product_bisbrand'))";
        $wpdb->query($q);
    }
    
    function aw_remove_product_bisbrand($term_id) {
        $terms = get_terms('product_bisbrand',array('hide_empty' => false));
        foreach($terms as $term) {
                    if(!term_exists($term->name, 'product_brand')) {
                            wp_delete_term($term->term_id,'product_bisbrand');
                    delete_option("taxonomy_".$term->term_id);
                    }
        }
        delete_option("taxonomy_".$term_id);
        
        //Delete any orphaned brand mappings -- delete any brand mappings where there is no brand to map to it
        global $wpdb;
        $q = "UPDATE ".$wpdb->prefix."pc_brand_mapping SET brand_slug_to_match_to = '' WHERE brand_slug_to_match_to NOT IN 
             (SELECT t.slug FROM ".$wpdb->prefix."terms t 
                         INNER JOIN ".$wpdb->prefix."term_taxonomy tt ON tt.term_id = t.term_id 
                         WHERE tt.taxonomy IN ('product_brand','product_bisbrand'))";
        $wpdb->query($q);
    }
    
    function aw_extra_product_brand_fields($tag) {
	$tag_id = $tag->term_id;
	$term_meta = get_option("taxonomy_".$tag_id);
      	
	echo '<tr class="form-field">';
	echo '	<th scope="row" valign="top"><label for="term_meta[image]">'.__('Brand image','framework').'</label></th>';
	echo '	<td>';
	echo '		<input type="file" style="border: none" name="term_meta_image" id="term_meta[image]" size="40" value="'.(isset($term_meta['image']) ? $term_meta['image'] : '').'"><br />';
	echo '		<span class="description">'.__('Consider uploading an image of 150x100px','framework').'</span>';
	echo '	</td>';
	echo '</tr>';

    }

    function aw_product_brand_edit_form(){
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function(){
            jQuery('#edittag').attr( "enctype", "multipart/form-data" ).attr( "encoding", "multipart/form-data" );
                    });
            </script>
            <?php 
    }
    
    function aw_custom_add_product_brand_column( $original_columns ) {
	$new_columns = $original_columns;
	array_splice( $new_columns, 1 );
	$new_columns['custom_brand_column'] = __('Image','framework');
	return array_merge( $new_columns, $original_columns );
    }

    function aw_custom_add_product_brand_column_rows($row, $column_name, $term_id) {
 
            $fileUrl = $this->aw_get_brand_image_url($term_id);
            echo '<a href="?action=edit&amp;taxonomy=product_brand&amp;tag_ID='.$term_id.'&amp;post_type=product">';
            if($fileUrl != '') {
                    echo '<img width="130" height="90" src="'.$fileUrl.'" />';
            } else {
                    echo '<img src="'.get_bloginfo('template_url').'/img/admin/media-upload.png" />';
            }
            echo '</a>';
    }
    
    public static function aw_get_brand_image_url($term_id) {   
	
           
        $aw_product_brand_taxonomy = new aw_product_brand_taxonomy(false);
        
	$current_blog_id = get_current_blog_id();	
	$dir =  $aw_product_brand_taxonomy->uploadDirBrands.$term_id;
        $uploadDirbrands = $aw_product_brand_taxonomy->uploadDirBrands;
        $png = (glob( $dir. '.png') === false)? array() : glob( $dir. '.png');
	$gif = (glob( $dir. '.gif') === false)? array() : glob( $dir. '.gif');
	$jpg = (glob( $dir. '.jpg') === false)? array() : glob( $dir. '.jpg');
	$jpeg = (glob( $dir. '.jpeg') === false)? array() : glob( $dir. '.jpeg');
	$bmp = (glob( $dir. '.bmp') === false)? array() : glob( $dir. '.bmp');
	$png_c = (glob( $dir. '.PNG') === false)? array() : glob( $dir. '.PNG');
	$gif_c = (glob( $dir. '.GIF') === false)? array() : glob( $dir. '.GIF');
	$jpg_c = (glob( $dir. '.JPG') === false)? array() : glob( $dir. '.JPG');
	$jpeg_c = (glob( $dir. '.JPEG') === false)? array() : glob( $dir. '.JPEG');
	$bmp_c = (glob( $dir. '.BMP') === false)? array() : glob( $dir. '.BMP');
	$files = array_merge( $png, $gif,  $jpg, $jpeg, $bmp,  $png_c,  $gif_c,  $jpg_c,  $jpeg_c,  $bmp_c);
	if(count($files) != 0) {	
            if($current_blog_id === 1){		
                return get_bloginfo('wpurl').'/wp-content/uploads/compare/brands/'.str_replace( $uploadDirbrands,'',$files[0]);			
            } else {			
               return get_bloginfo('wpurl').'/wp-content/uploads/compare/brands/'.$current_blog_id.'/'.str_replace( $uploadDirbrands, '',$files[0]);              
            }
            
	}	
        unset($aw_product_brand_taxonomy);
	return '';
    }


    
     /*
     * function: aw_modify_brand_query
     * 
     * Modifies main WP query if product brand taxonomy page:
     *  1. Enables pagination
     * 
     * @param object $query
     * 
     */
    function aw_modify_brand_query($query){
        if ( ! is_admin() && $query->is_main_query() && is_tax('product_brand') ){
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query->set('paged', $paged);
        }
    }

    
    
    
}
?>