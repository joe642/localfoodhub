<?php

################################################################################
#                                                                              #
#		Filename:	send.php				       #
#		Author:		Dave Cockcroft				       #
#       Created:	20 Oct 2009				       #
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


if(empty($_SESSION['member_id'])) // must be logged in
{
		header ("Location:index.php");
		exit();
}


include 'header.php';
include 'footer.php';

if(!empty($_POST['Send'])) {
	if (trim($_POST['msg_subject']) == "") {
		$smarty->assign('error', "please enter a subject for your message");
		$smarty->assign('msg_body', $_POST['msg_body']);	
	} elseif (trim($_POST['msg_body']) == "") {
		$smarty->assign('error', "please enter some content for your message");
		$smarty->assign('msg_subject', $_POST['msg_subject']);
	} else {
		// save the message
		if(!mysql_query('INSERT INTO messages SET from_id = ' . $_POST['from_id'] . ', to_id = ' . $_POST['to_id'] . ', msg_subject = "' . mysql_real_escape_string($_POST['msg_subject']) . '", msg_body = "' . mysql_real_escape_string($_POST['msg_body']) . '"')) {
			$text = "There was an error saving you message.  The database returned the error:<br><br>" . mysql_error();
		} else {
			$text = "Your message has been sent...";
		}	
		$smarty->assign('done', $text);
	}
} 
// display the message form
$smarty->assign('from_id',$_SESSION['member_id']);
$smarty->assign('to_id',0);
$smarty->display('send_msg.tpl');
?>
