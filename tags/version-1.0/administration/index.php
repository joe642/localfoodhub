<?php

################################################################################
#                                                                              #
#		Filename:	login.php				       #
#		Author:		Martin Settle				       #
#               Created:	19 May 2006				       #
#		Description:	request login information and process 	       #
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
	- 2006.05.19 file created
	-

*/

session_start();
header("Cache_control: private");

$Secure = 1;
require '../config.php';

$smarty->assign('pagetitle','Admin Log In');

include '../header.php';

# If login data submitted, test...

if(!empty($_POST['Password']))
{
	$admin_password = get_config_value('admin_password');
	
	if(md5($_POST['Password']) == $admin_password)
	{
		$_SESSION['admin'] = TRUE;
		$admin_date_test = mktime(0,0,0,date('m'),date('d')-3,date('y'));
		$date_query = "SELECT order_date AS admin_date FROM calendar WHERE order_date > FROM_UNIXTIME({$admin_date_test}) LIMIT 1";
		$date_lookup = mysql_query($date_query);
		$_SESSION['admin_date'] = mysql_result($date_lookup,0);
		
		// and auto login as admin if not already logged in 
		if (empty($_SESSION['member_id'])) {
			$query = "SELECT * FROM member WHERE member_id = 1";
			$Memberquery = mysql_query("$query");
			$Member = mysql_fetch_array($Memberquery);
			extract($Member);
			$_SESSION['member_id'] = $member_id;
			$_SESSION['membership_num'] = $membership_num;
			$_SESSION['first_name'] = $member_first_name;
			$_SESSION['last_name'] = $member_last_name;
			$_SESSION['email'] = $member_email;
		}
		
		include ('admin.php');

		exit();
		}
	else 
	{
		$_SESSION['AdminLogin']++;
		$smarty->assign('warning','The password entered is incorrect.  Please try again.');
		if($_SESSION['AdminLogin'] > 3)
		{
			reset_password($MemberID,$E-mail);
			$smarty->assign('warning','You have failed to correctly log in on three consecutive attempts. This system is now disabled.');
			
			include '../footer.php';

			$smarty->display('index.tpl');
			exit();
			}
		}
	}

	
$loginform = ("
<form action='index.php' method='POST'>
<table>
	<tr>
		<th>Admin Password:</th>
		<td><input type='password' size='12' name='Password'></td>
	</tr>
	<tr>
		<td class='button'><input type='Submit' value='Login'>
	</tr>
</table>
</form>
");

$smarty->assign('body_text',$loginform);

include '../footer.php';
	
$smarty->display('admin_login.tpl');

?>
