<?php

################################################################################
#                                                                              #
#		Filename:	messages.php			       #
#		Author:		Dave Cockcroft				       #
#       Created:	23 Oct 2009				       #
#		Description:	admin view members messages		       #
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


// get the includes out of the way
include '../header.php';
include '../footer.php';

// we have a member ID so get the transaction data

if(isset($_GET['done'])) {
	$done = $_GET['done']; 
	$limit = "LIMIT 0, 100";
	$smarty->assign('done', 1);
} else {
	$done = 0;
	$limit = "";
	$smarty->assign('done', 0);
}

$messages_lookup = mysql_query("SELECT msg_id, done, UNIX_TIMESTAMP(msg_date) as msg_sent, msg_subject, msg_body, member_first_name, member_last_name, member_email FROM messages, member
								WHERE done = $done AND to_id = 0 AND messages.from_id = member.member_id
								ORDER BY msg_date DESC $limit");
if(mysql_num_rows($messages_lookup) == 0) {
	$smarty->assign('body_text','There are no messages to display.<br/><br/>You can still <a href="messages.php?done=1">view old messages that have already been dealt with...</a>');
	$smarty->display('index.tpl');
	exit();
}

$count = 0;
while($msg = mysql_fetch_array($messages_lookup)) {
	$messages[$count]['msg_id'] = $msg['msg_id'];
	if ($msg['done'] == 1) {
		$messages[$count]['done'] = "checked disabled";
	} else {
		$messages[$count]['done'] = "";
	}
	$messages[$count]['date'] = date("d-M-y", $msg['msg_sent']) . "&nbsp;" . date("H:i", $msg['msg_sent']);
	$messages[$count]['subject'] = $msg['msg_subject'];
	$messages[$count]['body'] = substr(nl2br($msg['msg_body']), 0, 50);
	if (strlen($msg['msg_body']) > 50) $messages[$count]['body'] .= "<a href='../ajax/message_details.php?message_id=" . $msg['msg_id'] ."' rel='facebox'>...</a>";
	$messages[$count]['from'] = $msg['member_first_name'] . ' ' . $msg['member_last_name'];
	$messages[$count]['email'] = $msg['member_email'];
	$count++;
}

$smarty->assign('messages', $messages);

$member_lookup = mysql_query("SELECT * FROM member WHERE member_id = {$_POST['member_id']}");
$m = mysql_fetch_array($member_lookup,MYSQL_ASSOC);

$smarty->assign('pagetitle','Administrator Messages');

$smarty->display('admin_messages.tpl');

?>
