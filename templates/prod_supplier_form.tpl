{* Smarty *}
	
{include file='header.tpl'}

<br>

<center>

<form action="producer.php?function=editinfo" method="POST" class="Control_Panel">
<input type="hidden" name="supplier_id" value="{$supplier.supplier_id}">
<p class="Main_head">Producer/Supplier Information</p>
<table class="Panel_Inset">
	<tr>
		<th align="left">Supplier Name:</th>
		<td><input type="text" name="supplier_name" value="{$supplier.supplier_name}" size="40"></td>
	</tr>
	<tr>
			<th align="left">Account Reference:</th>
		<td><input type="text" name="supplier_account" value="{$supplier.supplier_account}" size="10"></td>
	</tr>
	<tr>
		<th align="left">Supplier Contact:</th>
		<td><input type="text" name="supplier_contact_name" value="{$supplier.supplier_contact_name}" size="30"></td>
	</tr>
	<tr>
		<th align="left">Phone:</th>
		<td><input type="text" name="supplier_phone" value="{$supplier.supplier_phone}" size="15"></td>
	</tr>
	<tr>
		<th align="left">Fax:</th>
		<td><input type="text" name="supplier_fax" value="{$supplier.supplier_fax}" size="15"></td>
	</tr>
	<tr>
		<th align="left">E-mail:</th><td><input type="text" name="supplier_email" value="{$supplier.supplier_email}" size="60"></td>
	</tr>
	<tr>
		<th align="left">Address:</th>
		<td><input type="text" name="supplier_address1" value="{$supplier.supplier_address1}" size="40"><br>
		<input type="text" name="supplier_address2" value="{$supplier.supplier_address2}" size="40"><br>
		<input type="text" name="supplier_address3" value="{$supplier.supplier_address3}" size="40"></td>
	</tr>
	<tr>
		<th align="left">Town:</th><td><input type="text" name="supplier_town" value="{$supplier.supplier_town}" size="20"></td>
	</tr>
	<tr>
		<th align="left">County:</th><td><input type="text" name="supplier_county" value="{$supplier.supplier_county}" size="20"></td>
	</tr>
	<tr>
		<th align="left">Post Code:</th><td><input type="text" name="supplier_postcode" value="{$supplier.supplier_postcode}" size="8"></td>
	</tr>
	<tr>
		<th align="left">Active:</th><td><select name="supplier_active"><option value=0{if $supplier.supplier_active==0} selected{/if}>No</option><option value=1{if $supplier.supplier_active==1} selected{/if}>Yes</option></select></td>
	</tr>
	<tr>
		<th align="left">Available by Default:</th><td><select name="supplier_recurring"><option value=0{if $supplier.supplier_recurring==0} selected{/if}>No</option><option value=1{if $supplier.supplier_recurring==1} selected{/if}>Yes</option></select></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<th align="left" valign="top">More Info:</th>
		<td>Content of this section will display in a pop-up window when a member requests further information.  It will display as formatted.<br>
		{assign var=supplier_info value=$supplier.supplier_info}
		{fckeditor BasePath="thirdparty/FCKeditor/" InstanceName='supplier_info' Value="$supplier_info" Height="300px" ToolbarSet="MyTools"}</td>
	</tr>

</table><br>
<input type="submit" name="submit" value="Save changes">
</form>

{include file='footer.tpl'}
