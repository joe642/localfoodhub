{* Smarty *}
	
{include file='header.tpl'}

<p>
{$email_response}
</p>
	
<h2>Member Packing Orders</h2>

<p>Delivery quantities marked as "---" indicate that no delivery has been recorded for that product.</p> 


{section name=p loop=$members}
<table><tr><td>
<table>
	<tr>
		<th colspan="2" align=left>{$members[p].member_name} - {$members[p].membership_num}</th>
		<th style="text-align: left;">Unit</th>
		<th>Ordered</th>
		<th>Delivered</th>
		<th>Sorted</th>
	</tr>{assign var="member_id" value=$members[p].member_id}
{section name=o loop=$orders}
{if $orders[o].member_id == $member_id}
	<tr>
		<td style="width: 2em;">&nbsp;</td>
		<td style="width: 20em;">{$orders[o].product_name}: {$orders[o].product_description} </td>
		<td style="text-align: left; padding-right: 2em; width: 5em;">{$orders[o].units}</td>
		<td style="text-align: right; padding-right: 2em; width: 3em;">{$orders[o].requested|string_format:"%.0f"}</td>
		<td style="text-align: right; padding-right: 2em; width: 3em;">{$orders[o].delivered}</td>
		<td style="border: thin solid black;">&nbsp;</td>
	</tr>
{/if}{/section}
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
</table>
</td></tr></table>{/section}


{include file='footer.tpl'}
