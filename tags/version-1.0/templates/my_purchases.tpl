{* Smarty *}

{include file="header.tpl"}
<center>

<h2>Purchases for {$order_date|date_format:"%A, %e %B %Y"}</h2>
{if !empty($invoice_number)}<strong>Invoice:</strong> {$invoice_number}{else}<p class="warning">{$warning}</p>{/if}<br>

<table>
		<tr valign="bottom">
			<th align="left">Product</th>
			<th align="right" style="padding-right:1em;">Request</th>
			<th align="right" style="padding-right:1em;">Delivered</th>
			<th align="right" style="padding-right:1em;">Unit Price</th>
			<th align="right" style="padding-right:1em;">Product Total</th>
		</tr>

{section name=pr loop=$orders}
		<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
			<td style="font-weight:600; color: green;">{$orders[pr].product_name}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].requested}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].delivered}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].vat_rate}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].product_total}</td>
		</tr>
{/section}

		<tr>
			<th colspan="4" align="right">Product Total</th>
			<td align="right">{$order_total|string_format:"%.2f"}</td>
		</tr>
<!-- no volunteer discount
	<tr>
		<th colspan="4" align="right">Volunteer Discount</th>
			<td align="right">{$discount|string_format:"%.2f"|default:"0:00"}</td>
	</tr>
-->
{if $use_VAT == 1}
	<tr>
		<th colspan="4" align="right">VAT included in Total</th>
		<td align="right">{$VAT_amount|string_format:"%.2f"|default:"0.00"}</td>
	</tr>
{/if}
	<tr>
		<th colspan="4" align="right">ORDER TOTAL</th>
		<td align="right"><strong>{$order_total+$discount|string_format:"%.2f"}</strong></td>
	</tr>

</table>
<br>
<form action="purchases.php" name="ViewDate" method="POST">
View purchases from: {html_options name="order_date" options=$history selected="$selected_date" onChange="document.ViewDate.submit();"} <a 
href="javascript:void()" onClick="document.ViewDate.submit();">Go</a>
</form>
<br>
<strong>{$volunteer_message}</strong></p>

</center>



{include file="footer.tpl"}
