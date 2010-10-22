{* Smarty *}
	
{include file='header.tpl'}

{$message}
<form action="{$smarty.server.PHP_SELF}" method="POST">
{html_options name=member_id options=$members}
<input type="submit" value="Go">
</form>


{include file='footer.tpl'}
