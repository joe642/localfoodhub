<?php

################################################################################
#                                                                              #
#		Filename:	        new_invoice.php				       #
#		Author:		Martin Settle				       #
#               Created:	        15 Feb 2009				       #
#		Description:	print and post order invoices using invoice.class		       #
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
	- 2009.02.15

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

include('invoice_class.php');
include('../header.php');
include('../footer.php');

// look up of invoices
$members_lookup = mysql_query("SELECT order_member_id FROM orders 
                                                                WHERE order_quantity_requested > 0 
                                                                AND order_date = '{$_SESSION['admin_date']}'
                                                                GROUP BY order_member_id");
while($m = mysql_fetch_array($members_lookup))
{
        $member_id = $m['order_member_id'];
        $i = new invoice($member_id, $_SESSION['admin_date']);
	if($_POST['function'] == 'save') $i->save();
        $invoice[] = $i;
		}

$smarty->assign('logo',"$BaseURL/images/$LogoFile");
$smarty->assign('date',$_SESSION['admin_date']);
$smarty->assign('invoices',$invoice);
$smarty->display('new_invoices.tpl');
               

?>
