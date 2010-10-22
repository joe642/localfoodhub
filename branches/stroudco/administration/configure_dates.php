<?php

################################################################################
#                                                                              #
#		Filename:	configure_dates.php			       #
#		Author:		Martin Settle				       #
#               Created:	15 January 2009				       #
#		Description:	configures availability of suppliers	       #
#		Calls:		config.php.inc, supplier_functions.php	       #
#		Called by:	calendar.php				       #
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
	- 2009.01.15 file created
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
include 'supplier_functions.php';

// this script just looks up and displays available suppliers.  Changes are handled by ajax

$order_date = date('Y-m-d',$_GET['order_date']);

$smarty->assign('suppliers',get_available_by_date($order_date));
$smarty->assign('date',$_GET['order_date']);
$smarty->assign('javascripts',array('java_object.js','availability.js'));

$smarty->display('configure_dates.tpl');

?>
