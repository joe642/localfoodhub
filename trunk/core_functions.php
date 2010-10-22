<?php

################################################################################
#                                                                              #
#		Filename:	core_functions.php.inc			       #
#		Author:		Martin Settle				       #
#               Created:	19 May 2006				       #
#		Description:	Contains frequently used functions	       #
#		Calls:		Nothing					       #
#		Called by:	config.php.inc				       #
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
	- 2008.04.04 pre-payment functions added
	- 2008.05.28 mailto system administrator added
  - 2009.03.04 process_payment altered for multiple distribution sites
  - 2009.03.05 update_product_calendar function added

*/

# submit a specific database query and return the result array
function arrayMyQuery ($query)
{
	$query_result = mysql_query("$query");
	$result_array = mysql_fetch_array($query_result);
	
	return $result_array;
	}

# return a specific config variable
function get_config_value($name)
{
	$query_result = mysql_query("SELECT value FROM config WHERE name = \"{$name}\"");
	$value = mysql_result($query_result,0);
	if(empty($value)) return FALSE;
	return $value;
	}
	
# generate a random password
function generatePassword ($length = 8)
{
  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
		    
  // set up a counter
 $i = 0; 
		        
  // add random characters to $password until $length is reached
  while ($i < $length) { 

  // pick a random character from the possible ones
       $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			        
    // we don't want this character if it's already in the password
       if (!strstr($password, $char)) { 
           $password .= $char;
           $i++;
	   }
       }

   // done!
   return $password;
   }

# reset a members password and e-mail.
function reset_password ($MemberID,$Email)
{
	global $CoopName, $BaseURL;
	$newpass = generatePassword();
	$newmd5 = md5($newpass);
	mysql_query("UPDATE member SET member_password = '$newmd5' WHERE member_id = $MemberID");
	print mysql_error();
	if(strstr($Email,'@'))
	{
		mail($Email,$CoopName,"Your password on the $CoopName ordering system has been set to '$newpass'.  For security, please log in and change your password now.\n\nThe web address of the $CoopName website is $BaseURL","From: {$SystemEmail}");
		}
	return $newpass;
	}


# transfer recurring orders to temp orders
function process_recurring ($MemberID)
{
	global $_SESSION;
	$query = ("SELECT recurring.*, frequency_SQL_add, product_cost
		   FROM recurring, frequency, product
		   WHERE 
		   	recurring_next_order = '$_SESSION[order_date]'
		   AND
		   	recurring_member_id = $MemberID
		   AND
		   	recurring_frequency = frequency_id
		   AND
		   	recurring_product_id = product_id");
	$recurring_orders = mysql_query($query);
	while($recurring = mysql_fetch_array($recurring_orders))
	{
		$insert = ("INSERT INTO temp_orders
			    SET
			    	order_date = '$_SESSION[order_date]',
			    	order_member_id = $recurring[recurring_member_id],
			    	order_product_id = $recurring[recurring_product_id],
				order_current_price = $recurring[product_cost],
			    	order_quantity_requested = $recurring[recurring_quantity]");
		mysql_query($insert);
		
		$update = ("UPDATE recurring
			    SET 
			    	recurring_next_order = date_add('$_SESSION[order_date]',$recurring[frequency_SQL_add])
			    WHERE
			    	recurring_id = $recurring[recurring_id]");
		mysql_query($update);
		print mysql_error();
		}
	}

//clean up mysql input

// This is to prevent sql injection attacks.  It needs to be added to all scripts written before 2008.02.29
function quote_smart($value)
{
	if( is_array($value) ) 
	{ 
		return array_map("quote_smart", $value);
		} 
	else 
	{
		if( get_magic_quotes_gpc() ) 
		{
			$value = stripslashes($value);
			}
		if( !is_numeric($value) || $value[0] == '0' ) 
		{
			$value = "'".mysql_real_escape_string($value)."'";
			}
		return $value;
		}
	}


// process payment received (transfer temp_orders to orders up to payment value

function receive_payment($member_id, $amount, $reference)
{
	$multiple_distribution = get_config_value('use_distribution_sites');
	if($multiple_distribution == 1)
	{
		$member_dist_lookup = mysql_query("SELECT member_distribution_id FROM member WHERE member_id = {$member_id}");
		$dist_id = mysql_result($member_dist_lookup,0,'member_distribution_id');
		}
	
	if($amount != 0)
	{
		$post_credit = "INSERT INTO credit
						SET
							credit_member_id = $member_id,
							credit_date = NOW(),
							credit_amount = {$amount},
							credit_reference = {$reference}";

		if(!mysql_query($post_credit))  {
			$error =  "Posting of the credit failed.  The database reported the following error: " . mysql_error();
			return $error;
		} else {
			// Save a record of the change to the member balance
			$memberlookup = mysql_query("SELECT member_account_balance as balance FROM member WHERE member_active = 1 AND member_id = " . $member_id);
			if ($member = mysql_fetch_array($memberlookup)) {
				$new_balance = $member['balance']  - $amount;
				if (!mysql_query("INSERT INTO balance_update (comment, member_id, old_balance, new_balance) VALUES ('receive_payment(): $amount', " . $member_id . ", " . $member['balance'] . ", " . $new_balance . ")")) print mysql_error();		
			}
			
			$update_balance = "UPDATE member
					SET member_account_balance = member_account_balance - $amount
					WHERE member_id = $member_id";
			if(!mysql_query($update_balance))
			{
				$error = "Although the credit was posted, the database failed to update the member balance.  The database reported the following error: " . mysql_error();
				}
			}
		}	
		
	// look up to see if we need to include a discount
	$volunteer_lookup = mysql_query("SELECT SUM(volunteer_hours) FROM volunteer WHERE volunteer_member_id = {$member_id} AND MONTH(volunteer_date) = (MONTH('$_SESSION[admin_date]') - 1)");
	$volunteer = mysql_result($volunteer_lookup,0);

	$required_hours = get_config_value('volunteer_discount_hours');
	$discount = get_config_value('volunteer_discount');

	$discount_amount = round($order_total * $discount, 2);

	if($volunteer < $required_hours) $discount = 0;
	
	// now process temp_orders up to the member balance
	$balance_lookup = mysql_query("SELECT member_account_balance, markup_id FROM member WHERE member_id = {$member_id}");
	$balance = mysql_result($balance_lookup,0,'member_account_balance');
	$markup_id = mysql_result($balance_lookup,0,'markup_id');
	if(empty($markup_id)) $use_markup = $_SESSION['markup'];
	else 
	{
		$markup_lookup = mysql_query("SELECT markup FROM markup WHERE markup_id = {$markup_id}");
		$use_markup = mysql_result($markup_lookup,0,'markup');
		}

	$temp_order_lookup = mysql_query("SELECT product_id, 
					order_date,
					order_current_price,
					order_quantity_requested, 
					ROUND(order_current_price * (1-$discount) * (1 + iFNULL(product_markup, $use_markup )) * 
						(1 + product_VAT_rate),2) AS unit_price, 
					ROUND(order_current_price * (1-$discount) * (1 + IFNULL(product_markup, $use_markup)) * 
						(1 + product_VAT_rate),2) * order_quantity_requested AS order_product_total
					FROM temp_orders, product 
					WHERE 
					order_date >= (SELECT timestampadd(DAY,value,NOW()) FROM config WHERE name = 'days_notice')
					AND order_product_id = product_id 
					AND order_member_id = {$member_id}
					ORDER BY order_date ASC");
	while($t = mysql_fetch_array($temp_order_lookup))
	{
		$newbalance = $balance + $t['order_product_total'];
		if($newbalance < 0.005)
		{
			mysql_query("INSERT INTO orders 
							SET order_date = '{$t['order_date']}',
							order_product_id = {$t['product_id']},
							order_member_id = {$member_id},
							order_current_price = {$t['order_current_price']},
							order_paid_price = {$t['order_current_price']},
							order_quantity_requested = {$t['order_quantity_requested']},
							order_time = NOW()");
			$balance = $newbalance;
			mysql_query("DELETE FROM temp_orders WHERE order_date = '{$t['order_date']}' AND
							order_product_id = {$t['product_id']} AND
							order_member_id = {$member_id}");
			
      update_product_calendar($t['order_date'],$t['product_id']);
			}
		elseif(0-$balance > $t['unit_price'])  // not enough left to pay for everything, process partial order
		{
			$units = floor((0-$balance)/$t['unit_price']);
								mysql_query("INSERT INTO orders 
							SET order_date = '{$t['order_date']}',
							order_product_id = {$t['product_id']},
							order_member_id = {$_POST['member_id']},
							order_current_price = {$t['order_current_price']},
							order_paid_price = {$t['order_current_price']},
							order_quantity_requested = {$units},
							order_time = NOW()");
			$balance = $balance + $units * $t['unit_price'];
			mysql_query("UPDATE temp_orders SET
						order_quantity_requested = ({$t['order_quantity_requested']} - {$units})
						WHERE order_date = '{$t['order_date']}' AND
							order_product_id = {$t['product_id']} AND
							order_member_id = {$member_id}");
		
      update_product_calendar($t['order_date'],$t['product_id']);
      }
		}
	
  // correct the account balance to show orders processed
	mysql_query("UPDATE member
				SET member_account_balance = {$balance}
				WHERE member_id = $member_id");
	}

// send an e-mail message to the system administrator

function mail_admin($subject,$text)
{
	global $SystemEmail;
	mail($SystemEmail,$subject,$text,"From: $SystemEmail");
	}


// update the product_calendar table to record orders received
function update_product_calendar($order_date,$product_id)
{
  // process will depend on whether multiple distribution is enabled
  $multiple_distribution = get_config_value('use_distribution_sites');
  // first get the case ordering details
  $product_lookup = mysql_query("SELECT product_case_size, product_pkg_count, product_allow_stock, product_current_stock 
    FROM product 
    WHERE product_id = {$product_id}");
  $case_size = mysql_result($product_lookup,0,'product_case_size');
  $pkg_count = mysql_result($product_lookup,0,'product_pkg_count');
  $allowed_stock = mysql_result($product_lookup,0,'product_allow_stock');
  $current_stock = mysql_result($product_lookup,0,'product_current_stock');
  if($multiple_distribution == 1)
  {
    // look up the total ordered, for the distribution site
    {
      $total_orders = 0;
      $orders_lookup = mysql_query("SELECT member_distribution_id, SUM(quantity_requested) AS request 
            FROM orders, member
            WHERE member_id = order_member_id
            AND order_product_id = {$product_id}
            AND order_date = '{$order_date}'
            GROUP BY member_distribution_id'");
      while($dists = mysql_fetch_array($orders_lookup))
      {
        $total_orders += FLOOR($dists['request']/$pkg_count) * $pkg_count;
        }
      }
    }
  else // we aren't using distribution sites, just post cases/splits ordered
  {
    $orders_lookup = mysql_query("SELECT SUM(order_quantity_requested) AS request 
      FROM orders
      WHERE order_date = '{$order_date}'
      AND order_product_id = {$product_id}");
    $total_request = mysql_result($orders_lookup,0,'request');
    // splitting at hub may allow stock
    // MODS DJC - don't know what this is for so ignore: $total_orders = FLOOR(($total_request - $current_stock + $allowed_stock)/$pkg_count) * $pkg_count;
    }	
  // update calendar records: MODS DJC - save the total_request value (total currently ordered)
  mysql_query("UPDATE product_calendar SET purchase_quantity = {$total_request}
    WHERE product_id = {$product_id} AND order_date = '{$order_date}'");				
  }
  
?>
