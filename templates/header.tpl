{* Smarty *}
{if empty($suppress_headers)}
<html>
<!--  Copyright 2010 Trellis Ltd. Licensed under the Apache License, Version 2.0 -->
	<head>
{/if}
		<title>{$pagetitle}</title>
{section name=j loop=$javascripts}
		<SCRIPT LANGUAGE="JavaScript" SRC="javascripts/{$javascripts[j]}"></SCRIPT>{/section}
		<link REL='StyleSheet' HREF='screen.css' TYPE='text/css' MEDIA='screen'>
		<link REL='StyleSheet' HREF='print.css' TYPE='text/css' MEDIA='print'>
		<!--[if IE]>
		<link href="ie.css" media="screen" rel="stylesheet" type="text/css">
		<![endif]-->

	</head>
	
	<body>
			<div id="main_frame">
			
				<div id="control_frame">
					{if empty($control_menu)}
					<div id="loginreg">
					<form action="login.php"  method="POST" name="LoginRegister">
						<input type="hidden" name="tested" value="0">
						<strong>Member Login</strong><br />
						Email:&nbsp;<input type="text" name="Email" size="14">
						<span id="password_spacer">
						Password:&nbsp;<input type="password" name="Password" size="10">
						<input type="submit" name="Login" value="Login">
						</span>
				  </form>
				  </div>
					{else}
					{section name=c loop=$control_menu}
					<a href="{$control_menu[c].address}">{$control_menu[c].label}</a>
					{/section}
					{/if}
				</div>
				
				<div id="banner_frame">
					{$title}
					
					{if !empty($date_notice)}
					<div id="date_notice">
					{$date_notice} {if !empty($smarty.session.admin_date)}{$smarty.session.admin_date|date_format:"%A, %e %b, %Y"}{else}{$smarty.session.order_date|date_format:"%A, %e %b, %Y"}{/if}
					{if !empty($smarty.session.member_id)} <br/>
					Member: {$smarty.session.membership_num}, {$smarty.session.first_name} {$smarty.session.last_name}
					{/if}
					</div>
					{/if}

				</div>
				
				<div id="menu_frame">
					<dl>{section name=m loop=$main_menu}
						<dt><a href="{$main_menu[m].address}">{$main_menu[m].label}</a></dt>{/section}
					</dl>&nbsp;
				</div>
				

				<div id="content">
