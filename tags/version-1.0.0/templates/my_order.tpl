{* Smarty *}
	
{include file='header.tpl'}
<table noborder width="100%"><tr><td>
<center>

<h2>My Order for {$order_date|date_format:"%A, %e %B, %Y"}</h2>


<table noborder cellspacing="0" cellpadding="2" width="95%">
<tr valign="bottom"><th align="left">Product</th>
<th align="left">Units</th>
<th align="right">Price</th>
<th align="right">Qty</th>
<th align="right">Total</th>
<!-- no recurring orders
<td>&nbsp;</td>
-->
</tr>

{section name=pr loop=$order}
<form action="recurring.php" name="recurring_{$smarty.section.pr.index}" method="POST"><input type="hidden" name="{$order[pr].id_type}" value="{$order[pr].id}"><input type="hidden" name="process" value="{$order[pr].recurring_process}"><tr style="font-size: 0.8em; background-color: {cycle values="#F8F8F8,#F0F0F0"} ">
	<td style="font-weight:600; color: green;">{$order[pr].product_name}</td>
	<td align="left">{$order[pr].product_units|lower}</td>
	<td align="right">{$order[pr].unit_price}</td>
	<td align="right">{$order[pr].order_quantity_requested|string_format:"%.0f"}</td>
	<td align="right">{$order[pr].order_product_total}</td>
<!-- no recurring orders	
	<td><a href="javascript:void(0);document.recurring_{$smarty.section.pr.index}.submit();">{$order[pr].recurring_type}</a></td>
-->
</tr></form>
{/section}
<tr>
	<th colspan="4" align="right">Product Total</th>
	<td align="right">{$order_total|string_format:"%.2f"}</td>
</tr>
<!-- no volunteer discount
<tr>
	<th colspan="4" align="right">Volunteer Discount</th>
	<td align="right">{$discount|string_format:"%.2f"}</td>
</tr>
-->
{if $use_VAT == 1}
<tr>
	<th colspan="4" align="right">VAT included</th>
	<td align="right">{$VAT_amount|string_format:"%.2f"}</td>
</tr>
{/if}

<tr>
	<th colspan="4" align="right">ORDER TOTAL</th>
	<td align="right"><strong>{$order_total+$discount|string_format:"%.2f"}</strong></td>
</tr>
<tr>
	<th colspan="4" align="right">Opening Balance</th>
	<td align="right">{math|string_format:"%.2f" equation="0-b" b=$opening_balance}</td>
</tr>
<tr>
	<th colspan="4" align="right">Payment Due</th>
	<td align="right"><strong>{$opening_balance+$order_total+$discount|string_format:"%.2f"}</strong></td>
</tr>
</table>
<br>
<form action="productlist.php" name="EditOrder" method="POST">
<input type="hidden" name="filter_type" value="">
<input type="submit" value="Edit these items" onClick="document.EditOrder.filter_type.value='current';">
<input type="submit" value="Add new items">
</form>
<br>
<form action="payment.php" method="POST">
<input type="hidden" name="payment_amount" value="{$opening_balance+$order_total+$discount}">
<input type="hidden" name="balance" value="{$opening_balance}">
<input type="submit" value="Confirm Order or Make Payment">

<p align="left"> In order to complete your order click the Confirm or Pay button above.<br/> 
If there is enough money in your account then your order will be placed immediately, otherwise make a payment and your order is confirmed automatically as soon as we recieve notice of that payment. </p>
</form>
<!--  <strong>{$volunteer_message}</strong> -->
</center>
</tr></td></table>

{include file='footer.tpl'}
