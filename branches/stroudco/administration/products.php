<?php

################################################################################
#                                                                              #
#		Author:		Martin Settle				       #
#               Created:	26 January 2007				       #
#		Description:	product administration			       #
#		Calls:		config.php.inc				       #
#		Called by:	suppliers.php				       #
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
	- 2007.01.26 file created
	- 2008.03.12 updated for product_calendar usage

*/
//this function updates the product_calendar table if the product availability has changed

function update_availability($id, $available,$new = 0,$product_cost)
{
	$current_available_lookup = mysql_query ("SELECT product_available, supplier_active * supplier_recurring AS active FROM product, supplier
											WHERE product_supplier_id = supplier_id
											AND product_id = {$id}");
	$c_product_available = mysql_result($current_available_lookup,0,'product_available');
	$c_supplier_active = mysql_result($current_available_lookup,0,'active');
	if($c_supplier_active == 1)
	{
		switch($available)
		{
			case '0': // delete all future product_calendar entries (where no orders exist)
				mysql_query("UPDATE product_calendar SET quantity_available = 0,
								current_price = {$product_cost}
								WHERE product_id = {$id}
								AND order_date >= '{$_SESSION['admin_date']}'");
				break;
			case '1': // create product_calendar entries
				mysql_query("INSERT INTO product_calendar 
					(SELECT  order_date, 
						product.product_id, 
						product_default_quantity_available AS quantity_available,
						0 AS quantity_ordered,
						NULL AS purchase_quantity,
						'{$product_cost}' AS current_price,
						NULL AS delivered_quantity,
						NULL AS transaction_id
						FROM product, calendar
						WHERE product_id = {$id}
						AND order_date >= '{$_SESSION['admin_date']}')
						ON DUPLICATE KEY UPDATE quantity_available = VALUES(quantity_available), current_price='{$product_cost}'");
				print mysql_error();
			}
		}
	}


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
	
	$smarty->assign('VAT_rates',array('0.00' => 'Exempt or Zero',
					'0.050' => 'Low rate (5%)',
					'0.175' => 'Full VAT (17.5%)'));
	}


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

switch($_POST['submit_button'])
{
	case '': # if called without any post data, print an empty form to add a product
		if(!empty($_GET['product_id'])) break;
		if(!empty($_GET['supplier_id'])) 
		{
			$smarty->assign('product_supplier_id',$_GET['supplier_id']);
			$smarty->assign('supplier_disabled','disabled="true"');
			$smarty->assign('hidden_supplier',"<input type=\"hidden\" name=\"product_supplier_id\" value={$_GET['supplier_id']}>");
			$smarty->assign('lock_supplier',"<input type=\"hidden\" name=\"lock_supplier\" value=1>");
			}
		make_select_lists();
		$smarty->display('product_detail.tpl');
		exit();
		break;
	case 'Update Product List':
		$_POST = quote_smart($_POST);
		while(list($key,$id) = each($_POST['product_id']))
		{
			$costlabel = 'product_cost_' . $id;
			$newcost = $_POST[$costlabel];
			$availablelabel = 'product_available_' . $id;
			if(empty($_POST[$availablelabel])) $available = 0;
			else $available = $_POST[$availablelabel];
			
			update_availability($id,$available,0,$newcost);

			$query = "UPDATE product SET product_cost = {$newcost},
				product_available = {$available}
				WHERE product_id = {$id}";
			mysql_query($query);
			print mysql_error();
			}
		$smarty->assign('message','The product list has been updated');
		
	case 'Show Products':	# show products by supplier (from suppliers_detail)
		if(empty($_POST['supplier_id'])) header("location:suppliers.php");
		$query = "SELECT * FROM product WHERE product_supplier_id = {$_POST['supplier_id']} AND product_archived = 0 ORDER BY product_name";
		$products_lookup = mysql_query($query);
		print mysql_error();
		$count = 0;
		while ($p = mysql_fetch_array($products_lookup))
		{
			$products[$count]['product_id'] = $p['product_id'];
			$products[$count]['product_name'] = $p['product_name'];
			$products[$count]['product_description'] = $p['product_description'];
			$products[$count]['product_local'] = $p['product_local'];
			$products[$count]['product_organic'] = $p['product_organic'];
			$products[$count]['product_fairtrade'] = $p['product_fairtrade'];
			$products[$count]['product_cost'] = $p['product_cost'];
			$products[$count]['product_available'] = $p['product_available'];
			$count++;
			}
		$smarty->assign('products',$products);
		$supplier_lookup = mysql_query("SELECT supplier_name FROM supplier WHERE supplier_id = {$_POST['supplier_id']}");
		$smarty->assign('supplier_name',mysql_result($supplier_lookup,0));
		$smarty->assign('supplier_id',$_POST['supplier_id']);
		$smarty->display('supplier_products.tpl');
		exit();
		break;
	case 'Update Record':
			$p_id = $_POST['product_id'];
			$p_av = $_POST['product_available'];
			if(empty($p_av)) $p_av = 0;
			$newcost = quote_smart($_POST['product_cost']);
			update_availability($p_id,$p_av,0,$newcost);

			$_POST = quote_smart($_POST);
			if(empty($_POST['product_perishable'])) $_POST['product_perishable'] = 0;
			if(empty($_POST['product_local'])) $_POST['product_local'] = 0;
			if(empty($_POST['product_organic'])) $_POST['product_organic'] = 0;
			if(empty($_POST['product_fairtrade'])) $_POST['product_fairtrade'] = 0;
			
			$query = "UPDATE product SET
				product_name = {$_POST['product_name']},
				product_description = {$_POST['product_description']},
				product_category_id = {$_POST['product_category_id']},
				product_supplier_id = {$_POST['product_supplier_id']},
				product_code = {$_POST['product_code']},
				product_cost = {$_POST['product_cost']},
				product_VAT_rate = {$_POST['product_VAT_rate']},
				product_units = {$_POST['product_units']},
				product_pkg_count = {$_POST['product_pkg_count']},
				product_case_size = {$_POST['product_case_size']},
				product_allow_stock = {$_POST['product_allow_stock']},
				product_current_stock = {$_POST['product_current_stock']},
				product_perishable = {$_POST['product_perishable']},
				product_local = {$_POST['product_local']},
				product_organic = {$_POST['product_organic']},
				product_fairtrade = {$_POST['product_fairtrade']},
				product_markup = ";
				
			if($_POST['product_markup'] == "''") $query .= 'NULL,';
			else $query .= "{$_POST['product_markup']},";
			$query .= "product_available = {$p_av}
					WHERE product_id = {$_POST['product_id']}";
		mysql_query($query);
			
		$smarty->assign('message',"{$_POST['product_name']} has been updated");
		$product_id = $_POST['product_id'];
		break;
	case 'Add Product':
		if(empty($_POST['product_markup'])) $_POST['product_markup'] = NULL;
		$query = "INSERT INTO product SET
				product_name = \"{$_POST['product_name']}\",
				product_description = \"{$_POST['product_description']}\",
				product_category_id = \"{$_POST['product_category_id']}\",
				product_supplier_id = \"{$_POST['product_supplier_id']}\",
				product_code = \"{$_POST['product_code']}\",
				product_cost = \"{$_POST['product_cost']}\",
				product_VAT_rate = \"{$_POST['product_VAT_rate']}\",
				product_units = \"{$_POST['product_units']}\",
				product_pkg_count = \"{$_POST['product_pkg_count']}\",
				product_case_size = \"{$_POST['product_case_size']}\",
				product_allow_stock = \"{$_POST['product_allow_stock']}\",
				product_current_stock = \"{$_POST['product_current_stock']}\",
				product_perishable = \"{$_POST['product_perishable']}\",
				product_local = \"{$_POST['product_local']}\",
				product_organic = \"{$_POST['product_organic']}\",
				product_fairtrade = \"{$_POST['product_fairtrade']}\",
				product_default_quantity_available = NULL,
				product_markup = ";
                if(empty($_POST['product_markup'])) $query .= 'NULL,';
                else $query .= "\"{$_POST['product_markup']}\",";
		$query .= "product_available = \"{$_POST['product_available']}\"";
		mysql_query($query);
		
		$id = mysql_insert_id();
		update_availability($id,$_POST['product_available'],1,$_POST['product_cost']);
		
		$product_id = mysql_insert_id();
		$smarty->assign('message',"{$_POST['product_name']} has been added");		
		break;
	}

if(!empty($_GET['product_id'])) $product_id = $_GET['product_id'];
$product_lookup = mysql_query("SELECT * FROM product WHERE product_id = $product_id");
$p = mysql_fetch_array($product_lookup, MYSQL_ASSOC);
while(list($key,$var) = each($p))
{
	$smarty->assign("$key","$var");
	}
$smarty->assign('submit_value','Update Record');

if(!empty($_POST['lock_supplier']) || !empty($_GET['supplier_id']))
{
        $smarty->assign('supplier_disabled','disabled="true"');
        $smarty->assign('hidden_supplier',"<input type=\"hidden\" name=\"product_supplier_id\" value={$p['product_supplier_id']}>");
        $smarty->assign('lock_supplier',"<input type=\"hidden\" name=\"lock_supplier\" value=1>");
	}
	
make_select_lists();

$smarty->display('product_detail.tpl');


?>
