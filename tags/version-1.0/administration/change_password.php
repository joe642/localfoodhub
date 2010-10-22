<?php

################################################################################
#                                                                              #
#		Filename:	changepassword.php			       #
#		Author:		Martin Settle				       #
#               Created:	2 October 2006				       #
#		Description:	member change password			       #
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

include '../header.php';

include '../footer.php';

if(!empty($_POST['Change']))
{
	$newmd5 = md5($_POST['current_password']);
	$testpassword = mysql_query("SELECT value FROM config WHERE name = 'admin_password'");
	if($newmd5 != mysql_result($testpassword, 0))
	{
		$message = 'Your current password was entered incorrectly.  Please try again';
		}			
	elseif((empty($_POST['new_password_1'])) OR (empty($_POST['new_password_2'])))
	{
		$message = 'Please ensure all fields are complete';
		$smarty->assign('current_password',$_POST['current_password']);
		}
	elseif($_POST['new_password_1'] != $_POST['new_password_2'])
	{
		$smarty->assign('current_password',$_POST['current_password']);
		$message = 'Your new password did not match.  Please try again.';
		}
	else
	{
		$md5pass = md5($_POST['new_password_1']);
		mysql_query("UPDATE config SET value = '$md5pass' WHERE name = 'admin_password'");
		$body_text = 'The admin password has been changed';
		$smarty->assign('body_text',$body_text);
		$smarty->display('index.tpl');
		exit();
		}
	$smarty->assign('message',$message);
	}
	
$smarty->display('change_password.tpl');
	
?>
