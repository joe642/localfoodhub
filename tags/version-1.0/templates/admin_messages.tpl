{* Smarty *}
{assign var=suppress_headers value=1}
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
<script src="../thirdparty/javascripts/jquery.js" type="text/javascript"></script>
<link href="../facebox/facebox.css" media="screen" rel="stylesheet" type="text/css">
<script src="../facebox/facebox.js" type="text/javascript"></script>
<script src="../javascripts/java_object.js" type="text/javascript"></script>
<script src="../javascripts/updateorder.js" type="text/javascript"></script>
<SCRIPT SRC="../javascripts/logintest.js"></SCRIPT>
<script type="text/javascript" >
{literal}
$(document).ready(function() {
    $('a[rel*=facebox]').facebox();
  });

 function getInfo(product_id)
 {
 
 	}
{/literal}
</script>

{include file='header.tpl'}


<div id="messagelist_frame">

<h1>{if $done == 0}New {else}Old {/if}{$pagetitle}</h1>

<p>Click Subject, or ... to view full message. Click From name to reply by email. <br />
{if $done == 0}<br />Tick Done on any messages you're finished with and then click Set Done to remove them from the list. <br />
You can still <a href="messages.php?done=1">view them if you need to</a>.
{else}
<br />Go <a href="messages.php">back to the new messages...</a> {/if}</p>
<form action="message_status.php" method="post" name="update_status">
<table noborder cellspacing="2" cellpadding="2" width="95%">
<tr valign="bottom">
	<th valign="top" align="left">Done{if $done == 0}<br />
<input name="update_status" type="submit" value="Set Done"/>{/if}</th>
	<th valign="top" align="left">No.</th>
	<th valign="top" align="left">Date</th>
	<th align="left" valign="top">Subject</th>
	<th align="left" valign="top">From</th>
	<th align="left" valign="top">Message (first 50 chars) </th>
</tr>
{section name=mn loop=$messages}
<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
<td><input type="checkbox" name="status_done[]" value="{$messages[mn].msg_id}" {$messages[mn].done}></td>
	<td>{$messages[mn].msg_id}</td>
	<td>{$messages[mn].date}</td>
	<td><a href="../ajax/message_details.php?message_id={$messages[mn].msg_id}" rel="facebox">{$messages[mn].subject}</a></td>
	<td><a href="mailto:{$messages[mn].email}?subject=Re: {$messages[mn].subject}">{$messages[mn].from}</a></td>
	<td>{$messages[mn].body}</td>
</tr>
{/section}
{if $done == 0}
<tr>
<td><input name="update_status" type="submit" value="Set Done"/></td>
<td colspan="5">&nbsp;</td>
</tr>
{/if}
</table>
</form>
<p>
<strong>You can view <a href="messages.php?done=1">messages where the status is set to Done...</a></strong></p>
{include file='footer.tpl'}
