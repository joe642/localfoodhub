{* Smarty *}
	
{include file='header.tpl'}
	
<h2>Pending Orders (not paid) for {$orderdate}</h2>

{section name=p loop=$members}
{assign var="total" value=0.00}
<table width="95%"><tr><td>
<table width="100%">
	<tr>
		<th style="text-align: left;" colspan="2" width="50%">{$members[p].member_name}</th>
		<th style="text-align: left;" width="15%">Unit</th>
		<th style="text-align: right;" width="10%">Quantity</th>
		<th style="text-align: right;" width="12%">Price</th>
		<th style="text-align: right;" width="12%">Total</th>
	</tr>{assign var="member_id" value=$members[p].member_id}
{section name=o loop=$orders}
{if $orders[o].member_id == $member_id}
	<tr>
		<td style="width: 2em; text-align: left;">&nbsp;</td>
		<td >{$orders[o].product_name}: {$orders[o].product_description}</td>
		<td >{$orders[o].units}</td>
		<td style="text-align: right;">{$orders[o].quantity|string_format:"%.0f"}</td>
		<td style="text-align: right;">&pound;{$orders[o].unit_price}</td>
		<td style="text-align: right;">&pound;{$orders[o].total_price}</td>
		{assign var="total" value=`$total+$orders[o].total_price`}
	</tr>
{/if}
{/section}
	<tr>
		<td colspan="5">&nbsp;</td>
		<td  style="text-align: right; border-top: thin solid black;"><strong>&pound;{$total|string_format:"%.2f"}</strong></td>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
</table>
</td></tr></table>
{/section}


{include file='footer.tpl'}
