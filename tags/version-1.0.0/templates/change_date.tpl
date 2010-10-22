{* Smarty *}
	
{include file='header.tpl'}
	
Please select the date you wish to use:<br><br>

<form action="{$smarty.server.PHP_SELF}" method="POST">
{html_options name=new_date options=$order_dates selected=$current_date}
<br>
<input type="submit" value="Change Date">
</form>


{include file='footer.tpl'}
