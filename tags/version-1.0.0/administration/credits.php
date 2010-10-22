<?php

################################################################################
#                                                                              #
#		Filename:	credits.php				       #
#		Author:		Martin Settle				       #
#               Created:	27 Oct 2006				       #
#		Description:	post single or multiple credits		       #
#		Calls:		config.php.inc				       #
#		Called by:	nothing 				       #
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
	- 2006.10.27 file created
	- 2008.03.12 adapted to deal with payment in advance

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

// if available, switch on single or multiple

if(!empty($_POST['method']))
{
	switch($_POST['method'])
	{
		case 'single':
			// if no member_id provided, return to main with a message
			if(empty($_POST['member_id']) || empty($_POST['credit_amount'])) {
				$smarty->assign('message','Please select a member and input the amount received or credited.');
				break;
			}
			
			// check the membership number is correct - helps prevent accidental receipt to the wrong member
			$member_check = "SELECT * FROM member WHERE membership_num = '" . $_POST['credit_member_num'] . "' AND member_id = " . $_POST['member_id'];
			$result = mysql_query($member_check);
			if (mysql_num_rows($result) == 0) {
				$smarty->assign('body_text',"<b>SORRY DETAILS ARE WRONG:</b> &pound;$_POST[credit_amount] can not be added as the membership number did not match the member you selected. Please check the membership number is correct and try again.<br/><br/><i>Please note this extra check is added for your safety to prevent accidental credit to the wrong account.</i><br/>" );
			
				include '../header.php';
				include '../footer.php';
				$smarty->display('index.tpl');
				exit();
			}
			
			$_POST = quote_smart($_POST);
			// otherwise post the receipt
			$error = receive_payment($_POST['member_id'],$_POST['credit_amount'],$_POST['credit_reference']);
				
			// if we've had an error, print the message
			if(!empty($error)) $smarty->assign('message',$error);
			else $smarty->assign('body_text',"<b>SUCCESS:</b> &pound;$_POST[credit_amount] has been credited to the member's account, orders processed to the maximum value, and the balance has been updated successfully");
			
			include '../header.php';
			include '../footer.php';
			$smarty->display('index.tpl');
			exit();
			break;
		
		case 'multiple':
			break;
		

		}
	}

// build the member array

$members_lookup = mysql_query("SELECT 
					member_id, 
					CONCAT(member_first_name,' ',member_last_name) as member_name
				FROM 
					member 
				ORDER BY 
					member_first_name");
while($member = mysql_fetch_array($members_lookup))
{
	$members[$member['member_id']] = $member['member_name'];
	}

	
$smarty->assign('members',$members);

include "../header.php";
include "../footer.php";
$smarty->display('credit_select.tpl');

?>
