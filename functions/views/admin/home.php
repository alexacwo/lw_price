<?php

// Catching direct file access
if('wp-product-admin.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('<h2>Direct File Access Prohibited</h2>');
// Wordpress access to the database
global $wpdb;

$uploadDir = ABSPATH.'wp-content/uploads/compare/';
if(!is_dir($uploadDir)) {
	wp_mkdir_p($uploadDir);
}
?>

<style type="text/css">

h3 {
	margin-top:0;
}

.base-desc {
	font-size:14px;
	font-weight:bold;
	font-style:italic;
	text-align:center;
	margin:0;
}

#blocks {
	width:650px;
	margin-top:25px;
}

.block {
	width:168px;
	float:left;
	border:1px solid #ccc;
	background-color:#f6f6f6;
	text-align:center;
	padding:5px 15px 15px;
	margin-bottom:25px;
	border-radius:5px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
}

.block.featured {
	height:350px;
}

.block .desc, .big-block .desc {
	text-align:left;
	margin:0;
}

.big-block {
	width:618px;
	border:1px solid #ccc;
	background-color:#f6f6f6;
	padding:15px;
	margin-bottom:25px;
	border-radius:5px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
}

.block.middle {
	margin:0 25px;
}

.clear {
	clear:both;
}


.config-success { 
	color:green; 
}

.config-error { 

	color:red; 
}

.config-error span{
color:#000;
float: right;
}

</style>

<div class="wrap">
	
	<div id="icon-link-manager" class="icon32"></div>
	<h2><?php _e('Compare - Premium Price Comparison Theme','framework'); ?></h2>
	<div id="blocks">
		
		<?php 
		// if force create tables
		
		if(isset($_GET['force']) && $_GET['force'] == 'tables') { ?>
		<div class="big-block">
			<ul>
			<?php 
			// check if table exists
			$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_relationships';";
			$pc_products_relationships = $wpdb->get_results($q);
			$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_merchants';";
			$pc_products_merchants = $wpdb->get_results($q);				
			$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products';";
			$pc_products = $wpdb->get_results($q);
			$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_options';";
			$pc_options= $wpdb->get_results($q);
			$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_custom';";
			$pc_products_custom= $wpdb->get_results($q);
			
			
			// create table pc_products_relationships
	/*		if ($pc_products_relationships[0]->table_exist != 1){
				
				$sql = create_mysql_query_string("pc_products_relationships", $wpdb->prefix);
				$result = $wpdb->get_results($sql);
			
				//check again if table exists
				$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_relationships';";
				$pc_products_relationships = $wpdb->get_results($q); ?>
		
				<li class="<?php if ($pc_products_relationships[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
				 <?php if ($pc_products_relationships[0]->table_exist == 1){ echo "Table 'pc_products_relationships' is successfully created."; }else{ echo "There was a problem while creating 'pc_products_relationships' table."; } ?>
				</li>
			<?php }	*/
				
			// create table pc_products_merchants
			if ($pc_products_merchants[0]->table_exist != 1){
				
				$sql = create_mysql_query_string("pc_products_merchants", $wpdb->prefix);
				$result = $wpdb->get_results($sql);
			
				//check again if table exist
				$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_merchants';";
				$pc_products_merchants = $wpdb->get_results($q); ?>
		
				<li class="<?php if ($pc_products_merchants[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
				 <?php if ($pc_products_merchants[0]->table_exist == 1){ echo "Table 'pc_products_merchants' is successfully created."; }else{ echo "There was a problem while creating 'pc_products_merchants' table."; } ?>
				</li>
			<?php }	
			
			// create table pc_products
			if ($pc_products[0]->table_exist != 1){
				
				$sql = create_mysql_query_string("pc_products", $wpdb->prefix);
				$result = $wpdb->get_results($sql);
			
				//check again if table exist
				$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products';";
				$pc_products = $wpdb->get_results($q); ?>
		
				<li class="<?php if ($pc_products[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
				 <?php if ($pc_products[0]->table_exist == 1){ echo "Table 'pc_products' is successfully created."; }else{ echo "There was a problem while creating 'pc_products' table."; } ?>
				</li>
			<?php }	
			
			// create table pc_options
			if ($pc_options[0]->table_exist != 1){
				
				$sql = create_mysql_query_string("pc_options", $wpdb->prefix);
				$result = $wpdb->get_results($sql);
			
				//check again if table exist
				$q = "SELECT COUNT(*) AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_options';";
				$pc_options = $wpdb->get_results($q); ?>
		
				<li class="<?php if ($pc_options[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
				 <?php if ($pc_options[0]->table_exist == 1){ echo "Table 'pc_options' is successfully created."; }else{ echo "There was a problem while creating 'pc_options' table."; } ?>
				</li>
			<?php }	
			
			// create pc_products_custom
			if ($pc_products_custom[0]->table_exist != 1){
				
				$sql = create_mysql_query_string("pc_products_custom", $wpdb->prefix);
				$result = $wpdb->get_results($sql);
			
				//check again if table exist
				$q = "SELECT COUNT(*) AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_custom';";
				$pc_products_custom = $wpdb->get_results($q); ?>
		
				<li class="<?php if ($pc_products_custom[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
				 <?php if ($pc_products_custom[0]->table_exist == 1){ echo "Table 'pc_products_custom' is successfully created."; }else{ echo "There was a problem while creating 'pc_products_custom' table."; } ?>
				</li>
			<?php }	?>
			
			
			</ul>
		</div>
		<?php 
		}
		?>
		
		
		<div class="big-block">
			<h3 class="base-desc"><?php _e('If you have any questions or problems, please read the documentation or contact us.','framework'); ?></h3>
		</div>
	
		<div class="block featured">
			<img src="<?php echo get_template_directory_uri();; ?>/img/admin/icon-gear.png" alt="" />
			<br />
			<h3><?php _e('Theme Options','framework'); ?></h3>
			<p class="desc"><?php _e('Compare has extensive theme options to change the logo, header banner, tracking code, currency, colours and background pattern.','framework'); ?><br /><br /><a href="themes.php?page=theme-options.php"><?php _e('Customise your theme','framework'); ?> &raquo;</a></p>
		</div>
		
		<div class="block middle featured">
			<img src="<?php echo get_template_directory_uri(); ?>/img/admin/icon-email.png" alt="" />
			<br />
			<h3><?php _e('Support &amp; Contact','framework'); ?></h3>
			<p class="desc"><?php _e('We included detailed theme documentation and in case you still encounter problems please contact us using our Helpdesk.','framework'); ?><br /><br /><a href="http://www.wppricecomparison.com/support/"><?php _e('Visit our support area','framework'); ?> &raquo;</a></p>
		</div>
		
		<div class="block featured">
			<img src="<?php echo get_template_directory_uri();; ?>/img/admin/icon-power.png" alt="" />
			<br />
			<h3>Compare +</h3>
			<p class="desc"><?php _e('Give your price comparison website another dimension and add XML/CSV product feed capabilities and utilise another set of additional features.','framework'); ?><br /><br /><a href="http://www.wppricecomparison.com/#plugin-features"><?php _e('More about Compare +','framework'); ?> &raquo;</a></p>
			
		</div>
		
		<div class="clear"></div>
		
		<div class="big-block">
			<h3><?php _e('What to do next ?','framework'); ?></h3>
			<ol>
				<li><?php printf(__('%1$sCreate your homepage%2$s and %3$sset it up as your front page%4$s','framework'), '<a href="post-new.php?post_type=page">','</a>','<a href="options-reading.php">','</a>'); ?></li>
				
				<li><?php printf(__('%1$sCreate your blog page%2$s and %3$sset it up as your posts page%4$s','framework'), '<a href="post-new.php?post_type=page">','</a>','<a href="options-reading.php">','</a>'); ?></li>
				
				<li><?php printf(__('%1$sSetup your permalink structure%2$s as explained in the documentation','framework'), '<a href="options-permalink.php">','</a>'); ?></li>
				
				<li><?php printf(__('%1$sAdd some widgets%2$s to your homepage, your sidebar and your footer','framework'), '<a href="widgets.php">','</a>'); ?></li>
				
				<li><?php printf(__('%1$sImport products%2$s or manage them from the %3$sproduct management%4$s panel','framework'), '<a href="admin.php?page=products_feed">','</a>', '<a href="admin.php?page=products_management">','</a>'); ?></li>

				<li><?php printf(__('Customise %1$sRetailers%2$s, %3$sBrands%4$s or %5$sProduct Categories%6$s','framework'), '<a href="admin.php?page=merchants_management">','</a>', '<a href="edit-tags.php?taxonomy=product_brand&post_type=product">','</a>', '<a href="edit-tags.php?taxonomy=product_category&post_type=product">','</a>'); ?></li>

			</ol>
		</div>
		
		<div class="big-block">
		<h3><?php _e('Check database tables existence','framework'); ?></h3>
		<ul>
		<?php
		//echo DB_NAME;
		
		$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_relationships';";
		$pc_products_relationships = $wpdb->get_results($q);
		?> 
			<li class="<?php if ($pc_products_relationships[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
			pc_products_relationships <?php if ($pc_products_relationships[0]->table_exist == '0'){ echo "<span>Force create table? <a href='./admin.php?page=compare&force=tables'>YES</a></span>"; } ?>
			</li>
		<?php 
		
		$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_merchants';";
		$pc_products_merchants = $wpdb->get_results($q);
		?> 
			<li class="<?php if ($pc_products_merchants[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
			pc_products_merchants <?php if ($pc_products_merchants[0]->table_exist == '0'){ echo "<span>Force create table? <a href='./admin.php?page=compare&force=tables'>YES</a></span>"; } ?>
			</li>
		<?php 
		
		$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products';";
		$pc_products = $wpdb->get_results($q);
		?> 
			<li class="<?php if ($pc_products[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
			pc_products <?php if ($pc_products[0]->table_exist == '0'){ echo "<span>Force create table? <a href='./admin.php?page=compare&force=tables'>YES</a></span>"; } ?>
			</li>
		<?php 
		
		$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_options';";
		$pc_options= $wpdb->get_results($q);
		?> 
			<li class="<?php if ($pc_options[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
			pc_options <?php if ($pc_options[0]->table_exist == '0'){ echo "<span>Force create table? <a href='./admin.php?page=compare&force=tables'>YES</a></span>"; } ?>
			</li>
		<?php
		$q = "SELECT COUNT(*)AS table_exist FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = '".$wpdb->prefix."pc_products_custom';";
		$pc_products_custom= $wpdb->get_results($q);
		?> 
			<li class="<?php if ($pc_products_custom[0]->table_exist == 1){echo "config-success"; } else { echo "config-error"; } ?>">
			pc_products_custom <?php if ($pc_products_custom[0]->table_exist == '0'){ echo "<span>Force create table? <a href='./admin.php?page=compare&force=tables'>YES</a></span>"; } ?>
			</li>
			
		</ul>
		<br />
		<h3><?php _e('Check folder permissions','framework'); ?></h3>
		<ul>
		<?php
		 
		 $folder_compare = $uploadDir;
		 $folder_brands = $uploadDir."brands/";
		 $folder_categories = $uploadDir."categories/";
		 $folder_merchants = $uploadDir."merchants/";
		 
		?> 
			<li class="<?php if (is_writable($folder_compare)){echo "config-success"; } else { echo "config-error"; } ?>">
			<?php echo $folder_compare; ?>
			</li>

			<li class="<?php if (is_writable($folder_brands)){echo "config-success"; } else { echo "config-error"; } ?>">
			<?php echo $folder_brands; ?>
			</li>
			
			<li class="<?php if (is_writable($folder_categories)){echo "config-success"; } else { echo "config-error"; } ?>">
			<?php echo $folder_categories; ?>
			</li>
			
			<li class="<?php if (is_writable($folder_merchants)){echo "config-success"; } else { echo "config-error"; } ?>">
			<?php echo  $folder_merchants; ?>
			</li>
		</ul>
		</div>
		
		<div class="clear"></div>
		
		<div class="big-block">
			
			<a href="http://www.wppricecomparison.com/"><img style="float:left;" src="<?php echo get_template_directory_uri();; ?>/img/admin/wppc-logo5.png" alt="AWESEM Limited" /></a>
			
			<div style="float:left; margin:0 25px; width:200px;">
				
			</div>
			
			<div style="float:right;width:175px;">
				<a href="http://www.wppricecomparison.com/">WP Price Comparison</a><br />
				<a href="http://themeforest.net/user/awesem?ref=awesem"><?php _e('Visit us on ThemeForest','framework'); ?></a><br />
				<a href="https://twitter.com/wppricecompare"><?php _e('Follow us on Twitter','framework'); ?></a>
			</div>
			
			
			<div class="clear"></div>
			
		</div>
	
		<div class="clear"></div>
		
	</div>
	
</div>