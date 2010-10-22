<?php

################################################################################
#                                                                              #
#		Filename:	home.php				       #
#		Author:		Martin Settle				       #
#               Created:	26 January 2007				       #
#		Description:	Supplier administration			       #
#		Calls:		config.php.inc				       #
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
	- 2007.01.26 file created
	- 2008.03.12 request values cleaned for mysql injection
    - 2009.01.15 handling of per-date availability

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
		
	// MODS DJC 21-Sep-09: allow admin to impersonate a producer to check their setup / info etc
	$become_id = $_REQUEST['become_id'];
	if (!empty($become_id)) {
		$_SESSION['producer'] = $become_id;
		header ("Location:../producer.php");
		exit();
	}
	
	if($_POST['producer_cp'] == 'Show Producer Control Panel') {
		$_SESSION['producer'] = $_REQUEST['supplier_id'];
		header ("Location:../producer.php");
		exit();
	}

	header ("Cache-control: private");

	$Secure = 1;
	include '../config.php';
	}
	

include '../header.php';
include '../footer.php';
include 'supplier_functions.php';

$supplier_id = $_REQUEST['supplier_id'];

$smarty->assign('pagetitle',"$CoopName Supplier Administration");

if(empty($supplier_id))
{
	$query = "SELECT supplier_id, supplier_name FROM supplier ORDER BY supplier_name";
	$supplier_lookup = mysql_query($query);
	$count = 0;
	while($s = mysql_fetch_array($supplier_lookup))
	{
		$suppliers[$count]['id'] = $s['supplier_id'];
		$suppliers[$count]['name'] = $s['supplier_name'];
		$count++;
		}
	$smarty->assign('suppliers',$suppliers);
	$smarty->display('suppliers_list.tpl');
	exit();
	}
	
switch($_POST['function'])
{
	case 'delete_member':
		$_POST['member_id'] = quote_smart($_POST['member_id']);
		mysql_query("UPDATE member SET supplier_id = NULL WHERE member_id = {$_POST['member_id']}");
		break;
	case 'add_member':
		$_POST['member_id'] = quote_smart($_POST['member_id']);
		mysql_query("UPDATE member SET supplier_id = {$supplier_id} WHERE member_id = {$_POST['member_id']}");
		break;
	}
		

if($_POST['submit_button'] == 'Update Record')
{
			if(empty($_POST['supplier_active'])) $_POST['supplier_active'] = 0;
			// if the supplier has changed active status, change future product listings
			$activelookup = mysql_query("SELECT supplier_active, supplier_recurring FROM supplier
								WHERE supplier_id = {$_POST['supplier_id']}");
			$active = mysql_result($activelookup,0,'supplier_active');
			$recurring = mysql_result($activelookup,0,'supplier_recurring');
			if ($_POST['supplier_active'] != $active)
			{
				//get product list1
				$allproducts = '';
				$productlist_lookup = mysql_query("SELECT product_id, product_available
											FROM product WHERE product_supplier_id = {$_POST['supplier_id']}");
				while($p = mysql_fetch_array($productlist_lookup))
				{
					$allproducts .= $p['product_id'] . ',';
					if ($p['product_available'] == 1) $activeproducts[] = $p['product_id'];
					}
				
				$allproducts = trim($allproducts,',');
				// update records
				switch($_POST['supplier_active'])
				{
					case 1:
						if($_POST['supplier_recurring'] == 0) break;
						foreach($activeproducts AS $product_id)
						{
							mysql_query("INSERT INTO product_calendar
								SELECT  order_date,
								product.product_id,
								product_default_quantity_available AS quantity_available,
								0 AS quantity_ordered,
								NULL AS purchase_quantity,
								product_cost AS current_price,
								NULL AS delivered_quantity,
								NULL AS transaction_id
								FROM product, calendar
								WHERE product_id = {$product_id}
								AND order_date >= '{$_SESSION['order_date']}'
								ON DUPLICATE KEY UPDATE quantity_available = product_default_quantity_available");
							}
						break;
					case 0:
						// delete future listings
						mysql_query("UPDATE product_calendar
							SET quantity_available = 0
							WHERE product_id IN ($allproducts)
							AND order_date >= '{$_SESSION['order_date']}'");
						print mysql_error();
					
					}
				}
	
	$_POST = quote_smart($_POST);
	$query = "UPDATE supplier SET
			supplier_name = {$_POST['supplier_name']},
			supplier_account = {$_POST['supplier_account']},
			supplier_contact_name = {$_POST['supplier_contact_name']},
			supplier_phone = {$_POST['supplier_phone']},
			supplier_fax = {$_POST['supplier_fax']},
			supplier_email = {$_POST['supplier_email']},
			supplier_address1 = {$_POST['supplier_address1']},
			supplier_address2 = {$_POST['supplier_address2']},
			supplier_address3 = {$_POST['supplier_address3']},
			supplier_town = {$_POST['supplier_town']},
			supplier_county = {$_POST['supplier_county']},
			supplier_postcode = {$_POST['supplier_postcode']},
			supplier_order_method = {$_POST['supplier_order_method']},
			supplier_delivery_day = {$_POST['supplier_delivery_day']},
			supplier_active = {$_POST['supplier_active']},
			supplier_recurring = {$_POST['supplier_recurring']}
			WHERE supplier_id = $supplier_id
			";
	mysql_query($query);
	}
if($_POST['submit_button'] == 'Show Products')
{
	include('products.php');
	exit();
	}


if($supplier_id == 'new')
{
	$_POST = quote_smart($_POST);
	$query = "INSERT INTO supplier SET supplier_name = {$_POST['supplier_name']}";
	mysql_query($query);
	$supplier_id = mysql_insert_id();
	}

$supplier_id = quote_smart($supplier_id);

$query = "SELECT * FROM supplier WHERE supplier_id = $supplier_id";
$supplier_lookup = mysql_query($query);

$s = mysql_fetch_array($supplier_lookup);

$smarty->assign('supplier_id',$s['supplier_id']);
$smarty->assign('supplier_name',$s['supplier_name']);
$smarty->assign('supplier_account',$s['supplier_account']);
$smarty->assign('supplier_contact_name',$s['supplier_contact_name']);
$smarty->assign('supplier_phone',$s['supplier_phone']);
$smarty->assign('supplier_fax',$s['supplier_fax']);
$smarty->assign('supplier_email',$s['supplier_email']);
$smarty->assign('supplier_address1',$s['supplier_address1']);
$smarty->assign('supplier_address2',$s['supplier_address2']);
$smarty->assign('supplier_address3',$s['supplier_address3']);
$smarty->assign('supplier_town',$s['supplier_town']);
$smarty->assign('supplier_county',$s['supplier_county']);
$smarty->assign('supplier_postcode',$s['supplier_postcode']);
$smarty->assign('supplier_order_method',$s['supplier_order_method']);
$smarty->assign('supplier_delivery_day',$s['supplier_delivery_day']);
$smarty->assign('supplier_active',$s['supplier_active']);
$smarty->assign('supplier_recurring',$s['supplier_recurring']);
$smarty->assign('methods', array('Phone','Fax','Email'));
$smarty->assign('days',array(0 => 'Sunday',
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday'));

$smarty->assign('calendar',get_availability($supplier_id));

$producers_lookup = mysql_query("SELECT member_id, CONCAT(member_first_name, ' ',member_last_name) AS member_name
									FROM member WHERE supplier_id = {$supplier_id}");
$count=0;
print mysql_error();
while($p = mysql_fetch_array($producers_lookup))
{
	$producer[$count]['member_id'] = $p['member_id'];
	$producer[$count]['member_name'] = $p['member_name'];
	$count++;
	}
$smarty->assign('producer',$producer);

$members[0] = '---SELECT TO ADD---';
$members_lookup = mysql_query("SELECT member_id, CONCAT(member_first_name, ' ',member_last_name) AS member_name
									FROM member WHERE supplier_id IS NULL ORDER BY member_name");
while($m = mysql_fetch_array($members_lookup))
{
		$members[$m['member_id']] = $m['member_name'];
		}
$smarty->assign('members',$members);

$smarty->assign('javascripts',array('java_object.js','availability.js'));

$smarty->display('suppliers_detail.tpl');

?>
