<?php

################################################################################
#                                                                              #
#		Filename:		join.php				       #
#		Author:		Martin Settle				       #
#               Created:		3 June 2008				       #
#		Description:	System for automatic registration of new members				       #
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
	- 2008.06.03 file created
	-

*/

session_start();
header("Cache-control: private");

$Secure = 1;
require 'config.php';

$smarty->assign('pagetitle',$CoopName);
	
include 'header.php';
include 'footer.php';

if(get_config_value('allow_autoregistration') != TRUE) header('Location: index.php');

// This is where the main logic for each file goes.

function set_distribution()
{
	global $smarty;
	$distribution_lookup = mysql_query("SELECT distribution_id, distribution_name FROM distribution ORDER BY distribution_name");
	while($dis = mysql_fetch_array($distribution_lookup))
	{
		$distribution[$dis['distribution_id']] = $dis['distribution_name'];
		}
	$smarty->assign('distribution',$distribution);
	}

if(!empty($_POST['Register']))
{
	$error_extra = '';
	// Check that we have all of the information required:
	if (empty($_POST['member_first_name'])) $error_extra .= '<li>First Name';
	if (empty($_POST['member_last_name'])) $error_extra .= '<li>Last Name';
	if (empty($_POST['member_email'])) $error_extra .= '<li>E-mail';
	if (empty($_POST['member_homephone']) && empty($_POST['member_workphone']) && empty($_POST['member_mobilephone'])) $error_extra .= '<li>at least one phone number';
	if (!empty($error_extra))
	{
		// we don't, so go back to the form
		$error = 'Unfortunately, some required information is missing from your registration.  Please complete the following fields and resubmit:<ul>';
		$error .= $error_extra;
		$error .= '</ul>';
		$smarty->assign('message',"{$CoopName} Membership</h2><p>{$error}</p><h2>");
		$smarty->assign('member_details',$_POST);
		set_distribution();
		$smarty->assign('new_member',1);
		$smarty->display('member_details.tpl');
		exit();
		}
	
	// check that the e-mail address isn't a duplicate
	$C_POST = quote_smart($_POST);
	$email_lookup = mysql_query("SELECT * FROM member WHERE member_email = {$C_POST['member_email']}");
	if(mysql_num_rows($email_lookup) > 0)
	{
		// it's a duplicate, send an error notice
		$member_id = mysql_result($email_lookup,0,'member_id');
		$smarty->assign('warning',"ERROR: Duplicate E-mail");
		$smarty->assign('body_text',"<p>The e-mail address provided, {$_POST['member_email']} is already registered to a member of the food club.</p><p>If you recently registered for the food club, and have not received a welcome e-mail (with account verification instructions), click <a href='verify.php?member_id={$member_id}&function=send_email'>here</a> to have the message sent again.</p><p>If you cannot remember your login password, you can have it reset and e-mailed to you by attempting to log in three times.</p>");
		$smarty->display('index.tpl');
		exit();
		}

	// input the new member
	// first create a verification code
	$verification_code = generatePassword(12);
	$password = 'not set';
	mysql_query("INSERT INTO member SET
				member_first_name = {$C_POST['member_first_name']},
				member_last_name = {$C_POST['member_last_name']},
				member_password = MD5('{$password}'),
				member_email = {$C_POST['member_email']},
				member_homephone = {$C_POST['member_homephone']},
				member_workphone = {$C_POST['member_workphone']},
				member_mobilephone = {$C_POST['member_mobilephone']},
				member_address1 = {$C_POST['member_address1']},
				member_address2 = {$C_POST['member_address2']},
				member_address3 = {$C_POST['member_address3']},
				member_town = {$C_POST['member_town']},
				member_county = {$C_POST['member_county']},
				member_postcode = {$C_POST['member_postcode']},
				member_distribution_id = {$C_POST['member_distribution_id']},
				member_account_balance = 0,
				member_active = 1,
				verification_code = '{$verification_code}'");
	$member_id = mysql_insert_id();
	
	$function = 'send_email';
	include 'verify.php';
	exit();
				
	}
	
if (!empty($_REQUEST['Email'])) $member_details['member_email'] = $_REQUEST['Email']; 
set_distribution();
$smarty->assign('member_details',$member_details);
$smarty->assign('message',"$CoopName Membership</h2><p>You must agree to the consumer terms and conditions and make your membership payment before joining. Please provide the following details to join the $CoopName.  Following registration, an e-mail will be sent to you to confirm your registration and to provide your login details.</p><h2>");
$smarty->assign('new_member','1');
$smarty->display('member_details.tpl');

?>
