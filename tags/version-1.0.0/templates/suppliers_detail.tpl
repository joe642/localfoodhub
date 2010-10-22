{* Smarty *}
	
{include file='header.tpl'}

{literal}
<script language="Javascript">
<!--
	function enable_edit()
	{
		document.supplier_details.supplier_name.disabled = false;
		document.supplier_details.supplier_account.disabled = false;
		document.supplier_details.supplier_contact_name.disabled = false;
		document.supplier_details.supplier_phone.disabled = false;
		document.supplier_details.supplier_fax.disabled = false;
		document.supplier_details.supplier_email.disabled = false;
		document.supplier_details.supplier_address1.disabled = false;
		document.supplier_details.supplier_address2.disabled = false;
		document.supplier_details.supplier_address3.disabled = false;
		document.supplier_details.supplier_town.disabled = false;
		document.supplier_details.supplier_county.disabled = false;
		document.supplier_details.supplier_postcode.disabled = false;
		document.supplier_details.supplier_order_method.disabled = false;
		document.supplier_details.supplier_delivery_day.disabled = false;
		document.supplier_details.supplier_active.disabled = false;
		document.supplier_details.supplier_recurring.disabled = false;
		document.supplier_details.submit_button.value = "Update Record";
		}
-->	
</script>
{/literal}	

<br>

<center>

<form action="suppliers.php" name="supplier_details" method="POST">
<input type="hidden" name="supplier_id" value="{$supplier_id}">
<input type="radio" name="editable" onClick="enable_edit();"> Edit this Record<br>
<table>
	<tr>
		<th align="left">Supplier Name:</th>
		<td><input type="text" name="supplier_name" value="{$supplier_name}" size="40" disabled="true"></td>
	</tr>
	<tr>
			<th align="left">Account Reference:</th>
		<td><input type="text" name="supplier_account" value="{$supplier_account}" size="10" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Supplier Contact:</th>
		<td><input type="text" name="supplier_contact_name" value="{$supplier_contact_name}" size="30" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Phone:</th>
		<td><input type="text" name="supplier_phone" value="{$supplier_phone}" size="15" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Fax:</th>
		<td><input type="text" name="supplier_fax" value="{$supplier_fax}" size="15" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">E-mail:</th><td><input type="text" name="supplier_email" value="{$supplier_email}" size="60" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Address:</th>
		<td><input type="text" name="supplier_address1" value="{$supplier_address1}" size="40" disabled="true"><br>
		<input type="text" name="supplier_address2" value="{$supplier_address2}" size="40" disabled="true"><br>
		<input type="text" name="supplier_address3" value="{$supplier_address3}" size="40" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Town:</th><td><input type="text" name="supplier_town" value="{$supplier_town}" size="20" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">County:</th><td><input type="text" name="supplier_county" value="{$supplier_county}" size="20" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Post Code:</th><td><input type="text" name="supplier_postcode" value="{$supplier_postcode}" size="8" disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Order Method:</th><td>{html_options name=supplier_order_method values=$methods output=$methods selected=$supplier_order_method disabled=true}</td>
	</tr>
	<tr>
		<th align="left">Delivery Day:</th><td>{html_options name=supplier_delivery_day options=$days selected=$supplier_delivery_day disabled=true}</td>
	</tr>
	<tr>
		<th align="left">Active:</th><td><input type="checkbox" name="supplier_active" value="1" {if $supplier_active==1}checked{/if} disabled="true"></td>
	</tr>
	<tr>
		<th align="left">Available by Default:</th><td><select name="supplier_recurring" disabled="true"><option value=0{if $supplier_recurring==0} selected{/if}>No</option><option value=1{if $supplier_recurring==1} selected{/if}>Yes</option></select></td></tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="submit_button" value="Show Products"></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="producer_cp" value="Show Producer Control Panel"></td>
	</tr>
</table>
</form>
<h3>Availability</h3>
{foreach from=$calendar key=date item=a}
<input type="checkbox" name="{$date}" {if $a==1}checked{/if} 
onChange="change_availability({$supplier_id},'{$date}');"{if $producer} disabled{/if}> {$date|date_format:"%e %B, 
%Y"}<br>
{/foreach}

<h3>Producer Members</h3>
<table>
{section loop=$producer name=p}
<tr>
	<td valign="top">{$producer[p].member_name}</td>
	<td valign="top"><form action="suppliers.php" name="delete_{$producer[p].member_id}" method="POST">
		<input type="hidden" name="function" value="delete_member">
		<input type="hidden" name="member_id" value="{$producer[p].member_id}">
		<input type="hidden" name="supplier_id" value="{$supplier_id}">
		<a href="javascript: void(0); document.delete_{$producer[p].member_id}.submit();">delete</a></td>
</tr></form>
{/section}
<tr>
	<td colspan="2"><form action="suppliers.php" name="add_producer" method="POST">
		<input type="hidden" name="function" value="add_member">
		<input type="hidden" name="supplier_id" value="{$supplier_id}">
		Add:{html_options name=member_id options=$members onChange="document.add_producer.submit();"}</form></td>
</tr>
</table>

</center>
</form>

{include file='footer.tpl'}
