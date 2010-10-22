{* Smarty *}
	
{include file='header.tpl'}
<br><br>
<table noborder><tr><td>
<center>

<h1>Purchases for {$order_date|date_format:"%A, %e %B, %Y"}</h1>


<table noborder cellspacing="0" cellpadding="2">
<tr valign="bottom"><th align="left">Product</th><th align="right">Unit Price</th><th align="right">Quantity</th><th align="right">Product Total</th></tr>

{section name=pr loop=$order}
<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
	<td style="font-weight:600; color: green;">{$order[pr].product_name}</td>
	<td align="right">{$order[pr].unit_price}</td>
	<td align="right">{$order[pr].order_quantity_requested}</td>
	<td align="right">{$order[pr].order_product_total}</td>
</tr>
{/section}
<tr>
	<th colspan="3" align="right">Product Total</th>
	<td align="right">{$order_total|string_format:"%.2f"}</td>
</tr>
<!-- no volunteer discount
<tr>
	<th colspan="3" align="right">Volunteer Discount</th>
	<td align="right">{$discount|string_format:"%.2f"}</td>
</tr>
-->
{if $use_VAT == 1}
<tr>
	<th colspan="3" align="right">VAT</th>
	<td align="right">{$VAT_amount|string_format:"%.2f"}</td>
</tr>
{/if}

<tr>
	<th colspan="3" align="right">ORDER TOTAL</th>
	<td align="right"><strong>{$order_total+$discount+$VAT_amount}</strong></td>
</tr>
</table>
<br>
<form action="purchases.php" name="ViewDate" method="POST">
View purchases from: {html_options name="order_date" options=$history onChange="document.ViewDate.submit();"}
</form>
<br>
<strong>{$volunteer_message}</strong>
</center>
</tr></td></table>

{include file='footer.tpl'}
