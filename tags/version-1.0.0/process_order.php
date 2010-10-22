<?php

################################################################################
#                                                                              #
#		Filename:	process_order.php			       #
#		Author:		Martin Settle				       #
#               Created:	2 Sept 2006				       #
#		Description:	order processing			       #
#		Calls:		config.php				       #
#		Called by:	productlist.php				       #
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
	- 2006.09.02 file created
	- 2008.03.12 modified to use temp_orders

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

if($_POST['process'] == 'Update my Order') {
	// process the orders
	
	foreach($_POST['quantity'] as $product_id => $new_request) {
		$new_request = trim($new_request);
		//echo $product_id . " => " .  $new_request . "<br/>";
		if($new_request != '') {
			//submission logic goes here
			$new_request = quote_smart($new_request);
			$existinglookup = mysql_query("SELECT * FROM temp_orders WHERE order_product_id = $product_id AND order_member_id = $_SESSION[member_id] AND order_date = '$_SESSION[order_date]'");
			if (mysql_num_rows($existinglookup) == 1) {
				$existing_id = mysql_result($existinglookup,0,'temp_order_id');
				if ($new_request == 0) {
					//echo "DEL: " . $product_id . " => " .  $new_request . "<br/>";
					mysql_query("DELETE FROM temp_orders WHERE temp_order_id = $existing_id");
				}
				elseif ($new_request > 0) {
					//echo "UPD: " . $product_id . " => " .  $new_request . "<br/>";
					mysql_query("UPDATE temp_orders SET order_quantity_requested = $new_request WHERE temp_order_id = $existing_id");
				}
			}
			else if ($new_request > 0) {
				//echo "INS: " . $product_id . " => " .  $new_request . "<br/>";
				$fetch_price = mysql_query("SELECT current_price FROM product_calendar WHERE product_id = $product_id and order_date = '{$_SESSION['order_date']}'");
				$current_price = mysql_result($fetch_price,0);				
				mysql_query("INSERT INTO temp_orders SET order_product_id = $product_id, order_member_id = $_SESSION[member_id], order_date = '" . $_SESSION['order_date'] . "', order_quantity_requested = $new_request, order_current_price = $current_price");
			}
		}
	}

	header("Location: my_order.php");
	exit();
}
header("Location: productlist.php");


?>
