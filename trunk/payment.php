<?php

################################################################################
#                                                                              #
#		Filename:		paymentphp				#
#		Author:		Martin Settle				       #
#               Created:		19 May 2007				       #
#		Description:	processes orders on payment		       #
#		Calls:		config.php	, payment_functions.php			       #
#		Called by:	self, process_paypal				       #
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
	- 2006.05.19 file created
	-

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
	
include 'payment_functions.php';

include 'header.php';
include 'footer.php';

if (isset($_GET['function'])) {
	switch($_GET['function']) {
		case 'cancelled':
			$body_text = '<p>You have cancelled your online payment and <b>your order has not been completed</b>.</p><p>To complete your order please <a href="my_order.php">return to your shopping basket</a> and make an online payment, bank transfer or send a cheque to the food hub office before the closing date.</p>';
			$smarty->assign('body_text',$body_text);
			$smarty->display('index.tpl');
			exit();
			break;
		case 'success':
			$body_text = "<p>Your payment has been made by PayPal and your order will be processed automatically.</p>
			<p>Don't worry if the payments page looks like you still need to pay. As long as you've got an email from PayPal that confirms your payment then we will process it and complete you order as soon as possible.</p>
			<p>You will receive a confirmation email from us a couple of days before you need to pick up your order. </p><p>Thank you for purchasing through $CoopName</p>";
			$smarty->assign('body_text',$body_text);
			$smarty->display('index.tpl');
			exit();
			break;
	}
}

// MODS DJC Jan 2010: check balance and if it's got some in then process as much of the order as possible
// opening_balance as $_POST['balance'] is now passed in from my_order.tpl
$opening_balance = $_POST['balance'];
$balance = $_POST['balance'];
$partpaid = "Your current balance is &pound;" . number_format(- $opening_balance, 2) . " - this is not enough to pay for any of the items in your order.";
		
if($_POST['payment_amount'] <= 0 ||  $opening_balance <= -0.01) {
	receive_payment($_SESSION['member_id'],0,'');
	$balance_lookup = mysql_query("SELECT member_account_balance FROM member WHERE member_id = {$_SESSION['member_id']}");
	$balance = mysql_result($balance_lookup,0,'member_account_balance');
	
	if ($_POST['payment_amount'] <= 0) { // there's enough in the account to pay it in full
		$text = "	Your order has been processed using the available credit on your account.  Your new balance is &pound;" . number_format(- $balance, 2) ;
		$smarty->assign('body_text',$text);
		$smarty->display('index.tpl');
		exit();
	}
	
	if ($opening_balance != $balance) { // it's been part paid
		$partpaid = "	Your existing balance of &pound;" . number_format(- $opening_balance, 2) . " has been used to part process your order. Your new balance is &pound;" . number_format(- $balance, 2);
	} 
} 

	$smarty->assign('partpaid', $partpaid);


	// check to see if we can accept payment by paypal
	if(get_config_value('accept_paypal') == 1) {
		$methods[] = 'PayPal';
		$minimum_paypal = get_config_value('paypal_minimum_payment');
		if($_POST['payment_amount'] >= $minimum_paypal)	{
			$paypal_charge = round(get_config_value('paypal_charge') * $_POST['payment_amount'], 2);
			$button_vars = array(
								'custom' => $_SESSION['member_id'],
								'invoice' => "Basket {$_SESSION['order_date']}/" . time(),
								'no_shipping' => '1',
								'item_name_1' => "$CoopName payment by: " . $_SESSION['membership_num'] . " " . $_SESSION['first_name'] . " " . $_SESSION['last_name'],
								'amount_1' => $_POST['payment_amount'],
								'item_name_2' => "PayPal Charge",
								'amount_2' => $paypal_charge,
								'return' => "$BaseURL/payment.php?function=success",
								'rm' => 2,
								'cancel_return' => "$BaseURL/payment.php?function=cancelled",
								'notify_url' => "$BaseURL/process_paypal.php"
								);
			$paypal_button = paypal_form($button_vars);
			$smarty->assign('paypal_button',$paypal_button);
			$smarty->assign('paypal_charge',$paypal_charge);
		} else {
			$smarty->assign('paypal_notice',"The value of your order is below the minimum payment of &pound;$minimum_paypal that $CoopName accepts through PayPal.  Please pay by another method.");
		}
	} 
	
	// Bank transfer
	$methods[] = 'Bank Transfer';
	$smarty->assign('bank_transfer_msg', "To pay by bank transfer use the bank details provided in your membership pack. Please <a href='send_msg.php'>send a message</a> to tell us the date and amount you've transfered.");
	
	// and cheque payments
	if(get_config_value('accept_cheque_payments') == 1)
	{
		$methods[] = 'Cheque';
		$smarty->assign('cheque_payee',get_config_value('foodclub_cheques_to'));
		$smarty->assign('foodclub_name',get_config_value('foodclub_name'));
		$smarty->assign('foodclub_postal_address',nl2br(get_config_value('foodclub_post_address')));
		$smarty->assign('foodclub_manager',get_config_value('foodclub_manager'));
	}	
	
	
	$smarty->assign('methods',$methods);
	$smarty->assign('payment_amount',$_POST['payment_amount']);
	
	$smarty->display('payment_methods.tpl');


?>
