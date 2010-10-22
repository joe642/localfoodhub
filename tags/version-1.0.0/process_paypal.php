<?php

################################################################################
#                                                                              #
#		Filename:	process_paypal.php				       #
#		Author:		Martin Settle				       #
#               Created:	28 May 2008				       #
#		Description:	back-end process for Paypal IPN			
#		Calls:		config.php				       #
#		Called by:	PayPal					       #
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
	- 2008.05.28 file created
	- 2010.10.14 modified to deduct the PayPal charge before calling receive_payment()

*/



session_start();
header("Cache-control: private");

$Secure = 1;
require 'config.php';

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) 
{
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
	$old_post[$key] = $value;
	}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

// check for sandbox usage
if(get_config_value('paypal_use_sandbox') == 1) $address = 'ssl://www.sandbox.paypal.com';
else $address = 'ssl://www.paypal.com';

$fp = fsockopen ($address, 443, $errno, $errstr, 30);

// assign posted variables to local variables
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$member_id = $_POST['custom'];

if (!$fp) {
// HTTP ERROR -- should mail a warning to the system administrator
	$member = member_details($member_id);
	$message = "The PayPal payment processing system of $CoopName failed to connect to the PayPal IPN confirmation address.\n\n";
	$message .= "The system failed trying to confirm a payment of $payment_amount $payment_currency sent by {$member['first_name']} {$member['last_name']}.  The initial notification showed the payment status as $payment_status.\n\n";
	$message .= "Please manually check the PayPal account page to confirm payment, and process any credit received manually.";
	mail_admin('PayPal Error',$message);
	exit;
	} 
else 
{
	fputs ($fp, $header . $req);
	while (!feof($fp)) 
	{
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) 
		{

			// check the payment_status is Completed -- if not, die
			if($payment_status != 'Completed') exit();
			// check that txn_id has not been previously processed -- if so, mail a warning to the system administrator and die
			$used = mysql_query("SELECT UNIX_TIMESTAMP(credit_date) AS c_date FROM credit WHERE credit_reference = 'PayPal $txn_id'");
			if(mysql_num_rows($used) != 0)
			{
				$c_date = mysql_result($used,0,'c_date');
				$member = member_details($member_id);
				$message = "The PayPal payment processing system of $CoopName has encountered an error.\n\n";
				$message .= "Notification has been received for a payment by {$member['first_name']} {$member['last_name']} of $payment_amount $payment_currency.  However, the PayPal transaction ID, $txn_id, has already been used for a payment on ";
				$message .= date_format($c_date, 'j F, Y');
				$message .= "\n\nPlease check the PayPal account records, or communicate directly with the member about this matter.";
				mail_admin('PayPal Error',$message);
				exit();
				}
			
			// check that receiver_email is your Primary PayPal email -- if not, mail a warning...
			if(get_config_value('paypal_use_sandbox') == 1) $seller_account = get_config_value('paypal_sandbox_account');
			else $seller_account = get_config_value('paypal_account');
			if($receiver_email != $seller_account)
			{
				$member = member_details($member_id);
				$message = "The PayPal payment processing system of $CoopName has encountered an error.\n\n";
				$message .= "Notification has been received for a payment by {$member['first_name']} {$member['last_name']} of $payment_amount $payment_currency.  However, the payment has been credited to the PayPal account of $receiver_email rather than the system's account.";
				$message .= "\n\nPlease communicate directly with the member about this matter.";
				mail_admin('PayPal Error',$message);
				exit();
				}
			
			// check that payment_currency are correct -- if not, mail a warning...
			if($payment_currency != get_config_value('paypal_currency'))
			{
				$member = member_details($member_id);
				$message = "The PayPal payment processing system of $CoopName has encountered an error.\n\n";
				$message .= "A payment by {$member['first_name']} {$member['last_name']} of $payment_amount is in the wrong currency.  The payment has been credited in $payment_currency.  No orders have been processed.  Confirm any payment on the PayPal account, and process the order as a manual credit.";
				$message .= "\n\nPlease communicate directly with the member about this matter.";
				mail_admin('PayPal Error',$message);
				exit();
				}
			
			// process payment - deduct the PayPal payment surcharge based on the configured percentage. Only receive_payment for the amount due for sales of goods - not the full amount paid by PayPal.
			$payment_for_goods = round($payment_amount / (1 + get_config_value('paypal_charge')), 2);
			receive_payment($member_id, $payment_for_goods, "'PayPal $txn_id'");
			
			exit();
			}
		else if (strcmp ($res, "INVALID") == 0) 
		{
			// log for manual investigation -- mail a warning...
			$message = "The PayPal payment processing system of $CoopName has encountered an error.\n\n";
			$message .= "An INVALID response has been received from the PayPal IPN validator.\n\n";
			$message .= "The transaction in question contained the following information:\n\n";
			foreach($old_post as $key => $value)
			{
				$message .= "$key: $value\n";
				}
			$message .= "\nThe POST sent was:\n$req\n";
			$message .= "\nPlease investigate this transaction with the apparent payer.  No transaction has been logged.";
			mail_admin('PayPal Error',$message);
			}
		}		
	fclose ($fp);
	}

?>

