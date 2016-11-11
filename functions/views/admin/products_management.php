<?php 
// Catching direct file access
if('wp-product-admin.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('<h2>Direct File Access Prohibited</h2>');
// Wordpress access to the database
global $wpdb;
?>

<?php if(isset($_GET['action']) && $_GET['action'] == 'delete_product'): // Display product information ?>

<?php
$id_product = (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) ? $_GET['product_id'] : 0;
$q = "SELECT wp_post_id FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product = '".$id_product."' LIMIT 1";
$post_id = $wpdb->get_var($q);
wp_delete_post($post_id,true);
if(aw_is_compare_plus_installed()){
    $q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product = '".$id_product."' LIMIT 1";
    $wpdb->query($q);
}
$q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$id_product."'";
$wpdb->query($q);


if(aw_is_compare_plus_installed()){
	$q = "DELETE FROM ".$wpdb->prefix."pc_products_feeds_relationships WHERE id_product = '".$id_product."'";
	$wpdb->query($q);
	
	$q = "DELETE FROM ".$wpdb->prefix."pc_product_original_retailer WHERE id_product = '".$id_product."'";
	$wpdb->query($q);
}
?>
<div class="wrap">

	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Delete Product','framework'); ?></h2>
	
	<br />
	<div class="success"><?php _e('Product successfully deleted','framework'); ?></div>
	<p><a href="?page=products_management" class="button-secondary"><?php _e('View Products','framework'); ?></a></p>
	
</div>

<?php elseif(isset($_GET['action']) && $_GET['action'] == 'bulk_delete'): // Bulk Delete product information ?>

<?php

$ids = (isset($_GET['ids'])) ? explode(",",$_GET['ids']) : array();


foreach($ids as $id) {
	if($id == "") continue;
	
	$q = "SELECT wp_post_id FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product = '".$id."' LIMIT 1";
	$post_id = $wpdb->get_var($q);
	wp_delete_post($post_id,true);
	$q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product = '".$id."' LIMIT 1";
	$wpdb->query($q);
	$q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$id."'";
	$wpdb->query($q);
	
	if(aw_is_compare_plus_installed()){	
		$q = "DELETE FROM ".$wpdb->prefix."pc_products_feeds_relationships WHERE id_product = '".$id."'";
		$wpdb->query($q);
	
		$q = "DELETE FROM ".$wpdb->prefix."pc_product_original_retailer WHERE id_product = '".$id."'";
		$wpdb->query($q);
	}
}
?>

<div class="wrap">

	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Delete Products','framework'); ?></h2>
	
	<br />
	<div class="success"><?php _e('Products successfully deleted','framework'); ?></div>
	<p><a href="?page=products_management" class="button-secondary"><?php _e('View Products','framework'); ?></a></p>
	
</div>

<?php elseif(isset($_GET['action']) && $_GET['action'] == 'delete_merchant'): // Delete product merchant ?>

<?php

$id_product = (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) ? $_GET['product_id'] : 0;
$id_merchant = isset($_GET['merchant_id']) ? $_GET['merchant_id'] : '';

$q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE id_product = '".$id_product."' AND id_merchant = '".$id_merchant."'";
$wpdb->query($q);

?>

<div class="wrap">

	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Delete Product Retailer','framework'); ?></h2>
	
	<br />
	<div class="success"><?php _e('Product Retailer successfully deleted','framework'); ?></div>
	<p><a href="?page=products_management&amp;action=show_product&amp;product_id=<?php echo $id_product; ?>" class="button-secondary"><?php _e('View Product','framework'); ?></a></p>
	
</div>

<?php elseif(isset($_GET['action']) && $_GET['action'] == 'show_product'): // Display the product information ?>

<?php

$product_id = (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) ? $_GET['product_id'] : 0;

// Form "new" submitted
if(isset($_POST['submit']['new'])) {
        
        $form = array();
    
	$_POST['new'] = array_map('trim',$_POST['new']);
	$_POST['new'] = array_map('stripslashes',$_POST['new']);
	extract($_POST['new']);
	// Replacements
	$merchant_price = str_ireplace(',','.',$merchant_price);
	// Validate
	if($merchant_slug == '') { $form = $_POST; $errors['merchant_slug'] = __("Retailer name is required",'framework'); }
	if(!is_numeric($merchant_price)) { $form = $_POST; $errors['merchant_price'] = __("Not numeric",'framework'); }
	if($merchant_price == '') { $form = $_POST; $errors['merchant_price'] = __("Price required",'framework'); }
	if($merchant_deeplink == '') { 
		$form = $_POST; 
		$errors['merchant_deeplink'] = __("Product URL required",'framework');
	} else {

	}
	if($feed_product_image == '') { 
		$form = $_POST; 
		$errors['feed_product_image'] = __("Product Image URL required",'framework');
	}

	if(!isset($errors)) {
		$sql = "SELECT pr.* FROM ".$wpdb->prefix."pc_products p RIGHT OUTER JOIN ".$wpdb->prefix."pc_products_relationships pr ON pr.id_product = p.id_product WHERE pr.id_product = '".$product_id."' LIMIT 1";
		$product = $wpdb->get_row($sql);
		if($product != NULL) {
			$wp_post = get_post($product->wp_post_id);
			//$post_image = get_post_meta($wp_post->ID,'image_meta',true);
			// Insert
			$q = "
				INSERT INTO ".$wpdb->prefix."pc_products
				SET 
					id_product = '".esc_sql($product_id)."',
					id_merchant = '".esc_sql($merchant_slug)."',
					feed_product_name = '".esc_sql($wp_post->post_title)."',
					feed_product_desc = '".esc_sql($wp_post->post_content)."',
					feed_product_image = '".esc_sql($feed_product_image)."',
					price = '".esc_sql($merchant_price)."',
					deeplink = '".esc_sql($merchant_deeplink)."',
					shipping = '".esc_sql($merchant_shipping)."',
					voucher = '".esc_sql($merchant_voucher)."',
					last_update = '".time()."'
				ON DUPLICATE KEY UPDATE
					id_product = '".esc_sql($product_id)."',
					id_merchant = '".esc_sql($merchant_slug)."',
					feed_product_name = '".esc_sql($wp_post->post_title)."',
					feed_product_desc = '".esc_sql($wp_post->post_content)."',
					feed_product_image = '".esc_sql($feed_product_image)."',
					price = '".esc_sql($merchant_price)."',
					deeplink = '".esc_sql($merchant_deeplink)."',
					shipping = '".esc_sql($merchant_shipping)."',
					voucher = '".esc_sql($merchant_voucher)."',
					last_update = '".time()."'
				";
			$wpdb->query($q);
			$success = __("Retailer successfully added to this product",'framework');
		}
	}	
}

// Form "edit" submitted
if(isset($_POST['submit']['edit'])) {
	$_POST['merchant'] = array_map('trim',$_POST['merchant']);
	$_POST['merchant'] = array_map('stripslashes',$_POST['merchant']);
	extract($_POST['merchant']);
	// Replacements
	$merchant_price = str_ireplace(',','.',$merchant_price);
	// Validate
	
	if($merchant_slug != '' && $feed_product_name != '') {
		if(!is_numeric($merchant_price)) { $form = $_POST; $errors['merchant_price'] = __("Not numeric",'framework'); }
		if($merchant_price == '') { $form = $_POST; $errors['merchant_price'] = __("Price required",'framework'); }
		if($merchant_deeplink == '') { 
			$form = $_POST; 
			$errors['merchant_deeplink'] = __("Product URL required",'framework');
		} else {
			/*
if(!validate_url($merchant_deeplink)) { 
				$form = $_POST; 
				$errors['merchant_deeplink'] = __("Invalid URL",'framework');
			}
*/
		}
		if(!isset($errors)) {
			// Update
			$q = "
				UPDATE ".$wpdb->prefix."pc_products
				SET 
					feed_product_name = '".esc_sql($feed_product_name)."',
					
					feed_product_desc = '".esc_sql($feed_product_desc)."',
					price = '".esc_sql($merchant_price)."',
					deeplink = '".esc_sql($merchant_deeplink)."',
					feed_product_image = '".esc_sql($feed_product_image)."',
					shipping = '".esc_sql($merchant_shipping)."',
					voucher = '".esc_sql($merchant_voucher)."',
					
					last_update = '".time()."'
				WHERE 
					
					id_merchant = '".esc_sql($merchant_slug)."'
					AND id_product = '".esc_sql($product_id)."'
				";
			$wpdb->query($q);

			$success = __("Retailer successfully updated",'framework');
		}
	}	
}
// Form order submitted
if(isset($_POST['submit']['order']))
{
    $_POST['order'] = array_map('trim',$_POST['order']);
    $_POST['order'] = array_map('stripslashes',$_POST['order']);
    
    extract($_POST['order']);
    
    // Replacements
    $display_order = str_ireplace(',','.',$display_order);
    // Validate
    if($display_order != '') {
            //Check the display order is a number
            if(!is_numeric($display_order)) { $form = $_POST; $errors['display_order'] = __("Not numeric",'framework'); }
            if(!isset($errors)) {
                    // Update
                    $q = "
                            UPDATE ".$wpdb->prefix."pc_products_relationships
                            SET 
                                    display_order = '".esc_sql($display_order)."'
                            WHERE 
                                    id_product = '".esc_sql($product_id)."'
                            ";
                    $wpdb->query($q);
                    $success = __("Display order successfully updated",'framework');
		}
	}	
}
?>

<div class="wrap">

	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Products Management','framework'); ?></h2>
	<br />
	
	<?php if(isset($errors)): ?>
	<br />
	<div class="error">
		<?php foreach($errors as $error): ?>
		<?php echo $error ?><br />
		<?php endforeach; ?>
	</div>
	<br />
	<?php elseif(isset($success)): ?>
	<br />
	<div class="success">
		<?php echo $success; ?>
	</div>
	<br />
	<?php endif; ?>
	
	<?php
	$sql = "SELECT p.*, pr.* FROM ".$wpdb->prefix."pc_products p RIGHT OUTER JOIN ".$wpdb->prefix."pc_products_relationships pr ON pr.id_product = p.id_product WHERE pr.id_product = '".$product_id."'";
	$product = $wpdb->get_row($sql);
	if($product != NULL):
		$wp_post = get_post($product->wp_post_id);
		$post_image = get_post_meta($wp_post->ID,'image_meta',true);
	?>
	<h3><?php _e('Product Information','framework'); ?></h3>
	<table>
		<tr>
			<td width="130">
				<?php if($post_image != ""): ?>
				<img src="<?php echo $post_image ?>" alt="" style="margin-right:20px;" width="130" />
				<?php else: ?>
				<?php _e('No image','framework'); ?>
				<?php endif; ?>
			</td>
			<td>
				<h4><?php echo $wp_post->post_title ?></h4>
				<p>
					<strong><?php _e('ID:','framework'); ?></strong> <?php echo $product->id_product ?><br />
					<strong><?php _e('Categories:','framework'); ?></strong> 
					<?php		
					$categories = array();		
					$terms = wp_get_object_terms($wp_post->ID,'product_category','framework');
					foreach($terms as $category) {
						$categories[] = $category->name;
					}
					echo implode(', ',$categories); 
					?><br />
					<strong><?php _e('Brand:','framework'); ?></strong>
					<?php		
					$brands = array();		
					$terms = wp_get_object_terms($wp_post->ID,'product_brand','framework');
					foreach($terms as $brand) {
						$brands[] = $brand->name;
					}
					echo implode(', ',$brands); 
					?><br />
					<strong><?php _e('Full name:','framework'); ?></strong> <?php echo $wp_post->post_title ?><br />
                                        <strong><?php _e('Display order:','framework'); ?></strong> <?php echo $product->display_order ?>
				</p>
			</td>
		</tr>
	</table><br />
	<h3><?php _e('Change Display Order','framework'); ?></h3>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <table>
            <tr>
                <td width="130">
                    Display Order
                </td>
                <td>
                    <input type="text" name="order[display_order]" value="<?php echo (isset($form['display_order']) ? $form['display_order'] : $product->display_order) ?>" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2"><span class="description"><?php _e('This must be numeric.  Setting this to 0 means it will always appear at the top of the results for all order types, after this the lower the number the higher up the list it will be for the default ordering setting.','framework'); ?></span></td>
            </tr>
        </table>
        <p class="submit">
		<input type="submit" name="submit[order]" id="submit" class="button-secondary" value="<?php _e('Save Display Order','framework'); ?>" />
	</p>
        </form>
        <br/>
	<h3><?php _e('Existing retailers','framework'); ?></h3>
	
	<table class="widefat">
		<!-- THEAD -->
		<thead>
			<tr>
				<th scope="col" class="manage-column" style=""><?php _e('Retailer name','framework'); ?></th>
				
				<th scope="col" class="manage-column" style=""><?php _e('Product title','framework'); ?></th>
				
				<th scope="col" class="manage-column" style=""><?php _e('Product description','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Product Image','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Price','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Shipping','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Voucher','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Deeplink','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Actions','framework'); ?></th>
			</tr>
		</thead>
		<!-- TFOOT -->
		<tfoot>
			<tr>
				<th scope="col" class="manage-column" style=""><?php _e('Retailer name','framework'); ?></th>
				
				<th scope="col" class="manage-column" style=""><?php _e('Product title','framework'); ?></th>
				
				<th scope="col" class="manage-column" style=""><?php _e('Product description','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Product Image','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Price','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Shipping','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Voucher','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Deeplink','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Actions','framework'); ?></th>
			</tr>
		</tfoot>
		<!-- TBODY -->
		<tbody>
			<?php
			$q = "SELECT pm.*, p.* FROM ".$wpdb->prefix."pc_products_relationships pr, ".$wpdb->prefix."pc_products_merchants pm, ".$wpdb->prefix."pc_products p WHERE pm.slug = p.id_merchant AND p.id_product = pr.id_product AND pr.wp_post_id = '".$wp_post->ID."' ORDER BY pm.name ASC";
			$merchants = $wpdb->get_results($q);

			if($merchants == NULL):
			?>
			<tr>
				<td colspan="5"><?php _e('No retailer','framework'); ?></td>
			</tr>
			<?php else: ?>
				<?php $i = 0; foreach($merchants as $merchant): ?>
				<tr>
					<td><?php echo $merchant->name ?></td>
					
					<td><?php echo $merchant->feed_product_name ?></td>
					
					<td><?php 
					if(strlen($merchant->feed_product_desc) > 50 ){
						echo substr($merchant->feed_product_desc,0,50) . " [...]";
					} else {
						echo $merchant->feed_product_desc;
					}
					?></td>
					<td><img src="<?php echo $merchant->feed_product_image ?>" width="48" /></td>
					<td><?php echo $merchant->price ?></td>
					<td><?php echo $merchant->shipping ?></td>
					<td><?php echo $merchant->voucher ?></td>
					<td><a target="_blank" href="<?php echo $merchant->deeplink ?>"><?php _e('Retailer site','framework'); ?></a></td>
					<td><a href="#" onclick="jQuery('.embed_form').css('display','none'); jQuery('#embed_form_<?php echo $i ?>').css('display','table-row'); return false;"><?php _e('Edit','framework'); ?></a> - <a href="?page=products_management&amp;action=delete_merchant&amp;product_id=<?php echo $product_id ?>&amp;merchant_id=<?php echo $merchant->slug ?>" onclick="if(!confirm('<?php _e('Are you sure ?','framework'); ?>')) return false;"><?php _e('Delete','framework'); ?></a></td>
				</tr>
				<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<tr class="embed_form" id="embed_form_<?php echo $i ?>" style="display:none;">
					<td><?php echo $merchant->name ?></td>
					<td><input type="text" name="merchant[feed_product_name]" value="<?php echo $merchant->feed_product_name ?>" /></td>
					
					<td><textarea rows="4" cols="50" name="merchant[feed_product_desc]"><?php echo $merchant->feed_product_desc ?></textarea></td>
					
					<td><input type="text" name="merchant[feed_product_image]" value="<?php echo $merchant->feed_product_image ?>" /></td>
					<td><input type="text" name="merchant[merchant_price]" value="<?php echo $merchant->price ?>" /></td>
					<td><input type="text" name="merchant[merchant_shipping]" value="<?php echo $merchant->shipping ?>" /></td>
					<td><input type="text" name="merchant[merchant_voucher]" value="<?php echo $merchant->voucher ?>" /></td>
					<td><input type="text" name="merchant[merchant_deeplink]" value="<?php echo $merchant->deeplink ?>" /></td>
					<td>
						<input type="hidden" name="merchant[merchant_slug]" value="<?php echo $merchant->id_merchant ?>" />
						<input type="submit" name="submit[edit]" class="button-secondary" value="<?php _e('Save','framework'); ?>" />
					</td>
					</form>
				</tr>
				<?php $i++; endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	
	<?php
	$q = "SELECT * FROM ".$wpdb->prefix."pc_products_merchants ORDER BY name ASC";
	$merchants = $wpdb->get_results($q);
	if(count($merchants) != 0):
	?>
        <br/>
	<h3><?php _e('Associate another retailer with this product','framework'); ?></h3>
	<p><?php _e('Use this form to add an existing retailer to this product.','framework'); ?> <?php _e('Click here to','framework'); ?> <a href="/wp-admin/admin.php?page=merchants_management&action=add_merchant"><?php _e('create new retailer','framework'); ?></a></p>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row"><label for="merchant_slug"><?php _e('Choose retailer','framework'); ?></label></th>
			<td>
				<select name="new[merchant_slug]">
					<!-- MERCHANT NAME-->
					<?php foreach($merchants as $merchant): ?>
					<option value="<?php echo $merchant->slug ?>"<?php echo ((isset($form['merchant_slug']) && $merchant->slug == $form['merchant_slug']) ? ' selected="selected"' : '') ?>><?php echo $merchant->name ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
		<!-- MERCHANT PRICE-->
			<th scope="row"><label for="merchant_price"><?php _e('Product price','framework'); ?> *</label></th>
			<td>
				<input type="text" name="new[merchant_price]" value="<?php echo (isset($form['merchant_price']) ? $form['merchant_price'] : '') ?>" /><br />
				<span class="description"><?php _e('Must be a numeric value (eg: 99 or 99.99 or 99,99)','framework'); ?></span>
			</td>
		</tr>
		<!-- MERCHANT SHIPPING-->
		<tr>
			<th scope="row"><label for="merchant_shipping"><?php _e('Shipping cost','framework'); ?></label></th>
			<td>
				<input type="text" name="new[merchant_shipping]" value="<?php echo (isset($form['merchant_shipping']) ? $form['merchant_shipping'] : '') ?>" /><br />
				<span class="description"><?php _e('A numeric value or a something like "Free delivery".','framework'); ?></span>
			</td>
		</tr>
		<!-- MERCHANT VOUCHER-->
		<tr>
			<th scope="row"><label for="merchant_voucher"><?php _e('Voucher Code','framework'); ?></label></th>
			<td>
				<input type="text" name="new[merchant_voucher]" value="<?php echo (isset($form['merchant_voucher']) ? $form['merchant_voucher'] : '') ?>" /><br />
				<span class="description"><?php _e('A code that you can use to redeem a discount when you reach the checkout','framework'); ?></span>
			</td>
		</tr>
		<!-- MERCHANT DEEPLINK-->
		<tr>
			<th scope="row"><label for="merchant_deeplink"><?php _e('Retailer product page URL','framework'); ?> *</label></th>
			<td>
				<input type="text" name="new[merchant_deeplink]" value="<?php echo (isset($form['merchant_deeplink']) ? $form['merchant_deeplink'] : '') ?>" /><br />
				<span class="description"><?php _e('Full URL, with "http://"','framework'); ?></span>
			</td>
		</tr>
		<!-- MERCHANT PRODUCT IMAGE URL-->
		<tr>
			<th scope="row"><label for="feed_product_image"><?php _e('Retailer product image URL','framework'); ?> *</label></th>
			<td>
				<input type="text" name="new[feed_product_image]" value="<?php echo (isset($form['feed_product_image']) ? $form['feed_product_image'] : '') ?>" /><br />
				<span class="description"><?php _e('Full Product Image URL, with "http://"','framework'); ?></span>
			</td>
		</tr>

	</table>
	<p class="submit">
		<input type="submit" name="submit[new]" id="submit" class="button-secondary" value="<?php _e('Add retailer','framework'); ?>" />
	</p>
	</form>
	<?php endif; ?>

	
	<?php else: ?>
	
	<p><?php _e('Product not found','framework'); ?></p>
	
	<?php endif; ?>

</div>

<?php else: ?>

<div class="wrap">

	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Products Management','framework'); ?> <a href="post-new.php?post_type=product" class="add-new-h2"><?php _e('Add new product','framework'); ?></a></h2>
	
	<?php
	$where = (isset($_GET['s']) && $_GET['s'] != '') ? " AND product_name LIKE '%".esc_sql(trim($_GET['s']))."%'" : '';		
	$q = "SELECT p.*, count(p.id_product) as merchants, pr.wp_post_id FROM ".$wpdb->prefix."pc_products p, ".$wpdb->prefix."pc_products_relationships pr WHERE p.id_product = pr.id_product".$where." GROUP BY id_product ORDER BY product_name";
	$products_ok = $wpdb->get_results($q, OBJECT_K);
	$items = $wpdb->num_rows;
	$q = "SELECT * FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product NOT IN (SELECT DISTINCT id_product FROM ".$wpdb->prefix."pc_products)".$where;
	$products_ko = $wpdb->get_results($q);
	
	$limit = "";
	
	if($items > 0) {
			$p = new pagination;
			$p->items($items);
			$p->limit(30); // Limit entries per page
			$p->target("admin.php?page=products_management");
			@$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
			$p->calculate(); // Calculates what to show
			$p->parameterName('paging');
			$p->adjacents(1); //No. of page away from the current page
	 
			if(!isset($_GET['paging'])) {
				$p->page = 1;
			} else {
				$p->page = $_GET['paging'];
			}
	 
			//Query for limit paging
			$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;
	 
	}
	 
	?>
	
	<ul class="subsubsub">
		<li><a href="#" onclick="return false;" class="current"><?php _e('All','framework'); ?> <span class="count">(<?php echo count($products_ok); ?>)</span></a></li>
	</ul>
	
	<form method="get" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	<p class="search-box">
		<label class="screen-reader-text" for="post-search-input"><?php _e('Search product:','framework'); ?></label>
		<input type="hidden" name="page" value="products_management" />
		<input type="text" id="post-search-input" name="s" value="<?php echo (isset($_GET['s']) ? trim($_GET['s']) : ''); ?>" />
		<input type="submit" id="search-submit" class="button" value="<?php _e('Search product','framework'); ?>" />
	</p>
	</form>
	
	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="action">
				<option value="-1" selected="selected"><?php _e('Bulk Actions','framework'); ?></option>
				<option value="delete" class="hide-if-no-js"><?php _e('Delete','framework'); ?></option>
			</select>
			<input type="submit" name="" id="doaction" class="button-secondary" value="<?php _e('Apply','framework'); ?>" />
		</div>
		
		<?php if( isset($p) && $p): ?>
		<div class='tablenav-pages'>
			<?php echo $p->show();  // Echo out the list of paging. ?>
		</div>
		<br class="clear" />
		<?php endif; ?>
	</div>
	
	<table class="widefat">
		<!-- THEAD -->
		<thead>
			<tr>
				<th><input type="checkbox" class="bulk_cb select" onclick="if(jQuery('.bulk_cb').hasClass('select')) { jQuery('.bulk_to_select, .bulk_cb').attr('checked','checked'); } else { jQuery('.bulk_to_select, .bulk_cb').removeAttr('checked'); } jQuery('.bulk_cb').toggleClass('select');"/></th>
				<th scope="col" class="manage-column" style=""><?php _e('PID','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Image','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Categories','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Brand','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Product name','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Retailers','framework'); ?></th>
				<th scope="col" class="manage-column" style="width:160px;"><?php _e('Actions','framework'); ?></th>
			</tr>
		</thead>
		<!-- TFOOT -->
		<tfoot>
			<tr>
				<th><input type="checkbox" class="bulk_cb select" onclick="if(jQuery('.bulk_cb').hasClass('select')) { jQuery('.bulk_to_select, .bulk_cb').attr('checked','checked'); } else { jQuery('.bulk_to_select, .bulk_cb').removeAttr('checked'); } jQuery('.bulk_cb').toggleClass('select');"/></th>
				<th scope="col" class="manage-column" style=""><?php _e('PID','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Image','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Categories','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Brand','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Product name','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Retailers','framework'); ?></th>
				<th scope="col" class="manage-column" style="width:180px;"><?php _e('Actions','framework'); ?></th>
			</tr>
		</tfoot>
		<!-- TBODY -->
		<tbody>
			<?php
			if(count($products_ko) != 0):
				foreach($products_ko as $product):
				$wp_post = get_post($product->wp_post_id);
				$post_image = get_post_meta($wp_post->ID,'image_meta',true);
				?>
				<tr>
					<th><input type="checkbox" value="<?php echo $product->id_product; ?>" class="bulk_to_select" /></th>
					<td><?php echo $product->id_product ?></td>
					<td>
						<?php if($post_image != ""): ?>
						<img src="<?php echo $post_image ?>" style="max-width:150px; max-height:50px;" alt="" title="" />
						<?php else: ?>
						<div style="width:50px; height:50px;">&nbsp;</div>
						<?php endif; ?>
					</td>
					<td>
						<?php		
						$categories = array();		
						$terms = wp_get_object_terms($wp_post->ID,'product_category');
						foreach($terms as $category) {
							$categories[] = $category->name;
						}
						echo implode(', ',$categories); 
						?>
					</td>
					<td>
						<?php		
						$brands = array();		
						$terms = wp_get_object_terms($wp_post->ID,'product_brand');
						foreach($terms as $brand) {
							$brands[] = $brand->name;
						}
						echo implode(', ',$brands); 
						?>
					</td>
					<td><?php echo $wp_post->post_title ?></td>
					<td><span style="color:red; font-weight:bold;">0</span></td>
					<td>
						<a href="?page=products_management&amp;action=show_product&amp;product_id=<?php echo $product->id_product ?>"><?php _e('Manage product retailers and default ordering','framework'); ?></a>
						<br />
						<a href="post.php?post=<?php echo $product->wp_post_id ?>&action=edit"><?php _e('Edit product information','framework'); ?></a>
						<br />
						<a href="?page=products_management&amp;action=delete_product&amp;product_id=<?php echo $product->id_product ?>" onclick="if(!confirm('<?php _e('Are you sure ? It will delete every occurences of this product.','framework'); ?>')) return false;"><?php _e('Delete product &amp; retailers','framework'); ?></a>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php
			$q = "SELECT p.*, count(p.id_product) as merchants, pr.wp_post_id FROM ".$wpdb->prefix."pc_products p, ".$wpdb->prefix."pc_products_relationships pr WHERE p.id_product = pr.id_product".$where." GROUP BY id_product ORDER BY product_name";// $limit";
			$products = $wpdb->get_results($q, OBJECT_K);
			if(count($products_ko) == 0 && count($products) == 0):
			?>
			<tr>
				<td colspan="8"><?php _e('No product','framework'); ?></td>
			</tr>
			<?php
			endif;
			foreach($products as $product):
			$wp_post = get_post($product->wp_post_id);
			$post_image = get_post_meta($wp_post->ID,'image_meta',true);
			?>
			<tr>
				<th><input type="checkbox" value="<?php echo $product->id_product; ?>" class="bulk_to_select" /></th>
				<td><?php echo $product->id_product ?></td>
				<td>
					<?php if($post_image != ""): ?>
					<img src="<?php echo $post_image ?>" style="max-width:150px; max-height:50px;" alt="" title="" />
					<?php else: ?>
					<div style="width:50px; height:50px;">&nbsp;</div>
					<?php endif; ?>
				</td>
				<td>
					<?php		
					$categories = array();		
					$terms = wp_get_object_terms($wp_post->ID,'product_category');
					foreach($terms as $category) {
						$categories[] = $category->name;
					}
					echo implode(', ',$categories); 
					?>
				</td>
				<td>
					<?php		
					$brands = array();		
					$terms = wp_get_object_terms($wp_post->ID,'product_brand');
					foreach($terms as $brand) {
						$brands[] = $brand->name;
					}
					echo implode(', ',$brands); 
					?>
				</td>
				<td><?php echo $wp_post->post_title ?></td>
				<td><span style="color:green; font-weight:bold;"><?php echo $product->merchants ?></span></td>
				<td>
					<a href="?page=products_management&amp;action=show_product&amp;product_id=<?php echo $product->id_product ?>"><?php _e('Manage product retailers and default ordering','framework'); ?></a>
					<br />
					<a href="post.php?post=<?php echo $product->wp_post_id ?>&action=edit"><?php _e('Edit product information','framework'); ?></a>
					<br />
					<a href="?page=products_management&amp;action=delete_product&amp;product_id=<?php echo $product->id_product ?>" onclick="if(!confirm('<?php _e('Are you sure ? It will delete every occurences of this product.','framework'); ?>')) return false;"><?php _e('Delete product &amp; retailers','framework'); ?></a>
				</td>
			</tr>
			<?php endforeach; ?>
		
		</tbody>
	</table>
	
	<p>
		<select name="action2">
			<option value="-1" selected="selected"><?php _e('Bulk Actions','framework'); ?></option>
			<option value="delete" class="hide-if-no-js"><?php _e('Delete','framework'); ?></option>
		</select>
		<input type="submit" name="" id="doaction2" class="button-secondary" value="<?php _e('Apply','framework'); ?>" />
	</p>
	
</div>

<script type="text/javascript">
function bulk_delete() {
	var ids = "";
	jQuery('.bulk_to_select:checked').each(function() {
		ids += jQuery(this).val()+",";
	});
	window.location = "?page=products_management&action=bulk_delete&ids="+ids;
}

jQuery('#doaction').click(function() {
	if(jQuery("[name=action]").val() == "delete") {
		bulk_delete();
	}
});

jQuery('#doaction2').click(function() {
	if(jQuery("[name=action2]").val() == "delete") {
		bulk_delete();
	}
});
</script>

<?php endif; ?>