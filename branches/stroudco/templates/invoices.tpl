{* Smarty *}

{include file="header.tpl"}
</div>

<div class="WebControl">
	<h2 class="no_print">Invoices for {$date|date_format:"%A, %e %B, %Y"}</h2>

{if $invoices_posted == 'TRUE'}
	<p class="no_print"><em class="no_print">These invoices have already been posted.</em></p>
{else}
	{if $not_recieved > 0}
		<p class="no_print"><em class="no_print">Cannot post invoices yet as not all suppliers goods have been received.</em></p>
	{else}
		<p class="no_print">These invoices have not yet been posted to the member balances.  To update account balances, click below.</p>
		<form action="post_invoices.php" method="POST" class="no_print">
		<center><input type="submit" value="Post Invoices" class="no_print"></center>
		</form>
	{/if}
{/if}

</div>

<div class="Main">


{section name=in loop=$members}
{assign var="member_id" value=$members[in].member_id}

<div class="invoice">
<a anchor="{$members[in].member_id}">
	<table noborder cellspacing="0" cellpadding="2" width="100%">
		<tr class="print_header">
			<th align="left"><img src="{$logo}" width="200px"></th>
			<th align="right" colspan="6" font-size="+1">{$title}</th>
		<tr>
			<th align="left" colspan="3"><br>{$members[in].member_name} - {$members[in].membership_num}</th>
		<th align="right" colspan="3">{$date|date_format:"%e %B, %Y"}<br>Invoice: {$members[in].invoice_number}</th>
		</tr>
		<tr>
			<td colspan="6">{$members[in].member_address1}<br>
					{if !empty($members[in].member_address2)}{$members[in].member_address2}<br>{/if}
					{if !empty($members[in].member_address3)}{$members[in].member_address3}<br>{/if}
					{$members[in].member_town}<br>
					{$members[in].member_county}<br>
					{$members[in].member_postcode}</td>
		</tr>
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr valign="bottom">
			<th align="left">Product</th>
			<th align="right" style="padding-right:1em;">Unit</th>
			<th align="right" style="padding-right:1em;">Requested</th>
			<th align="right" style="padding-right:1em;">Delivered</th>
			<th align="right" style="padding-right:1em;">Unit Price</th>
			<th align="right" style="padding-right:1em;">Product Total</th>
		</tr>

{section name=pr loop=$orders}
{if $orders[pr].member == $member_id}
		<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
			<td style="font-weight:600; color: green;">{$orders[pr].product_name}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].product_units}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].requested|string_format:"%.0f"}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].delivered|string_format:"%.0f"}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].unit_price}</td>
			<td align="right" style="padding-right:1em;">{$orders[pr].product_total}</td>
		</tr>
{/if}
{/section}

		<tr>
			<th colspan="5" align="right">Product Total</th>
			<td align="right">{$members[in].gross|string_format:"%.2f"}</td>
		</tr>
<!-- no volunteer discount to apply
	<tr>
		<th colspan="4" align="right">Volunteer Discount</th>
			<td align="right">{$members[in].discount|string_format:"%.2f"|default:"0:00"}</td>
	</tr>
-->
{if $use_VAT == 1}
	<tr>
		<th colspan="5" align="right">VAT included in Total</th>
		<td align="right">{$members[in].vat|string_format:"%.2f"|default:"0.00"}</td>
	</tr>
{/if}
	<tr>
		<th colspan="5" align="right">ORDER TOTAL</th>
		<td align="right"><strong>{$members[in].total|string_format:"%.2f"}</strong></td>
	</tr>
{if $invoices_posted != 'TRUE'}
	<tr>
		<th colspan="6">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="5" align="right">Opening Balance</th>
		<td align="right"><strong>{math|string_format:"%.2f"|default:"0.00" equation="0-b" b=$members[in].member_balance}</td>
	</tr>
	<tr>
		<th colspan="5" align="right">Payment Received</th>
		<td align="right"><strong>{$members[in].amount_paid|string_format:"%.2f"}</td>
	<tr>
		<th colspan="5" align="right">Current Balance</th>
		<td align="right"><strong>{math|string_format:"%.2f" equation="0-b" b=$members[in].current_balance}</strong></td>
	</tr>
{else}
	<tr>
		<th colspan="6">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="5" align="right">Current Balance</th>
		<td align="right"><strong>{math|string_format:"%.2f" equation="0-b" b=$members[in].member_balance}</strong></td>
	</tr>
{/if}
</table>
<br>

{if $invoices_posted != 'TRUE'}<p class="no_print"><a href="adjust_invoice.php?member_id={$members[in].member_id}">Adjust this invoice</a></p>{/if}
<p class="print_header"><em>{$invoice_message}</em></p>
<p class="print_header"><strong>{$volunteer_message}</strong></p>

</center>
</div>
{/section}

{include file="footer.tpl"}
