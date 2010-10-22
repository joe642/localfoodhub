<?php

################################################################################
#                                                                              #
#		Filename:	invoice.php				       #
#		Author:		Martin Settle				       #
#               Created:	13 October 2006				       #
#		Description:	print and post order invoices		       #
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
	- 2008.04.04 adjusted for pre-payments recieved

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

if(!empty($_POST['print_invoice']) || !empty($_GET['update']))
{
	//get member names
	$query = "SELECT 
			member_id, 
			membership_num,
			CONCAT(member_first_name,' ',member_last_name) AS member_name,
			member_address1,
			member_address2,
			member_address3,
			member_town,
			member_county,
			member_postcode,
			member_account_balance
		FROM member WHERE member_id IN 
		(SELECT DISTINCT(member_id) FROM member, orders WHERE member_id = order_member_id AND order_date = '$_SESSION[admin_date]') 
		ORDER BY member_last_name";
	$member_lookup = mysql_query($query);
	print mysql_error();
	$count = 0;
	while($m = mysql_fetch_array($member_lookup))
	{

		$members[$count]['member_id'] = $m['member_id'];
		$members[$count]['membership_num'] = $m['membership_num'];
		$members[$count]['member_name'] = $m['member_name'];
		$members[$count]['member_address1'] = $m['member_address1'];
		$members[$count]['member_address2'] = $m['member_address2'];
		$members[$count]['member_address3'] = $m['member_address3'];
		$members[$count]['member_town'] = $m['member_town'];
		$members[$count]['member_county'] = $m['member_county'];
		$members[$count]['member_postcode'] = $m['member_postcode'];
		$members[$count]['member_balance'] = $m['member_account_balance'];
		$members[$count]['invoice_number'] = str_replace('-','',$_SESSION['admin_date']) . str_pad($count,4,'0',STR_PAD_LEFT);
		// build a reverse array referencing member_id to use later
		$memb_id[$m['member_id']] = $count;
		$count++;
		}
		
	// Check if we need to include VAT
	$use_VAT = get_config_value('use_VAT');
	$smarty->assign('use_VAT',$use_VAT);

	// Now get order amounts and cost details
	$query = "SELECT
			order_member_id,
			product_name, 
			product_units,
			order_quantity_requested, 
			order_quantity_delivered,
			ROUND(order_current_price * (1+product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup] )),2) AS unit_price, 
			ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup, $_SESSION[markup])),2) * order_quantity_delivered AS order_product_total,
			product_VAT_rate 
		FROM 
			orders, 
			product 
		WHERE 
			order_product_id = product_id
		AND 
			order_date = '$_SESSION[admin_date]'
		AND
			order_quantity_requested != 0 
		ORDER BY 
			product_name";
	$orders_lookup = mysql_query($query);

	$count = 0;
	while($o = mysql_fetch_array($orders_lookup))
	{
		$orders[$count]['member_id'] = $o['order_member_id'];
		$orders[$count]['product_name'] = $o['product_name'];
		$orders[$count]['product_units'] = $o['product_units'];
		$orders[$count]['requested'] = $o['order_quantity_requested'];
		$orders[$count]['delivered'] = $o['order_quantity_delivered'];
		$orders[$count]['unit_price'] = $o['unit_price'];
		$orders[$count]['product_total'] = $o['order_product_total'];
		$orders[$count]['member_id'] = $o['member_id'];
		$orders[$count]['vat_rate'] = $o['vat_rate'];
		$orders[$count]['member'] = $o['order_member_id'];
		$count++;
		}

	$smarty->assign('orders',$orders);

	// now look up the member order totals -- total paid should equal the amount requested
	// amount paid is based on order_paid_price and not order_current_price which may have been changed by adjust invoice
	$query = "SELECT 
			order_member_id, 
			SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * (1+product_VAT_rate),2) * order_quantity_delivered) AS order_total,
			SUM(ROUND(order_paid_price * (1 + IFNULL(product_markup, $_SESSION[markup])) *(1+product_VAT_rate),2) * order_quantity_requested) AS order_total_paid
		FROM 
			orders, 
			product 
		WHERE 
			order_product_id = product_id 
		AND 
			order_date = '$_SESSION[admin_date]'
		GROUP BY order_member_id";
	$totals_lookup = mysql_query($query);
	$count = 0;
	while($m = mysql_fetch_array($totals_lookup))
	{
		$member_count = $memb_id[$m['order_member_id']];
		$members[$member_count]['gross'] = $m['order_total'];
		$members[$member_count]['paid'] = $m['order_total_paid'];
		$gross[$m['order_member_id']] = $m['order_total'];
		$paid[$m['order_member_id']] = $m['order_total_paid'];
		$count++;
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
	$count = 0;
	if(!empty($volunteers))
	{
		foreach($volunteers AS $vol)
		{
			$member_count = $memb_id[$vol];
			$members[$member_count]['percent_discount'] = $discount;
			$members[$member_count]['discount'] = round($discount * $gross[$vol],2);
			$members[$member_count]['discount_taken'] = round($discount * $paid[$vol],2);
			$count++;
			}
		}


	// look up VAT totals, if required
	if($use_VAT)
	{
		$query = "SELECT
				order_member_id, 
				SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * product_VAT_rate,2) * order_quantity_delivered) as total_VAT,
				SUM(ROUND(order_current_price * (1 + IFNULL(product_markup, $_SESSION[markup])) * product_VAT_rate,2) * order_quantity_requested) as total_VAT_paid 
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
			$membercount = $memb_id[$v['order_member_id']];
			$members[$membercount]['vat'] = round($v['total_VAT'] * (1-$members[$membercount]['percent_discount']),2);
			$members[$membercount]['vat_paid'] = round($v['total_VAT_paid'] * (1-$members[$membercount]['percent_discount']),2);
			}
		}

	// TODO: fix current balance lookup if later invoices are posted
	// now calculate the invoice total

	foreach($memb_id AS $id => $count)
	{
		$members[$count]['total'] = $members[$count]['gross'] - $members[$count]['discount'];
		$members[$count]['amount_paid'] = $members[$count]['paid'] - $members[$count]['discount_taken'];
		//$members[$count]['current_balance'] = $members[$count]['member_balance'];
		$members[$count]['current_balance'] = $members[$count]['member_balance'] - $members[$count]['amount_paid'] + $members[$count]['total'];
		//$members[$count]['current_balance'] = $members[$count]['member_balance'] + $members[$count]['total'];
		}
	

	$smarty->assign('members',$members);

	// send config variables
	
	$smarty->assign('logo',"$BaseURL/images/$LogoFile");
	$smarty->assign('date',$_SESSION['admin_date']);

	// Check to see if the invoices have been posted

	$query = "SELECT invoice_id FROM invoice WHERE invoice_date = '$_SESSION[admin_date]'";
	$invoices_lookup = mysql_query($query);
	if(mysql_num_rows($invoices_lookup) > 0) {$smarty->assign('invoices_posted','TRUE');}

	// send the closing messages for the print invoice
	$disc = $discount * 100;
	$smarty->assign('volunteer_message',$Message['volunteer_message']);
	$smarty->assign('invoice_message',$Message['invoice_message']);
	
	// add the nor_recieved control
	$smarty->assign('not_recieved',$_POST['not_recieved']);
	
	include '../header.php';
	include '../footer.php';

	
	$smarty->display('invoices.tpl');
	exit();
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


include '../header.php';
include '../footer.php';

$message = '<p>Please ensure that all deliveries have been recorded.</p>';

if ($not_recieved > 0) {
	$message .= "<p><strong>Warning following producers goods have not yet been recieved: </strong><br/>";
	for ($i = 0; $i < $not_recieved; $i++) {
		$message .= $supplier_not_recieved[$i]  . "<br/>";
	}
	$message .= "</p>";
}

$message .= '<p>If any supplier order remains outstanding and products have been distributed, please use the "Receive Orders" function to record "0" (zero) as the delivery amount.</p>
<form action="invoice.php" method="POST">';
$message .= '<input type="hidden" name="not_recieved" value="' . $not_recieved . '">';
$message .= '<input type="submit" name="print_invoice" value="Print Invoices"></form>';

$smarty->assign('no_received',$not_recieved);

$smarty->assign('body_text',$message);

$smarty->display('index.tpl');

?>
