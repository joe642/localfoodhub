{* Smarty *}
	
{include file='header.tpl'}
	
	<p class='warning'>{$warning}</p>

	<form action="recurring.php" method="POST">
	<input type="hidden" name="recurring_id" value="{$recurring_id}"}
	
	<table>
		<tr>
			<th valign="top" align="left">Product</th>
			<td>{$recurring_product_name}<br>
			{$recurring_product_description}</td>
		</tr>
		<tr>
			<th align="left">Quantity</th>
			<td><input type="text" name="recurring_quantity" size="3" value="{$recurring_quantity}"></td>
		</tr>
		<tr>
			<th align="left">Frequency</th>
			<td>{html_options name=recurring_frequency options=$frequencies selected=$recurring_frequency}</td>
		</tr>
		<tr>
			<th align="left">Next Delivery</th>
			<td>{html_options name=recurring_next_order options=$future_dates selected=$recurring_next_delivery}</td>
		</tr>
		<tr>
			<th align="right">&nbsp;</th>
			<td><input type="submit" name="process" value="Update"></td>
		</tr>
		<tr>
			<th align="right">&nbsp;</th>
			<td><input type="submit" name="process" value="Delete"></td>
		</tr>
	</table>
	</form> 

{include file='footer.tpl'}
