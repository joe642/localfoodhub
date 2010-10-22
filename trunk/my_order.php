<?php

################################################################################
#                                                                              #
#		Filename:	my_order.php				       #
#		Author:		Martin Settle				       #
#               Created:	4 Sept 2006				       #
#		Description:	Shows current order			       #
#		Calls:		config.php				       #
#		Called by:	process_order.php			       #
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
	- 2006.09.04 file created
	- 2008.03.12 modified to use temp_orders

*/

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

include 'header.php';
$smarty->assign('pagetitle',"{$CoopName} Shopping Basket");

# Check if we need to include VAT
$use_VAT = get_config_value('use_VAT');

if(!$use_VAT) $VAT_add = '* (1 + product_VAT_rate)';
else $get_VAT_rate = ', product_VAT_rate';


$order_lookup = mysql_query("SELECT product_id, product_name, product_units,
							order_quantity_requested, 
							ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) AS unit_price,
							ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * order_quantity_requested AS order_product_total,
							product_VAT_rate 
							FROM temp_orders, product 
							WHERE order_product_id = product_id 
							AND order_member_id = $_SESSION[member_id] 
							AND order_quantity_requested != 0
							AND  order_date = '$_SESSION[order_date]' ORDER BY product_supplier_id, product_name");
print mysql_error();
$count = 0;
while($orders = mysql_fetch_array($order_lookup,MYSQL_ASSOC))
{
	foreach($orders AS $key => $value)
	{
		$order[$count][$key] = $value;
		}
	$recurring_lookup = mysql_query("SELECT recurring_id FROM recurring WHERE recurring_member_id = $_SESSION[member_id] AND recurring_product_id = $orders[product_id]");
	if(mysql_num_rows($recurring_lookup) > 0)
	{
		$order[$count]['id_type'] = 'recurring_id';
		$order[$count]['id'] = mysql_result($recurring_lookup,0);
		$order[$count]['recurring_process'] = 'Edit';
		$order[$count]['recurring_type'] = 'Edit Recurring';
		}
	else
	{
		$order[$count]['id_type'] = 'product_id';
		$order[$count]['id'] = $orders['product_id'];
		$order[$count]['recurring_process'] = 'Add';
		$order[$count]['recurring_type'] = 'Make Recurring';
		}
		
	$count++;
	}

$order_total_query ="SELECT SUM(ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * order_quantity_requested) 
							FROM temp_orders, product 
							WHERE order_product_id = product_id 
							AND order_member_id = $_SESSION[member_id] 
							AND order_date = '$_SESSION[order_date]'";
$order_total_lookup = mysql_query($order_total_query);
$order_total = mysql_result($order_total_lookup,0);
//echo $order_total_query . "<br>";

$smarty->assign('order_total',$order_total);

$volunteer_lookup = mysql_query("SELECT SUM(volunteer_hours) FROM volunteer WHERE volunteer_member_id = $_SESSION[member_id] AND MONTH(volunteer_date) = (MONTH('$_SESSION[order_date]') - 1)");
$volunteer = mysql_result($volunteer_lookup,0);

$required_hours = get_config_value('volunteer_discount_hours');
$discount = get_config_value('volunteer_discount');

$discount_amount = round($order_total * $discount,2);



if($volunteer >= $required_hours) $smarty->assign('discount', -$discount_amount);
else 
{
	$smarty->assign('volunteer_message', "");
	$smarty->assign('discount',"0.00");
	$discount = 0;
	}


if($use_VAT)
{
	$VAT_lookup = mysql_query("SELECT SUM(ROUND( order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * order_quantity_requested  * product_VAT_rate,2)) as total_VAT FROM temp_orders, product WHERE order_product_id = product_id AND order_member_id = $_SESSION[member_id] AND order_date = '$_SESSION[order_date]'");
	$smarty->assign("VAT_amount",mysql_result($VAT_lookup,0));
	}
else $smarty->assign('VAT_amount',0);

$balance_lookup = mysql_query("SELECT member_account_balance FROM member WHERE member_id = {$_SESSION['member_id']}");
$balance = mysql_result($balance_lookup,0,'member_account_balance');
// MODS DJC - $outstanding_orders_lookup = mysql_query("SELECT SUM(ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])), 2) * order_quantity_requested) AS order_total)
// MODS DJC - what is outstanding_balance? - it just seems to confuse the shopping basket screen by applying Opening Balance to old temp_orders
/*$outstanding_orders_lookup = mysql_query("SELECT SUM(ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])), 2) * order_quantity_requested) AS order_total
										FROM temp_orders, product 
										WHERE 
											order_product_id = product_id 
											AND order_member_id = {$_SESSION['member_id']}
											AND order_date != '{$_SESSION['order_date']}'");
$outstanding_balance = mysql_result($outstanding_orders_lookup,0,'order_total');*/
$outstanding_balance = 0;
$smarty->assign('opening_balance', $balance + $outstanding_balance);

$smarty->assign('order_date',$_SESSION['order_date']);
$smarty->assign('order',$order);
$smarty->assign('use_VAT',$use_VAT);

include 'footer.php';

$smarty->display('my_order.tpl');

?>
