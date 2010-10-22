{* Smarty *}
	
{include file='header.tpl'}
	
<br>

<center>

<strong>Please select the supplier:</strong>
<br>

<form action="process_delivery.php" method="POST">

<select name="supplier_id">
{html_options options=$suppliers}
</select>
<br><br>
<input type="submit" value="Record Delivery">

</center>
</form>

{if $not_recieved > 0}
<p><strong>The following suppliers have not yet had goods recieved:</strong><br/>
{section name=n loop=$supplier_not_recieved}
{$supplier_not_recieved[n]}<br/>
{/section}
</p>
{else}
<p>All suppliers goods have been recieved. You may select a supplier to modify the receipt numbers if required.</p>
{/if}
{include file='footer.tpl'}
