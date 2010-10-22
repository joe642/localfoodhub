<?php

################################################################################
#                                                                              #
#		Filename:	header.php.inc				       #
#		Author:		Martin Settle				       #
#               Created:	19 May 2006				       #
#		Description:	upper page template			       #
#		Calls:		nothing					       #
#		Called by:	All pages				       #
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
	- 2006.09.01 menu override created
	- 2008.06.03 autoregistration functions added
	- 2008.10.30 control/main menus separated

*/

if(!empty($menu_override))
{
	// this just causes the menu to not appear in certain pages, forcing 
	// navigation by forms (i.e. product ordering pages)
	$control_menu = array(
			array ( 'address' => "$BaseURL/my_order.php",
	       			'label' => 'Shopping Basket' ),
			array ( 'address' => "$BaseURL/memberdetails.php",
				'label' => 'My Details' ) 
				);
	}

elseif(empty($_SESSION['member_id']))
{
	$menu = array(  
			array ( 'address' => "$BaseURL/info.php",
		               'label' => 'Home' ),
		    //   array ( 'address' => "$BaseURL/contact.php",
		    //           'label' => "Contact Us" ),
		    //   array ( 'address' => "$BaseURL/links.php",
		    //           'label' => 'Links' ),
		        array ( 'address' => "$BaseURL/login.php",
		                'label' => 'Login' )
			);
	if(get_config_value('allow_autoregistration') == TRUE) $menu[] = array('address' => "$BaseURL/join.php", 'label' => 'Join');
	
	$main_menu = array(
			array ( 'address' => "$BaseURL/info.php",
		               'label' => 'Home' ),
		       //array ( 'address' => "$BaseURL/contact.php",
		       //        'label' => "Contact Us" ),
		       //array ( 'address' => "$BaseURL/links.php",
		       //        'label' => 'Links' )
		);
	}
// we always retain the SESSION['producer'] variable but also set 'act_as_member' to control the menus	
elseif(!empty($_SESSION['producer']) && empty($_SESSION['act_as_member']) )
{
	$menu = array ( 
			array ( 'address' => "$BaseURL/producer_exit.php",
	       			'label' => 'Go to Member Page' ));
              
	  $control_menu = array(
			array ( 'address' => "$BaseURL/producer.php",
				'label' => "Producer Control Panel"),
			array ( 'address' => "$BaseURL/producer_exit.php",
	       			'label' => 'Member Area' ),
			array ( 'address' => "$BaseURL/logout.php",
				'label' => 'Logout'));
	
	}
elseif(empty($_SESSION['admin']))
{
	$menu = array ( 
			array ( 'address' => "$BaseURL/productlist.php",
				'label' => 'Browse and Order'),
			array ( 'address' => "$BaseURL/my_order.php",
	       			'label' => 'My Shopping Basket' ),
	       		array ( 'address' => "$BaseURL/purchases.php",
	       			'label' => 'My purchases' ),
			array ( 'address' => "$BaseURL/recurring.php",
				'label' => 'Recurring Orders' ),
			//array ( 'address' => "$BaseURL/volunteer.php",
			//	'label' => 'Volunteering Records'),
			array ( 'address' => "$BaseURL/memberdetails.php",
				'label' => 'My Details' ),
			array ( 'address' => "$BaseURL/change_password.php",
				'label' => 'Change Password'),
			array ( 'address' => "$BaseURL/administration",
				'label' => 'Admin' ),
			//array ( 'address' => "$BaseURL/info.php",
			//	'label' => 'About' ),
			//array ( 'address' => "$BaseURL/contact.php",
			//	'label' => 'Contact Us' ),
			//array ( 'address' => "$BaseURL/links.php",
			//	'label' => 'Links' ),
			array ( 'address' => "$BaseURL/logout.php",
				'label' => 'Logout' )
			);
	// if(!empty($_SESSION['producer_id']))
	if(!empty($_SESSION['act_as_member']))
	{
		$menu[] = array( 'address' => "$BaseURL/producer_return.php",
				 'label' => 'Producer Control Panel');
		}

	$main_menu = array(
				array ( 'address' => "$BaseURL/productlist.php",
				'label' => 'Stroudco Shop'),
				array ( 'address' => "$BaseURL/my_order.php",
	       			'label' => 'Shopping Basket' ),
	       		array ( 'address' => "$BaseURL/purchases.php",
	       			'label' => 'Order History' ),

			//array ( 'address' => "$BaseURL/recurring.php",
			//	'label' => 'Repeat Orders' ),
			//array ( 'address' => "$BaseURL/volunteer.php",
			//	'label' => 'Volunteering'),
			//array ( 'address' => "$BaseURL/info.php",
			//	'label' => 'About' ),
			//array ( 'address' => "$BaseURL/contact.php",
			//	'label' => 'Contact Us' ),
			//array ( 'address' => "$BaseURL/links.php",
			//	'label' => 'Links' )
				);
		
	$control_menu = array(
			array ( 'address' => "$BaseURL/my_order.php",
	       			'label' => 'Shopping Basket' ),
			array ( 'address' => "$BaseURL/memberdetails.php",
				'label' => 'My Details' ),
			array ( 'address' => "$BaseURL/change_password.php",
				'label' => 'Change Password'),
			//array ( 'address' => "$BaseURL/administration",
			//	'label' => 'Administration' )
			array ( 'address' => "$BaseURL/send_msg.php",
				'label' => 'Send Message' )
				);
	// if(!empty($_SESSION['producer_id']))
	if(!empty($_SESSION['act_as_member']))
	{
		$control_menu[] = array( 'address' => "$BaseURL/producer_return.php",
							'label' => 'Producer Control Panel');
		}

	$control_menu[] = 	array ( 'address' => "$BaseURL/logout.php",
							'label' => 'Logout' );



	$date_notice = "Ordering for ";
	$date = $_SESSION['order_date'];
	}

else
{
	$menu = array (
			array ( 'address' => "$BaseURL/administration/order_date.php",
				'label' => 'Change Admin Date'),
			array ( 'address' => "$BaseURL/administration/calendar.php",
				'label' => 'Set Up Calendar'),
			array ( 'address' => "$BaseURL/administration/supplier_orders.php",
				'label' => 'Order Products' ),
			array ( 'address' => "$BaseURL/administration/process_delivery.php",
				'label' => 'Receive Goods' ),
			array ( 'address' => "$BaseURL/administration/sorting.php",
				'label' => 'Sort Goods'),
			array ( 'address' => "$BaseURL/administration/invoice.php",
				'label' => 'Print Invoices'),
			array ( 'address' => "$BaseURL/administration/credits.php",
				'label' => 'Receipts and Credits'),
			array ( 'address' => "$BaseURL/administration/suppliers.php",
				'label' => 'Suppliers and Products'),
			array ( 'address' => "$BaseURL/memberdetails.php",
				'label' => 'Edit Member'),
			array ( 'address' => "$BaseURL/administration/new_member.php",
				'label' => 'Add Member'),
			array ( 'address' => "$BaseURL/administration/reset_password.php",
				'label' => "Reset Member Password"),
			array ( 'address' => "$BaseURL/administration/account_statement.php",
				'label' => 'Account Statements'),
			array ( 'address' => "$BaseURL/administration/infopages.php",
				'label' => 'Edit Info Pages'),	
			array ( 'address' => "$BaseURL/administration/config_values.php",
				'label' => 'Configuration'),        
			array ( 'address' => "$BaseURL/administration/logout.php",
				'label' => 'Logout Admin')
			);

	$control_menu = array(
			array ( 'address' => "$BaseURL/administration/order_date.php",
				'label' => 'Change Admin Date'),
			array ( 'address' => "$BaseURL/administration/calendar.php",
				'label' => 'Set Up Calendar'),
			//array ( 'address' => "$BaseURL/administration/infopages.php",
			//	'label' => 'Public Information'),	
			array ( 'address' => "$BaseURL/administration/config_values.php",
				'label' => 'Configuration'),
			array ( 'address' => "$BaseURL/administration/change_password.php",
				'label' => 'Admin Password'),        
			array ( 'address' => "$BaseURL/administration/logout.php",
				'label' => 'Logout Admin')
			);
			
	$main_menu = array(
			array ( 'address' => "$BaseURL/administration/messages.php",
				'label' => 'Messages' ), 
			array ( 'address' => "$BaseURL/administration/pending.php?select_by=customer",
				'label' => 'Pending' ),  
			array ( 'address' => "$BaseURL/administration/supplier_orders.php",
				'label' => 'Order' ),
			array ( 'address' => "$BaseURL/administration/process_delivery.php",
				'label' => 'Receive' ),
			array ( 'address' => "$BaseURL/administration/sorting.php",
				'label' => 'Sort'),
			array ( 'address' => "$BaseURL/administration/invoice.php",
				'label' => 'Invoice'),
			array ( 'address' => "$BaseURL/administration/credits.php",
				'label' => 'Receipts'),
			array ( 'address' => "$BaseURL/memberdetails.php",
				'label' => 'Member Admin'),
			//array ( 'address' => "$BaseURL/administration/new_member.php",
			//	'label' => 'Add Member'),
			//array ( 'address' => "$BaseURL/administration/reset_password.php",
			//	'label' => "Passwords"),
			array ( 'address' => "$BaseURL/administration/account_statement.php",
				'label' => 'Statements'),
			array ( 'address' => "$BaseURL/administration/suppliers.php",
				'label' => 'Suppliers/Products')
			);

	$date_notice = "Administering orders for ";
	$date = $_SESSION['admin_date'];
	}

	// MODS DJC - $smarty->assign('main_menu',array_reverse($main_menu));
	if (is_array($main_menu))  { $smarty->assign('main_menu',array_reverse($main_menu)); }
	$smarty->assign('control_menu',$control_menu);
	$smarty->assign('menu',$menu);
	if(!empty($date_notice)) $smarty->assign('date_notice',$date_notice);
	if(!empty($date)) $smarty->assign('date',$date);
?>
