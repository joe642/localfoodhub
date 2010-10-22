<?php

################################################################################
#                                                                              #
#		Filename:	config_values.php				       #
#		Author:		Martin Settle				       #
#               Created:		27 October 2008				       #
#		Description:	config value editing			       #
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

include '../header.php';
include '../footer.php';

if(!empty($_POST['save_changes']))
{
	unset($_POST['save_changes']);
	// TODO: validate
	//save changes
	$_POST['markup'] = $_POST['markup']/100;
	$_POST['volunteer_discount'] = $_POST['volunteer_discount']/100;
	$_POST['paypal_charge'] = $_POST['paypal_charge']/100;
	
	$_POST = quote_smart($_POST);
	foreach($_POST as $name =>$value)
	{
		mysql_query("INSERT INTO config (name, value) VALUES('{$name}',{$value})
				ON DUPLICATE KEY UPDATE value = VALUES(value)");
		
		}
	$smarty->assign('warning','Changes have been saved.');
	}

$manager_email = "";
$orders_from_email = "";

$values_lookup = mysql_query("SELECT * FROM config");
while($values = mysql_fetch_array($values_lookup)) 
{
	if($values['name'] == 'foodclub_post_address') 
	{
		$v = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $values['value']);
		$values['value'] = str_replace("<br />","\n",$v);
	}
	$smarty->assign($values['name'],$values['value']);
	if($values['name'] == 'manager_email') $manager_email = $values['value'];
	if($values['name'] == 'orders_from_email') $orders_from_email = $values['value'];
}
	
	if ($manager_email == "") $smarty->assign('manager_email', $SystemEmail);
	if ($orders_from_email == "") $smarty->assign('orders_from_email', $SystemEmail);
	

$weekdays = array(0 => 'Sunday',
				1 => 'Monday',
				2 => 'Tuesday',
				3 => 'Wednesday',
				4 => 'Thursday',
				5 => 'Friday',
				6 => 'Saturday');
$smarty->assign('weekdays',$weekdays);

$freq_lookup = mysql_query("SELECT * FROM frequency");
while($f = mysql_fetch_array($freq_lookup))
{
	$frequencies[$f['frequency_id']] = $f['frequency_name'];
	}
$smarty->assign('frequencies',$frequencies);

$smarty->display('config_values.tpl');

?>
