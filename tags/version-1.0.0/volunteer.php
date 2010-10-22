<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	8 Feb 2007				       #
#		Description:	member volunteering page		       #
#		Calls:		config.php				       #
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
	- 2007.02.08 file created
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
include 'footer.php';

if(!empty($_POST['function']))
{
	switch($_POST['function'])
	{
		case 'delete':
			foreach($_POST['volunteer_id'] as $key=>$var)
			{
				mysql_query("DELETE FROM volunteer WHERE volunteer_id = $var");
				}
			break;
		case 'add':
			$volunteer_date = $_POST['volunteer_date_Year'] . '-' . $_POST['volunteer_date_Month'] . '-' . $_POST['volunteer_date_Day'];
			if(!empty($_POST['volunteer_task']))
			{
				mysql_query("INSERT INTO volunteer SET volunteer_member_id = {$_SESSION['member_id']}, volunteer_task = \"{$_POST['volunteer_task']}\", volunteer_hours = \"{$_POST['volunteer_hours']}\", volunteer_date = \"{$volunteer_date}\"");
				}
			break;
		}
	}

$current_month_lookup = mysql_query("SELECT * FROM volunteer WHERE volunteer_member_id = {$_SESSION['member_id']} AND month(volunteer_date) = month(NOW()) ORDER BY volunteer_date");

$count = 0;
while($c = mysql_fetch_array($current_month_lookup))
{
	$current_month[$count]['volunteer_date'] = $c['volunteer_date'];
	$current_month[$count]['volunteer_task'] = $c['volunteer_task'];
	$current_month[$count]['volunteer_hours'] = $c['volunteer_hours'];
	$current_month[$count]['volunteer_id'] = $c['volunteer_id'];
	$count++;
	}

$past_month_lookup = mysql_query ("SELECT * FROM volunteer WHERE volunteer_member_id = {$_SESSION['member_id']} AND month(volunteer_date) = month(DATE_SUB(NOW(),INTERVAL 1 MONTH)) ORDER BY volunteer_date");
$count = 0;
while($c = mysql_fetch_array($past_month_lookup))
{
	$past_month[$count]['volunteer_date'] = $c['volunteer_date'];
	$past_month[$count]['volunteer_task'] = $c['volunteer_task'];
	$past_month[$count]['volunteer_hours'] = $c['volunteer_hours'];
	$past_month[$count]['authorised'] = $c['volunteer_hours_authorised'];
	$count++;
	}

$smarty->assign('current_hours',$current_month);
$smarty->assign('past_hours',$past_month);
$smarty->display('volunteer.tpl');

?>
