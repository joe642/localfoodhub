<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	26 April 2007				       #
#		Description:	admin adjust member invoice page	       #
#		Calls:		config.php.inc				       #
#		Called by:	nothing					       #
#
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
	- 2007.04.26 file created

*/

if(empty($CoopName))
{	
	session_start();
	
	if(empty($_SESSION['member_id']))
	{
		header ("Location:../index.php");
		exit();
		}
	elseif(empty($_SESSION['admin']))
	{
		header ("Location:index.php");
		exit();
		}

	header ("Cache-control: private");

	$Secure = 1;
	include '../config.php';
	}

include '../header.php';
include '../footer.php';

if(empty($_GET['member_id'])) $_GET['member_id'] = $_POST['member_id'];

if(empty($_GET['member_id']))
{
	// Show a select form for member id
	$smarty->assign('pagetitle','Select Member');
	include '../header.php';
	$message = '<p>Please select the member:</p>';			
	$smarty->assign('message',$message);
	
	$memberslookup = mysql_query("SELECT member_id, CONCAT(member_first_name,' ',member_last_name) AS name FROM member WHERE member_active = 1 ORDER BY name");
	while($member = mysql_fetch_array($memberslookup))
	{
		$members[$member['member_id']] = $member['name'];
		}
	$smarty->assign('members',$members);
	
	include '../footer.php';
	
	$smarty->display('member_select.tpl');
	exit();
	}

// process any changes to the order
if(!empty($_POST['order']))
{
	foreach($_POST['order'] AS $c => $id)
	{		
		// get current order info	
		$existing = arrayMyQuery("SELECT * FROM orders WHERE order_id = $id");
		extract($existing);
		
		$productVAT = arrayMyQuery("SELECT product_VAT_rate FROM product WHERE product_id = $order_product_id");
		extract($productVAT);

		//echo "VAT: $product_VAT_rate,   Markup: $_SESSION[markup]<br/>";
		$current_price = $_POST["current_price_$id"] / (1 + $_SESSION[markup]) / (1 + $product_VAT_rate);
		//$request = $_POST["request_$id"];
		$delivered = $_POST["delivered_$id"];	
		if(empty($delivered)) $delivered = 'NULL';

		// only process if data has changed
		if(($delivered != $order_quantity_delivered) OR
			($current_price != $order_current_price))
		{
			// post the changes	- no longer allow requested to be changed since it effects the calculated paid amount - only delivered and price
			// mysql_query("UPDATE orders SET order_quantity_requested = '$request', order_quantity_delivered = $delivered, order_current_price = '{$current_price}' WHERE order_id = $id");
			mysql_query("UPDATE orders SET order_quantity_delivered = $delivered, order_current_price = '{$current_price}' WHERE order_id = $id");
			// update stock records, if required
			if(!empty($_POST["update_stock_$id"]) AND ($delivered != $order_quantity_delivered))
			{
				// get pre-update stock level
				$current_stock_lookup = mysql_query("SELECT product_current_stock, product_perishable FROM product WHERE product_id = $order_product_id");
				$current_stock = mysql_result($current_stock_lookup,0,'product_current_stock');
			
				// only change stock if product not perishable
				if(mysql_result($current_stock_lookup,0,'product_perishable') == 0)
				{
					// calculate new stock
					$new_stock = $current_stock + $order_quantity_delivered - $delivered;
	
					// post change
					mysql_query("UPDATE product SET product_current_stock = $new_stock WHERE product_id = $order_product_id");
					}			
				}
			// if not adjusting stock, need to adjust product_calendar and supplier_account
			elseif(($delivered != $order_quantity_delivered) || ($current_price != $order_current_price))
			{
				$change = $delivered - $order_quantity_delivered;
				mysql_query("UPDATE product_calendar SET delivered_quantity = delivered_quantity + {$change}
							WHERE product_id = {$order_product_id} AND order_date = '{$_SESSION['admin_date']}'");
				$trans_id_lookup = mysql_query("SELECT transaction_id FROm product_calendar
							WHERE product_id = {$order_product_id} AND order_date = '{$_SESSION['admin_date']}'");
				$transaction_id = mysql_result($trans_id_lookup,0,'transaction_id');
				$VAT_required = mysql_query("SELECT product_VAT_rate FROM product WHERE product_id = {$order_product_id}");
				$VAT = mysql_result($VAT_required,0,'product_VAT_rate');
				// TODO: this needs to be changed for multiple distribution sites, as per core_functions -- maybe convert to a function?
				mysql_query("UPDATE supplier_accounts
							SET transaction_value = transaction_value - $order_current_price * $order_quantity_delivered + $current_price * $delivered, 
							transaction_VAT = transaction_VAT - $order_current_price * $order_quantity_delivered * $VAT + $current_price * $delivered * $VAT
							WHERE transaction_id = {$transaction_id}");
				}
			}
		}
	}

// process new additions to the product list
if(!empty($_POST['new_order']))
{
	// set paid price to 0 since this has been added to their order and not paid for as yet
	mysql_query("INSERT INTO orders SET order_date = '{$_SESSION['admin_date']}', order_member_id = {$_POST['member_id']}, order_product_id = {$_POST['new_product_id']}, order_quantity_requested = {$_POST['new_quantity']}, order_quantity_delivered = {$_POST['new_quantity']}, order_current_price = (SELECT product_cost FROM product WHERE product_id = {$_POST['new_product_id']}), order_paid_price = 0");
	print mysql_error();
	// update stock for new order
	if(!empty($_POST['update_new_order_stock']))
	{
		// get pre-update stock level
		$current_stock_lookup = mysql_query("SELECT product_current_stock, product_perishable FROM product WHERE product_id = {$_POST['new_product_id']}");
		$current_stock = mysql_result($current_stock_lookup,0,'product_current_stock');
				
		// only change stock if product not perishable
		if(mysql_result($current_stock_lookup,0,'product_perishable') == 0)
		{
			// calculate new stock
			$new_stock = $current_stock - $_POST['new_quantity'];
	
			// post change
			mysql_query("UPDATE product SET product_current_stock = $new_stock WHERE product_id = {$_POST['new_product_id']}");
			}			
		}
	//TODO: update supplier records
	}
// PROCESSING COMPLETE, DISPLAY THE REVISED ORDER

// look up the member's current invoice details
$item_lookup = mysql_query("SELECT order_id, product.product_id, product_name, product_units, order_quantity_requested, order_quantity_delivered, order_current_price,
ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup] )),2) AS unit_price, 
ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * order_quantity_delivered AS order_product_total,
product_VAT_rate
 FROM orders,product WHERE product_id = order_product_id AND order_member_id = {$_GET['member_id']} AND order_date = '{$_SESSION['admin_date']}'  ORDER BY product_name");
$count = 0;
while($item = mysql_fetch_array($item_lookup))
{
	$order[$count]['order_id'] = $item['order_id'];
	$order[$count]['product_name'] = $item['product_name'];
	$order[$count]['product_units'] = $item['product_units'];
	$order[$count]['request'] = $item['order_quantity_requested'];
	$order[$count]['delivered'] = $item['order_quantity_delivered'];
	// $order[$count]['current_price'] = $item['order_current_price'];
	// show end user price not buy price - it makes more sense for the admin to adjust this.
	$order[$count]['current_price'] = $item['unit_price'];
	$count++;
	}

$smarty->assign('order',$order);

// build product list for new orders
$products_lookup = mysql_query("SELECT product_id, CONCAT(product_name,' - ' ,product_units,' - ',product_description) AS product_title FROM product WHERE product_available = 1 ORDER BY product_title");
while($p = mysql_fetch_array($products_lookup)) {
	if ($p['product_title'] != " -  - ") $products[$p['product_id']] = substr($p['product_title'],0,50);
}
$smarty->assign('products',$products);

// get member name for reference
$member_lookup = mysql_query("SELECT CONCAT(member_first_name,' ',member_last_name) AS member_name FROM member WHERE member_id = {$_GET['member_id']}");
print mysql_error();
$smarty->assign('member_name', mysql_result($member_lookup,0));
$smarty->assign('member_id', $_GET['member_id']);

$smarty->assign('pagetitle',$pagetitle . 'Adjust Member Invoice');

$smarty->display('adjust_invoice.tpl');

?>
