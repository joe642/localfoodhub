{* Smarty *}
	
{include file='header.tpl'}

<h2>Send a Message</h2>

{if !empty($done)}
	<p>{$done}</p>
{else}
{if ($to_id) == 0}
		<p>Message will be sent to the food hub administrator - please fill in the subject and message body below then click Send</p>
{/if}
{if !empty($error)}
		<p>{$error}</p>
{/if}

<form action="http://{$smarty.server.SERVER_NAME}{$smarty.server.PHP_SELF}" method="POST">
<input type="hidden" name="from_id" value="{$from_id}">
<input type="hidden" name="to_id" value="{$to_id}">
<table>

<tr>
	<th align="right">Subject</th>
	<td><input name="msg_subject" size="80" value="{$msg_subject}"></td>
</tr>
<tr>
	<th align="right" valign="top">Message</th>
	<td><textarea name="msg_body" textarea rows="10" cols="80">{$msg_body}</textarea></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="Send" value="Send"></td>
</tr>
</table>
{/if}
{include file='footer.tpl'}
