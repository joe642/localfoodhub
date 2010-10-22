<?php

################################################################################
#                                                                              #
#		Filename:	producer_exit.php				       #
#		Author:		Martin Settle				       #
#               Created:	12 Mar 2008				       #
#		Description:	drops session variable for producer page			       #
#		Calls:		config.php				       #
#		Called by:					       #
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
	- 2008.03.12 file created
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

$_SESSION['producer_id'] = $_SESSION['producer'];
$_SESSION['act_as_member'] = 'Y';
//unset($_SESSION['producer']);

include 'header.php';

$text = "You have logged out of the Producer Control Panel.  Return using the 'Producer Control' menu option";
$smarty->assign('body_text',$text);

include 'footer.php';

$smarty->display('index.tpl');

?>
