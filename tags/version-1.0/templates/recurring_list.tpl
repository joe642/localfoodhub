{* Smarty *}
	
{include file='header.tpl'}

<center>	
<h1>My Recurring Orders</h1>

<p><em>Add recurring orders to this page using the controls found on "My Current Order".</em></p>
	
<table>	
	<tr>
		<th align="left" style="padding-right: 5;">Product</th>
		<th align="right" style="padding-right: 5;">Quantity</th>
		<th align="left" style="padding-right: 5;">Frequency</th>
		<th align="left" style="padding-right: 5;">Next Delivery</th>
		<th>&nbsp;</th>
	</tr>
{section name=list loop=$recurring}
	<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
		<td align="left" style="padding-right: 5;">{$recurring[list].recurring_product}</td>
		<td align="right" style="padding-right: 5;">{$recurring[list].recurring_quantity}</td>
		<td style="padding-right: 5;">{$recurring[list].recurring_frequency}</td>
		<td style="padding-right: 5;">{$recurring[list].recurring_next_delivery|date_format:"%e %B, %Y"}</td>
		<td style="padding-right: 5;"><form action="recurring.php" method="POST"><input type="hidden" name="recurring_id" value="{$recurring[list].recurring_id}"><input type="submit" name="process" value="Edit"><input type="submit" name="process" value="Delete"></form></td>
	</tr>
{/section}

</table>
</center>

{include file='footer.tpl'}
