<?php

	
	define('WP_USE_THEMES', false);

	//define( 'SHORTINIT', true );
	
	$url = $_SERVER['REQUEST_URI'];
	$my_url = explode('wp-content' , $url); 
	$path = $_SERVER['DOCUMENT_ROOT']."/".$my_url[0];

	//require_once $path . '/wp-blog-header.php';
	require_once $path . '/wp-load.php';
	
 
		 
	  header('Content-Type: application/vnd.ms-excel');
	 header('Content-Disposition: attachment;filename="export.xlsx"');
	 header('Cache-Control: max-age=0'); 
	 
	require_once('../../classes/PHPExcel.php');
	
	$PHPExcel = new PHPExcel();
		
	
	$outputFileType = 'Excel2007';	
	$writer = PHPExcel_IOFactory::createWriter($PHPExcel, $outputFileType);
 
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		global $wpdb;
		
		 $main_categories = $wpdb->get_results( 
			"
				SELECT t.term_id, t.name
				FROM $wpdb->terms t
				LEFT JOIN $wpdb->term_taxonomy tt
				ON t.term_id = tt.term_taxonomy_id				
				WHERE tt.taxonomy = 'product_category'
					AND tt.parent = 0
			"
		);	
		$count_main_categories = $wpdb->num_rows;
		
		$products = $wpdb->get_results(
			"
				SELECT pr.id_product, pr.wp_post_id, pr.product_ean, pc.product_description, pr.product_name
				FROM ".$wpdb->prefix."pc_products_relationships pr 
				LEFT JOIN ".$wpdb->prefix."pc_products_custom pc
				ON (pr.wp_post_id = pc.product_id)  
				ORDER BY pr.id_product ASC
			"
		);
		
		$products_description_by_categories = array();
		
		$i = 0;
		$unique_parameter_values = array();
		$param_names = array();
		foreach ($products as $product) {
			
			$post = get_post($product->wp_post_id);
			$categories_ids = wp_get_post_terms($product->wp_post_id,'product_category');
			
			$product->fullname = $post->post_title;
			$product->description = str_replace(array("\r\n", "\n", "\r"),' ',strip_tags(stripslashes($post->post_content)));
			
			$parameters = $wpdb->get_results(
				"
					SELECT param_name, param_value
					FROM ".$wpdb->prefix."pc_products_params
					WHERE wp_post_id = '".$product->wp_post_id."'
				"
			);
			$param_values = array();
			foreach ($parameters as $parameter) {
				$param_values[] = $parameter->param_value;
			}
			$product->parameters = $param_values;
			
			$sorted_categories_ids = array();
			
			foreach($categories_ids as $category_id) {
				if ($category_id->parent == 0 ) {
					$products_description_by_categories[$category_id->term_id][] = $product;
					if (!isset($unique_parameter_values[$category_id->term_id])) {
						foreach ($parameters as $parameter) {
							$param_names[$category_id->term_id][] = $parameter->param_name;
						}
						$unique_parameter_values[$category_id->term_id] = 1;
					}
				}  else {
					$sorted_categories_ids[] = $category_id->name;
				} 
			}
			$product->categories = implode(",",$sorted_categories_ids);
			
		}
		 
		$i=0;
		while ($i < $count_main_categories) {
			
			$main_category_id = $main_categories[$i]->term_id;
			$main_category_name = htmlspecialchars_decode($main_categories[$i]->name);
			
			if ($i == 0) {			
				$PHPExcel->setActiveSheetIndex(0);
				$objWorkSheet = $PHPExcel->getSheet(0);
			} else {
				// Add new sheet
				$objWorkSheet = $PHPExcel->createSheet($i); //Setting index when creating
			}			
			
			$objWorkSheet	->setCellValueByColumnAndRow(0, 1,'UID')
							->setCellValueByColumnAndRow(1, 1,'CATEGORY')
							->setCellValueByColumnAndRow(2, 1,'BRAND')
							->setCellValueByColumnAndRow(3, 1,'FULLNAME')
							->setCellValueByColumnAndRow(4, 1,'PRICE')
							->setCellValueByColumnAndRow(5, 1,'SHIPPING')
							->setCellValueByColumnAndRow(6, 1,'IMAGE')
							->setCellValueByColumnAndRow(7, 1,'RETAILER')
							->setCellValueByColumnAndRow(8, 1,'DEEPLINK')
							->setCellValueByColumnAndRow(9, 1,'DESCRIPTION')
							->setCellValueByColumnAndRow(10, 1,'GLOBAL DESCRIPTION')
							->setCellValueByColumnAndRow(11, 1,'VOUCHER');
			$col = 12;
			foreach ($param_names[$main_category_id] as $parameter_name) {
				$objWorkSheet->setCellValueByColumnAndRow($col, 1, $parameter_name);
				$col++;
			}
				
			$j = 2;				
			foreach ($products_description_by_categories[$main_category_id] as $product) {
				
				$product_ean = $product->product_ean;
				$product_categories = $product->categories;
				$product_fullname = $product->product_name;
				$product_description = $product->description;
				$product_parameters = $product->parameters;
				
				 $merchants = $wpdb->get_results(
					"
						SELECT m.name, p.price, p.shipping, p.deeplink, p.voucher, p.feed_product_image
						FROM ".$wpdb->prefix.'pc_products'." p
						LEFT JOIN ".$wpdb->prefix.'pc_products_merchants'." m
						ON p.id_merchant = m.slug
						WHERE p.id_product = '".$product->id_product."'
					"
				);
				foreach($merchants as $merchant) {
					
					$product_price = $merchant->price;
					$product_shipping = $merchant->shipping; 
					$product_image = $merchant->feed_product_image;
					$product_retailer = $merchant->name;
					$product_deeplink = $merchant->deeplink;
					$product_global_description = str_replace(array("\r\n", "\n", "\r"),' ',strip_tags(stripslashes($pr->product_description)));
					$product_voucher = $merchant->voucher;
					
					//Write cells
					$objWorkSheet->setCellValueByColumnAndRow(0, $j, $product_ean)
								->setCellValueByColumnAndRow(1, $j, $product_categories)
								->setCellValueByColumnAndRow(2, $j, 'brand')
								->setCellValueByColumnAndRow(3, $j, $product_fullname)
								->setCellValueByColumnAndRow(4, $j, $product_price)
								->setCellValueByColumnAndRow(5,  $j, $product_shipping)
								->setCellValueByColumnAndRow(6, $j, $product_image)
								->setCellValueByColumnAndRow(7, $j, $product_retailer)
								->setCellValueByColumnAndRow(8, $j, $product_deeplink)
								->setCellValueByColumnAndRow(9, $j, $product_description)
								->setCellValueByColumnAndRow(10, $j, $product_global_description)
								->setCellValueByColumnAndRow(11, $j, $product_voucher);
					$col = 12;
					foreach ($product_parameters as $parameter_value) {
						$objWorkSheet->setCellValueByColumnAndRow($col, $j, $parameter_value);
						$col++;
					}
					$j++;
				}
			} 
			
			// Rename sheet
			$objWorkSheet->setTitle($main_category_name);
			
			$i++;
		}
		
		// This line will force the file to download
		 $writer->save('php://output');
	} else {
		die('Error');
	}