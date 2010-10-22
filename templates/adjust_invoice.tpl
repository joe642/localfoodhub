{* Smarty *}
	
{include file='header.tpl'}

<script language="Javascript">
<!--
{section name=j loop=$order}
	function enable_edit_{$order[j].order_id}()
{literal}	{{/literal}
		document.orders.current_price_{$order[j].order_id}.disabled = false;
		document.orders.delivered_{$order[j].order_id}.disabled = false;
		document.orders.update_stock_{$order[j].order_id}.disabled = false;
{literal}		}{/literal}
{/section} 
	function enable_new()
{literal}	{
		document.orders.new_product_id.disabled = false;
		document.orders.new_quantity.disabled = false;
		document.orders.update_new_order_stock.disabled = false;
		}
{/literal}
	function disable_submit()
{literal}	{
		document.orders.submit_button.disabled = true;
		}
{/literal}

-->
</script>

<h2>Order Details for {$member_name}</h2>

<form name="orders" action="adjust_invoice.php" method="POST">
<input type="hidden" name="member_id" value="{$member_id}">

<p><strong>Note:</strong> the unit price is the end user price.</p>
<table width="100%">
<tr>
	<th>&nbsp;</th><th align="left">Product<br>Name</th>
	<th align="left">Unit</th>
	<th align="left">Unit<br>
	Price</th>
	<th align="left">Requested</th><th align="left">Delivered</th>
	
	<th>Adjust<br>Stock</th>

</tr>
{section name=o loop=$order}
<tr>
	<td><input type="checkbox" name="order[]" value="{$order[o].order_id}" onClick="enable_edit_{$order[o].order_id}();"></td>
	<td>{$order[o].product_name}</td>
	<td>{$order[o].product_units}</td>
	<td><input type="text" name="current_price_{$order[o].order_id}" value="{$order[o].current_price}" size="6" disabled></td>
	<td><input type="text" name="request_{$order[o].order_id}" value="{$order[o].request|string_format:"%.0f"}" size="6" disabled></td>
	<td><input type="text" name="delivered_{$order[o].order_id}" value="{$order[o].delivered|string_format:"%.0f"}" size="6" disabled></td>

	<td><input type="checkbox" name="update_stock_{$order[o].order_id}" disabled></td>

</tr>
{/section}
</table>
<br>
<table>
<tr>
	<td>&nbsp;</td>
	<th>New Product</th>
	<th>Quantity</th>
	<th>Adjust Stock</th>
</tr>
<tr>	
	<td><input type="checkbox" name="new_order" onClick="enable_new();"></td>
	<td>{html_options name="new_product_id" options=$products disabled=true}</td>
	<td><input type="text" name="new_quantity" size="6" disabled></td>
	<td><input type="checkbox" name="update_new_order_stock"  disabled></td>
</tr>
</table>
<center>
<input type="submit" name="submit_button" value="Post Changes">
</form><br>
<form action="invoice.php#{$member_id}" method="POST">
<input type="hidden" name="print_invoice" value="Print Invoices">
<input type="submit" value="Return to Invoices">
</form>
</center>
{include file='footer.tpl'}
