<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	12 February, 2007			       #
#		Description:	admin recurring order processing	       #
#		Calls:		config.php.inc				       #
#		Called by:	system administration page		       #
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
	- 2007.02.12 file created
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

$memberslookup = mysql_query("SELECT DISTINCT(recurring_member_id) FROM recurring
	WHERE recurring_next_order ={$_SESSION['order_date']}");

print mysql_error();

while($m = mysql_fetch_array($memberslookup))
{
	process_recurring($m['recurring_member_id']);
	}


?>
