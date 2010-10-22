<?php

################################################################################
#                                                                              #
#		Filename:	reset_password.php			       #
#		Author:		Martin Settle				       #
#               Created:	2 Oct 2006				       #
#		Description:	admin reset members password		       #
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
	- 2006.10.02 file created
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

if(empty($_POST['member_id']))
{
	// Show a select form for member id
	$smarty->assign('pagetitle','Select Member');
	include '../header.php';
	$message = '<h2>Password Reset</h2><p>Please select the member who\'s password wish to reset.</p>';			
	$smarty->assign('message',$message);
	
	$memberslookup = mysql_query("SELECT member_id, CONCAT(member_first_name,' ',member_last_name) AS name FROM member WHERE member_active = 1 ORDER BY name");
	while($member = mysql_fetch_array($memberslookup))
	{
		$members[$member['member_id']] = $member['name'];
		}
	$smarty->assign('members',$members);
	
	include '../footer.php';
	
	$smarty->display('member_select.tpl');
	exit();
	}

if(empty($_POST['Confirm']))
{
	//Show a page to confirm the reset, and option to suppress e-mail
	$smarty->assign('pagetitle','Confirm password reset');
	$membernamelookup = mysql_query("SELECT CONCAT(member_first_name,' ',member_last_name) as name FROM member WHERE member_id = $_POST[member_id]");
	$name = mysql_result($membernamelookup,0);
	$smarty->assign('warning',"Are you sure you want to reset the password for $name?");
	$html = "<form action=\"$BaseURL/administration/reset_password.php\" method=\"POST\">
<input type=\"hidden\" name=\"member_id\" value=\"$_POST[member_id]\">
<input type=\"checkbox\" name=\"suppress_email\" checked> Suppress e-mail to member
<p>
<input type=\"submit\" name=\"Confirm\" value=\"Confirm\">
</form>";
	$smarty->assign('body_text',$html);	

	include '../header.php';
	include '../footer.php';
	
	$smarty->display('index.tpl');
	exit();
	}

if($_POST['suppress_password'] != 1)
{
	$email_lookup = mysql_query("SELECT member_email FROM member WHERE member_id = $_POST[member_id]");
	$email = mysql_result($email_lookup,0);
	}
else $email = '';

$newpass = reset_password($_POST['member_id'],$email);
$membernamelookup = mysql_query("SELECT CONCAT(member_first_name,' ',member_last_name) as name FROM member WHERE member_id = $_POST[member_id]");
$name = mysql_result($membernamelookup,0);


include '../header.php';

$text = "The password for $name has been reset.  The new password is<br><br><strong>$newpass</strong>.<br><br>Please inform the member.";
$smarty->assign('body_text',$text);

include '../footer.php';

$smarty->display('index.tpl');

?>
