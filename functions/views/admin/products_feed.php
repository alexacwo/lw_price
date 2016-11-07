<?php 
// Catching direct file access
if('products_feed.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('<h2>Direct File Access Prohibited</h2>');

// Wordpress access to the database
global $wpdb;

?>

<?php if(isset($_GET['action']) && $_GET['action'] == 'import'): // Import case ?>

	<div class="wrap">
	
		<div id="icon-link-manager" class="icon32"></div>
		<h2><?php _e('Import Product Feed','framework'); ?></h2>
		
		<p><?php _e('Import process will insert posts, categories, brands and retailers into the database','framework'); ?></p>

		<?php
		
		$filePath = ABSPATH.'wp-content/uploads/compare/data.csv';
		// Step 1 : check the file
		if(file_exists($filePath)) {
			// Step 2 : read the file
			@ini_set('auto_detect_line_endings', true);
			if (($handle = fopen($filePath, "r")) !== FALSE) {
				$row = 1;
				$uids = array();
				
				// Deletion flag
				//$q = "UPDATE ".$wpdb->prefix."pc_products SET updated = '0' WHERE id_merchant = '".$feed->merchant."'";
				//$wpdb->query($q);
				$delimiter_friendly = (isset($_GET['delimiter']) && $_GET['delimiter'] != "") ? $_GET['delimiter'] : 'semicolon';
				$delimiter = ";";
				switch ($delimiter_friendly) {
					case 'semicolon':
						$delimiter = ";";
						break;
					case 'comma':
						$delimiter = ",";
						break;
					case 'pipe':
						$delimiter = "|";
						break;
					case 'tab':
						$delimiter = "	";
						break;
					default:
						$delimiter = ";";
				}

				$new_base_products_uids = array();

				$is_a_base_feed_product = 0;
                    
				$params_names = array();   
				$total_col = 0;          
				
			    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					
			    	if($row == 1) {
						$row++;
						
						//Get names of product parameters
						$total_col = count($data);
						for ($i = 12; $i < $total_col; $i++) {
							$params_names[] = $data[$i];
						}
						
						continue;
					}         
					
					//Every time empty the array
					$params_values = array();  
			    	
			    	$uid = checkortransfromutf8(trim($data[0]));
			    	$product_category = checkortransfromutf8(trim($data[1]));
			    	$product_brand = checkortransfromutf8(trim($data[2]));
			    	$product_full_name = checkortransfromutf8(trim($data[3]));
					$merchant_price = checkortransfromutf8(trim($data[4]));
			    	$merchant_shipping = checkortransfromutf8(trim($data[5]));
			    	$product_image = checkortransfromutf8(trim($data[6]));
			    	$merchant_name = checkortransfromutf8(trim($data[7]));
			    	$merchant_deeplink = checkortransfromutf8(trim($data[8]));
					$product_description = checkortransfromutf8(trim($data[9]));					
					$product_global_description = checkortransfromutf8(trim($data[10]));
			    	$merchant_voucher = checkortransfromutf8(trim($data[11]));
					
					for ($i = 12; $i < $total_col; $i++) {
						$params_values[] = $data[$i];
					}
					
						 
			    	
			    	// Replacement
					$merchant_price = str_ireplace(',','.',$merchant_price);
			    	
			    	if(!in_array($uid, $uids)) {
					
			    		if($product_category == '') {
							echo '<span style="color:red;">'. __('Line','framework') . ' ' . $row.': '.__('No category detected','framework').'</span>'."<br />";
							$row++;
							continue;
				    	}
				    	
				    	if($product_brand == '') {
							echo '<span style="color:red;">'. __('Line','framework') . ' ' . $row.': '.__('No brand detected','framework').'</span>'."<br />";
							$row++;
							continue;
				    	}
			    	
			    		if($product_full_name == '') {
							echo '<span style="color:red;">'. __('Line','framework') . ' ' . $row.': '.__('Full product name required','framework').'</span>'."<br />";
							$row++;
							continue;
				    	}
                                        
                                        //If there is no merchant this is a base feed product, as such mark this against it so Compare+ knows not to delete it
                                        if($merchant_name==''){$is_a_base_feed_product = 1;}else {$is_a_base_feed_product = 0;}
                                        if($is_a_base_feed_product==1)
                                        {
                                            $new_base_products_uids[] = $uid;
                                        }
						
						
						$q = "SELECT * FROM ".$wpdb->prefix."pc_products_relationships WHERE product_ean = '".esc_sql($uid)."'";
						$post_relation = $wpdb->get_row($q);
							
						if($post_relation == null) {
							$q = "SELECT * FROM ".$wpdb->prefix."pc_products_relationships WHERE product_name = '".esc_sql($product_full_name)."'";
							$post_relation = $wpdb->get_row($q);
						}
												
						// The post
						if($post_relation == null) {
							// Insert wp_post
							$q = "
								INSERT IGNORE INTO ".$wpdb->prefix."posts SET						
									post_author = 1,
									post_date = NOW(),
									post_date_gmt = UTC_TIMESTAMP(),
									post_title = '".esc_sql($product_full_name)."',
									post_status = 'publish',
									ping_status = 'open',
									comment_status = 'open',
									post_name = '".compare_slugify($product_full_name)."',
									post_modified = NOW(),
									post_modified_gmt = UTC_TIMESTAMP(),
									post_parent = 0,
									post_type = 'product',
									post_content = '".esc_sql($product_description)."'
								";
							$wpdb->query($q);
							$id = $wpdb->insert_id;
							
							$wpdb->insert_id = 0; // Reset for the next product
							if($id == 0) { $counter_failed++; continue; }
							
							if($product_global_description != '' && $product_global_description != null){
								$q = "
									INSERT INTO ".$wpdb->prefix."pc_products_custom SET
										product_id = '".$id."',
										product_description = '".esc_sql($product_global_description)."',
										product_name = '".esc_sql($product_full_name)."',
                                                                                insertion_date = '".time()."'
									ON DUPLICATE KEY UPDATE
								
										product_id = '".$id."',
										product_description = '".esc_sql($product_global_description)."',
										product_name = '".esc_sql($product_full_name)."',
                                                                                insertion_date = '".time()."'
									";
								$wpdb->query($q);
							}
							if($product_full_name != '' && $product_full_name != null){
								$q = "
									INSERT INTO ".$wpdb->prefix."pc_products_relationships SET
										wp_post_id = '".$id."',
										product_ean = '".$uid."',
										product_name = '".esc_sql($product_full_name)."',
										last_update = '".time()."',
                                                                                base_feed_import =  '$is_a_base_feed_product'
									ON DUPLICATE KEY UPDATE
										wp_post_id = '".$id."',
										product_ean = '".$uid."',
										product_name = '".esc_sql($product_full_name)."',
										last_update = '".time()."'
									";
								$wpdb->query($q);
								$q = "UPDATE ".$wpdb->prefix."pc_products_relationships SET last_update = '".time()."' WHERE wp_post_id = '".$id."'";
								$wpdb->query($q);
								$q = "SELECT * FROM ".$wpdb->prefix."pc_products_relationships WHERE wp_post_id = '".$id."'";
								$post_relation = $wpdb->get_row($q);
								//$counter_created++;
							}
							echo '<span style="color:green;">'. __('Line','framework') . ' ' . $row.': '.$product_full_name.' '.__('created','framework').($merchant_name != '' ? '' : __(', no retailer information was supplied','framework')).'</span>'."<br />";
							
							for ($j = 0; $j < count($params_names); $j++) {
								$wpdb->query( $wpdb->prepare( 
									"
									INSERT INTO ".$wpdb->prefix."pc_products_params
									( id, product_id, param_name, param_value )
									VALUES ( %d, %d, %s, %s )
									", 
									'',
									$id,
									$params_names[$j], 
									$params_values[$j]
								) );
							}
						} else {
							// Update wp_post
							$id = $post_relation->wp_post_id;
							$q = "
								UPDATE ".$wpdb->prefix."posts SET
									post_title = '".esc_sql($product_full_name)."',
									post_name = '".compare_slugify($product_full_name)."',
									post_modified = NOW(),
									post_modified_gmt = UTC_TIMESTAMP(),
									post_type = 'product',
									post_content = '".esc_sql($product_description)."',
									ping_status = 'open',
									comment_status = 'open'
								WHERE ID = '".$id."'
								";
							$wpdb->query($q);
							if($product_global_description != '' && $product_global_description != null){
								$q = "UPDATE ".$wpdb->prefix."pc_products_relationships SET last_update = '".time()."' WHERE id_product = '".$post_relation->id_product."'";
								$wpdb->query($q);
								
								$q = "
									INSERT INTO ".$wpdb->prefix."pc_products_custom SET
										product_id = '".$id."',
										product_description = '".esc_sql($product_global_description)."',
										product_name = '".esc_sql($product_full_name)."'
									ON DUPLICATE KEY UPDATE
								
										product_id = '".$id."',
										product_description = '".esc_sql($product_global_description)."',
										product_name = '".esc_sql($product_full_name)."'
									";
								$wpdb->query($q);
							}
							//$counter_updated++;
							echo '<span style="color:green;">'. __('Line','framework') . ' ' . $row.': '.$product_full_name.' '.__('updated','framework').($merchant_name != '' ? '' : __(', no retailer information was supplied','framework')).'</span>'."<br />";
						}
									 
						update_post_meta($id,'image_meta',$product_image);
						
						// Categories
						$categories = explode(',',$product_category);
						$f = false;
						
						$a = wp_get_post_terms($id);
						
						foreach($categories as $category) {
							$category = trim($category);
							if(!($term = term_exists($category, 'product_category'))) {
								$term = wp_insert_term($category, 'product_category');
							}
							wp_set_post_terms($id, intval($term['term_id']), 'product_category',$f);
							$f = true;
						}
						
						// Brand
						$brand = trim($product_brand);
						if($brand == '') $brand = 'N-A';
						if(!($term = term_exists($brand, 'product_brand'))) {
							$term = wp_insert_term($brand, 'product_brand');
							// error_log('insert : >'.$brand.'< : '.$product_brand);
						}
						wp_set_post_terms($id, intval($term['term_id']), 'product_brand',true);
						if(!($term = term_exists($brand, 'product_bisbrand'))) {
							$term = wp_insert_term($brand, 'product_bisbrand');
						}
						wp_set_post_terms($id, intval($term['term_id']), 'product_bisbrand',true);
												
						$uids[] = $uid;
			    	}
			    	
			    	// Step 3 : create the merchant
			    	
			    	if($merchant_name == '') {
						//echo '<span style="color:red;">'. __('Line','framework') . ' ' . $row.': '.__('Retailer name required','framework').'</span>'."<br />";
						$row++;
						continue;
			    	}
			    	
			    	if(!is_numeric($merchant_price)) {
						echo '<span style="color:red;">'. __('Line','framework') . ' ' . $row.': '.__('Retail price must be a number','framework').' ('.$merchant_price.')</span>'."<br />";
						$row++;
						continue;
			    	}
			    	
			    	if($merchant_deeplink == '') {
						echo '<span style="color:red;">'. __('Line','framework') . ' ' . $row.': '.__('Retailer product link required','framework').'</span>'."<br />";
						$row++;
						continue;
			    	}
			    	
					$q = "
						INSERT INTO ".$wpdb->prefix.'pc_products_merchants'." SET
							name = '".$merchant_name."',
							slug = '".compare_slugify($merchant_name)."'
						ON DUPLICATE KEY UPDATE
							name = '".$merchant_name."',
							slug = '".compare_slugify($merchant_name)."'
						";
					$wpdb->query($q);
					
					$q = "
						INSERT INTO ".$wpdb->prefix.'pc_products'." SET
							id_product = '".$post_relation->id_product."',
							id_merchant = '".compare_slugify($merchant_name)."',
							feed_product_name = '".esc_sql($product_full_name)."',
							feed_product_desc = '".esc_sql($product_description)."',
							feed_product_image = '".esc_sql($product_image)."',
							price = '".esc_sql($merchant_price)."',
							deeplink = '".esc_sql($merchant_deeplink)."',
							shipping = '".esc_sql($merchant_shipping)."',
							voucher = '".esc_sql($merchant_voucher)."',
							last_update = '".time()."'
						ON DUPLICATE KEY UPDATE
							id_product = '".$post_relation->id_product."',
							id_merchant = '".compare_slugify($merchant_name)."',
							feed_product_name = '".esc_sql($product_full_name)."',
							feed_product_desc = '".esc_sql($product_description)."',
							feed_product_image = '".esc_sql($product_image)."',
							price = '".esc_sql($merchant_price)."',
							deeplink = '".esc_sql($merchant_deeplink)."',
							shipping = '".esc_sql($merchant_shipping)."',
							voucher = '".esc_sql($merchant_voucher)."',
							last_update = '".time()."'
						";
					$wpdb->query($q);
			    	 
			   		$row++;
					
			    }
			    fclose($handle);
			    
				// # patch wp_post_id = 0
				$q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE wp_post_id = '0'";
				$wpdb->query($q);
				
				// Merchant out of date
				//$q = "DELETE FROM ".$wpdb->prefix."pc_products WHERE updated = '0' AND id_merchant = '".$feed->merchant."'";
				//$wpdb->query($q);
				
				// Posts out of date 
				 $new_base_products_uids_joined = implode(',', $new_base_products_uids);
                                 
                                 if($new_base_products_uids_joined==""){ $sql_string_1 = "";$sql_string_2 = "";} else {  $sql_string_1 = "AND product_ean NOT IN ($new_base_products_uids_joined)"; $sql_string_2 = "product_ean NOT IN ($new_base_products_uids_joined) AND ";}
				 
                                 
                                 $q = "DELETE a,b,c FROM ".$wpdb->prefix."posts a LEFT JOIN ".$wpdb->prefix."term_relationships b ON (a.ID = b.object_id)
				 LEFT JOIN ".$wpdb->prefix."postmeta c ON (a.ID = c.post_id) WHERE a.post_type = 'product' AND a.ID IN (SELECT wp_post_id FROM ".$wpdb->prefix."pc_products_relationships WHERE id_product NOT IN (SELECT id_product FROM ".$wpdb->prefix."pc_products GROUP BY id_product) $sql_string_1 )";
				 $wpdb->query($q);
				
				// Product relationships out of date
				 $q = "DELETE FROM ".$wpdb->prefix."pc_products_relationships WHERE $sql_string_2  id_product NOT IN (SELECT id_product FROM ".$wpdb->prefix."pc_products GROUP BY id_product)";
                                 $wpdb->query($q);
				
				// Update terms count
				$q = "UPDATE ".$wpdb->prefix."term_taxonomy SET count = (SELECT count(*) FROM ".$wpdb->prefix."term_relationships WHERE ".$wpdb->prefix."term_relationships.term_taxonomy_id = ".$wpdb->prefix."term_taxonomy.term_taxonomy_id)";
				$wpdb->query($q);
				
				unlink($filePath);
				
				if(get_option('spc_last_import') == NULL) {
			    	add_option('spc_last_import',time());
			    } else {
			    	update_option('spc_last_import',time());
			    }
			    flush_rewrite_rules();
			    echo "<p>".__('Import finished','framework')."</p>";
			}
		} else {
			echo "<p>".__('File not found')."</p>";
		}
		?>
		
		</textarea>
		
		<br />
		<a href="?page=products_feed" class="button-secondary"><?php _e('Feed Management','framework'); ?></a>
	</div>

<?php elseif(isset($_GET['action']) && $_GET['action'] == 'export'): // Export case ?>

	<?php

	$q = "SELECT * FROM ".$wpdb->prefix."pc_products_relationships pr 
	LEFT JOIN ".$wpdb->prefix."pc_products_custom pc ON (pr.wp_post_id = pc.product_id)  
	ORDER BY pr.id_product ASC";
	$prs = $wpdb->get_results($q);
	$products = array();
	foreach($prs as $pr) {
		$data = array();
		$data['UID'] = str_replace(';',',',$pr->id_product);
		// Fetch the post
		$post = get_post($pr->wp_post_id);
		// Categories
		$categories = array();
		$term_categories = wp_get_post_terms($pr->wp_post_id,'product_category');
		foreach($term_categories as $term) {
			$categories[] = str_replace(';',',',$term->name);
		}
		$data['CATEGORIES'] = implode(', ',$categories);
		// Brand
		$brands = array();
		$term_brands = wp_get_post_terms($pr->wp_post_id,'product_brand');
		foreach($term_brands as $term) {
			$brands[] = $term->name;
		}
		$data['BRAND'] = (count($brands) != 0) ? $brands[0] : null;
		// Other fields
		$data['FULLNAME'] = str_replace(';',',',$post->post_title);
				
		$q = "SELECT m.name, p.price, p.shipping, p.deeplink, p.voucher, p.feed_product_image FROM ".$wpdb->prefix.'pc_products'." p,".$wpdb->prefix.'pc_products_merchants'." m WHERE p.id_merchant = m.slug AND p.id_product = '".$pr->id_product."'";
		$merchants = $wpdb->get_results($q);
		foreach($merchants as $merchant) {
			$d = $data;
			$d['PRICE'] = str_replace(';',',',$merchant->price);
			$d['SHIPPING'] = str_replace(';',',',$merchant->shipping); 
			$d['IMAGE'] = str_replace(';',',',$merchant->feed_product_image);
			$d['MERCHANT'] = str_replace(';',',',$merchant->name);
			$d['DEEPLINK'] = str_replace(';',',',$merchant->deeplink);
			$d['DESCRIPTION'] = str_replace(';',',',str_replace(array("\r\n", "\n", "\r"),' ',strip_tags(stripslashes($post->post_content))));
			
			$d['GLOBAL_DESCRIPTION'] = str_replace(';',',',str_replace(array("\r\n", "\n", "\r"),' ',strip_tags(stripslashes($pr->product_description))));
			$d['VOUCHER'] = str_replace(';',',',$merchant->voucher);
			$products[] = $d;
		}
	}
	?>
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Export Feed','framework'); ?></h2>
	<p><?php _e('Copy the content from the textpane below into your text editor and save as a .csv file','framework'); ?></p>
	<?php
	echo '<textarea rows="20" style="width:98%;">';
	echo 'UID;CATEGORIES;BRAND;FULL NAME;PRICE;SHIPPING;IMAGE;RETAILER;DEEPLINK;DESCRIPTION;GLOBAL_DESCRIPTION;VOUCHER'."\n";
	for($i=0;$i < count($products); $i++) {
		echo implode(';',$products[$i]);
		if($i != count($products) -1) echo "\n";
	}
	echo '</textarea>';
	?>

<?php else: // Other cases ?>

	<?php
	// FEED UPLOAD
	if(isset($_POST['submit'])) {
            // Upload directory & file
            $uploadDir = ABSPATH.'wp-content/uploads/compare/';
            if(!is_dir($uploadDir)) {
                    wp_mkdir_p($uploadDir);
            }
            // Upload	
            $uploadFile = $uploadDir.'data.csv';
            if (move_uploaded_file($_FILES['feed']['tmp_name'], $uploadFile)) {
                    $success = __("File successfully uploaded, you can now import products into WordPress",'framework');
                    if(get_option('spc_last_upload') == NULL) {
                            add_option('spc_last_upload',time());
                    } else {
                            update_option('spc_last_upload',time());
                    }
            } else {
                    $error = __("Upload failed",'framework');
            }		
	}
	?>
	<div class="wrap">
	
		<div id="icon-link-manager" class="icon32"></div>
		<h2><?php _e('Products Feed','framework'); ?></h2>
		
		<?php if(isset($error)): ?>
		<div class="error">
			<?php echo $error ?>
		</div>
		<?php elseif(isset($success)): ?>
		
		<div class="success" style="margin-top:10px;">
			<?php echo $success ?>
		</div>
		<?php endif; ?>
		
		<style type="text/css">
			fieldset {
				margin-top:20px;
				border:1px solid #ccc;
				background-color:#f6f6f6;
				margin-botton:15px;
				padding:10px;
				border-radius:5px;
				-moz-border-radius:5px;
				-webkit-border-radius:5px;
			}
			fieldset legend { font-size:20px; font-weight:bold; }
			fieldset h3 { margin-top:5px; }
		</style>
		
		<fieldset>
			<legend><?php _e('Import','framework'); ?></legend>
		
			<!-- UPLOAD FEED -->
			<h3><?php _e('Step 1: Upload feed','framework'); ?></h3>
			<p><?php _e('Upload your manual CSV feed','framework'); ?> (<a href="<?php echo get_template_directory_uri() ?>/csv/sample.csv" target="_blank"><?php _e('sample file','framework'); ?></a>)</p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" enctype="multipart/form-data">
				<input type="file" name="feed" id="file" value="" accept="text/csv" />
				<input type="submit" name="submit" id="submit" class="button-secondary" value="Upload" />
			</form>
			<p><strong><?php _e('Last upload:','framework'); ?></strong> <?php echo ((get_option('spc_last_upload') == NULL) ? __('Never','framework') : date((get_option('tz_date_format') != '' ? get_option('tz_date_format') : 'd-m-Y H:i'),get_option('spc_last_upload'))) ?></p>
			
			<!-- IMPORT PRODUCTS -->
			<h3><?php _e('Step 2: Import products','framework'); ?></h3>
			<p><?php _e('Import products from the feed into WordPress','framework'); ?></p>
			
			<p><select id="compare_theme_feed_seperator">
				<option value="semicolon" selected><?php _e('Semicolon','framework'); ?></option>
				<option value="comma"><?php _e('Comma','framework'); ?></option>
				<option value="pipe"><?php _e('Pipe','framework'); ?></option>
				<option value="tab"><?php _e('Tab','framework'); ?></option>
			</select> <span><?php _e('Choose delimiter','framework'); ?></span>
			</p>
			<p><a href="?page=products_feed&action=import&delimiter=semicolon" id="import-button" class="button-secondary"><?php _e('Import','framework'); ?></a></p>
			<p><strong><?php _e('Last import :','framework'); ?></strong> <?php echo ((get_option('spc_last_import') == NULL) ? __('Never','framework') : date((get_option('tz_date_format') != '' ? get_option('tz_date_format') : 'd-m-Y H:i'),get_option('spc_last_import'))) ?></p>
		</fieldset>
	
		<fieldset>
			<legend><?php _e('Export','framework'); ?></legend>
			
			<!-- DOWNLOAD FEED -->
			<h3><?php _e('Download feed','framework'); ?></h3>
			<p><?php _e('Download feed to your computer in order to edit it manually','framework'); ?></p>
			<p><a href="?page=products_feed&amp;action=export" class="button-secondary"><?php _e('Download','framework'); ?></a></p>
		</fieldset>
		
	</div>
<script type="text/javascript">


jQuery('#compare_theme_feed_seperator').change(function() {
	
	current_href = "?page=products_feed&action=import&delimiter="+jQuery(this).val();
	
	jQuery('#import-button').attr('href', current_href);
});
</script>
<?php endif; ?>