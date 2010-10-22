<?php

################################################################################
#                                                                              #
#		Filename:	login.php				       #
#		Author:		Martin Settle				       #
#               Created:	19 May 2006				       #
#		Description:	request login information and process 	       #
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
	- 2006.05.19 file created
	-

*/

session_start();
header("Cache_control: private");

$Secure = 1;
require 'config.php';

$smarty->assign('pagetitle',"{$CoopName} Log In");

include 'header.php';

# If login data submitted, test...


if(!empty($_POST['Email']) && !empty($_POST['Password']))
{
	$email = quote_smart($_POST['Email']);
	$query = "SELECT * FROM member WHERE member_email = {$email}";
	$Memberquery = mysql_query("$query");
	$Member = mysql_fetch_array($Memberquery);
	extract($Member);

	if(md5($_POST['Password']) == $Member['member_password'])
	{
		$_SESSION['member_id'] = $member_id;
		$_SESSION['membership_num'] = $membership_num;
		$_SESSION['first_name'] = $member_first_name;
		$_SESSION['last_name'] = $member_last_name;
		$_SESSION['email'] = $member_email;
		if(!empty($markup_id)) // override the global markup if member is part of a markup group
		{
			$markup_lookup = mysql_query("SELECT markup FROM markup WHERE markup_id = {$markup_id}");
			$_SESSION['markup'] = mysql_result($markup_lookup,0,'markup');
			}
		if(!empty($verification_code))
		{
			$smarty->assign('warning','Your account has not yet been verified.  Please check your registered e-mail address for your registration information, and follow the instructions to complete your registration');
			$smarty->assign('body_text','If you have not received a welcome message, click <a href="verify.php?member_id={$member_id}&function=send_email">here</a> to have it resent');
			$smarty->display('index.tpl');
			exit();
			}
		process_recurring($member_id);
		if(!empty($supplier_id))
		{
			$_SESSION['producer'] = $supplier_id;
			include 'producer.php';
			exit();
			}
		include 'home.php';
		exit();
		}
	else 
	{
		$_SESSION['BadLogin']++;
		$smarty->assign('warning','The password entered is incorrect.  Please try again.');
		if($_SESSION['BadLogin'] > 2)
		{
			reset_password($member_id,$member_email);
			$smarty->assign('warning','You have failed to correctly log in on three consecutive attempts. To ensure your account is protected, your password has been reset, and the new password sent to your registered e-mail address. Please check your e-mail for your new password before attempting to log in again.  If you do not have access to your registered e-mail please contact the site administrator.');
			$_SESSION['BadLogin'] = '';
			
			include 'footer.php';

			$smarty->display('index.tpl');
			exit();
			}
		}
	}

if(!empty($_POST['Email']) && empty($_POST['Password'])) 
{
	$smarty->assign('warning','Please enter your password.');
	}
	
$loginform = ("
<form action='login.php' method='POST'>
<table>
	<tr>
		<th>E-mail:</th>
		<td><input type='text' size='60' name='Email' value='" . $_POST['Email'] . "'></td>
	</tr>
	<tr>
		<th>Password:</th>
		<td><input type='password' size='12' name='Password'></td>
	</tr>
	<tr>
		<td class='button'><input type='Submit' value='Login'>
	</tr>
</table>
</form>
");

$smarty->assign('body_text',$loginform);

include 'footer.php';
	
$smarty->display('index.tpl');

?>
