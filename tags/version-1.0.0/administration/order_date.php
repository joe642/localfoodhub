<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	19 May 2006				       #
#		Description:	member home page			       #
#		Calls:		config.php.inc				       #
#		Called by:	login.php				       #
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

if(!empty($_POST['new_date']))
{
	$_SESSION['admin_date'] = $_POST['new_date'];
	header("location:admin.php");
	exit();
	}

$orders_lookup = mysql_query('SELECT order_date, DATE_FORMAT(order_date, "%a, %D %M, %Y") AS formatted_date FROM calendar ORDER BY order_date DESC');
print mysql_error();
while($orders = mysql_fetch_array($orders_lookup))
{
	$order_dates[$orders['order_date']] = $orders['formatted_date'];
	}

$smarty->assign('order_dates',$order_dates);
$smarty->assign('current_date',$_SESSION['admin_date']);


include '../header.php';
include '../footer.php';

$smarty->display('change_date.tpl');

?>
