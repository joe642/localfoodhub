<?php

################################################################################
#                                                                              #
#		Filename:	supplier_availability.php		       #
#		Author:		Martin Settle				       #
#               Created:	15 Jan 2009				       #
#		Description:	ajax handler for supplier service dates	       #
#		Calls:		config.php, supplier_functions.php	       #
#		Called by:	availability.js				       #
#									       #
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
		exit();
		}
	elseif(empty($_SESSION['admin']))
	{
		exit();
		}

	$Secure = 1;
	include '../config.php';
	}

include '../administration/supplier_functions.php';

// find out what the current status is for the day

$availability  = get_availability($_REQUEST['supplier_id']);

$current = $availability[$_REQUEST['order_date']];

// The changed status is the opposite of current...
$new = ABS($current - 1);

change_supplier_availability($_REQUEST['supplier_id'],$_REQUEST['order_date'],$new);

// we should be all done

