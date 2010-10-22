{* Smarty *}
	
{include file='header.tpl'}

<p class="warning">Please double check the quantities you have entered, as confirming will automatically allocate products to customers on a first-ordered, first-filled basis</p>

<form action="allocate_products.php" method="POST">

<table cellpaddidng="2">
	
{assign var=suppliername value=''}
{section name=a loop=$products}
	{if $products[a].supplier_name != $suppliername}
		{assign var=suppliername value=$products[a].supplier_name}
<tr><td colspan="7">&nbsp;</td></tr>
<tr><td colspan="7"><h2>{$suppliername}</h2></td></tr>
	<tr>
		<th align="left">Code</th>
		<th align="left">Product</th>
		<th align="right">Requested</th>
		<th align="right" colspan="2">Order Amount</th>
		<th align="right" colspan="2">Delivered</th>
	</tr>
	{/if}
	
	<tr>
		<td>{$products[a].product_code}</td>
		<td>{$products[a].product_name}</td>
		<td align="right">{$products[a].request}</td>
		<td align="right">{$products[a].units}</td>
		<td> {$products[a].cases}</td>
		<td align="right"><input type="hidden" name="delivered[{$products[a].product_id}]" value="{$products[a].delivered}"><strong>{$products[a].delivered}</strong></td>
		<td> {$products[a].cases}</td>
	</tr>
{/section}
	<tr><td colspan="7">&nbsp;</td></tr>
	<tr>
		<td colspan="7" align="right"><input type="submit" name="PROCESS" value="Confirm Numbers"></td>
	</tr>
</table>
</form>
{include file='footer.tpl'}
