<?php

################################################################################
#                                                                              #
#		Filename:	productlist.php				       #
#		Author:		Martin Settle				       #
#               Created:	1 Septembre 2006			       #
#		Description:	Browse and select products		       #
#		Calls:		config.php				       #
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
	- 2006.09.01 file created
	- 2008.03.12 adapted to use product_calendar for lookup of items, and process to temp_orders

*/

if(empty($CoopName))
{	
	session_start();

	//if(empty($_SESSION['member_id']))
	//{
	//	header ("Location:index.php");
	//	exit();
	//	}
	
	header ("Cache-control: private");

	$Secure = 1;
	include 'config.php';
	}
	
include 'product_pic.php';

if(isset($_POST['filter_type']) && $_POST['filter_type'] == 'category' && $_POST['category'] == '')
{
	unset($_POST['filter_type']);
	$smarty->assign('message','Please select a category');
	}

if(empty($_POST['filter_type']))
{
	// print a form asking to select category, search, or show all

	
	include 'header.php';
	
	$categories_lookup = mysql_query("SELECT category_id, category_name FROM category,product, product_calendar 
									WHERE product_category_id = category_id 
									AND product.product_id = product_calendar.product_id 
									AND order_date = '{$_SESSION['order_date']}'
									AND (quantity_available > 0 OR quantity_available IS NULL)
									ORDER BY category_name");
	

	while(	$cat = mysql_fetch_array($categories_lookup) )
	{
		$key = $cat['category_id'];
		$name = $cat['category_name'];
		$categories["$key"] = "$name";
		}
		
	$smarty->assign('categories',$categories);
	
	include 'footer.php';
	
	$smarty->display('view_product_category.tpl');
	
	exit();
	}

// Allow following queries to run even if not logged in
if (isset($_SESSION['member_id'])) {
	$member_id = $_SESSION['member_id'];
} else {
	$member_id = -1;	// not logged in
	// echo $_SESSION['order_date'] . "<br/>"; // note that order_date is set correctly even when not logged in
}

switch($_POST['filter_type'])
{
	case 'category':
		$cat = $_POST['category'];
		$sql = "SELECT product.product_id, 
						product_name, 
						product_pic,
						product_description, 
						product_local, 
						product_fairtrade, 
						product_organic, 
						product_units,
						product.product_supplier_id sid,
						supplier_name, 
						order_quantity_requested, 
						quantity_available,
						quantity_ordered,
						ROUND(current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup,$_SESSION[markup])),2) AS price, 
						product_pkg_count,
						if(isnull(product_more_info),0,1) AS more_info
					FROM supplier, category, product_calendar,
						product LEFT JOIN 
							(SELECT order_product_id, 
								order_quantity_requested 
								FROM temp_orders 
								WHERE order_member_id = $member_id 
								AND order_date = '$_SESSION[order_date]') AS member_orders 
							ON member_orders.order_product_id = product_id 
						WHERE supplier.supplier_id = product.product_supplier_id 
						AND category.category_id = product.product_category_id 
						AND product_calendar.product_id = product.product_id
						AND product_calendar.order_date = '{$_SESSION['order_date']}'
						AND (product_calendar.quantity_available != 0 || product_calendar.quantity_available is NULL)
						AND category.category_id = $cat ORDER BY product_name";
		$headerlookup = mysql_query("SELECT category_name FROM category WHERE category_id = $cat");
		$heading = mysql_result($headerlookup,0);
		break;
	case 'search': 
				$search = $_POST['search_term'];
				//echo "Searching for: " . $search . "<br>";
				$sql = "SELECT product.product_id, 
					product_name, 
					product_pic,
					product_description, 
					product_local, 
					product_fairtrade, 
					product_organic, 
					product_units,
					product.product_supplier_id sid,
					supplier_name, 
					order_quantity_requested, 
					quantity_available,
					quantity_ordered,
					ROUND(current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup,$_SESSION[markup])),2) AS price, 
					product_pkg_count ,
						if(isnull(product_more_info),0,1) AS more_info
				FROM supplier, category, product_calendar, 
					product LEFT JOIN 
						(SELECT order_product_id, 
							order_quantity_requested 
							FROM temp_orders 
							WHERE order_member_id = $member_id 
							AND order_date = '$_SESSION[order_date]') 
						AS member_orders 
					ON member_orders.order_product_id = product_id 
				WHERE supplier.supplier_id = product.product_supplier_id 
				AND product_calendar.product_id = product.product_id
				AND product_calendar.order_date = '{$_SESSION['order_date']}'
				AND (product_calendar.quantity_available != 0 || product_calendar.quantity_available is NULL)
				AND category.category_id = product.product_category_id 
				AND (product_name LIKE '%" . $search . "%' or product_description LIKE '%" . $search . "%' or supplier_name LIKE '%" . $search . "%') 
				ORDER BY product_name";
		$heading = "Results of search for " . $search;
		break;    
	case 'show_all':
		$sql = "SELECT product.product_id, 
					product_name, 
					product_pic,
					product_description, 
					product_local, 
					product_fairtrade, 
					product_organic,
					product_units,
					product.product_supplier_id sid, 
					supplier_name, 
					order_quantity_requested, 
					quantity_available,
					quantity_ordered,
					ROUND(current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup,$_SESSION[markup])),2) AS price, 
					product_pkg_count ,
						if(isnull(product_more_info),0,1) AS more_info
				FROM supplier, category, product_calendar, 
					product LEFT JOIN 
						(SELECT order_product_id, 
							order_quantity_requested 
							FROM temp_orders 
							WHERE order_member_id = $member_id 
							AND order_date = '$_SESSION[order_date]') 
						AS member_orders 
					ON member_orders.order_product_id = product_id 
				WHERE supplier.supplier_id = product.product_supplier_id 
				AND product_calendar.product_id = product.product_id
				AND product_calendar.order_date = '{$_SESSION['order_date']}'
				AND (product_calendar.quantity_available != 0 || product_calendar.quantity_available is NULL)
				AND category.category_id = product.product_category_id 
				ORDER BY product.product_category_id, product.product_supplier_id, product_name";
		$heading = "All Products";
		break;
	case 'current':
		$sql = "SELECT product.product_id, 
					product_name, 
					product_pic,
					product_description, 
					product_local, 
					product_fairtrade, 
					product_organic,
					product_units,
					product.product_supplier_id as sid, 
					supplier_name, 
					order_quantity_requested, 
					quantity_available,
					quantity_ordered,
					ROUND(current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup,$_SESSION[markup])),2) AS price, 
					product_pkg_count ,
						if(isnull(product_more_info),0,1) AS more_info
				FROM supplier, category, product_calendar,
					product INNER JOIN 
						(SELECT order_product_id, 
								order_quantity_requested 
								FROM temp_orders 
								WHERE order_member_id = $member_id 
								AND order_date = '$_SESSION[order_date]') AS member_orders 
							ON member_orders.order_product_id = product_id 
							WHERE supplier.supplier_id = product.product_supplier_id 
							AND product_calendar.product_id = product.product_id
							AND product_calendar.order_date = '{$_SESSION['order_date']}'
							AND category.category_id = product.product_category_id 
							ORDER BY product.product_category_id, product.product_supplier_id, product_name";
	}

#$menu_override = 1;

include 'header.php';

$product_lookup = mysql_query("$sql");
print mysql_error();
$count = 0;

while($product = mysql_fetch_array($product_lookup,MYSQL_ASSOC))
{
	
	/* MODS DJC - number ordered (paid for or not) is stored in quantity_ordered in the product_calendar table
	   so extra code here is redundant and availability can be calculated by: 
	   quantity_remaining = quantity_available - quantity_ordered + order_quantity_requested    (add the number this customer current has on order)
	*/
	if($product['quantity_available'] != 0) {
		$products[$count]['quantity_remaining'] = $product['quantity_available'] - $product['quantity_ordered'] + $product['order_quantity_requested'];
	} else {
		$product['quantity_remaining'] = 99999;
	}
	/* 
	# For each product find out remainder on current split
	$order_count_lookup = mysql_query("SELECT SUM(order_quantity_requested) FROM orders WHERE order_date = '$_SESSION[order_date]' AND order_product_id = $product[product_id]");
	$order_count = mysql_result($order_count_lookup,0);

	$products[$count]['current_split'] = $product['product_pkg_count'] - fmod($order_count,$product['product_pkg_count']);
	if($product['quantity_available'] != 0) $products[$count]['quantity_remaining'] = $product['quantity_available'] - $order_count;
	*/
	
	if ($product['product_pic'] <> 0) {
		$product['thumbnail'] = product_pic_thumbnail($product['product_id']);
	} else {
		$product['thumbnail'] = "";
	}
	
	foreach($product as $key => $value)
	{
		$products[$count][$key] = $value;
		}
	$count++;
	}


$order_total_lookup = mysql_query("SELECT COUNT(temp_order_id) AS products, 
							SUM(ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup,$_SESSION[markup])),2) * order_quantity_requested)  AS total_cost
							FROM temp_orders, product 
							WHERE order_product_id = product_id
							AND order_quantity_requested > 0 
							AND order_member_id = $member_id 
							AND order_date = '$_SESSION[order_date]'");
if(mysql_num_rows($order_total_lookup) == 0)
{
	$total_cost = '0.00';
	$product_count = 0;
	}
else
{
	$total_cost = mysql_result($order_total_lookup,0,'total_cost');
	$product_count = mysql_result($order_total_lookup,0,'products');
	}

$smarty->assign('product_count',$product_count);
$smarty->assign('total_cost',$total_cost);

$smarty->assign('heading',$heading);
$smarty->assign('products',$products);
$smarty->assign('VAT_warning',$VAT_warning);

include 'footer.php';

if ($member_id == -1) {
	$smarty->assign('view_only',"YES");
	$balance = 0;
} else {
	$balance_lookup = mysql_query("SELECT member_account_balance FROM member WHERE member_id = {$_SESSION['member_id']}");
	$balance = mysql_result($balance_lookup,0,'member_account_balance');
}
$smarty->assign('opening_balance', $balance);
	
$smarty->display('product_list.tpl');

?>
