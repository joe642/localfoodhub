<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	19 May 2006				       #
#		Description:	member home page			       #
#		Calls:		config.php				       #
#		Called by:	login.php				       #
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
	

print ("<pre>\n");
print_r($_POST);
print ("</pre>\n");


include 'header.php';
include 'footer.php';

$smarty->assign('body_text',$text);
$smarty->display('index.tpl');

?>
