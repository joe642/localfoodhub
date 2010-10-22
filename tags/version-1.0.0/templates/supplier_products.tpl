{* Smarty *}
	
{include file='header.tpl'}

<script language="Javascript">
<!--
{section name=j loop=$products}
	function enable_edit_{$products[j].product_id}()
{literal}	{{/literal}
		document.product_list.product_cost_{$products[j].product_id}.disabled = false;
		document.product_list.product_available_{$products[j].product_id}.disabled = false;
		document.product_list.submit_button.disabled = false;
{literal}		}{/literal}
{/section} 
-->
</script>

<center><a href="suppliers.php">Back to Suppliers List</a></center>


<h2>{$supplier_name} Products</h2>	
<p>{$message}</p>

<form name="product_list" action="products.php" method="POST">
<input type="hidden" name="supplier_id" value="{$supplier_id}">
<table>
	<tr>		
		<th>&nbsp;</th>
		<th align="left">Product</th>
		<th align="left">Description</th>
		<th align="left" colspan="3">Sourcing</th>
		<th align="left">Cost per unit</th>
		<th align="left">Available</th>
	</tr>
{section name=p loop=$products}
	<tr>
		<td><input type="checkbox" name="product_id[]" value="{$products[p].product_id}" onClick="enable_edit_{$products[p].product_id}();"></td>
		<td><a href="products.php?product_id={$products[p].product_id}&supplier_id={$supplier_id}">{$products[p].product_name}</a></td>
		<td>{$products[p].product_description}</td>
		<td>{if $products[p].product_organic == 1}<img src="../images/organic.jpg" alt="Organic">{else}&nbsp;{/if}</td>
		<td>{if $products[p].product_fairtrade == 1}<img src="../images/fairtrade.jpg" alt="Fair Trade">{else}&nbsp;{/if}</td>
		<td>{if $products[p].product_local == 1}<img src="../images/local.jpg" alt="Local">{else}&nbsp;{/if}</td>
		<td><input type="text" name="product_cost_{$products[p].product_id}" value="{$products[p].product_cost}" size="5" disabled="true"></td>
		<td><input type="checkbox" name="product_available_{$products[p].product_id}" value="1" {if $products[p].product_available==1}checked {/if} disabled="true"></td>
	</tr>
{/section}
	<tr>
		<td colspan="7" align="right"><input type="submit" name="submit_button" value="Update Product List" disabled="true"></td>
	</tr>
</table>
</form>
<center><a href="products.php?supplier_id={$supplier_id}">Add a Product from {$supplier_name}</a></center>
<br>
{include file='footer.tpl'}
