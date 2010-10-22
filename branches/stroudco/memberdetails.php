<?php

################################################################################
#                                                                              #
#		Filename:	memberdetails.php			       #
#		Author:		Martin Settle				       #
#               Created:	1 October 2006				       #
#		Description:	member home page			       #
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
	- 2006.10.01 file created
	-

*/

function update_details($Details)
{
	mysql_query('UPDATE member
		SET
			membership_num = "' . $Details['membership_num'] . '",
			member_first_name = "' . $Details['member_first_name'] . '", 
			member_last_name = "' . $Details['member_last_name'] . '",
			member_email = "' . $Details['member_email'] . '",
			member_homephone = "' . $Details['member_homephone'] . '",
			member_workphone = "' . $Details['member_workphone'] . '",
			member_mobilephone = "' . $Details['member_mobilephone'] . '",
			member_address1 = "' . $Details['member_address1'] . '",
			member_address2 = "' . $Details['member_address2'] . '",
			member_address3 = "' . $Details['member_address3'] . '",
			member_town = "' . $Details['member_town'] . '",
			member_county = "' . $Details['member_county'] . '",
			member_postcode = "' . $Details['member_postcode'] . '",
			member_distribution_id = "' . $Details['member_distribution_id'] . '"
			WHERE member_id = "' . $Details['member_id'] . '"');
			print mysql_error();
			
	}

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

	
if(!empty($_SESSION['admin']))
{
	if(empty($_POST['member_id']))
	{
		// Show a select form for member id
		$smarty->assign('pagetitle','Select Member');
		include 'header.php';
		$message = '<h2>Membership Admin Options</h2>
<ul>
  <li><a href="administration/new_member.php">Add a new member</a></li>
  <li><a href="administration/reset_password.php">Reset a member\'s password</a></li>
  <li><a href="administration/account_statement.php">Member statements</a> </li>
  <li>Edit member details - select below: </li>
</ul><p>Please select the member you wish to edit.</p>';			
		$smarty->assign('message',$message);
		
		$memberslookup = mysql_query("SELECT member_id, CONCAT(member_first_name,' ',member_last_name) AS name FROM member WHERE member_active = 1 ORDER BY name");
		while($member = mysql_fetch_array($memberslookup))
		{
			$members[$member['member_id']] = $member['name'];
			}
		$smarty->assign('members',$members);

		include 'footer.php';
		
		$smarty->display('member_select.tpl');
		exit();
		}
	elseif(!empty($_POST['Update']))
	{
		update_details($_POST);
		header("Location:memberdetails.php");
		exit();
		}
	else
	{
		$member_id = $_POST['member_id'];
		$smarty->assign('edit_membership_num', 'YES');   // allow membership number update
		}
	}
else
{
	if(!empty($_POST['Update']))
	{
		update_details($_POST);
		header("Location:home.php");
		exit();
		}

	$member_id = $_SESSION['member_id'];

	}

$memberdetailslookup = mysql_query("SELECT * FROM member WHERE member_id = $member_id");
$smarty->assign('member_details',mysql_fetch_array($memberdetailslookup,MYSQL_ASSOC));

$distribution_lookup = mysql_query("SELECT distribution_id, distribution_name FROM distribution ORDER BY distribution_name");
while($dis = mysql_fetch_array($distribution_lookup))
{
	$distribution[$dis['distribution_id']] = $dis['distribution_name'];
	}
$smarty->assign('distribution',$distribution);

$smarty->assign('pagetitle','Edit My Details');
include 'header.php';
include 'footer.php';

$smarty->display('member_details.tpl');

?>
