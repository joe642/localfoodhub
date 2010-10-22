{* Smarty *}
	
{include file='header.tpl'}

<p>{$message}</p>

<form name="product_details" action="products.php" method="post">
{if !empty($product_id)}
<input type="hidden" name="product_id" value="{$product_id}"}
{/if}

<table>
	<tr>
		<th align="left">Product Name:</th>
		<td><input type="text" name="product_name" size="35" value="{$product_name}"></td>
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
		<th align="left">Supplier:</th>
		<td><select name="product_supplier_id" {$supplier_disabled}>{html_options options=$suppliers selected=$product_supplier_id}</select>{$hidden_supplier}{$lock_supplier}</td>
	</tr>
	<tr>
		<th align="left">Product Order Code:</th>
		<td><input type="text" name="product_code" size="12" value="{$product_code}"></td>
	</tr>
	<tr>
		<th align="left">Purchase Cost (per unit):</th>
		<td><input type="text" name="product_cost" size="6" value="{$product_cost}"></td>
	</tr>
	<tr>
		<th align="left">VAT Rate:</th>
		<td>{html_options name="product_VAT_rate" options=$VAT_rates selected=$product_VAT_rate}</td>
	</tr>
	<tr>
		<th align="left">Markup:</th>
		<td><input type="text" name="product_markup" value="{$product_markup}" size="4"> (overrides global value -- leave empty for standard markup)</td>
	</tr>	
	<tr>
		<th align="left">Unit type:</th>
		<td><input type="text" name="product_units" size="12" value="{$product_units}"></td>
	</tr>
	<tr>
		<th align="left">Minimum Order (units):</th>
		<td><input type="text" name="product_pkg_count" size="3" value="{$product_pkg_count}"></td>
	</tr>
	<tr>
		<th align="left">Case Size (units):</th>
		<td><input type="text" name="product_case_size" size="3" value="{$product_case_size}"></td>
	</tr>
	<tr>
		<th align="left" valign="top">Maximum Stock:</th>
		<td><input type="text" name="product_allow_stock" size="3" value="{$product_allow_stock}"><br>
		    <input type="checkbox" name="product_perishable" value="1" {if $product_perishable==1}checked{/if}> Perishable (allow surplus orders, but do not track stock)</td>
	</tr>
	<tr>
		<th align="left">Current Stock:</th>
		<td><input type="text" name="product_current_stock" size="3" value="{$product_current_stock}"></td>
	</tr>
	<tr>
		<th align="left" valign="top">Product Sourcing:</th>
		<td><input type="checkbox" name="product_local"  value="1" {if $product_local==1}checked{/if}> Local<br>
		    <input type="checkbox" name="product_organic"  value="1" {if $product_organic==1}checked{/if}> Organic<br>
		    <input type="checkbox" name="product_fairtrade"  value="1" {if $product_fairtrade==1}checked{/if}> Fair Trade<br></td>
	</tr>
	<tr>
		<th align="left">Currently Available:</a>
		<td><input type="checkbox" name="product_available" value="1" {if $product_available==1}checked{/if}></td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="submit" name="submit_button" value="{$submit_value|default:'Add Product'}">{if !empty($lock_supplier)}<br>
		</form><form action="products.php" name="return" method="POST"><input type="hidden" name="submit_button" value="Show Products"><input type="hidden" name="supplier_id" value="{$product_supplier_id}"}><input type="submit" value="Return to Product List">{/if}</td>
	</tr>
</table>
</form>


{include file='footer.tpl'}
