<?php

################################################################################
#                                                                              #
#		Filename:	message_status.php			       #
#		
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


	// process the messages and set status to done
	
	foreach($_POST['status_done'] as $done_id => $msg_id) {

		//echo $done_id . " => " .  $msg_id . "<br/>";
		$update_query = "UPDATE messages SET done = 1 WHERE msg_id = $msg_id";
		//echo $update_query . "<br/>";
		mysql_query($update_query);
	}
	//exit(0);
	header("Location: messages.php");


?>
