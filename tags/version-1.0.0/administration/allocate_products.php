<?php

################################################################################
#                                                                              #
#		Filename:	allocate_products.php			       #
#		Author:		Martin Settle				       #
#               Created:	21 September 2006			       #
#		Description:	allocate deliveries to customers	       #
#		Calls:		config.php.inc				       #
#		Called by:	nothing					       #
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
	- 2006.09.21 file created
	- 2008-04-04 make redundant entry in product_calendar

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

if(empty($_POST['PROCESS']))
{
	// get confirmation of delivery numbers
	$smarty->assign('pagetitle','Confirm Delivery');
	$smarty->assign('products',$_POST['products']);
	include ("../header.php");
	include ("../footer.php");
	$smarty->display('confirm_delivery.tpl');
	
/*	foreach($_POST['products'] as $key => $value)
	{
		foreach($value as $k => $v)
		{
			print ($k . ": ");
	        	print ($v . "<br>");	
			}
		}
*/
	exit();
}

foreach($_POST['delivered'] as $product_id => $delivered)
{
	//get current stock
	if(empty($delivered)) $delivered = 0;
	$stocklookup = mysql_query("SELECT IFNULL(product_current_stock,0) as product_current_stock, product_case_size FROM product WHERE product_id = $product_id");
	$currentstock = mysql_result($stocklookup,0,'product_current_stock');
	$case_size = mysql_result($stocklookup,0,'product_case_size');
	$delivery = $delivered * $case_size;
	//rough check for rounding errors:
	$round_check = mysql_query("SELECT IF(ABS({$delivery} - purchase_quantity) < 0.1, purchase_quantity,{$delivery}) AS delivered 
					FROM product_calendar WHERE product_id = {$product_id}
					AND order_date = '{$_SESSION['admin_date']}'");
	$delivery = mysql_result($round_check,0,'delivered');
	$unallocated = $delivery + $currentstock;
	
	// get the orders
	$orders_query = mysql_query("SELECT order_id, order_quantity_requested FROM orders WHERE order_product_id = $product_id AND order_date = '$_SESSION[admin_date]' ORDER BY order_time ASC");
	while($orders = mysql_fetch_array($orders_query))
	{
		if($orders['order_quantity_requested'] <= $unallocated)
		{
			mysql_query("UPDATE orders SET order_quantity_delivered = order_quantity_requested WHERE order_id = $orders[order_id]");
			$unallocated = $unallocated - $orders['order_quantity_requested'];
			}
		else
		{
			mysql_query("UPDATE orders SET order_quantity_delivered = $unallocated WHERE order_id = $orders[order_id]");
			$unallocated = 0;
			}
		}
	// set up or update the supplier_accounts transaction
	$details_lookup = mysql_query("SELECT current_price, product_VAT_rate FROM product, product_calendar 
									WHERE product.product_id = product_calendar.product_id
									AND order_date = '{$_SESSION['admin_date']}'
									AND product.product_id = {$product_id}");
	$VAT_rate = mysql_result($details_lookup,0,'product_VAT_rate');
	$current_price = mysql_result($details_lookup,0,'current_price');
	$transaction_id_lookup = mysql_query("SELECT transaction_id FROM supplier_accounts 
										WHERE supplier_id = (SELECT product_supplier_id FROM product WHERE product_id = {$product_id}) 
										AND transaction_date = '{$_SESSION['admin_date']}'
										AND transaction_reference LIKE 'Goods Delivered'");
	if(mysql_num_rows($transaction_id_lookup) != 0)
	{
		$transaction_id = mysql_result($transaction_id_lookup,0,'transaction_id');
		mysql_query("UPDATE supplier_accounts SET transaction_value = IFNULL(transaction_value,0) + {$delivered} * {$case_size} * {$current_price},
					transaction_VAT = IFNULL(transaction_VAT,0) + {$delivered} * {$case_size} * {$current_price} * {$VAT_rate}
					WHERE transaction_id = {$transaction_id}");
		}
	else
	{
		mysql_query("INSERT INTO supplier_accounts
					SET supplier_id = (SELECT product_supplier_id FROM product WHERE product_id = {$product_id}), 
					transaction_date = '{$_SESSION['admin_date']}',
					transaction_reference = 'Goods Delivered',
					transaction_value = {$delivered} * {$case_size} * {$current_price},
					transaction_VAT = {$delivered} * {$case_size} * {$current_price} * {$VAT_rate}");
		print mysql_error();
		$transaction_id = mysql_insert_id();

		}	 
	
	// update the product_calendar delivered field
	mysql_query("UPDATE product_calendar SET delivered_quantity = {$delivery}, transaction_id = {$transaction_id} WHERE product_id = {$product_id} AND order_date = '{$_SESSION['admin_date']}'");
	mysql_query("UPDATE product SET product_current_stock = $unallocated WHERE product_id = $product_id");
	}
	
include '../header.php';
$smarty->assign('pagetitle','Delivery Allocated');
$smarty->assign('body_text','The delivery has been allocated to customer orders.<p>If deliveries from all suppliers have been received you may now process sorting data');
include '../footer.php';

$smarty->display('index.tpl');

?>
