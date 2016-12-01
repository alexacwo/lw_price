<?php 
// Catching direct file access
if('wp-product-admin.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('<h2>Direct File Access Prohibited</h2>');
// Wordpress access to the database
global $wpdb;
// Upload directory for merchants images
$current_blog_id = get_current_blog_id();
if($current_blog_id == 1){
	$uploadDir = ABSPATH.'wp-content/uploads/compare/merchants/';
} else {
	// For WP multisite installation
	$uploadDir = ABSPATH.'wp-content/uploads/compare/merchants/'.$current_blog_id.'/';
}
if(!is_dir($uploadDir)) {
	wp_mkdir_p($uploadDir);
}

/**
 * Check if a slug if available for a merchant
 */
function isSlugAvailable($slug) {
	global $wpdb;
	$q = "SELECT slug FROM ".$wpdb->prefix.'pc_products_merchants'." WHERE slug = '".$slug."' LIMIT 1";
	$slug = $wpdb->get_row($q);
	return ($slug == NULL); 
}
?>
<?php if(isset($_GET['action']) && $_GET['action'] == "add_merchant"): // Add a merchant ?>
<?php
// Form submitted
if(isset($_POST['merchant']['submit'])) {
	// Extracting data
	$_POST['merchant'] = array_map('stripslashes',$_POST['merchant']);
	$_POST['merchant'] = array_map('trim',$_POST['merchant']);
	extract($_POST['merchant']);
	// Slug check
	$slug = $_POST['merchant']['slug'] = (empty($slug) ? compare_slugify($name) : compare_slugify($slug));
	// Validate
	if(empty($name)) { $form = $_POST['merchant']; $errors['merchant'] = __("Retailer name is required",'framework'); }
	if(!isSlugAvailable($slug)) { $form = $_POST['merchant']; $errors['slug'] = __("Slug is already used, please choose another one",'framework'); }
	// Save data
	if(!isset($form) && !isset($errors)) {
		$data = array();
		$data['name'] = $name;
		$data['slug'] = $slug;
		$data['url'] = $url;
		
		if (is_uploaded_file($_FILES['upload_merchant_logo']['tmp_name'])) {
			$uploadedFileName = $_FILES['upload_merchant_logo']['name'];
		   	$fileMoved = move_uploaded_file($_FILES['upload_merchant_logo']['tmp_name'],$uploadDir.$uploadedFileName);
		   	$data['image'] = $uploadedFileName;
		}
		
		$wpdb->insert($wpdb->prefix.'pc_products_merchants',$data);
		$success = __("Retailer successfully added to database",'framework');
	}
}
?>
<div class="wrap">
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Add retailer','framework'); ?></h2>
	
	<?php if(isset($success)): ?>
		<div class="success"><?php echo $success; ?></div>
		<p><a href="?page=merchants_management" class="button-secondary"><?php _e('View Retailers','framework'); ?></a></p>
	<?php else: ?>
	
		<p><?php _e('Use this form to add a new retailer to the database','framework'); ?></p>
		
		<?php if(isset($errors)): ?>
		<div class="error">
			<?php foreach($errors as $error): ?>
			<?php echo $error ?><br />
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">	
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="merchant_name"><?php _e('Retailer name','framework'); ?> *</label></th>
					<td><input type="text" size="75" name="merchant[name]" id="merchant_name" value="<?php echo (isset($form['name']) ? $form['name'] : '') ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="merchant_slug"><?php _e('Retailer slug','framework'); ?></label></th>
					<td><input type="text" size="75" name="merchant[slug]" id="merchant_slug" value="<?php echo (isset($form['slug']) ? $form['slug'] : '') ?>" />
					<br />
					<small><?php _e('(slug will be automatically generated if the field is empty)','framework'); ?></small>
				</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="merchant_url"><?php _e('Retailer URL','framework'); ?></label></th>
					<td><input type="text" size="75" name="merchant[url]" id="merchant_url" value="<?php echo (isset($form['url']) ? $form['url'] : '') ?>" /></td>
				</tr>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="upload_merchant_logo"><?php _e('Retailer logo','framework'); ?></label></th>
					<td>
						<input type="file" name="upload_merchant_logo" id="upload_merchant_logo" value="" />
						<br />
						<small><?php _e('Upload image with 150x100 pixels','framework'); ?></small>
					</td>
					
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="merchant[submit]" id="submit" class="button-secondary" value="<?php _e('Add New Retailer','framework'); ?>" />
			</p>
		</form>
		
	<?php endif; ?>	
</div>
<?php elseif(isset($_GET['action']) && $_GET['action'] == "edit_merchant"): // Edit a merchant ?>
<?php
// Form submitted
if(isset($_POST['merchant']['submit'])) {
	// Extracting data
	$_POST['merchant'] = array_map('stripslashes',$_POST['merchant']);
	$_POST['merchant'] = array_map('trim',$_POST['merchant']);
	extract($_POST['merchant']);
	// Slug check
	$slug = $_POST['merchant']['slug'] = (empty($slug) ? compare_slugify($name) : compare_slugify($slug));
	// Validate
	if(empty($name)) { $form = $_POST['merchant']; $errors['merchant'] = "Retailer name is required"; }
	if(!isSlugAvailable($slug) && $slug != $_GET['merchant']) { $form = $_POST['merchant']; $errors['slug'] = __("Slug is already used, please choose another one",'framework'); }
	// Save data
	if(!isset($form) && !isset($errors)) {
		$data = array();
		$data['name'] = $name;
		$data['slug'] = $slug;
		$data['url'] = $url;
		if (is_uploaded_file($_FILES['upload_merchant_logo']['tmp_name'])) {
			$uploadedFileName = $_FILES['upload_merchant_logo']['name'];
		   	$fileMoved = move_uploaded_file($_FILES['upload_merchant_logo']['tmp_name'],$uploadDir.$uploadedFileName);
		   	$data['image'] = $uploadedFileName;
		}
		
		$q = "UPDATE ".$wpdb->prefix."pc_products SET id_merchant = '".$data['slug']."' WHERE id_merchant = '".$_GET['merchant']."'";
		$wpdb->query($q);
	
                if(aw_is_compare_plus_installed()){
                    $q = "UPDATE ".$wpdb->prefix."pc_feeds SET merchant = '".$data['slug']."' WHERE merchant = '".$_GET['merchant']."'";
                    $wpdb->query($q);
                } 
		
		$wpdb->update($wpdb->prefix.'pc_products_merchants',$data,array('slug' => $_GET['merchant']));
		$success = __("Retailer successfully updated in the database",'framework');
	}
}
if(!isset($form)) {
	$q = "SELECT * FROM ".$wpdb->prefix.'pc_products_merchants'." WHERE slug = '".$_GET['merchant']."'";
	$merchant = $wpdb->get_row($q);
	if($merchant != NULL) {
		$form['name'] = stripslashes($merchant->name);
		$form['slug'] = $merchant->slug;
		$form['url'] = $merchant->url;
	} else {
		$errors['notFound'] = __("Retailer not found.",'framework');
	}
}
?>
<div class="wrap">
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Edit retailer','framework'); ?></h2>
	 
	<?php if(isset($success)): ?>
		<br />
		<div class="success"><?php echo $success; ?></div></p>
		<p><a href="?page=merchants_management" class="button-secondary"><?php _e('View Retailers','framework'); ?></a></p>
		
	<?php else: ?>	
	
		<p><?php _e('Use this form to edit the retailer','framework'); ?></p>
		
		<?php if(isset($errors)): ?>
		<div class="error">
			<?php foreach($errors as $error): ?>
			<?php echo $error ?><br />
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">	
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="merchant_name"><?php _e('Retailer name','framework'); ?> *</label></th>
					<td><input type="text" size="75" name="merchant[name]" id="merchant_name" value="<?php echo (isset($form['name']) ? stripslashes($form['name']) : '') ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="merchant_slug"><?php _e('Retailer slug','framework'); ?></label></th>
					<td>
						<input type="text" size="75" name="merchant[slug]" id="merchant_slug" value="<?php echo (isset($form['slug']) ? $form['slug'] : '') ?>" />
						<br /><small><?php _e('(slug will be automatically generated if empty)','framework'); ?></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="merchant_url"><?php _e('Retailer URL','framework'); ?></label></th>
					<td><input type="text" size="75" name="merchant[url]" id="merchant_url" value="<?php echo (isset($form['url']) ? $form['url'] : '') ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="upload_merchant_logo"><?php _e('Retailer logo','framework'); ?></label></th>
					<td>
						<input type="file" name="upload_merchant_logo" id="upload_merchant_logo" value="" />						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php _e('Current retailer logo','framework'); ?></label></th>
					<td>
						<?php if($merchant->image != "" && file_exists($uploadDir.$merchant->image)): ?>
							<?php if($current_blog_id == 1): ?>
								<img src="<?php echo home_url().'/wp-content/uploads/compare/merchants/'.$merchant->image ?>" style="max-width:150px; max-height:50px;" alt="" title="" />
							<?php else: ?>
								<img src="\<?php echo home_url().'/wp-content/uploads/compare/merchants/'.$current_blog_id.'/'.$merchant->image ?>" style="max-width:150px; max-height:50px;" alt="" title="" />
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="merchant[submit]" id="submit" class="button-secondary" value="<?php _e('Save','framework'); ?>" />
			</p>
		</form>
	<?php endif; ?>
		
</div>
<?php  elseif(isset($_GET['action']) && $_GET['action'] == "bulk_delete"): // Delete merchant with bulk ?>
<?php
	
$ids = (isset($_GET['ids'])) ? explode(",",$_GET['ids']) : array();
foreach($ids as $id) {
	if($id == "") continue;
	
	$m = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."pc_products_merchants WHERE slug = '".$id."' LIMIT 1");
	if($m != null) {
		if($m->feed == 0) {
			// Merchant related products
			$sql = "DELETE FROM ".$wpdb->prefix.'pc_products'." WHERE id_merchant = '".$id."'";
			$wpdb->query($sql);
			// Merchant information
			$sql = "DELETE FROM ".$wpdb->prefix.'pc_products_merchants'." WHERE slug = '".$id."'";
			$wpdb->query($sql);
		} else {
			$error = true;
		}
	} else {
		$error = true;
	}
}
// Posts out of date
$q = "DELETE a,b,c FROM ".$wpdb->prefix."posts a LEFT JOIN ".$wpdb->prefix."term_relationships b ON (a.ID = b.object_id)
LEFT JOIN ".$wpdb->prefix."postmeta c ON (a.ID = c.post_id) WHERE a.post_type = 'product' AND a.ID IN (SELECT wp_post_id FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product NOT IN (SELECT id_product FROM ".$wpdb->prefix."pc_products GROUP BY id_product))";
$wpdb->query($q);
// Product relationships out of date
$q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product NOT IN (SELECT id_product FROM ".$wpdb->prefix."pc_products GROUP BY id_product)";
$wpdb->query($q);
// Update terms count
$q = "UPDATE ".$wpdb->prefix."term_taxonomy SET count = (SELECT count(*) FROM ".$wpdb->prefix."term_relationships WHERE ".$wpdb->prefix."term_relationships.term_taxonomy_id = ".$wpdb->prefix."term_taxonomy.term_taxonomy_id)";
$wpdb->query($q);
?>
<div class="wrap">
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Retailer deletion','framework'); ?></h2>
	
	<?php if(isset($error)): ?>
	<div class="error"><?php _e('One or more retailer has not been deleted','framework'); ?></div>
	<?php else: ?>
	<div class="success"><?php _e('Retailers deleted successfully from database','framework'); ?></div>
	<?php endif; ?>
	<p><a href="?page=merchants_management" class="button-secondary"><?php _e('View Retailers','framework'); ?></a></p>
	
</div>
<?php  elseif(isset($_GET['action']) && $_GET['action'] == "delete_merchant"): // Delete merchant ?>
<div class="wrap">
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Retailer deletion','framework'); ?></h2>
	<?php
	
	$merchant_id = (isset($_GET['merchant'])) ? $_GET['merchant'] : 0;
	$m = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."pc_products_merchants WHERE slug = '".$merchant_id."' LIMIT 1");
	
	if($m != null):
            
		if($m->feed == 0) {
		// Merchant related products
		$sql = "DELETE FROM ".$wpdb->prefix.'pc_products'." WHERE id_merchant = '".$merchant_id."'";
		$wpdb->query($sql);
		// Merchant information
		$sql = "DELETE FROM ".$wpdb->prefix.'pc_products_merchants'." WHERE slug = '".$merchant_id."'";
		$wpdb->query($sql);
		// Posts out of date
		$q = "DELETE a,b,c FROM ".$wpdb->prefix."posts a LEFT JOIN ".$wpdb->prefix."term_relationships b ON (a.ID = b.object_id)
		LEFT JOIN ".$wpdb->prefix."postmeta c ON (a.ID = c.post_id) WHERE a.post_type = 'product' AND a.ID IN (SELECT wp_post_id FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product NOT IN (SELECT id_product FROM ".$wpdb->prefix."pc_products GROUP BY id_product))";
		$wpdb->query($q);
		// Product relationships out of date
		$q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product NOT IN (SELECT id_product FROM ".$wpdb->prefix."pc_products GROUP BY id_product)";
		$wpdb->query($q);
		// Update terms count
		$q = "UPDATE ".$wpdb->prefix."term_taxonomy SET count = (SELECT count(*) FROM ".$wpdb->prefix."term_relationships WHERE ".$wpdb->prefix."term_relationships.term_taxonomy_id = ".$wpdb->prefix."term_taxonomy.term_taxonomy_id)";
		$wpdb->query($q);
		?>
		<br />
		<div class="success"><?php _e('Retailer deleted successfully from database','framework'); ?></div>
		<p><a href="?page=merchants_management" class="button-secondary"><?php _e('View Retailers','framework'); ?></a></p>
		<?php
		} else {
		?>
		<br />
		<div class="warning"><?php _e('This retailer cannot be deleted because it has been created by a feed','framework'); ?></div>
		<p><a href="?page=merchants_management" class="button-secondary"><?php _e('View Retailers','framework'); ?></a></p>
		<?php
		}
	else:
	?>
	<br />
	<div class="error"><?php _e('Retailer not found','framework'); ?></div>
	<p><a href="?page=merchants_management" class="button-secondary"><?php _e('View Retailers','framework'); ?></a></p>
	<?php endif; ?>
		
</div>
<?php else : // Every other cases ?>
<div class="wrap">
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Retailer Management','framework'); ?> <a href="?page=merchants_management&amp;action=add_merchant" class="add-new-h2"><?php _e('Add new retailer','framework'); ?></a></h2>
	
	<p>
		<select name="action">
			<option value="-1" selected="selected"><?php _e('Bulk Actions','framework'); ?></option>
			<option value="delete" class="hide-if-no-js"><?php _e('Delete','framework'); ?></option>
		</select>
		<input type="submit" name="" id="doaction" class="button-secondary" value="<?php _e('Apply','framework'); ?>" />
	</p>
	
	<table class="widefat">
		<!-- THEAD -->
		<thead>
			<tr>
				<th><input type="checkbox" class="bulk_cb select" onclick="if(jQuery('.bulk_cb').hasClass('select')) { jQuery('.bulk_to_select, .bulk_cb').attr('checked','checked'); } else { jQuery('.bulk_to_select, .bulk_cb').removeAttr('checked'); } jQuery('.bulk_cb').toggleClass('select');"/></th>
				<th scope="col" class="manage-column" style=""><?php _e('Image','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Name','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Slug','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('URL','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Actions','framework'); ?></th>
			</tr>
		</thead>
		<!-- TFOOT -->
		<tfoot>
			<tr>
				<th><input type="checkbox" class="bulk_cb select" onclick="if(jQuery('.bulk_cb').hasClass('select')) { jQuery('.bulk_to_select, .bulk_cb').attr('checked','checked'); } else { jQuery('.bulk_to_select, .bulk_cb').removeAttr('checked'); } jQuery('.bulk_cb').toggleClass('select');"/></th>
				<th scope="col" class="manage-column" style=""><?php _e('Image','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Name','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Slug','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('URL','framework'); ?></th>
				<th scope="col" class="manage-column" style=""><?php _e('Actions','framework'); ?></th>
			</tr>
		</tfoot>
		<!-- TBODY -->
		<tbody>
			<?php
			global $wpdb;
			$q = "SELECT * FROM ".$wpdb->prefix."pc_products_merchants ORDER BY name ASC";
			$a = $wpdb->get_results($q);
			$merchants = $wpdb->get_results($q, OBJECT);
			if(count($merchants) == 0):
			?>
			<tr>
				<td><?php _e('No retailer','framework'); ?></td>
			</tr>
			<?php
			endif;
			foreach($merchants as $merchant):
			?>
			<tr>
				<th><input type="checkbox" value="<?php echo $merchant->slug ?>" class="bulk_to_select" /></th>
				<td style="max-width:150px;">
					<?php if($merchant->image != "" && file_exists($uploadDir.$merchant->image)): ?>
					<?php if($current_blog_id == 1): ?>
						<img src="<?php echo home_url().'/wp-content/uploads/compare/merchants/'.$merchant->image ?>" style="max-width:150px; max-height:50px;" alt="" title="" />
					<?php  else: ?>
						<img src="<?php echo home_url().'/wp-content/uploads/compare/merchants/'.$current_blog_id.'/'.$merchant->image ?>" style="max-width:150px; max-height:50px;" alt="" title="" />
					<?php  endif; ?>
					<?php endif; ?>
				</td>
				<td><?php echo stripslashes($merchant->name); ?></td>
				<td><?php echo $merchant->slug ?></td>
				<td><a href="<?php echo $merchant->url ?>"><?php echo $merchant->url ?></a></td>
				<td>
					<a href="?page=merchants_management&amp;action=edit_merchant&amp;merchant=<?php echo $merchant->slug ?>"><?php _e('Edit','framework'); ?></a>
					<br />
					<a href="?page=merchants_management&amp;action=delete_merchant&amp;merchant=<?php echo $merchant->slug ?>" onclick="if(!confirm('<?php _e('Are you sure ?','framework'); ?>')) return false;"><?php _e('Delete','framework'); ?></a>
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
	window.location = "?page=merchants_management&action=bulk_delete&ids="+ids;
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