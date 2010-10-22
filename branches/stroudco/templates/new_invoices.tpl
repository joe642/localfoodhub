{* Smarty *}

{include file="header.tpl"}
</div>

<div class="WebControl">
	<h2 class="no_print">Invoices for {$date|date_format:"%A, %e %B, %Y"}</h2>

{if $invoices_posted == 'TRUE'}
	<p class="no_print"><em class="no_print">These invoices have already been posted.</em></p>
{else}
	<p class="no_print">These invoices have not yet been posted to the member balances.  To update account balances, click below.</p>
	<form action="{$smarty.server.PHP_SELF}" method="POST" class="no_print">
	<input type="hidden" name="function" value="save">
	<center><input type="submit" value="Post Invoices" class="no_print"></center>
	</form>
{/if}

</div>

<div class="Main">


{section name=i loop=$invoices}
{assign var="member_id" value=$invoices[i]->member->member_id}

<div class="invoice">
<a anchor="{$member_id}">
	<table noborder cellspacing="0" cellpadding="2">
		<tr class="print_header">
			<th align="left"><img src="{$logo}" width="200px"></th>
			<th align="right" colspan="5" font-size="+1">{$title}</th>

		<tr>
			<th align="left"><br>{$invoices[i]->member->firstname} {$invoices[i]->member->lastname}</th>
		<th align="right" colspan="4">{$invoices[i]->order_date|date_format:"%e %B, %Y"}
			<br>Invoice: {$invoices[i]->number}</th>
		</tr>
		<tr>
			<td colspan="5">{$invoices[i]->member->address1}<br>
					{if !empty($invoices[i]->member->address2)}{$invoices[i]->member->address2}<br>{/if}
					{if !empty($invoices[i]->member->address3)}{$invoices[i]->member->address3}<br>{/if}
					{$invoices[i]->member->town}<br>
					{$invoices[i]->member->county}<br>
					{$invoices[i]->member->postcode}</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr valign="bottom">
			<th align="left">Product</th>
			<th align="right" style="padding-right:1em;">Request</th>
			<th align="right" style="padding-right:1em;">Delivered</th>
			<th align="right" style="padding-right:1em;">Unit Price</th>
			<th align="right" style="padding-right:1em;">Product Total</th>
		</tr>
{section name=pr loop=$invoices[i]->orders}
		<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
			<td style="font-weight:600; color: green;">{$invoices[i]->orders[pr]->product_name}</td>
			<td align="right" style="padding-right:1em;">{$invoices[i]->orders[pr]->quantity_requested|string_format:"%.2f"}</td>
			<td align="right" style="padding-right:1em;">{$invoices[i]->orders[pr]->quantity_delivered|string_format:"%.2f"}</td>
			<td align="right" style="padding-right:1em;">{$invoices[i]->orders[pr]->unit_price|string_format:"%.2f"}</td>
			<td align="right" style="padding-right:1em;">{$invoices[i]->orders[pr]->total|string_format:"%.2f"}</td>
		</tr>
{/section}
{if $use_VAT == 1}
		<tr>
			<th colspan="4" align="right">Product Total</th>
			<td align="right">{$invoices[i]->order_gross|string_format:"%.2f"}</td>
		</tr>
{* Volunteer discounts will be deprecated once the group discounts are in place
	<tr>
		<th colspan="4" align="right">Volunteer Discount</th>
			<td align="right">{$invoices[i]->discount|string_format:"%.2f"|default:"0:00"}</td>
	</tr>
*}
	<tr>
		<th colspan="4" align="right">VAT included in Total</th>
		<td align="right">{$invoices[i]->vat|string_format:"%.2f"|default:"0.00"}</td>
	</tr>
{/if}
	<tr>
		<th colspan="4" align="right">ORDER TOTAL</th>
		<td align="right"><strong>{$invoices[i]->order_net|string_format:"%.2f"}</strong></td>
	</tr>
{if $invoices_posted != 'TRUE'}
	<tr>
		<th colspan="5">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="4" align="right">Opening Balance</th>
		<td align="right"><strong>{$invoices[i]->member->balance|string_format:"%.2f"|default:"0.00"}</td>
	</tr>
	<tr>
		<th colspan="4" align="right">Payment Received</th>
		<td align="right"><strong>{$invoices[i]->payment_received|string_format:"%.2f"}</td>
	<tr>
		<th colspan="4" align="right">Current Balance</th>
		<td align="right"><strong>{$invoices[i]->ending_balance|string_format:"%.2f"}</strong></td>
	</tr>
{else}
	<tr>
		<th colspan="5">&nbsp;</th>
	</tr>
	<tr>
		<th colspan="4" align="right">Current Balance</th>
		<td align="right"><strong>{$invoices[i]->member_balance|string_format:"%.2f"}</strong></td>
	</tr>
{/if}
</table>
<br>

{if $invoices_posted != 'TRUE'}<p class="no_print"><a href="adjust_invoice.php?member_id={$invoices[i]->member->member_id}">Adjust this invoice</a></p>{/if}
<p class="print_header"><em>{$invoice_message}</em></p>

</center>
</div>
{/section}

{if $invoices_posted != 'TRUE'}<p class="no_print"><a href="adjust_invoice.php">Create a new invoice for another member</a></p>{/if}


{include file="footer.tpl"}
