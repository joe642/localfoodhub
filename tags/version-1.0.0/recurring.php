<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	08 Dec 2006				       #
#		Description:	recurring order admin			       #
#		Calls:		config.php				       #
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
	- 2006.12.08 file created
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

include 'header.php';

if(!empty($_POST['process'])) $process = $_POST['process'];
elseif(!empty($_GET['process'])) $process = $_GET['process'];
else
{
	$query = "SELECT recurring.*, product_name, frequency_name
			FROM recurring, product, frequency
			WHERE recurring_product_id = product_id
			AND recurring_frequency = frequency_id
			AND recurring_member_id = $_SESSION[member_id]
			ORDER BY recurring_next_order, product_name";
	//echo $query;
	//exit(0);
	$orders_lookup = mysql_query($query);
	print mysql_error();
	
	$count = 0;
	
	while($order = mysql_fetch_array($orders_lookup))
	{
		$recurring[$count]['recurring_product'] = $order['product_name'];
		$recurring[$count]['recurring_quantity'] = $order['recurring_quantity'];
		$recurring[$count]['recurring_frequency'] = $order['frequency_name'];
		$recurring[$count]['recurring_next_delivery'] = $order['recurring_next_order'];
		$recurring[$count]['recurring_id'] = $order['recurring_id'];
		$count++;
		}

	$smarty->assign('recurring',$recurring);
	$smarty->display('recurring_list.tpl');
	
	exit();
	}


$distribution = get_config_value('distribution_frequency');

switch($process)
{
	case 'Update':
		$_POST = quote_smart($_POST);
		$update = "UPDATE recurring
				SET recurring_quantity = $_POST[recurring_quantity],
					recurring_frequency = $_POST[recurring_frequency],
					recurring_next_order = FROM_UNIXTIME($_POST[recurring_next_order])
				WHERE recurring_id = $_POST[recurring_id]";
		mysql_query($update);
		header("Location: recurring.php?process=");
		break;
		
	case 'Delete':
		$id = quote_smart($_POST['recurring_id']);
		$product_lookup = mysql_query("SELECT product_name 
						FROM product, recurring
						WHERE recurring_product_id = product_id
						AND recurring_id = {$id}");
		$product = mysql_result($product_lookup, 0);
		
		$message = "<p>Please confirm that you wish to delete your recurring order for <strong>$product</strong>.</p>
		<form action=\"recurring.php\" method=\"POST\">
		<input type=\"hidden\" name=\"recurring_id\" value=\"$_POST[recurring_id]\">
		<center><input type=\"submit\" name=\"process\" value=\"Confirm\">&nbsp;&nbsp;<input type=\"submit\" value=\"Cancel\"></center>
		</form>";
		$smarty->assign('body_text',$message);
		$smarty->display('index.tpl');
		exit();
		
	case 'Confirm':
		$_POST = quote_smart($_POST);
		mysql_query("DELETE FROM recurring WHERE recurring_id = $_POST[recurring_id]");
		header("Location: recurring.php?process=");
		break;
	
	case 'Add':
		$SQL_Lookup = mysql_query("SELECT frequency_SQL_add
				FROM frequency
				WHERE frequency_id = {$distribution}");
		$SQL_add = mysql_result($SQL_Lookup,0);		

		$_POST = quote_smart($_POST);
		$SQL_insert = "INSERT INTO recurring
				SET recurring_member_id = $_SESSION[member_id],
				recurring_product_id = $_POST[product_id],
				recurring_quantity = 1,
				recurring_frequency = {$distribution},
				recurring_next_order = date_add('$_SESSION[order_date]', $SQL_add)";
		mysql_query($SQL_insert);
		$id = mysql_insert_id();
		header("Location: recurring.php?process=");
		break;
		
	case 'Edit':
		if(empty($id))
		{
			$id = quote_smart($_REQUEST['recurring_id']);
			}			
		$lookup = "SELECT recurring.*,UNIX_TIMESTAMP(recurring_next_order) AS next_order, product_name, product_description
				FROM recurring, product
				WHERE recurring_product_id = product_id
				AND recurring_id = $id";
		$recurring_lookup = mysql_query($lookup);
		
		$r = mysql_fetch_array($recurring_lookup);
		
		$smarty->assign('recurring_id',$r['recurring_id']);
		$smarty->assign('recurring_product_name',$r['product_name']);
		$smarty->assign('recurring_product_description',$r['product_description']);
		$smarty->assign('recurring_quantity',$r['recurring_quantity']);
		$smarty->assign('recurring_frequency',$r['recurring_frequency']);
		$smarty->assign('recurring_next_delivery',$r['next_order']);
		
		$freq_lookup = mysql_query("SELECT * FROM frequency");
		while($freq = mysql_fetch_array($freq_lookup))
		{
			$frequency[$freq['frequency_id']] = $freq['frequency_name'];
			}
		$smarty->assign('frequencies',$frequency);
		
		$SQL_Lookup = mysql_query("SELECT frequency_SQL_add
				FROM frequency
				WHERE frequency_id = {$distribution}");
		$SQL_add = mysql_result($SQL_Lookup,0);
		
		$dates_lookup = mysql_query("SELECT UNIX_TIMESTAMP(order_date) AS order_date FROM calendar WHERE order_date > '{$_SESSION['order_date']}' ORDER BY order_date ASC LIMIT 4");
		while($d = mysql_fetch_array($dates_lookup))
		{
			$future_dates[$d['order_date']] = date("d M, Y", $d['order_date']);
			}
		
		$smarty->assign('future_dates',$future_dates);
		
		$smarty->display('recurring_order_form.tpl');
		exit();
	}


include 'footer.php';

$smarty->display('index.tpl');

?>
