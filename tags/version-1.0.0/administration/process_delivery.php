<?php

################################################################################
#                                                                              #
#		Filename:	process_delivery.php			       #
#		Author:		Martin Settle				       #
#               Created:	21 September 2006			       #
#		Description:	record deliveries and allocate to customers    #
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
	- 2006.09.15 file created
	-

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

if(empty($_POST['supplier_id']))
{
	$smarty->assign('pagetitle','Record Deliveries Received');
	
	include '../header.php';

	$query = "SELECT supplier_id, supplier_name FROM supplier, orders, product WHERE orders.order_product_id = product.product_id AND product.product_supplier_id = supplier.supplier_id AND orders.order_date = '$_SESSION[admin_date]'";
	
	$supplierlookup = mysql_query($query);

print mysql_error();

	while($supply = mysql_fetch_array($supplierlookup)) {
		$supplier[$supply['supplier_id']] = $supply["supplier_name"];
	}
	
	// Check suppliers for this order date
	$query = "SELECT distinct supplier_id, supplier_name 
				FROM supplier, orders, product 
				WHERE orders.order_product_id = product.product_id 
				AND product.product_supplier_id = supplier.supplier_id 
				AND orders.order_date = '$_SESSION[admin_date]' ORDER BY supplier_name";

	$supplierlookup = mysql_query($query);
	print mysql_error();
	$not_recieved = 0;
	
	while($supply = mysql_fetch_array($supplierlookup))
	{
		// for each supplier check delivery received
		$query = "SELECT * 
			FROM orders,product 
			WHERE orders.order_product_id = product.product_id
			AND product.product_supplier_id = " . $supply['supplier_id'] .
			" AND order_date = '$_SESSION[admin_date]' 
			AND order_quantity_delivered IS NOT NULL";
			//echo $query . "<br/>";
			
		$Delivery_received = mysql_query($query);
		if (mysql_num_rows($Delivery_received) == 0) {
			$supplier_not_recieved[$not_recieved] = $supply["supplier_name"];
			$not_recieved += 1;
		}
	}
	$smarty->assign('not_recieved',$not_recieved);
	$smarty->assign('supplier_not_recieved', $supplier_not_recieved);
	
	$smarty->assign('instructions','Record deliveries from which supplier?');
	$smarty->assign('suppliers',$supplier);

	include '../footer.php';

	$smarty->display('supplier_delivery_form.tpl');

	exit();
	}

include '../header.php';

	
$query = "SELECT 
		supplier_name, 
		product_id, 
		product_name,
	        product_code,	
		SUM(order_quantity_requested) AS request,
		IF(product_pkg_count < 1,SUM(order_quantity_requested),IF(SUM(order_quantity_requested)<product_current_stock,0,(FLOOR((SUM(order_quantity_requested) - product_current_stock + product_allow_stock)/product_pkg_count) * product_pkg_count/product_case_size))) AS units_order, 
		IF(product_case_size>1,' cases',product_units) AS cases
	FROM 
		product, 
		supplier, 
		orders 
	WHERE 
		order_product_id = product_id 
	AND 
		product_supplier_id = supplier_id 
	AND 
		order_date = '$_SESSION[admin_date]' 
	AND	
		supplier_id = $_POST[supplier_id]
	GROUP BY 
		supplier_name,
		product_id,
		product_name,
		product_units,
	        product_code,	
		product_current_stock, 
		product_allow_stock, 
		product_pkg_count, 
		product_case_size 
	ORDER BY 
		supplier_id, product_name";

	
$orderlookup = mysql_query($query);

print mysql_error();

//build an associative array to print each order by supplier
$count = 0;
while($orderslist = mysql_fetch_array($orderlookup))
{
	$supplier[$count]['supplier_name'] = $orderslist['supplier_name'];
	$supplier[$count]['product_id'] = $orderslist['product_id'];
	$supplier[$count]['product_name'] = $orderslist['product_name'];
	$supplier[$count]['product_code'] = $orderslist['product_code'];
	$supplier[$count]['request'] = $orderslist['request'];
	$supplier[$count]['units'] = $orderslist['units_order'];
	$supplier[$count]['cases'] = $orderslist['cases'];
	$count++;
	}

$smarty->assign('products',$supplier);
	
include '../footer.php';

$smarty->display('process_delivery_form.tpl');

?>
