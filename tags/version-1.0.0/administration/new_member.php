<?php

################################################################################
#                                                                              #
#		Filename:	new_member.php				       #
#		Author:		Martin Settle				       #
#               Created:	1 October 2006				       #
#		Description:	add new member				       #
#		Calls:		config.php.inc				       #
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

if(empty($_POST['Update']))
{
	$smarty->assign('message','New Member Details');
	$distributionlookup = mysql_query('select distribution_id, distribution_name FROM distribution');
	while($dis = mysql_fetch_array($distributionlookup))
	{
		$distribution[$dis[0]] = $dis[1];
		}
	$smarty->assign('distribution',$distribution);
	$smarty->assign('edit_membership_num', 'YES');
	$smarty->display('member_details.tpl');
	exit();
	}

if(empty($_POST['member_first_name']) OR empty($_POST['member_last_name']) OR empty($_POST['member_email']))
{
	$text = "Please ensure that you have provided (at the minimum) a first name, last name, and e-mail.  If the individual has no e-mail address, please use firstname_lastname as this will be the member's login";
	}

else
{
	
	if(!mysql_query('INSERT INTO member SET member_first_name = "' . $_POST['member_first_name'] . '", member_last_name = "' . $_POST['member_last_name'] . '", membership_num = "' . $_POST['membership_num'] . '", member_email = "' . $_POST['member_email'] . '", member_homephone = "' . $_POST['member_homephone'] . '", member_workphone = "' . $_POST['member_workphone'] . '", member_mobilephone = "' . $_POST['member_mobilephone'] . '", member_address1 = "' . $_POST['member_address1'] . '", member_address2 = "' . $_POST['member_address2'] . '", member_address3 = "' . $_POST['member_address3'] . '",	member_town = "' . $_POST['member_town'] . '", member_county = "' . $_POST['member_county'] . '", member_postcode = "' . $_POST['member_postcode'] . '", member_distribution_id = "' . $_POST['member_distribution_id'] . '"'))
	{
		$text = "There was an error processing your request.  The database returned the error:<br><br>" . mysql_error();
		}
	else
	{
		$text = "The member has been set up.  Please create a password now.";
		}
	}	
$smarty->assign('body_text',$text);
$smarty->display('index.tpl');

?>
