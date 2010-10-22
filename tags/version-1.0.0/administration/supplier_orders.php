<?php

################################################################################
#                                                                              #
#		Filename:	supplier_orders.php			       #
#		Author:		Martin Settle				       #
#               Created:	15 September 2006			       #
#		Description:	compile supplier orders (and e-mail)	       #
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
	- 2008.03.11 process to purchase order table

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

if(empty($_POST['supplier_id'])) {
	$smarty->assign('pagetitle','Supplier Purchase Orders');
	
	include '../header.php';
	$query = "SELECT supplier_id, supplier_name 
				FROM supplier, orders, product 
				WHERE orders.order_product_id = product.product_id 
				AND product.product_supplier_id = supplier.supplier_id 
				AND orders.order_date = '$_SESSION[admin_date]'";

	$supplierlookup = mysql_query($query);
	print mysql_error();

	while($supply = mysql_fetch_array($supplierlookup))
	{
		$supplier[$supply['supplier_id']] = $supply["supplier_name"];
		}	

	$smarty->assign('suppliers',$supplier);

	include '../footer.php';

	$smarty->display('supplier_form.tpl');

	exit();
}

$smarty->assign('pagetitle',"Purchase Orders");
include '../header.php';

if($_POST['supplier_id'] != 'ALL')
{
	$filter = "AND supplier_id = $_POST[supplier_id]";
	$filter2 = "AND product_supplier_id = $_POST[supplier_id]";
	}
	
	
$query = "SELECT 
		supplier_name, 
		supplier_email,
		supplier_contact_name,
		product_name,
		product_units,
	    product_code,	
		product_cost,
		order_current_price,
		product_VAT_rate,
		product_case_size,
		COUNT(order_quantity_requested) AS number_orders,
		SUM(order_quantity_requested) AS request,
		IF(product_pkg_count < 1,SUM(order_quantity_requested),IF(SUM(order_quantity_requested)<product_current_stock,0,(FLOOR((SUM(order_quantity_requested) - product_current_stock + product_allow_stock)/product_pkg_count) * product_pkg_count/product_case_size))) AS units_order, 
		IF(product_case_size>1,' cases',product_units) AS cases
	FROM 
		product, 
		supplier, 
		orders 
	WHERE 
		order_quantity_requested > 0
	AND
		order_product_id = product_id 
	AND 
		product_supplier_id = supplier_id 
	AND 
		order_date = '$_SESSION[admin_date]'
	$filter
	GROUP BY 
		supplier_name, 
		product_name,
		product_units,
	        product_code,
		product_cost,
		product_VAT_rate,
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
$Total_Cost = 0;
$Total_VAT = 0;
while($orderslist = mysql_fetch_array($orderlookup)) {
	$supplier[$count]['supplier_name'] = $orderslist['supplier_name'];
	$supplier[$count]['contact_name'] = $orderslist['supplier_contact_name'];
	$supplier[$count]['supplier_email'] = $orderslist['supplier_email'];
	$supplier[$count]['product_name'] = $orderslist['product_name'];
	$supplier[$count]['product_code'] = $orderslist['product_code'];
	$supplier[$count]['product_cost'] = $orderslist['product_cost'];
	$supplier[$count]['product_units'] = $orderslist['product_units'];
	$supplier[$count]['order_current_price'] = $orderslist['order_current_price'];
	$supplier[$count]['number_orders'] = $orderslist['number_orders'];
	$supplier[$count]['request'] = $orderslist['request'];
	$supplier[$count]['units'] = round($orderslist['units_order'],2);
	$supplier[$count]['cases'] = $orderslist['cases'];
	$cost = round($orderslist['order_current_price'],2) * $orderslist['product_case_size'] * $orderslist['units_order'];
	$supplier[$count]['cost'] = $cost;
	$Total_Cost += $cost;
	$VAT = round($orderslist['product_cost'] * $orderslist['product_case_size'] * $orderslist['units_order'] * $orderslist['product_VAT_rate'],2);
	$supplier[$count]['VAT'] = $VAT;
	$Total_VAT += $VAT;
	$count++;
}

function send_supplier_email($template, $contact, $supplier, $email, $date, $order) {

	$html = file_get_contents($template);
	$html = str_replace('{contact}', $contact, $html);
	$html = str_replace('{supplier}', $supplier, $html);
	$html = str_replace('{date}', date('D jS M Y',strtotime($date)), $html);
	$html = str_replace('{order}', $order, $html);	
	
	require_once '../thirdparty/phpmailer/class.phpmailer.php';
	
	$mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
	
	try {
	  $manager_email = get_config_value("manager_email");
	  $orders_from_email = get_config_value("orders_from_email");
	  $foodclub_name = get_config_value("foodclub_name");
	  $mail->AddReplyTo($orders_from_email, $foodclub_name);
	  $mail->SetFrom($orders_from_email, $foodclub_name);
	  $mail->AddAddress($email, $contact);
	  $mail->AddAddress($manager_email, $foodclub_name . ' Manager'); // send copy to the manager as well
	  $mail->Subject = $foodclub_name . ' Purchase Order: ' . $supplier . ", " . date('D jS M Y', strtotime($date));
	  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer'; // optional - MsgHTML will create an alternate automatically
	  $mail->MsgHTML($html);
	  $mail->AddAttachment('../images/logo.png');      // attachment
	  $mail->Send();
	  $response = "Order sent to " . $supplier . " at " . $email;
	} catch (phpmailerException $e) {
	  $response = $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  $response = $e->getMessage(); //Boring error messages from anything else!
	}
	return ($response);
}


function email_supplier_orders($supplier) {
$supplier_name = $supplier[0]['supplier_name'];
$contact_name = $supplier[0]['contact_name'];
$supplier_email = $supplier[0]['supplier_email'];
$supplier[sizeof($supplier)]['supplier_name'] = "END OF SUPPLIERS"; // terminate the array
$order = "";
$total = 0;
$VAT = 0;
$table_header = "<table cellpadding='3'><tr><th align='left'>Code</th><th align='left' width='35%'>Product</th><th align='left' width='15%'>Unit</th><th align='right'>Unit&nbsp;Cost</th><th align='right'>&nbsp;&nbsp;Qty</th><th align='right'>&nbsp;&nbsp;Sub-Total</th><th align='right'>&nbsp;&nbsp;VAT</th></tr>";
$order = $table_header;
$response = "";

	foreach ($supplier as $s) {
		if ($s['supplier_name'] != $supplier_name) {
			$order .= "<tr>
						<th colspan='5' align='right'>Net Total </th>
						<td align='right'>" . number_format($total, 2) . "</td>
						<td >&nbsp;</td>
						</tr>
						<tr>
								<th colspan='5' align='right'>VAT</th>
								<td align='right'>" . number_format($VAT, 2) . "</td>
								<td >&nbsp;</td>
						</tr>
						<tr>
								<th colspan='5' align='right'>Total</th>
								<td align='right'>" . number_format($total + $VAT, 2) . "</td>
								<td >&nbsp;</td>
						</tr></table>";
				
			// send the email
			$response .= send_supplier_email('supplier_order_email.html', $contact_name, $supplier_name, $supplier_email, $_SESSION['admin_date'], $order);	
			$response .= "<br/>";
			
			// initialise for next email
			$supplier_name = $s['supplier_name'];
			$contact_name = $s['contact_name'];
			$supplier_email = $s['supplier_email'];
			$order = $table_header;
			$total = 0;
			$VAT = 0;
		}
		$order .= "<tr><td>" . $s['product_code'] . "</td><td>" . $s['product_name'] . "</td><td>" . $s['product_units'] . "</td><td align='right'>" . number_format($s['product_cost'], 2) . "</td><td align='right'>" . $s['units'] . "</td><td align='right'>" . number_format($s['cost'], 2) . "</td><td align='right'>" . number_format($s['VAT'], 2) . "</td></tr>";
		$total = $total + $s['cost'];
		$VAT = $VAT + $s['VAT'];
	}
	return ($response);
}


if (isset($_POST['email_orders'])) {
	$response = email_supplier_orders($supplier);
	$smarty->assign('email_response', "<h3>Orders emailed as follows:</h3>" . $response);	
} else {
	$smarty->assign('email_response',"No email's were sent.");
}

$smarty->assign('products',$supplier);
/*
$query = ("SELECT 
	ROUND(FLOOR(SUM(product_cost * order_quantity_requested)*100)/100,2) AS cost, 
	ROUND(FLOOR(SUM(product_cost * product_VAT_rate * order_quantity_requested)*100)/100,2) AS vat 
	FROM orders, product 
	WHERE order_product_id = product_id 
	AND order_date = '$_SESSION[admin_date]' 
	$filter2");

$totals_lookup = mysql_query($query);

$totals = mysql_fetch_array($totals_lookup);

$smarty->assign('total_cost',$totals['cost']);
$smarty->assign('total_VAT',$totals['vat']);
*/

$smarty->assign('total_cost',$Total_Cost);
$smarty->assign('total_VAT',$Total_VAT);

include '../footer.php';

$smarty->display('purchase_order.tpl');

?>
