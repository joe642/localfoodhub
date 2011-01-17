{* Smarty *}

{include file='header.tpl'}

<div class="Control_Panel">
<p class="Main_Head">Product List for {$order_date|date_format:"%e %B, %Y"}</p>
{* comparison on different date formats always fails
{if $order_date <= $smarty.session.order_date}
<p class="warning">Orders for this day have closed.  No changes to this form will have any effect.</p>{/if}
*}
<form action="producer.php?order_date={$order_date}" method="POST">
<input type="hidden" name="function" value="update_products">
<table class="Panel_Inset">
<tr>
	<th align="left" valign="bottom">Product</th>
	<th align="left" valign="bottom">Unit</th>
	<th align="left" valign="bottom">Current<br>Price</th>
	<th align="left" valign="bottom">Quantity<br>Available</th>
	<th align="left" valign="bottom">Ordered</th>
	<th align="left" valign="bottom">Pending<br>Payment</th>
	<th align="left" valign="bottom">Delivered</th>
</tr>
{section name=p loop=$product_list}
<tr>
	<td><input type="hidden" name="product_id[{$smarty.section.p.index}]" value="{$product_list[p].product_id}"><a href="producer.php?function=productview&id={$product_list[p].product_id}&returnpage=1">{$product_list[p].product_name}</a></td>
	<td>{$product_list[p].product_units}</td>
	<td><input type="text" name="current_price[{$product_list[p].product_id}]" value='{$product_list[p].current_price|string_format:"%.2f"}' size="6" {if !empty($product_list[p].purchase_quantity)} disabled="TRUE"{/if}><input type="hidden" name="hidden_price[{$product_list[p].product_id}]" value="{$product_list[p].current_price}"></td>
	<td><input type="text" name="quantity_available[{$product_list[p].product_id}]" value="{$product_list[p].quantity_available}" size="6"><input type="hidden" name="hidden_available[{$product_list[p].product_id}]" value="{$product_list[p].quantity_available}" size="6"></td>
	<td>{$product_list[p].purchase_quantity}</td>
	<td>{$product_list[p].pending}</td>
	<td>{$product_list[p].delivered_quantity|default:'N/A'}</td>
</tr>{/section}
<tr>
	<td colspan="6" align="right"><input type="submit" value="Update"></form></td>
</tr>
<tr>
	<td colspan="6" ><dl>
		<dt>Current price:</dt>
		<dd>Will set the product price for this day only</dd>
		<dt>Quantity available:</dt> 
		<dd>leave blank for no limit, set to zero to make product inactive for this day</dd>
		</dl>
		<a href="producer.php" class="Navlink">Return to Control Panel</a>
	</td>
</tr>

</table>

</div>


{include file='footer.tpl'}
