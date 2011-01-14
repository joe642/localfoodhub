<?php

################################################################################
#                                                                              #
#		Filename:		producer_functions.php.inc			       #
#		Author:		Martin Settle				       #
#             Created:		13 March 2008				       #
#		Description:	Contains functions used by elements of the producer control panel	       #
#		Calls:		Nothing					       #
#		Called by:		producer.php				       #
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
	
*/

// check for any closed but undelivered orders
function get_closed_dates($now)
{
	$days_notice =get_config_value('days_notice');
	$closed_date = mktime(0,0,0,date('m'),date('d')+$days_notice,date('Y'));
	$closed_query = "SELECT calendar.calendar_id, calendar.order_date AS order_date
								FROM  calendar, product_calendar, product 
								WHERE calendar.order_date = product_calendar.order_date
								AND product_calendar.product_id = product.product_id
								AND product_supplier_id = {$_SESSION['producer']}
								AND calendar.order_date > FROM_UNIXTIME({$now})
								AND calendar.order_date < FROM_UNIXTIME({$closed_date})
								GROUP BY calendar.calendar_id, calendar.order_date
								ORDER BY calendar.order_date DESC";
	//echo "get_closed_dates: " . $now .  " : " . $closed_query . "<br>";
	$closed_lookup = mysql_query($closed_query);
	print mysql_error();
	$count=0;
	while($p = mysql_fetch_array($closed_lookup))
	{
		$closed_dates[$count]['calendar_id'] = $p['calendar_id'];
		$closed_dates[$count]['order_date'] = $p['order_date'];
		$count++;
		}
	return $closed_dates;	
	}

// get an array of future orders 
function get_future_dates($now)
{

	$days_notice =get_config_value('days_notice');
	$closed_date = mktime(0,0,0,date('m'),date('d')+$days_notice,date('Y'));
	
	$future_lookup = mysql_query("SELECT calendar.calendar_id, calendar.order_date, products 
								FROM calendar 
									LEFT JOIN 
										(SELECT order_date, count(product_calendar.product_id) AS products 
											FROM product_calendar,product WHERE product_calendar.product_id = product.product_id 
											AND product_supplier_id = {$_SESSION['producer']} AND (quantity_available != 0 OR quantity_available is NULL) GROUP BY order_date) AS productavailability 
										ON productavailability.order_date = calendar.order_date
								WHERE calendar.order_date > FROM_UNIXTIME({$closed_date})
								ORDER BY calendar.order_date ASC");
	$count=0;
	while($p = mysql_fetch_array($future_lookup))
	{
		$future_dates[$count]['calendar_id'] = $p['calendar_id'];
		$future_dates[$count]['order_date'] = $p['order_date'];
		$future_dates[$count]['products_available'] = $p['products'];
		$count++;
		}
	return $future_dates;
	}

// get an array of past orders
function get_past_dates($now)
{
	$past_query = "SELECT calendar.calendar_id, calendar.order_date 
								FROM calendar, product_calendar, product 
								WHERE calendar.order_date = product_calendar.order_date
								AND product_calendar.product_id = product.product_id
								AND purchase_quantity > 0
								AND product_supplier_id = {$_SESSION['producer']}
								AND calendar.order_date < FROM_UNIXTIME({$now})
								GROUP BY calendar.calendar_id, calendar.order_date
								ORDER BY calendar.order_date DESC";
	//echo "get_past_dates: " . $now .  " : " . $past_query . "<br>";
	$past_lookup = mysql_query($past_query);
	$count=0;
	if(mysql_num_rows($past_lookup) > 0)
	{
		while($p = mysql_fetch_array($past_lookup))
		{
			$past_dates[$count]['calendar_id'] = $p['calendar_id'];
			$past_dates[$count]['order_date'] = $p['order_date'];
			$count++;
			}
		}
	return $past_dates;
	}

// get all the supplier details
function get_supplier_details()
{
	$supplier_lookup = mysql_query("SELECT * FROM supplier WHERE supplier_id = {$_SESSION['producer']}");
	$supplier_details = mysql_fetch_array($supplier_lookup,MYSQL_ASSOC);
	return $supplier_details;
	}

// get the supplier product list with optional order_date
function get_products($order_date)
{
	//echo "get_products(" . $order_date . ")<br/>";
	if(empty($order_date)) $query = "SELECT product_id, product_name, product_code FROM product WHERE product_supplier_id = {$_SESSION['producer']} AND product_archived = 0";
	else $query = "SELECT product.product_id, product_name, product_code FROM product, product_calendar WHERE product.product_id = product_calendar.product_id AND order_date = FROM_UNIXTIME({$order_date}) AND product_supplier_id = {$_SESSION['producer']}";
	
	$product_lookup = mysql_query($query);
	$count = 0;
	while($p = mysql_fetch_array($product_lookup))
	{
		$products[$count]['product_id'] = $p['product_id'];
		$products[$count]['product_name'] = $p['product_name'];
		$products[$count]['product_code'] = $p['product_code'];
		$count++;
		}
	return $products;
	}


// get product orders for a particular date
function get_orders($order_date)
{
	//echo "get_orders(" . $order_date . ")<br/>";
	// MODS DJC - $mq = "SELECT product.product_id, product_name, quantity_available, purchase_quantity, deliverd_quantity 
	$mq = "SELECT product.product_id, product_name, quantity_available, purchase_quantity, delivered_quantity 
								FROM product, product_calendar
								WHERE product.product_id = product_calendar.product_id
								AND (quantity_available != 0 || quantity_available is NULL)
								AND order_date = '{$order_date}' 
								AND product_supplier_id = {$_SESSION['producer']}
								ORDER BY purchase_quantity DESC";

	$orders_lookup = mysql_query($mq);
	$count=0;
	while ($o = mysql_fetch_array($orders_lookup))
	{
		$orders[$count]['product_id'] = $o['product_id'];
		$orders[$count]['product_name'] = $o['product_name'];
		$orders[$count]['quantity_available'] = $o['quantity_available'];
		$orders[$count]['purchase_quantity'] = $o['purchase_quantity'];
		$orders[$count]['delivered_quantity'] = $o['delivered_quantity'];
		$count++;;
		}
	return $orders;
	}

// get a listing of all products (not archived) and date related details
function get_all_products($order_date)
{
	//echo "get_all_products(" . $order_date . ")<br/>";
	$product_query = "SELECT product.product_id, product_name, product_units, product_VAT_rate,
									IF(current_price IS NULL, product_cost, current_price) AS current_price, 
									if(quantity_available is NULL,'no limit',quantity_available) as quantity_available,
									purchase_quantity, IF(order_date IS NULL, 0, 1) AS active,
									delivered_quantity
									FROM product LEFT JOIN (SELECT * FROM product_calendar WHERE order_date = {$order_date}) AS product_calendar 
									ON product_calendar.product_id = product.product_id 
									WHERE product_supplier_id = {$_SESSION['producer']}
									AND product_archived = 0";
	//echo "get_all_products: " . $order_date .  " : " . $product_query . "<br>";
	$product_lookup = mysql_query($product_query);

	$count = 0;
	$farm_gate = get_config_value('use_farm_gate_pricing');
	while($p = mysql_fetch_array($product_lookup))
	{
		$pending_query = "SELECT sum(order_quantity_requested) AS pending FROM temp_orders
										WHERE order_date = {$order_date} 
										AND order_product_id = {$p['product_id']}";
		//echo $pending_query . "<br/>";
		$pending_lookup = mysql_query($pending_query);
		$product[$count]['pending'] = mysql_result($pending_lookup,0,'pending');
		$product[$count]['product_id'] = $p['product_id'];
		$product[$count]['product_name'] = $p['product_name'];
		$product[$count]['product_units'] = $p['product_units'];
		if ($farm_gate) $product[$count]['current_price'] = round($p['current_price']*(1+$_SESSION['markup'])*(1+$p['product_VAT_rate']),2);
		else $product[$count]['current_price'] = $p['current_price'];
		$product[$count]['quantity_available'] = $p['quantity_available'];
		$product[$count]['purchase_quantity'] = $p['purchase_quantity'];
		$product[$count]['delivered_quantity'] = $p['delivered_quantity'];
		$product[$count]['active'] = $p['active'];
		$count++;
		}
	return $product;
	}

// quick check to see if a particular date is "on"
function check_date($order_date)
{
	$date_query = "SELECT * FROM product_calendar, product WHERE product.product_id = product_calendar.product_id
								AND product_supplier_id = {$_SESSION['producer']}
								AND order_date = {$order_date}";
	//echo "check_date: " . $order_date .  " : " . $date_query . "<br>";
	$date_lookup = mysql_query($date_query);

	if(mysql_num_rows($date_lookup) == 0) return false;
	else return true;
	}
	
// build the select lists for the product form
function make_select_lists()
{
	global $smarty;
	$suppliers_lookup = mysql_query('SELECT supplier_id,supplier_name FROM supplier ORDER BY supplier_name');
	while($s = mysql_fetch_array($suppliers_lookup))
	{
		$suppliers[$s['supplier_id']] = $s['supplier_name'];
		}
	$smarty->assign('suppliers',$suppliers);
	
	$categories_lookup = mysql_query('SELECT category_id, category_name FROM category ORDER BY category_name');
	while($c = mysql_fetch_array($categories_lookup))
	{
		$categories[$c['category_id']] = $c['category_name'];
		}
	$smarty->assign('categories',$categories);
	
	// MODS 1-Jan-2011: changed VAT rate - should move this into database table
	$smarty->assign('VAT_rates',array('0.00' => 'Exempt or Zero',
					'0.05' => 'Low rate (5%)',
					'0.2' => 'Full VAT (20%)'));
	}

include_once 'product_pic.php';