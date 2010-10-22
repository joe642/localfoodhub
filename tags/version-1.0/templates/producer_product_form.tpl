{* Smarty *}
	
{include file='header.tpl'}

{if !empty($message)}<p>{$message}</p>{/if}
<script language="JavaScript" src="javascripts/gen_validatorv31.js" type="text/javascript"></script>
<!-- how to use validator see http://www.javascript-coder.com/html-form/javascript-form-validation.phtml -->
<form name="product_details" action="producer.php" method="post"  enctype="multipart/form-data" class="Control_Panel">
{if !empty($product_id)}
<input type="hidden" name="product_id" value="{$product_id}">
{/if}

<table>
	<tr>
		<th align="left">Product Name:</th>
		<td><input type="text" name="product_name" size="35" value="{$product_name}"></td>
	</tr>
	<tr>
		<th align="left" valign="top">Product Image:</th>
		<td><input type="file" name="product_pic" size="35" value="">
		{if ($product_pic <> 0)}
			&nbsp;&nbsp;&nbsp;&nbsp;Delete Image
			<input type="checkbox" name="product_pic_delete"  value="1"> 
			<img src="{$thumbnail}" align="right" />
		{else}
			<input type="hidden" name="product_pic_delete"  value="0">
		{/if}
		</td>
	</tr>
	<tr>
		<th align="left">Description:</th>
		<td><input type="text" name="product_description" size="60" value="{$product_description}"></td>
	</tr>
	<tr>
		<th align="left">Category:</th>
		<td>{html_options name=product_category_id options=$categories selected=$product_category_id}</td>
	</tr>
	<tr>
		<th align="left">Your product code:</th>
		<td><input type="text" name="product_code" size="12" value="{$product_code}"></td>
	</tr>
	<tr>
		<th align="left">Price:</th>
		<td><input type="text" name="product_cost" size="6" value="{$product_cost|string_format:"%.2f"}"> (farm gate price, including VAT if applicable)</td>
	</tr>
	<tr>
		<th align="left">VAT rate:</th>
		<td>{html_options name="product_VAT_rate" options=$VAT_rates selected=$product_VAT_rate}</td>
	</tr>
	<tr>
		<th align="left">Unit type:</th>
		<td><input type="text" name="product_units" size="12" value="{$product_units}"></td>
	</tr>
	{* <tr>
		<th align="left">Minimum Order (units):</th>
		<td> *}
		<input type="hidden" name="product_pkg_count" size="3" value="{$product_pkg_count|default:1}">
	{*	</td>
	</tr>
	<tr>
		<th align="left">Case Size (units):</th>
		<td> *}
		<input type="hidden" name="product_case_size" size="3" value="{$product_case_size|default:1}">
	{*	</td>
	</tr> *}
	<tr>
		<th align="left" valign="top">Default quantity available:</th>
		<td><input type="text" name="product_default_quantity_available" size="3" value="{$product_default_quantity_available}"> (leave blank for unlimited)</td>
	</tr>
	<tr>
		<th align="left" valign="top">Product Sourcing:</th>
		<td>
			<input type="hidden" name="product_local" value="{$product_local}">
		    <input type="checkbox" name="product_organic"  value="1" {if $product_organic==1}checked{/if}> Organic<br>
			<input type="hidden" name="product_fairtrade" value="{$product_fairtrade}">
			</td>
	</tr>
	<tr>
		<th align="left">Currently Available:</a>
	  <td><input type="checkbox" name="product_available" value="1" {if $product_available==1}checked{/if}></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<th align="left" valign="top">More Info:</th>
		<td>Content of this section will display in a pop-up window when a member requests further information.  It will display as formatted.<br>
		{fckeditor BasePath="thirdparty/FCKeditor/" InstanceName='product_more_info' Value="$product_more_info" Height="300px" ToolbarSet="MyTools"}</td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="hidden" name="function" value="{$function}"><input type="submit" name="submit_button" value="{$submit_value|default:'Add Product'}"></form>
		{if !empty($product_id)}<br><br>
		<form action="producer.php" method="POST"><input type="hidden" name="function" value="archiveproduct"><input type="hidden" name="product_id" value="{$product_id}"><input type="submit" value="Delete"></form>{/if}</td>
	</tr>
	<tr>
		<td colspan="2">{if empty($returnpage)}<a href="producer.php" class="Navlink">Return to Control Panel</a>{else}<a href="{$returnpage}" class="Navlink">Go back</a>{/if}</td>
	</tr>
</table>
</form>
<SCRIPT language="JavaScript"  type="text/javascript">
var frmvalidator  = new Validator("product_details");
frmvalidator.addValidation("product_name","req","Please enter a Product Name");
frmvalidator.addValidation("product_description","req","Please enter a Product Description");
frmvalidator.addValidation("product_description","minlen",3);
frmvalidator.addValidation("product_code","req","Please enter your Product Code");

frmvalidator.addValidation("product_cost","dec","Check the price");
frmvalidator.addValidation("product_cost","gt=0","Price must be greater than 0");
</script>

{include file='footer.tpl'}
