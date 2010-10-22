{* Smarty *}
	
{include file='header.tpl'}

{if !empty($warning)}<p class="warning">{$warning}</p>{/if}

<table cellspacing="8" width="100%">
<tr>
	
	<td valign="top" class="Control_Panel">
		<p class="Main_Head">Producer Info</p>
<table class="Panel_Inset">
<tr><th colspan="2">{$supplier_details.supplier_name}</th></tr>
<tr><th align="left" valign="top">Address:</th><td>{if !empty($supplier_details.supplier_address1)}{$supplier_details.supplier_address1}<br>{/if}
{if !empty($supplier_details.supplier_address2)}{$supplier_details.supplier_address2}<br>{/if}
{if !empty($supplier_details.supplier_address3)}{$supplier_details.supplier_address3}<br>{/if}
{if !empty($supplier_details.supplier_town)}{$supplier_details.supplier_town}<br>{/if}
{if !empty($supplier_details.supplier_county)}{$supplier_details.supplier_county}  {/if}
{if !empty($supplier_details.supplier_postcode)}{$supplier_details.supplier_postcode}{/if}&nbsp;</td></tr>
<tr><th align="left">Phone:</th><td>{$supplier_details.supplier_phone|default:"&nbsp;"}</td></tr>
<tr><th align="left">Fax:</th><td>{$supplier_details.supplier_fax|default:"&nbsp;"}</td></tr>
<tr><th align="left">E-mail:</th><td>{$supplier_details.supplier_email|default:"&nbsp;"}</td></tr>
</table>
	<br><a href="producer.php?function=editinfo" class="Navlink">Edit this info</a>
	</td>




	<td rowspan="3" valign="top" class="Control_Panel"><p class="Main_Head">Products</p>{if empty($product_list)}<span class="Panel_Inset">No products listed</span>{else}
		<table class="Panel_Inset">{section name=p loop=$product_list}
		<tr>
			<td>{$product_list[p].product_code}</td>
			<td><a href="producer.php?function=productview&id={$product_list[p].product_id}">{$product_list[p].product_name}</a></td>
		</tr>{/section}
		</table>
	{/if}<br><a href="producer.php?function=newproduct" class="Navlink">Add a Product</a></td>

	</tr>
	<tr>
	<td valign="top" class="Control_Panel">
		<p class="Main_Head">Current Orders</p>
		<table class="Panel_Inset">
			<tr><td><p class="Sub_Head">{$future_dates[0].order_date|date_format:"%e %B, %Y"}</p>{if empty($current_orders)}No orders placed<br><br><a href="producer.php?function=configure_calendar&order_date={$future_dates[0].order_date}" class="Navlink">View all</a>{else}
		<table>
			<tr>
				<th align="left">Product</th>
				<th align="right">Avail</th>
				<th align="right">Orders</th>
			</tr>
{section name=c loop=$current_orders max=5}			<tr>
				<td>{$current_orders[c].product_name}</td>
				<td align="right">{$current_orders[c].quantity_available}</td>
				<td align="right">{$current_orders[c].purchase_quantity}</td>
			</tr>{/section}
			<tr><td colspan="3"><a href="producer.php?function=configure_calendar&order_date={$future_dates[0].order_date}" class="Navlink">View all</a></td></tr>
		</table>{/if}
		</td>
		</tr>
{if !empty($closed_orders)}
		<tr>
			<td><p class="Sub_Head">Due for Delivery on {$closed_dates[0].order_date|date_format:"%e %B, %Y"}</p><table>
			<tr>
				<th>Product</th>
				<th>Avail</th>
				<th>Orders</th>
			</tr>
{section name=c loop=$closed_orders}			<tr>
				<td>{$closed_orders[c].product_name}</td>
				<td>{$closed_orders[c].quantity_available}</td>
				<td>{$closed_orders[c].purchase_quantity}</td>
			</tr>{/section}
			<tr><td colspan="3"><a href="producer.php?function=configure_calendar&order_date={$closed_dates[0].order_date}" class="Navlink">View all</a></td></tr>
			</table>
			</td>
		</tr>{/if}
	</table>
	</td>	
	</tr>
	
	
	

	
	<tr>	
	<td class="Control_Panel"><p class="Main_Head">Delivery Schedule</p>
		<table class="Panel_Inset"><tr><td>
		<p class="Sub_Head">Future Dates</p>
<ul>{section name=f loop=$future_dates max=8}<li><a href="producer.php?function=configure_calendar&order_date={$future_dates[f].order_date}">{$future_dates[f].order_date|date_format:"%e %B, %Y"}</a></li>{sectionelse}No future dates available{/section}</ul>
{if !empty($closed_dates)}
<p class="Sub_Head">Closed Dates</p>
<ul>{section name=c loop=$closed_dates}<li><a href="producer.php?function=configure_calendar&order_date={$closed_dates[c].order_date}">{$closed_dates[c].order_date|date_format:"%e %B, %Y"}</a></li>{/section}</ul>
{/if}
<p class="Sub_Head">Past Dates</p>
<ul>{section name=a loop=$past_dates}<li><a href="producer.php?function=configure_calendar&order_date={$past_dates[a].order_date}">{$past_dates[a].order_date|date_format:"%e %B, %Y"}</a></li>{sectionelse}There are no past dates registered{/section}</ul>
		</td></tr></table>
</td>
</tr>
</table>


{include file='footer.tpl'}
