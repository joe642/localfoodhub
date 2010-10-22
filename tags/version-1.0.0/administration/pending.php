<?php

################################################################################
#                                                                              #
#		Filename:	sorting.php				       #
#		Author:		Martin Settle				       #
#               Created:	13 October 2006				       #
#		Description:	prints order sort reports		       #
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
	- 2006.10.13 file created
	- 2007.01.16 Product description added

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

switch($_REQUEST['select_by'])
{
	case 'supplier':
		// get supplier name for report
		$query = "SELECT supplier_name FROM supplier WHERE supplier_id = $_POST[supplier_id]";
		$name_lookup = mysql_query($query);
		$supplier = mysql_result($name_lookup,0);
		$smarty->assign('supplier',$supplier);
		
		// check delivery received
		$query = "SELECT * 
			FROM orders,product 
			WHERE orders.order_product_id = product.product_id
			AND product.product_supplier_id = $_POST[supplier_id]
			AND order_quantity_delivered IS NOT NULL";
		$Delivery_received = mysql_query($query);
		if(mysql_num_rows($Delivery_received) == 0)
		{
			$message = "The delivery for $supplier has not yet been processed.  Please use the <em>Receive Goods</em> function before requesting the sorting reports.";
			$smarty->assign('body_text',$message);
			$smarty->display('index.tpl');
			exit();
			}
		
		// get product names
		$query = "SELECT DISTINCT(product_name),product_description from product, orders WHERE product_id = order_product_id AND order_date = '$_SESSION[admin_date]' AND product_supplier_id = $_POST[supplier_id] ORDER BY product_name";
		$p = mysql_query($query);
		while($prod = mysql_fetch_array($p))
		{
			$products[] = $prod['product_name'];
			$description[] = $prod['product_description'];
			}
		
		// get all deliveries
		$query = "SELECT 
				CONCAT(member_first_name,' ',member_last_name) AS name,
				order_product_id,
				product_name,
				order_quantity_delivered
			  FROM
			  	orders,
				member,
				product
			  WHERE
			  	orders.order_product_id = product.product_id
			  AND
			  	orders.order_member_id = member.member_id
			  AND
			  	product.product_supplier_id = $_POST[supplier_id]
			  AND
			  	orders.order_date = '$_SESSION[admin_date]'
			  ORDER BY
			  	product_name,
				name";
		
		$Deliveries_lookup = mysql_query($query);
		$count = 0;
		#$products = array();
		while($D = mysql_fetch_array($Deliveries_lookup))
		{

			#	if(!array_search("$D[product_name]",$products))
			#{
				#	print $D['product_name']. '<br>';
				#$products[] = $D['product_name'];
				#}
			$sorting[$count]['product'] = $D['product_name'];
			$sorting[$count]['name'] = $D['name'];
			$sorting[$count]['quantity'] = $D['order_quantity_delivered'];
			$count++;
			}
		$smarty->assign('products',$products);
		$smarty->assign('description',$description);
		$smarty->assign('sorting',$sorting);

		$smarty->display('sort_supplier.tpl');
		exit();
		
	case 'customer':
		// get ordering member names
		$query = "SELECT member_id,CONCAT(member_first_name,' ',member_last_name) AS member_name FROM member WHERE member_id IN (SELECT DISTINCT(member_id) FROM member,temp_orders WHERE member_id = order_member_id AND order_date = '$_SESSION[admin_date]') ORDER BY member_last_name";
		$members_lookup = mysql_query($query);
		$count = 0;
		while($mem = mysql_fetch_array($members_lookup))
		{
			$members[$count]['member_id'] = $mem['member_id'];
		       	$members[$count]['member_name'] = $mem['member_name'];
			$count++;
			}
		
		$smarty->assign('members',$members);
		
		// get products

		$query = "SELECT product_name, product_description, product_units, order_member_id, order_quantity_requested AS quantity,
			ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) AS unit_price,
			ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * order_quantity_requested AS order_product_total
			FROM temp_orders,product
			WHERE order_product_id = product_id
			AND order_date = '$_SESSION[admin_date]'
			ORDER BY product_name";
			//echo $query;
		$orders_lookup = mysql_query($query);
		$count = 0;
		while($ord = mysql_fetch_array($orders_lookup))
		{
			$orders[$count]['member_id'] = $ord['order_member_id'];
			$orders[$count]['product_name'] = $ord['product_name'];
			$orders[$count]['product_description'] = $ord['product_description'];
			$orders[$count]['quantity'] = $ord['quantity'];
			$orders[$count]['units'] = $ord['product_units'];
			$orders[$count]['unit_price'] = $ord['unit_price'];
			$orders[$count]['total_price'] = $ord['order_product_total'];
			$count++;
			}

		$smarty->assign('orders',$orders);
				$smarty->assign('orderdate',$_SESSION[admin_date]);

		$smarty->display('pending_customers.tpl');
		exit();

	}

// Otherwise we need the form to get the select_by field.

// Get the suppliers in the current order
$query = ("SELECT supplier_id, supplier_name 
	FROM supplier
	WHERE supplier_id IN 
		(SELECT DISTINCT(product_supplier_id) 
		FROM product, orders 
		WHERE orders.order_product_id = product.product_id 
		AND orders.order_date = '$_SESSION[admin_date]')");

$supplier_lookup = mysql_query($query);
while($s = mysql_fetch_array($supplier_lookup))
{
	$supplier[$s['supplier_id']] = $s['supplier_name'];
	}

$smarty->assign('suppliers',$supplier);

$smarty->display('sort_select.tpl');


?>
