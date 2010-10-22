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

function send_member_order_email($template, $member_name, $membership_num, $email, $date, $order) {

	$html = file_get_contents($template);
	$html = str_replace('{contact}', $member_name, $html);
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
	  $mail->AddAddress($email, $member_name);
	  $mail->AddAddress($manager_email, $foodclub_name . ' Manager'); // send copy to the manager as well
	  $mail->Subject = $foodclub_name . ' Order: ' . $member_name . ', ' . date('D jS M Y', strtotime($date));
	  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer'; // optional - MsgHTML will create an alternate automatically
	  $mail->MsgHTML($html);
	  $mail->AddAttachment('../images/logo.png');      // attachment
	  $mail->Send();
	  $response = "Order sent to " . $member_name . " at " . $email;
	} catch (phpmailerException $e) {
	  $response = $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
	  $response = $e->getMessage(); //Boring error messages from anything else!
	}
	return ($response);
}


function email_member_orders($members, $orders) {
$response = "";

	foreach ($members as $m) {
		$order = "<p align='left'><strong>" . $m['member_name'] . ": " . $m['membership_num'] . "</strong></p>";
		$table_header = "<table cellpadding='3'><tr><th align='left' width='65%'>Product</th><th align='left' width='18%'>Unit</th><th align='right'>&nbsp;&nbsp;Qty</th></tr>";
		$order .= $table_header;

		foreach($orders as $o) {
			if ($o['member_id'] == $m['member_id']) {
				$order .= "<tr><td>" . $o['product_name'] . ": " . $o['product_description'] . "</td><td>" . $o['units'] . "</td><td align='right'>" . number_format($o['requested'],0) . "</td></tr>";
			}
		}

		$order .= "</table>";
		
		// send the email
		$response .= send_member_order_email('member_order_email.html', $m['member_name'], $m['membership_num'], $m['member_email'], $_SESSION['admin_date'], $order);	
		$response .= "<br/>";			
	}
	return ($response);
}


switch($_POST['select_by'])
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
			AND order_date = '$_SESSION[admin_date]' 
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
		$query = "SELECT DISTINCT(product_name), product_description, product_units from product, orders WHERE product_id = order_product_id AND order_date = '$_SESSION[admin_date]' AND product_supplier_id = $_POST[supplier_id] ORDER BY product_name";
		$p = mysql_query($query);
		while($prod = mysql_fetch_array($p))
		{
			$products[] = $prod['product_name'];
			$units[] = $prod['product_units'];
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
		$smarty->assign('units',$units);
		$smarty->assign('description',$description);
		$smarty->assign('sorting',$sorting);

		$smarty->display('sort_supplier.tpl');
		exit();
		
	case 'customer':
		// get ordering member names
		$query = "SELECT member_id, membership_num, member_email, CONCAT(member_first_name,' ',member_last_name) AS member_name FROM member WHERE member_id IN (SELECT DISTINCT(member_id) FROM member,orders WHERE member_id = order_member_id AND order_date = '$_SESSION[admin_date]') ORDER BY member_last_name";
		$members_lookup = mysql_query($query);
		$count = 0;
		while($mem = mysql_fetch_array($members_lookup))
		{
			$members[$count]['member_id'] = $mem['member_id'];
		    $members[$count]['member_name'] = $mem['member_name'];
		    $members[$count]['membership_num'] = $mem['membership_num'];
			$members[$count]['member_email'] = $mem['member_email'];						
			$count++;
		}
		
		$smarty->assign('members',$members);
		
		// get products

		$query = "SELECT product_name, product_description, product_units, order_member_id, IFNULL(order_quantity_delivered,'---') AS delivered, order_quantity_requested AS requested 
			FROM orders,product
			WHERE order_product_id = product_id
			AND order_date = '$_SESSION[admin_date]'
			ORDER BY product_name";
		$orders_lookup = mysql_query($query);
		$count = 0;
		while($ord = mysql_fetch_array($orders_lookup))	{
			$orders[$count]['member_id'] = $ord['order_member_id'];
			$orders[$count]['product_name'] = $ord['product_name'];
			$orders[$count]['product_description'] = $ord['product_description'];
			$orders[$count]['units'] = $ord['product_units'];
			$orders[$count]['requested'] = $ord['requested'];
			$orders[$count]['delivered'] = ($ord['delivered']=='---')?'---':round($ord['delivered'],0);
			$count++;
		}

		$smarty->assign('orders',$orders);
		
		if (isset($_POST['email_orders'])) {
			$response = email_member_orders($members, $orders);
			$smarty->assign('email_response', "<h3>Orders emailed as follows:</h3>" . $response);	
		} else {
			$smarty->assign('email_response',"No email's were sent.");
		}
		

		$smarty->display('sort_customers.tpl');
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
