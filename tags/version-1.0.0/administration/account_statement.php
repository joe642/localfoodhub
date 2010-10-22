<?php

################################################################################
#                                                                              #
#		Filename:	account_statement.php			       #
#		Author:		Martin Settle				       #
#               Created:	2 Oct 2006				       #
#		Description:	admin reset members password		       #
#
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
	- 2006.10.02 file created
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

if(empty($_POST['member_id'])) {
	// Show a select form for member id
	$smarty->assign('pagetitle','Select Member');
	include '../header.php';
	$message = '<h2>Account Statement</h2><p>Please select the member for whom you wish to view an account statement</p>';			
	$smarty->assign('message',$message);
	
	$memberslookup = mysql_query("SELECT member_id, CONCAT(member_first_name,' ',member_last_name) AS name FROM member WHERE member_active = 1 ORDER BY name");
	while($member = mysql_fetch_array($memberslookup))
	{
		$members[$member['member_id']] = $member['name'];
		}
	$smarty->assign('members',$members);
	
	include '../footer.php';
	
	$smarty->display('member_select.tpl');
	exit();
}

// If there's a new account balance set that before displaying the member statement
if (!empty($_POST['acct_balance'])) {
	$memberlookup = mysql_query("SELECT member_account_balance as balance FROM member WHERE member_active = 1 AND member_id = " . $_POST['member_id']);
	if ($member = mysql_fetch_array($memberlookup)) {
		if (!mysql_query("INSERT INTO balance_update (comment, member_id, old_balance, new_balance) VALUES ('Statement - manual balance adjustment', " . $_POST['member_id'] . ", " . $member['balance'] . ", " . $_POST['acct_balance'] . ")")) print mysql_error();
		if (!mysql_query("UPDATE member SET member_account_balance = " . $_POST['acct_balance'] . " WHERE member_id = " . $_POST['member_id'] . " LIMIT 1")) print mysql_error();
	}
}


// get the includes out of the way
include '../header.php';
include '../footer.php';

// we have a member ID so get the transaction data

$transactions_lookup = mysql_query("SELECT transactions.* FROM
									(SELECT invoice_member_id AS member_id, 
									invoice_date AS trans_date, 
									invoice_number AS reference, 
									invoice_total AS amount 
									FROM invoice 
									UNION 
									SELECT credit_member_id AS member_id, 
									credit_date as trans_date, 
									credit_reference as reference,
									0 - credit_amount AS amount 
									FROM credit) 
								AS transactions 
								WHERE member_id = {$_POST['member_id']} 
								ORDER BY trans_date");
if(mysql_num_rows($transactions_lookup) == 0)
{
	$smarty->assign('body_text','There are no transactions on record for this member');
	$smarty->display('index.tpl');
	exit();
	}

$balance = 0;
$count = 0;

while($t = mysql_fetch_array($transactions_lookup))
{
	$trans[$count]['date'] = $t['trans_date'];
	$trans[$count]['reference'] = $t['reference'];
	if($t['amount'] > 0) $trans[$count]['debit'] = $t['amount'];
	else $trans[$count]['credit'] = $t['amount'];
	$balance = $balance + $t['amount'];
	$trans[$count]['balance'] = $balance;
	$count++;
	}

$smarty->assign('trans', $trans);

$member_lookup = mysql_query("SELECT * FROM member WHERE member_id = {$_POST['member_id']}");
$m = mysql_fetch_array($member_lookup,MYSQL_ASSOC);

$smarty->assign('member',$m);

$recent_payment = "0.00";

$invoices = mysql_query("SELECT invoice_member_id, invoice_date FROM invoice WHERE invoice_member_id = {$_POST['member_id']} ORDER BY invoice_date DESC");
if ($latest_invoice = mysql_fetch_array($invoices)) {
	$last_invoice_date = $latest_invoice['invoice_date'];
	// Check if we need to include VAT
	$use_VAT = get_config_value('use_VAT');

	if(!$use_VAT) {
		$VAT_add = '* (1 + product_VAT_rate)';
	} else {
		$VAT_add = "";
	}
	
	$new_orders_total = mysql_query("SELECT 
		order_member_id, 
		SUM(ROUND(order_paid_price * (1 + IFNULL(product_markup, $_SESSION[markup])) $VAT_add, 2) * order_quantity_requested) AS paid_total
	FROM 
		orders, 
		product 
	WHERE 
		order_member_id = {$_POST['member_id']}
	AND 
		order_date > '{$last_invoice_date}' 
	AND
		order_product_id = product_id");
	if ($new_total = mysql_fetch_array($new_orders_total)) { 
		$recent_payment = $new_total['paid_total'];
	} 
}
$smarty->assign('recent_payment', $recent_payment);
$smarty->assign('balance', round($balance + $recent_payment, 2));
$smarty->assign('date',date('Y-m-d'));
$smarty->assign('pagetitle','Member Account Statement');
$smarty->assign('logo',"$BaseURL/images/$LogoFile");

$smarty->display('member_statement.tpl');

?>
