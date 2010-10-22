<?php

################################################################################
#                                                                              #
#		Filename:	post_invoices.php				       #
#		Author:		Martin Settle				       #
#               Created:	13 October 2006				       #
#		Description:	updates database			       #
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
	- 2006.10.27 file created
	- 2008-04-04 adjusted to handle pre-payment (i.e. posting of invoice only changes member_balance by ordered/delivered difference

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

//get member names
$query = "SELECT 
		member_id, 
		member_account_balance
	FROM member WHERE member_id IN 
	(SELECT DISTINCT(member_id) FROM member, orders WHERE member_id = order_member_id AND order_date = '$_SESSION[admin_date]') 
	ORDER BY member_last_name";
$member_lookup = mysql_query($query);
print mysql_error();
$count = 0;
while($m = mysql_fetch_array($member_lookup))
{
	$balance[$m['member_id']] = $m['member_account_balance'];
	$invoice[$m['member_id']] = str_replace('-','',$_SESSION['admin_date']) . str_pad($count,4,'0',STR_PAD_LEFT);
	$count++;
	}
	
// Check if we need to include VAT
$use_VAT = get_config_value('use_VAT');

if(!$use_VAT) {
	$VAT_add = '* (1 + product_VAT_rate)';
} else {
	$VAT_add = "";
}

// now look up the member order totals
// Fixing inconsistencies in invoice.php and post_invoices.php handling of rounding
// change to be the same as invoice.php
// SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * (1+product_VAT_rate),2) * order_quantity_delivered) AS order_total,
// SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) *(1+product_VAT_rate),2) * order_quantity_requested) AS order_total_paid
// and not as it was in here
// ROUND(SUM(FLOOR(100 * order_current_price $VAT_add * (1 + IFNULL(product_markup, $_SESSION[markup])))/100 * order_quantity_delivered),2) AS order_total,
// ROUND(SUM(FLOOR(100 * order_current_price $VAT_add * (1 + IFNULL(product_markup, $_SESSION[markup])))/100 * order_quantity_requested),2) AS paid_total
// amount paid is based on order_paid_price and not order_current_price which may have been changed by adjust invoice
$query = 
	"SELECT 
		order_member_id, 
		SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) $VAT_add, 2) * order_quantity_delivered) AS order_total,
		SUM(ROUND(order_paid_price * (1 + IFNULL(product_markup, $_SESSION[markup])) $VAT_add, 2) * order_quantity_requested) AS paid_total
	FROM 
		orders, 
		product 
	WHERE 
		order_product_id = product_id 
	AND 
		order_date = '$_SESSION[admin_date]'
	GROUP BY order_member_id";
$totals_lookup = mysql_query($query);
while($m = mysql_fetch_array($totals_lookup))
{
	$gross[$m['order_member_id']] = $m['order_total'];
	$paid[$m['order_member_id']] = $m['paid_total'];
	}

// look up volunteer discounts
$required_hours = get_config_value('volunteer_discount_hours');
$discount =  get_config_value('volunteer_discount');

// build array of members who qualify for volunteer discount
$query = "SELECT 
		volunteer_member_id, 
		SUM(volunteer_hours) AS hours 
	FROM 
		volunteer 
	WHERE 
		MONTH(volunteer_date) = (MONTH('$_SESSION[admin_date]') - 1) 
	GROUP BY 
		volunteer_member_id";
$volunteers_lookup = mysql_query($query);
while($v = mysql_fetch_array($volunteers_lookup))
{
	if($v['hours'] >= $required_hours) $volunteers[] = $v['volunteer_member_id'];
	}
if(!empty($volunteers))
{
	foreach($volunteers AS $vol)
	{
		$v_discount[$m['volunteer_member_id']] = round($discount * $gross[$vol],2);
		$discount_rate[$m['volunteer_member_id']] = $discount;
		}
	}

// look up VAT totals, if required
// amount paid is based on order_paid_price and not order_current_price which may have been changed by adjust invoice
if($use_VAT)
{
	$query = "SELECT
			order_member_id, 
			SUM(ROUND(order_paid_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * order_quantity_requested * product_VAT_rate,2)) as total_VAT_paid,
			SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * order_quantity_delivered * product_VAT_rate,2)) as total_VAT	
		FROM 
			orders, 
			product 
		WHERE 
			order_product_id = product_id 
		AND 
			order_date = '$_SESSION[admin_date]'
		GROUP BY
			order_member_id";
	$VAT_lookup = mysql_query($query);
	while($v = mysql_fetch_array($VAT_lookup))
	{
		$VAT[$v['order_member_id']] = round($v['total_VAT'] * (1 - $discount_rate[$v['order_member_id']]),2);
		$VAT_paid[$v['order_member_id']] = round($v['total_VAT_paid']* (1 - $discount_rate[$v['order_member_id']]),2);
		}
	}

// now calculate the invoice totals and insert into database

foreach($balance AS $id => $current_balance)
{
	$total = $gross[$id] - $v_discount[$id] + $VAT[$id];
	$total_paid = $paid[$id] + $VAT_paid[$id];
	$newbalance = $current_balance + $total - $total_paid;
	
	/*
	echo "gross: " . $gross[$id] . "<br/>";
	echo "VAT: " . $VAT[$id] . "<br/>";	
	echo "Discount: " . $v_discount[$id] . "<br/>";
	echo "total: " . $total . "<br/>";
	echo "paid: " . $paid[$id] . "<br/>";
	echo "VAT_paid: " . $VAT_paid[$id] . "<br/>";
	echo "total_paid: " . $total_paid . "<br/>";
	echo "current_balance: " . $current_balance . "<br/>";
	echo "newbalance: " . $newbalance . "<br/>";
	exit(0);
	*/

	$invoice_query = "INSERT INTO invoice 
				SET 
				invoice_member_id = $id,
				invoice_date = '$_SESSION[admin_date]',
				invoice_total = $total,
				invoice_VAT = {$VAT[$id]},
				invoice_number = '$invoice[$id]'";
	if(!mysql_query($invoice_query)) {
		print ("<strong><em>Invoice number $invoice[$id] failed to register.</strong></em></br>\n" . mysql_error());
	}
	
	// Save a record of the change to the member balance
	$memberlookup = mysql_query("SELECT member_account_balance as balance FROM member WHERE member_active = 1 AND member_id = " . $id);
	if ($member = mysql_fetch_array($memberlookup)) {
		if (!mysql_query("INSERT INTO balance_update (comment, member_id, old_balance, new_balance) VALUES ('post_invoices: total $total  paid $total_paid', " . $id . ", " . $member['balance'] . ", " . $newbalance . ")")) print mysql_error();		
	}
	
	$balance_update = "UPDATE member
			SET	member_account_balance = $newbalance
			WHERE member_id = $id";
	
	if(!mysql_query($balance_update)) {
		print ("<strong><em>The system failed to update the balance of member $id.</strong></em></br>\n");
	}	
}



header("location: invoice.php");

?>
