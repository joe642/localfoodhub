<?php

################################################################################
#                                                                              #
#		Filename:		verify.php			       #
#		Author:		Martin Settle				       #
#               Created:		3 June 2008			       #
#		Description:	autorregistration account verification				       #
#		Calls:		config.php				       #
#		Called by:		join.php					       #
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

if(!$Secure)
{
	session_start();
	header("Cache-control: private");
	$Secure = 1;
	require 'config.php';
	}
	
$smarty->assign('pagetitle',$CoopName);
	
include 'header.php';
include 'footer.php';

// This is where the main logic for each file goes.

// only set up function if this isn't already set by a higher script
if(empty($function)) 
{
	$function = $_REQUEST['function'];
	$member_id = quote_smart($_REQUEST['member_id']);
	}


if($function == 'send_email')
{
	// get the member details
	$member_lookup = mysql_query("SELECT * FROM member WHERE member_id = {$member_id}");
	print mysql_error();
	$member = mysql_fetch_array($member_lookup);
	
	if(empty($member['verification_code']))
	{
		// member has already verified, redirect to login
		header("Location: login.php");
		exit();
		}
	
	// set a new password to e-mail to member
	$password = generatePassword();
	mysql_query("UPDATE member SET member_password = MD5('{$password}') WHERE member_id = {$member_id}");
	
	$email = ("
Thank you for registering with $CoopName.
	
Your initial password has been automatically generated.  You will be able to change your password once you have logged in, but you will need to use this password for your first log in.
Your password has been set to: {$password}

To verify this e-mail address and activate your account, direct your browser to:
{$BaseURL}/verify.php?v_code={$member['verification_code']}&member_id={$member_id}

This is an automatically generated e-mail.  Please do not reply to it.");
	mail($member['member_email'],"$CoopName Account Verification",$email,"From: {$SystemEmail}");
	
	$message = "<p>Thank you for registering with {$CoopName}.</p> An e-mail has been sent to {$member['member_email']} with instructions for activating your account.  Please check your e-mail in the next few minutes to continue.</p>";
	$smarty->assign('body_text', $message);
	$smarty->display('index.tpl');
	exit();
	}

if(empty($_REQUEST['v_code'])) header("Location: index.tpl");

// save the v_code before quote_smart
$v_code = $_REQUEST['v_code'];
$_REQUEST = quote_smart($_REQUEST);

$confirm_lookup = mysql_query("SELECT * FROM member WHERE member_id = {$_REQUEST['member_id']}");
$member = mysql_fetch_array($confirm_lookup);
if(empty($member['verification_code']))
{
	// member has already verified, redirect to login
	header("Location: login.php");
	exit();
	}
elseif($v_code != $member['verification_code'])
{
	// bad verification attempted
	$smarty->assign('warning','The account verification details provided are not valid.  Please contact the system administrator.');
	$smarty->display('index.tpl');
	exit();
	}

mysql_query("UPDATE member SET verification_code = NULL WHERE member_id = {$_REQUEST['member_id']}");

$smarty->assign('body_text',"<p>Congratulations, {$member['member_first_name']}!  You have successfully registered and activated your account with $CoopName.</p><p>To begin using your membership, select 'Login' from the menu.  You will need your e-mail address and the password included in the verification e-mail</p>");

$smarty->display('index.tpl');
?>
