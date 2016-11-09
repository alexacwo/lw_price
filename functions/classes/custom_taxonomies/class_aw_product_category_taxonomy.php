<?php
class aw_product_category_taxonomy {
    
    protected $current_blog_id;
    protected $uploadDirCategories;
    
    function __construct(){
       $this->aw_init_global_properties();
       $this->aw_set_upload_path();
       $this->aw_register_taxonomy();
       $this->aw_add_taxonomy_actions();
       $this->aw_add_taxonomy_filters();
    }
    
    public function aw_register_taxonomy(){        
      // Labels
	$labels = array(
		'name' => __('Product categories','framework'),
		'singular_name' => __('Product category','framework'),
		'search_items' => __('Search a category','framework'),
		'all_items' => __('All categories','framework'),
		'parent_item' => __('Parent category','framework'),
		'parent_item_colon' => __('Parent category:','framework'),
		'edit_item' => __('Edit category','framework'), 
		'update_item' => __('Update category','framework'),
		'add_new_item' => __('Add new category','framework'),
		'new_item_name' => __('New category','framework'),
		'menu_name' => __('Categories','framework')
	);
	
	$slug = (get_option('tz_custom_taxonomies_slug') != '') ? get_option('tz_custom_taxonomies_slug') : 'products';
	
	// Arguments
	$args =  array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array(
			/*'slug' => $slug,*/
			'hierarchical' => true
		)
	);
	// Register taxonomy
	register_taxonomy('product_category',array('product'),$args);
    }
    
    function aw_add_taxonomy_actions(){
        add_action('product_category_edit_form_fields', array( $this, 'aw_extra_product_category_fields' ));
        add_action('product_category_edit_form', array( $this, 'aw_product_category_edit_form' ));
        add_action('edited_product_category', array( $this, 'aw_save_extra_product_category' ));
        add_action('delete_product_category', array( $this, 'aw_delete_category_mapped_categories' ));
        add_action('pre_get_posts', array( $this, 'aw_modify_category_query' ));
    }
    
    function aw_add_taxonomy_filters(){  
        add_filter('manage_edit-product_category_columns', array( $this, 'aw_custom_add_product_category_column' ));
        add_filter('manage_product_category_custom_column', array( $this, 'aw_custom_add_product_category_column_rows'), 10, 3);
    }
    
    function aw_init_global_properties(){
        $this->current_blog_id = get_current_blog_id();
    }
    
    function aw_set_upload_path(){
        if($this->current_blog_id == 1){
            $this->uploadDirCategories = ABSPATH.'wp-content/uploads/compare/categories/';
        } else {
            // For WP multisite installation
            $this->uploadDirCategories = ABSPATH.'wp-content/uploads/compare/categories/'.$this->current_blog_id.'/';
        }
        if(!is_dir($this->uploadDirCategories)) {
                wp_mkdir_p($this->uploadDirCategories);
        }
    }
    
    function aw_extra_product_category_fields($tag) {
	$tag_id = $tag->term_id;
	$term_meta = get_option("taxonomy_".$tag_id);
	
	echo '<tr class="form-field">';
	echo '	<th scope="row" valign="top"><label for="term_meta[icon]">'.__('Icon URL','framework').'</label></th>';
	echo '	<td>';
	echo '		<input type="text" name="term_meta[icon]" id="term_meta[icon]" size="40" value="'.($term_meta['icon'] ? $term_meta['icon'] : '').'"><br />';
	echo '		<span class="description">'.__('Use full URL (with http://)','framework').'</span>';
	echo '	</td>';
	echo '</tr>';
	
	echo '<tr class="form-field">';
	echo '	<th scope="row" valign="top"><label for="term_meta[image]">'.__('Category image','framework').'</label></th>';
	echo '	<td>';
	echo '		<input style="border: none" type="file" name="term_meta_image" id="term_meta[image]" size="40" value="'.(isset($term_meta['image']) ? $term_meta['image'] : '').'"><br />';
	echo '		<span class="description">'.__('Consider uploading an image of 250x150px','framework').'</span>';
	echo '	</td>';
	echo '</tr>';
    }
    function aw_product_category_edit_form(){
    ?>
        <script type="text/javascript">
        jQuery(document).ready(function(){
        jQuery('#edittag').attr( "enctype", "multipart/form-data" ).attr( "encoding", "multipart/form-data" );
                });
        </script>
    <?php 
    }
   
    function aw_save_extra_product_category($term_id) {
        if (isset($_POST['term_meta'])) {
            $term_meta = get_option("taxonomy_".$term_id);
            $cat_keys = array_keys($_POST['term_meta']);
                foreach($cat_keys as $key){
                if (isset($_POST['term_meta'][$key])){
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            update_option("taxonomy_".$term_id,$term_meta);
            $allowed_formats = array('png','jpeg','jpg','gif','bmp','PNG','JPEG','JPG','GIF','BMP');
            if (is_uploaded_file($_FILES['term_meta_image']['tmp_name'])) {
                // Remove old file if any      
                $dir = $this->uploadDirCategories.$term_id;			
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
                    $oldfilename = str_replace($this->uploadDirCategories,'',$files[0]);
                    unlink($this->uploadDirCategories.$oldfilename);
                }
                $uploadedFileName = $_FILES['term_meta_image']['name'];
                $extension_three_symbols = substr($uploadedFileName,-3);
                $extension_four_symbols  = substr($uploadedFileName,-4);
                if(in_array($extension_three_symbols,$allowed_formats)) {
                    $extension = $extension_three_symbols;
                    $uploadedFileName = $term_id.'.'.$extension;
                    $fileMoved = move_uploaded_file($_FILES['term_meta_image']['tmp_name'],$this->uploadDirCategories.$uploadedFileName);
                } else if (in_array($extension_four_symbols,$allowed_formats)){
                    $extension = $extension_four_symbols;
                    $uploadedFileName = $term_id.'.'.$extension;
                    $fileMoved = move_uploaded_file($_FILES['term_meta_image']['tmp_name'],$this->uploadDirCategories.$uploadedFileName);
                }
            }
        }
    }
    function aw_custom_add_product_category_column( $original_columns ) {
            $new_columns = $original_columns;
            array_splice( $new_columns, 1 );
            $new_columns['custom_category_column'] = __('Image','framework');
            return array_merge( $new_columns, $original_columns );
    }
    function aw_custom_add_product_category_column_rows($row, $column_name, $term_id) {
            $fileUrl = $this->aw_get_category_image_url($term_id);
            echo '<a href="?action=edit&amp;taxonomy=product_category&amp;tag_ID='.$term_id.'&amp;post_type=product">';
            if($fileUrl != '') {
                    echo '<img width="130" height="90" src="'.$fileUrl.'" />';
            } else {
                    echo '<img src="'.get_bloginfo('template_url').'/img/admin/media-upload.png" />';
            }
            echo '</a>';
    }
   
    function aw_get_category_image_url($term_id) {
           
            $current_blog_id = get_current_blog_id();
            $dir = $this->uploadDirCategories.$term_id;			
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
                    return get_bloginfo('wpurl').'/wp-content/uploads/compare/categories/'.str_replace($this->uploadDirCategories,'',$files[0]);
                } else {
                    return get_bloginfo('wpurl').'/wp-content/uploads/compare/categories/'.$current_blog_id.'/'.str_replace($this->uploadDirCategories,'',$files[0]);
                }
            }
            return '';
    }
   
    function aw_delete_category_mapped_categories($term_id) {
            global $wpdb;
            $q = "UPDATE ".$wpdb->prefix."pc_category_mapping SET mapped_category = '0' WHERE mapped_category = '".esc_sql($term_id)."'";
            $wpdb->query($q);
    }
    
    /*
     * function: aw_modify_category_query
     * 
     * Modifies main WP query if product category taxonomy page:
     *  1. Enables pagination
     * 
     * @param object $query
     * 
     */
    function aw_modify_category_query($query){
        if ( ! is_admin() && $query->is_main_query() && is_tax('product_category') ){
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query->set('paged', $paged);
        }
    }
    
}
?>