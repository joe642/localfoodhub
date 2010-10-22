{* Smarty *}
	
{include file='header.tpl'}

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
		<td><input type="hidden" name="products[{$smarty.section.a.index}][product_id]" value="{$products[a].product_id}"><input type="hidden" name="products[{$smarty.section.a.index}][supplier_name]" value="{$products[a].supplier_name}"><input type="hidden" name="products[{$smarty.section.a.index}][product_code]" value="{$products[a].product_code}">{$products[a].product_code}</td>
		<td><input type="hidden" name="products[{$smarty.section.a.index}][product_name]" value="{$products[a].product_name}">{$products[a].product_name}</td>
		<td align="right"><input type="hidden" name="products[{$smarty.section.a.index}][request]" value="{$products[a].request}">{$products[a].request}</td>
		<td align="right"><input type="hidden" name="products[{$smarty.section.a.index}][units]" value="{$products[a].units}">{$products[a].units|string_format:"%.2f"}</td>
		<td><input type="hidden" name="products[{$smarty.section.a.index}][cases]" value="{$products[a].cases}"> {$products[a].cases}</td>
		<td><input type="text" name="products[{$smarty.section.a.index}][delivered]" size="5"></td>
		<td> {$products[a].cases}</td>
	</tr>
{/section}
	<tr><td colspan="7">&nbsp;</td></tr>
	<tr>
		<td colspan="7" align="right"><input type="submit" value="Record Delivery"></td>
	</tr>
</table>
</form>
{include file='footer.tpl'}
