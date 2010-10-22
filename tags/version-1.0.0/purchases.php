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

if(empty($_POST['order_date'])) $order_date = $_SESSION['order_date'];
else $order_date = date('Y-m-d', $_POST['order_date']);


include 'header.php';
$smarty->assign('pagetitle',"{$CoopName} Purchase History");

// look for an invoice number
$invoice_lookup = mysql_query("SELECT invoice_number FROM invoice 
								WHERE invoice_member_id = {$_SESSION['member_id']}
								AND invoice_date = '{$order_date}'");
if(mysql_num_rows($invoice_lookup) == 1)
{
	$smarty->assign('invoice_number',mysql_result($invoice_lookup,0,'invoice_number'));
	$calc_use = 'order_quantity_delivered';
	}
else
{
	$smarty->assign('warning','This invoice has not yet been finalised, so cost information is presented <em>as if all orders have been delivered</em>');
	$calc_use = 'order_quantity_requested';
	}
	
// Check if we need to include VAT
$use_VAT = get_config_value('use_VAT');
$smarty->assign('use_VAT',$use_VAT);

// Now get order amounts and cost details
$query = "SELECT
		product_name, 
		order_quantity_requested, 
		order_quantity_delivered,
		ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup] )),2) AS unit_price, 
		ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * $calc_use AS order_product_total,
		product_VAT_rate 
	FROM 
		orders, 
		product 
	WHERE 
		order_product_id = product_id
	AND 
		order_date = '{$order_date}'
	AND
		order_member_id = {$_SESSION['member_id']}
	ORDER BY 
		product_name";
$orders_lookup = mysql_query($query);

$count = 0;
while($o = mysql_fetch_array($orders_lookup))
{
	$orders[$count]['product_name'] = $o['product_name'];
	$orders[$count]['requested'] = $o['order_quantity_requested'];
	$orders[$count]['delivered'] = $o['order_quantity_delivered'];
	$orders[$count]['unit_price'] = $o['unit_price'];
	$orders[$count]['product_total'] = $o['order_product_total'];
	$orders[$count]['member_id'] = $o['member_id'];
	$orders[$count]['vat_rate'] = $o['product_VAT_rate'];
	$orders[$count]['member'] = $o['order_member_id'];
	$count++;
	}

$smarty->assign('orders',$orders);
$order_total_lookup = mysql_query("SELECT sum(ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * {$calc_use}) FROM orders, product WHERE order_product_id = product_id AND order_member_id = $_SESSION[member_id] AND order_date = '{$order_date}'");
$order_total = mysql_result($order_total_lookup,0);

$smarty->assign('order_total',$order_total);

$volunteer_lookup = mysql_query("SELECT SUM(volunteer_hours) FROM volunteer WHERE volunteer_member_id = $_SESSION[member_id] AND MONTH(volunteer_date) = (MONTH('$order_date') - 1)");
$volunteer = mysql_result($volunteer_lookup,0);

$required_hours = get_config_value('volunteer_discount_hours');
$discount = get_config_value('volunteer_discount');
	
$discount_amount = round($order_total * $discount,2);

if($volunteer >= $required_hours) 
{
	$discount_rate = $discount;
	$smarty->assign('discount', -$discount_amount);
	}
else 
{
	$discount_rate = 0;
	$smarty->assign('volunteer_message', "");
	$smarty->assign('discount',"0.00");
	}

if($use_VAT)
{
// MODS DJC - $VAT_lookup = mysql_query("SELECT SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup]))),2) * {$calc_use} * (1 - $discount_rate) * product_VAT_rate as total_VAT FROM orders, product WHERE order_product_id = product_id AND order_member_id = $_SESSION[member_id] AND order_date = '{$order_date}'");	
	$VAT_lookup = mysql_query("SELECT SUM(ROUND( order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * {$calc_use} * product_VAT_rate, 2)) as total_VAT FROM orders, product WHERE order_product_id = product_id AND order_member_id = $_SESSION[member_id] AND order_date = '{$order_date}'");
	$smarty->assign("VAT_amount",mysql_result($VAT_lookup,0));
	}
else $smarty->assign('VAT_amount',0);

// the date can sometimes round down tot he previous day (not sure why) but adding an hour seems to fix it
$history_lookup = mysql_query("SELECT distinct UNIX_TIMESTAMP(order_date)+3600 AS order_date FROM orders WHERE order_member_id = {$_SESSION['member_id']}");
while($h = mysql_fetch_array($history_lookup))
{
	$history[$h['order_date']] = date('d M, Y',$h['order_date']);
	//echo date('d M, Y',$h['order_date']) . "<br>";
	}
$smarty->assign('history',$history);
//echo date('d M, Y',$_POST['order_date']) . "<br>";
$smarty->assign('selected_date',date('d M, Y',$_POST['order_date']));
$smarty->assign('order_date',$order_date);
$smarty->assign('order',$order);
$smarty->assign('use_VAT',$use_VAT);

include 'footer.php';

$smarty->display('my_purchases.tpl');

?>
