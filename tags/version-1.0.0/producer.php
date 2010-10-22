<?php

################################################################################
#                                                                              #
#		Filename:		producer.php				       #
#		Author:		Martin Settle				       #
#             Created:		2008.03.13				       #
#		Description:	producer control panel			       #
#		Calls:		config.php				       #
#		Called by:		login.php (if member is producer)			       #
#									       #
#   Copyright 2010 Trellis Ltd
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
################################################################################

/*
MODIFICATION HISTORY
	- 2008.03.13 file created
	-

*/
//error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(E_ALL | E_STRICT);

if(empty($CoopName))
{	
	session_start();
	
	if(empty($_SESSION['member_id']))
	{
		header ("Location:index.php");
		exit();
		}
	
	header ("Cache-control: private");

	$Secure = 1;
	include 'config.php';
	}
	
include 'producer_functions.php';
include_once 'product_pic.php';

$smarty->assign('pagetitle',"{$CoopName} Producer Control Panel");

$farm_gate = get_config_value('use_farm_gate_pricing');

if(!empty($_GET['order_date']))
{
	$order_date = quote_smart($_REQUEST['order_date']);
	switch($_REQUEST['function'])
	{
		case 'register_date':
			mysql_query("INSERT INTO product_calendar 
							(SELECT FROM_UNIXTIME({$order_date}) AS order_date, 
								product_id, 
								product_default_quantity_available AS quantity_available,
								0 AS quantity_ordered,
								NULL AS purchase_quantity, 
								product_cost AS current_price, 
								NULL AS delivered_quantity, 
								NULL as transaction_id 
									FROM product 
									WHERE product_supplier_id = {$_SESSION['producer']} 
									AND product_available = 1 AND product_archived = 0)");
			print mysql_error();
		case 'configure_calendar':
			if(empty($_REQUEST['manual_config']))
			{
				if(!check_date($order_date))
				{
					$smarty->assign('order_date', $order_date);
					$smarty->display('register_date_form.tpl');
					exit();
					}
				}
			$smarty->assign('order_date',$order_date);
			$smarty->assign('product_list',get_all_products($order_date));
			include 'header.php';
			include 'footer.php';
			$smarty->display('prod_date_control.tpl');
			exit();
		case 'update_products':
			foreach($_POST['product_id'] AS $p_id)
			{
				if($_POST['quantity_available'][$p_id] == "" || $_POST['quantity_available'][$p_id] == 'no limit')
				{
					$quantity = 'NULL';
					}
				else
				{
					$quantity = $_POST['quantity_available'][$p_id]/1;  # just to ensure it's an integer
					}
				// check for product_VAT_rate
				$VAT_rate_lookup = mysql_query("SELECT product_VAT_rate FROM product WHERE product_id = {$p_id}");
				$VAT_rate = mysql_result($VAT_rate_lookup,0,'product_VAT_rate');
				if (isset($_POST['current_price'][$p_id])) {
					$current_price = $_POST['current_price'][$p_id];
				} else { // field was disabled
					$current_price = $_POST['hidden_price'][$p_id];
				}
				if ($farm_gate) $price = $current_price / (1 + $_SESSION['markup']) / (1 + $VAT_rate);
				else $price = $current_price;
				// this means that there is a quantity and there should be a listing
				//check if the listing already exists
				mysql_query("INSERT INTO product_calendar
								SET product_id = {$p_id},
								order_date = FROM_UNIXTIME({$order_date}),
								current_price = {$price},
								quantity_available = {$quantity}
								ON DUPLICATE KEY UPDATE current_price = {$price}, quantity_available = {$quantity}");
				
				
				}
			header("location: producer.php?function=configure_calendar&order_date={$order_date}");
			exit();
					
				
			
		}
	}
	
elseif(!empty($_REQUEST['function']))
{
	switch($_REQUEST['function'])
	{
		case 'editinfo':
			// if there has been no submit button pressed, just display the form
			if(empty($_POST['submit']))
			{ 
				$smarty->assign('supplier',get_supplier_details());
				$smarty->display('prod_supplier_form.tpl');
				exit();
				}
			// if the producer has changed active status, change future listings
			$activelookup = mysql_query("SELECT supplier_active, supplier_recurring FROM supplier
								WHERE supplier_id = {$_SESSION['producer']}");
			$active = mysql_result($activelookup,0,'supplier_active');
			$recurring = mysql_result($activelookup,0,'supplier_recurring');
			if ($_POST['supplier_active'] != $active)
			{
				//get product list
				$allproducts = '';
				$productlist_lookup = mysql_query("SELECT product_id, product_available
											FROM product WHERE product_supplier_id = {$_SESSION['producer']}");
				while($p = mysql_fetch_array($productlist_lookup))
				{
					$allproducts .= $p['product_id'] . ',';
					if ($p['product_available'] == 1) $activeproducts[] = $p['product_id'];
					}
				
				$allproducts = trim($allproducts,',');
				// update records
				switch($_POST['supplier_active'])
				{
					case 1:
						if($_POST['supplier_recurring'] == 0) break;
						foreach($activeproducts AS $product_id)
						{
							mysql_query("INSERT INTO product_calendar
								SELECT  order_date,
								product.product_id,
								product_default_quantity_available AS quantity_available,
								0 AS quantity_ordered,
								NULL AS purchase_quantity,
								product_cost AS current_price,
								NULL AS delivered_quantity,
								NULL AS transaction_id
								FROM product, calendar
								WHERE product_id = {$product_id}
								AND order_date >= '{$_SESSION['order_date']}'
								ON DUPLICATE KEY UPDATE quantity_available = product_default_quantity_available");
							}
						break;
					case 0:
						// delete future listings
						mysql_query("UPDATE product_calendar
							SET quantity_available = 0
							WHERE product_id IN ($allproducts)
							AND order_date >= '{$_SESSION['order_date']}'");
						print mysql_error();
					
					}
				}
			extract(quote_smart($_POST));
			mysql_query("UPDATE supplier
						SET supplier_name = {$supplier_name},
						supplier_account = {$supplier_account},
						supplier_contact_name = {$supplier_contact_name},
						supplier_phone = {$supplier_phone},
						supplier_fax = {$supplier_fax},
						supplier_email = {$supplier_email},
						supplier_address1 = {$supplier_address1},
						supplier_address2 = {$supplier_address2},
						supplier_address3 = {$supplier_address3},
						supplier_town = {$supplier_town},
						supplier_county = {$supplier_county},
						supplier_postcode = {$supplier_postcode},
						supplier_active = {$supplier_active},
						supplier_recurring = {$supplier_recurring},
						supplier_info = {$supplier_info}
						WHERE supplier_id = {$_SESSION['producer']}");
			break;
		case 'productupdate':
			$_POST = quote_smart($_POST);
			$old_lookup = mysql_query("SELECT product_markup, product_available, product_VAT_rate, product_cost FROM product WHERE product_id = {$_POST['product_id']}");
			$oldproduct['product_markup'] = mysql_result($old_lookup,0,'product_markup');
			$oldproduct['product_available'] = mysql_result($old_lookup,0,'product_available');
			$oldproduct['product_VAT_rate'] = mysql_result($old_lookup,0,'product_VAT_rate');
			$oldproduct['product_cost'] = mysql_result($old_lookup,0,'product_cost');
			if(empty($oldproduct['product_markup']))
			{
				$oldproduct['product_markup'] = $_SESSION['markup'];
				}
			extract($_POST);
			if(empty($product_fairtrade)) $product_fairtrade = 0;
			if(empty($product_local)) $product_local = 0;
			if(empty($product_organic)) $product_organic = 0;
			if(empty($product_available)) $product_available = 0;
			$emptycheck = trim($product_default_quantity_available,"'");
			if(empty($emptycheck)) $product_default_quantity_available = 'NULL';
			$product_cost = trim($product_cost,"'");
			$product_cost = floatval($product_cost);
			if($farm_gate) 	$product_cost = $product_cost / (1 + $oldproduct['product_markup'])/(1 + $oldproduct['product_VAT_rate']);
			$upd_pic = "";
			if ($_FILES['product_pic']['name'] <> "") {
				 if (save_product_pic($product_id, $_FILES['product_pic']['name'], $_FILES['product_pic']['tmp_name'], $_FILES['product_pic']['type'])) {
				 	$upd_pic = "product_pic = 1,";
				 }
			}
			
			if(empty($product_pic_delete)) $product_pic_delete = 0;
			if ($product_pic_delete == 1) {
				$upd_pic = "product_pic = 0,";
			}
			
			$updq = "UPDATE product
						SET product_name = {$product_name},
						product_description = {$product_description},
						product_category_id = {$product_category_id},
						product_code = {$product_code},
						product_cost = {$product_cost},
						product_VAT_rate = {$product_VAT_rate},
						product_units = {$product_units},
						product_pkg_count = {$product_pkg_count},
						product_case_size = {$product_case_size},
						product_local = {$product_local},
						product_organic = {$product_organic},
						product_fairtrade = {$product_fairtrade},
						product_available = {$product_available},
						product_default_quantity_available = {$product_default_quantity_available},
						$upd_pic
						product_more_info = {$product_more_info}
						WHERE product_id = {$product_id}";
			//echo $updq . "<br/>";
			mysql_query($updq);
			//echo "Update DONE" . "<br/>";
			
			if(trim("'",$_POST['product_available']) != $oldproduct['product_available'] ||
				$product_cost != $oldproduct['product_cost'])
			{
				switch($_POST['product_available'])
				{
					case 1:
						// update or add future listings
						mysql_query("INSERT INTO product_calendar
                                	        	SELECT  order_date,
                                                	product.product_id,
                                                	product_default_quantity_available AS quantity_available,
													0 AS quantity_ordered,
                                                	NULL AS purchase_quantity,
                                                	product_cost AS current_price,
                                                	NULL AS delivered_quantity,
                                                	NULL AS transaction_id
                                                	FROM product, calendar
                                                	WHERE product_id = {$product_id}
                                                	AND order_date >= '{$_SESSION['order_date']}'
                                                	ON DUPLICATE KEY UPDATE quantity_available = product_default_quantity_available, current_price = $product_cost");
                                                	print mysql_error();
						break;
					case 0:
						// delete future listings
						mysql_query("UPDATE product_calendar
							SET quantity_available = 0
							WHERE product_id = {$product_id}
							AND order_date >= '{$_SESSION['order_date']}'");
						break;
					}
				}
			$id = $_POST['product_id'];
			$smarty->assign('message','The record has been updated');
			//go on to show product details again
		case 'productview':
			if(empty($id)) $id = quote_smart($_REQUEST['id']);
			if(!empty($_REQUEST['returnpage'])) $smarty->assign('returnpage',$_SERVER['HTTP_REFERER']);
			$product_lookup = mysql_query("SELECT * FROM product WHERE product_id = {$id}");
			$p = mysql_fetch_array($product_lookup,MYSQL_ASSOC);
			if(empty($p['product_markup'])) $p['product_markup'] = $_SESSION['markup'];
			if($farm_gate) $p['product_cost'] = round($p['product_cost']*(1+$p['product_markup'])*(1+$p['product_VAT_rate']),2);
			foreach($p AS $key => $value) {
				$smarty->assign($key,$value);
			}
			if ($p['product_pic'] <> 0) {
				$smarty->assign('thumbnail', product_pic_thumbnail($id));
			} else {
				$smarty->assign('thumbnail', '');
			}
				
			$smarty->assign('function','productupdate');
			$smarty->assign('submit_value','Update');
			make_select_lists();
			include 'header.php';
			include 'footer.php';
			$smarty->display('producer_product_form.tpl');
			exit();
		case 'newproduct':
			$smarty->assign('function','addproduct');
			make_select_lists();
			include 'header.php';
			include 'footer.php';
			$smarty->display('producer_product_form.tpl');
			exit();
		case 'addproduct':
			//TODO: lots of sanity checks on addition of new product.
			if(empty($_POST['product_default_quantity_available'])) $default_quantity = 'NULL';
			if(empty($_POST['product_fairtrade'])) $_POST['product_fairtrade'] = 0;
			if(empty($_POST['product_local'])) $_POST['product_local'] = 0;
			if(empty($_POST['product_organic'])) $_POST['product_organic'] = 0;
			if(empty($_POST['product_available'])) $_POST['product_available'] = 0;
			$_POST['product_cost'] = floatval($_POST['product_cost']);
			if($farm_gate) $_POST['product_cost'] = $_POST['product_cost'] / (1 + $_SESSION['markup'])/(1+$_POST['product_VAT_rate']);
			$_POST = quote_smart($_POST);
			if(empty($default_quantity)) $default_quantity = $_POST['product_default_quantity_available'];

			$ins_pic = "product_pic = 0,";
			if ($_FILES['product_pic']['name'] <> "") {
			 	$ins_pic = "product_pic = 1,";
			}

			mysql_query("INSERT INTO product
						SET product_name = {$_POST['product_name']},
						product_description = {$_POST['product_description']},
						product_supplier_id = {$_SESSION['producer']},			
						product_category_id = {$_POST['product_category_id']},
						product_code = {$_POST['product_code']},
						product_cost = {$_POST['product_cost']},
						product_VAT_rate = {$_POST['product_VAT_rate']},
						product_units = {$_POST['product_units']},
						product_pkg_count = {$_POST['product_pkg_count']},
						product_case_size = {$_POST['product_case_size']},
						product_local = {$_POST['product_local']},
						product_organic = {$_POST['product_organic']},
						product_fairtrade = {$_POST['product_fairtrade']},
						product_available = {$_POST['product_available']},
						product_default_quantity_available = {$default_quantity},
						$ins_pic
						product_more_info = {$_POST['product_more_info']}");
			$product_id = mysql_insert_id();
			
			// save pic after we know the product_id
			if ($_FILES['product_pic']['name'] <> "") {
				 save_product_pic($product_id, $_FILES['product_pic']['name'], $_FILES['product_pic']['tmp_name'], $_FILES['product_pic']['type']); 			}
			
			if($_POST['product_available'] == 1)
			{
				mysql_query("INSERT INTO product_calendar
                                	        SELECT  order_date,
                                                product.product_id,
                                                product_default_quantity_available AS quantity_available,
												0 AS quantity_ordered,
                                                NULL AS purchase_quantity,
                                                product_cost AS current_price,
                                                NULL AS delivered_quantity,
                                                NULL AS transaction_id
                                                FROM product, calendar
                                                WHERE product_id = {$product_id}
                                                AND order_date >= '{$_SESSION['order_date']}'");
				}
			break;

		case 'archiveproduct':
			// confirm that the product should be archived
			if(empty($_POST['confirm_archive']))
			{
				include 'header.php';
				include 'footer.php';
				$smarty->assign('product_id',$_POST['product_id']);
				$smarty->display('producer_product_delete.tpl');
				exit();
				}
			// delete from future periods
			mysql_query("UPDATE product_calendar SET quantity_available = 0 WHERE product_id = {$_POST['product_id']}
					AND order_date >= NOW()");
			// mark the product as archived
			mysql_query("UPDATE product SET product_archived = 1 WHERE product_id = {$_POST['product_id']}");
			break;
		}
	}

// look up the date information: past distributions, closed but not filled distributions, future distributions
$now = mktime(0,0,0);

$closed_dates = get_closed_dates($now);
$smarty->assign('closed_dates',$closed_dates);
$smarty->assign('past_dates',get_past_dates($now));
$future_dates = get_future_dates($now);
$smarty->assign('future_dates',$future_dates);

// get the supplier information
$smarty->assign('supplier_details', get_supplier_details());

// get the full product list
$smarty->assign('product_list',get_products(''));

// get the current orders list
$smarty->assign('current_orders',get_orders($future_dates[0]['order_date']));

// get the closed orders list
if(!empty($closed_dates)) $smarty->assign('closed_orders',get_orders($closed_dates[0]['order_date']));

include 'header.php';
include 'footer.php';

$smarty->display('producer_panel.tpl');

?>
