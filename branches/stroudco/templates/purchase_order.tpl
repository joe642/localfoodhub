{* Smarty *}
	
{include file='header.tpl'}

<p>
{$email_response}
</p>

<table cellpadding="3" width="100%">
	
{assign var=suppliername value=''}
{assign var=total value=0}
{assign var=vat value=0}
{section name=a loop=$products}
	{if $products[a].supplier_name != $suppliername}
		{assign var=suppliername value=$products[a].supplier_name}
		{if $total > 0}
			<tr>
				<th colspan="5" align="right">Net Total </th>
				<td align="right">{$total|string_format:"%.2f"}</td>
				<td >&nbsp;</td>
			</tr>
			<tr>
					<th colspan="5" align="right">VAT</th>
					<td align="right">{$vat|string_format:"%.2f"}</td>
					<td >&nbsp;</td>
			</tr>
			<tr>
					<th colspan="5" align="right">Total</th>
					<td align="right">{$total+$vat|string_format:"%.2f"}</td>
					<td >&nbsp;</td>
			</tr>
	{assign var=total value=0}
	{assign var=vat value=0}
	{/if}
<tr><td colspan="8">&nbsp;</td></tr>
<tr><td colspan="8"><h2>{$suppliername}</h2></td></tr>
	<tr>
		<th align="left">Code</th>
		<th align="left" width="35%">Product</th>
		<th align="left" width="15%">Unit</th>		
		<th align="right">Unit&nbsp;Cost</th>
		<th align="right">&nbsp;&nbsp;Qty</th>
	    <th align="right">&nbsp;&nbsp;Sub-Total</th>
	    <th align="right">&nbsp;&nbsp;VAT</th>
	</tr>
	{/if}
	
	<tr>
		<td>{$products[a].product_code}</td>
		<td>{$products[a].product_name}</td>
		<td align="left"> {$products[a].product_units} </td>
		<td align="right">{$products[a].order_current_price|string_format:"%.2f"}</td>
		<td align="right">{$products[a].units|string_format:"%.0f"}</td>
	    <td align="right">{$products[a].cost|string_format:"%.2f"}</td>
	    <td align="right">{$products[a].VAT|string_format:"%.2f"}</td>
{assign var=total value=$total+$products[a].cost}
{assign var=vat value=$vat+$products[a].VAT}
	</tr>
{/section}

	<tr>
		<th colspan="5" align="right">Net Total </th>
		<td align="right">{$total|string_format:"%.2f"}</td>
	    <td >&nbsp;</td>
	</tr>
        <tr>
                <th colspan="5" align="right">VAT</th>
                <td align="right">{$vat|string_format:"%.2f"}</td>
                <td >&nbsp;</td>
        </tr>
        <tr>
                <th colspan="5" align="right">Total</th>
                <td align="right">{$total+$vat|string_format:"%.2f"}</td>
                <td >&nbsp;</td>
        </tr>
</table>

{include file='footer.tpl'}
