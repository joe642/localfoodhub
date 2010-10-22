<?php

################################################################################
#                                                                              #
#		Filename:		calendar.php				       #
#		Author:		Martin Settle				       #
#             Created:		11 March 2008				       #
#		Description:	administer the food club calendar			       #
#		Calls:		config.php.inc				       #
#		Called by:						       #
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
	- 2008.03.11 file created
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

include '../header.php';
include '../footer.php';

$smarty->assign('pagetitle','Distribution Calendar');

// use the function switch to control what we are doing...

switch($_POST['function'])
{
	case 'Autogenerate': // creates all order dates based on config table from start date to end date
		$start_date = mktime(0,0,0,$_POST['start_Month'],$_POST['start_Day'],$_POST['start_Year']);
		$end_date = mktime(0,0,0,$_POST['end_Month'],$_POST['end_Day'],$_POST['end_Year']);
		
		// and get the config table info...
		$dist_day = get_config_value('distribution_day');
		$dist_freq_ID = get_config_value('distribution_frequency');
		$freq_SQL_lookup = mysql_query("SELECT frequency_SQL_add FROM frequency WHERE frequency_id = {$dist_freq_ID}");
		$freq_SQL = mysql_result($freq_SQL_lookup,0,'frequency_SQL_add');
		
		// now find out the weekday of the start, and correct so that we are using accurate days
		$start = getdate($start_date);
		if($start['wday'] != $dist_day)
		{
			//this uses modulus to make sure that we have a positive result
			$start_date = mktime(0,0,0,date('m',$start_date),date('d',$start_date) - ($start['wday']-$dist_day+7)%7,date('Y',$start_date));
			}
		$calendar_day = $start_date;
		while($calendar_day <= $end_date) // this will post one too many records
		{
			mysql_query("INSERT INTO calendar SET order_date = DATE_ADD(FROM_UNIXTIME({$calendar_day}), {$freq_SQL})");
			if(!mysql_error())
			{
				$last_id = mysql_insert_id();
				$last_lookup = mysql_query("SELECT UNIX_TIMESTAMP(order_date) AS od FROM calendar WHERE calendar_id = {$last_id}");
				$calendar_day = mysql_result($last_lookup,0,'od');
				// remove the extra record
				if($calendar_day > $end_date) mysql_query("DELETE FROM calendar WHERE order_date > FROM_UNIXTIME($end_date) ORDER BY order_date ASC LIMIT 1");
				// otherwise populate the product_calendar
				 else
				{
					mysql_query("INSERT INTO product_calendar 
							SELECT FROM_UNIXTIME($calendar_day) AS order_date, 
								product_id, 
								product_default_quantity_available AS quantity_available,
								0 AS quantity_ordered,
								NULL AS purchase_quantity,
								product_cost AS current_price,
								NULL AS delivered_quantity,
								NULL AS transaction_id
								FROM product, supplier
								WHERE product_supplier_id = supplier_id
								AND supplier_active = 1
								AND supplier_recurring = 1
								AND product_available = 1
								AND product_archived = 0");
					}
				
				}
			else
			{
				$new_date = mysql_query("SELECT DATE_ADD(FROM_UNIXTIME({$calendar_day}),{$freq_SQL}) AS new_date");
				$calendar_day = mysql_result($new_date,0,'new_date');
				}	
				
			}
		
		break;
	case 'Add_day': // adds a single day and populates the recurring product list
		$new_date = mktime(0,0,0,$_POST['New_Month'],$_POST['New_Day'],$_POST['New_Year']);
		mysql_query("INSERT INTO calendar SET order_date = FROM_UNIXTIME({$new_date})");
		mysql_query("INSERT INTO product_calendar 
							SELECT FROM_UNIXTIME($new_date) AS order_date, 
								product_id, 
								product_default_quantity_available AS quantity_available,
								0 AS quantity_ordered,
								NULL AS purchase_quantity,
								product_cost AS current_price,
								NULL AS delivered_quantity,
								NULL AS transaction_id
								FROM product, supplier
								WHERE product_supplier_id = supplier_id
								AND supplier_active = 1
								AND supplier_recurring = 1
								AND product_available = 1
								AND product_archived = 0");
		break;
	
	case 'Delete_day':
		$_POST = quote_smart($_POST);
		// TODO: needs to check if orders exist, and e-mail members/suppliers
		$order_date_lookup = mysql_query("SELECT order_date, UNIX_TIMESTAMP(order_date) as od_ts FROM calendar WHERE calendar_id = {$_POST['calendar_id']}");
		$order_date = mysql_result($order_date_lookup,0,'order_date');
		$printdate = date('d M, Y', mysql_result($order_date_lookup,0,'od_ts'));
		
		$existing_lookup = mysql_query("SELECT * FROM orders WHERE order_date = '{$order_date}'");
		if(mysql_num_rows($existing_lookup) > 0)
		{
			$smarty->assign('message',"The selected day ({$printdate}) cannot be deleted because orders have already been placed.");
			break;
			}
		mysql_query("DELETE FROM calendar WHERE calendar_id = {$_POST['calendar_id']}");
		mysql_query("DELETE FROM product_calendar WHERE order_date = '{$order_date}'");
		 
		break;
	}


// look up future calendar
$order_dates_lookup = mysql_query("SELECT calendar_id, order_date AS order_date FROM calendar WHERE order_date > NOW() ORDER BY order_date ASC");
print mysql_error();
while ($od = mysql_fetch_array($order_dates_lookup))
{
	$order_dates[] = $od['order_date'];
	$calendar_ids[] = $od['calendar_id'];
	}

$smarty->assign('order_dates',$order_dates);
$smarty->assign('calendar_ids',$calendar_ids);

$smarty->display('calendar.tpl');
?>
